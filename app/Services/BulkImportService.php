<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use enshrined\svgSanitize\Sanitizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Mews\Purifier\Facades\Purifier;
use ZipArchive;

class BulkImportService
{
    protected ImageManager $imageManager;

    protected Sanitizer $svgSanitizer;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver);
        $this->svgSanitizer = new Sanitizer;
        $this->svgSanitizer->minify(true);
    }

    /**
     * Parse CSV file into an array of associative rows.
     *
     * @return array<int, array<string, string>>
     */
    public function parseCsv(string $filePath): array
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            throw new \RuntimeException("CSV file cannot be read: {$filePath}");
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \RuntimeException("Empty or invalid CSV file.");
        }

        // Remove UTF-8 BOM if present
        $bom = pack('H*', 'EFBBBF');
        $content = preg_replace("/^{$bom}/", '', $content);

        // Normalize line endings
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        $tempStream = fopen('php://memory', 'r+');
        fwrite($tempStream, $content);
        rewind($tempStream);

        $rows = [];
        $headers = null;

        while (($data = fgetcsv($tempStream, 0, ',', '"', '\\')) !== false) {
            // Skip entirely empty lines
            if (count($data) === 1 && $data[0] === null) {
                continue;
            }

            if ($headers === null) {
                // Sanitize and lowercase header keys
                $headers = array_map(function ($h) {
                    $cleaned = trim((string) $h);
                    $cleaned = preg_replace('/[\x00-\x1F\x7F]/u', '', $cleaned);
                    return strtolower(str_replace([' ', '-'], '_', $cleaned));
                }, $data);
                continue;
            }

            if (count($data) < count($headers)) {
                $data = array_pad($data, count($headers), '');
            } else {
                $data = array_slice($data, 0, count($headers));
            }

            $row = [];
            foreach ($headers as $index => $header) {
                if (filled($header)) {
                    $row[$header] = trim((string) ($data[$index] ?? ''));
                }
            }

            // Only add rows that have at least one non-empty value
            if (count(array_filter($row, fn ($v) => $v !== '')) > 0) {
                $rows[] = $row;
            }
        }

        fclose($tempStream);

        return $rows;
    }

    /**
     * Build an image pool mapping filename (lowercase) => absolute file path.
     *
     * @param array<UploadedFile> $uploadedImages
     * @param UploadedFile|null $zipFile
     * @return array{pool: array<string, string>, tempDir: ?string}
     */
    public function buildImagePool(array $uploadedImages = [], ?UploadedFile $zipFile = null): array
    {
        $pool = [];
        $tempDir = null;

        // 1. Process directly uploaded multiple images
        foreach ($uploadedImages as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $name = strtolower($file->getClientOriginalName());
                $pool[$name] = $file->getRealPath();
            }
        }

        // 2. Process ZIP archive if provided
        if ($zipFile instanceof UploadedFile && $zipFile->isValid()) {
            $zip = new ZipArchive;
            $zipPath = $zipFile->getRealPath();

            if ($zip->open($zipPath) === true) {
                $tempDir = storage_path('app/temp_bulk_' . uniqid());
                File::ensureDirectoryExists($tempDir);

                $zip->extractTo($tempDir);
                $zip->close();

                // Recursively scan extracted folder for supported images
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif'];
                $allFiles = File::allFiles($tempDir);

                foreach ($allFiles as $file) {
                    $ext = strtolower($file->getExtension());
                    if (in_array($ext, $allowedExtensions, true)) {
                        $filename = strtolower($file->getFilename());
                        // Store in pool
                        $pool[$filename] = $file->getRealPath();
                    }
                }
            }
        }

        return [
            'pool' => $pool,
            'tempDir' => $tempDir,
        ];
    }

    /**
     * Clean up extracted temp directory.
     */
    public function cleanupTempDir(?string $tempDir): void
    {
        if ($tempDir && File::isDirectory($tempDir)) {
            File::deleteDirectory($tempDir);
        }
    }

    /**
     * Import Products from CSV data.
     *
     * @param array<int, array<string, string>> $rows
     * @param array<string, string> $imagePool
     * @param array{updateExisting?: bool, createCategories?: bool, userId?: ?int} $options
     * @return array{total: int, created: int, updated: int, skipped: int, images_attached: int, errors: list<string>, warnings: list<string>}
     */
    public function importProducts(array $rows, array $imagePool = [], array $options = []): array
    {
        $updateExisting = $options['updateExisting'] ?? true;
        $createCategories = $options['createCategories'] ?? true;
        $userId = $options['userId'] ?? auth()->id();

        $stats = [
            'total' => count($rows),
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'images_attached' => 0,
            'errors' => [],
            'warnings' => [],
        ];

        foreach ($rows as $index => $row) {
            $lineNum = $index + 2; // Accounting for 1-based index and header line

            $name = $row['name'] ?? $row['product_name'] ?? $row['title'] ?? '';
            if (blank($name)) {
                $stats['skipped']++;
                $stats['errors'][] = "Row #{$lineNum}: Product name is missing. Skipped.";
                continue;
            }

            $jobNumber = filled($row['job_number'] ?? null) ? trim((string) $row['job_number']) : null;
            $slug = filled($row['slug'] ?? null) ? Str::slug($row['slug']) : Str::slug($name);

            // Find existing product by job_number, slug, or name
            $product = null;
            if ($jobNumber) {
                $product = Product::where('job_number', $jobNumber)->first();
            }
            if (! $product) {
                $product = Product::where('slug', $slug)->orWhere('name', $name)->first();
            }

            if ($product && ! $updateExisting) {
                $stats['skipped']++;
                $stats['warnings'][] = "Row #{$lineNum}: Product '{$name}' already exists. Skipped.";
                continue;
            }

            // Resolve Category
            $categoryId = null;
            $categoryName = $row['category'] ?? $row['category_name'] ?? '';
            if (filled($categoryName)) {
                $categorySlug = Str::slug($categoryName);
                $category = Category::where('slug', $categorySlug)->orWhere('name', $categoryName)->first();

                if (! $category && $createCategories) {
                    $category = Category::create([
                        'name' => $categoryName,
                        'slug' => $categorySlug,
                        'is_active' => true,
                    ]);
                }

                if ($category) {
                    $categoryId = $category->id;
                }
            }

            // Parse specs
            $specs = $this->parseSpecsString($row['specs'] ?? $row['specifications'] ?? '');

            // Parse boolean flags
            $isFeatured = $this->parseBoolean($row['is_featured'] ?? $row['featured'] ?? false);
            $isActive = $this->parseBoolean($row['is_active'] ?? $row['active'] ?? true);
            $sortOrder = isset($row['sort_order']) && is_numeric($row['sort_order']) ? (int) $row['sort_order'] : 0;

            // Sanitize descriptions
            $shortDesc = filled($row['short_description'] ?? null) ? Str::limit(trim($row['short_description']), 200) : null;
            $desc = filled($row['description'] ?? null) ? Purifier::clean($row['description']) : null;

            try {
                DB::beginTransaction();

                $isNew = false;
                if (! $product) {
                    $isNew = true;
                    // Ensure slug uniqueness
                    $uniqueSlug = $slug;
                    $counter = 1;
                    while (Product::where('slug', $uniqueSlug)->exists()) {
                        $uniqueSlug = "{$slug}-{$counter}";
                        $counter++;
                    }

                    $product = Product::create([
                        'category_id' => $categoryId,
                        'name' => $name,
                        'slug' => $uniqueSlug,
                        'job_number' => $jobNumber,
                        'short_description' => $shortDesc,
                        'description' => $desc,
                        'specs' => $specs,
                        'is_featured' => $isFeatured,
                        'is_active' => $isActive,
                        'sort_order' => $sortOrder,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                    $stats['created']++;
                } else {
                    $product->update([
                        'category_id' => $categoryId ?? $product->category_id,
                        'name' => $name,
                        'job_number' => $jobNumber ?? $product->job_number,
                        'short_description' => $shortDesc ?? $product->short_description,
                        'description' => $desc ?? $product->description,
                        'specs' => ! empty($specs) ? $specs : $product->specs,
                        'is_featured' => $isFeatured,
                        'is_active' => $isActive,
                        'sort_order' => $sortOrder,
                        'updated_by' => $userId,
                    ]);
                    $stats['updated']++;
                }

                // Associate partner companies if present in CSV
                $companiesStr = $row['companies'] ?? $row['partner_companies'] ?? '';
                if (filled($companiesStr)) {
                    $this->syncProductCompanies($product, $companiesStr);
                }

                // Match and attach images
                $imagesAttached = $this->attachProductImages($product, $row, $imagePool);
                $stats['images_attached'] += $imagesAttached;

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                $stats['skipped']++;
                $stats['errors'][] = "Row #{$lineNum} ('{$name}'): " . $e->getMessage();
            }
        }

        return $stats;
    }

    /**
     * Import Companies from CSV data.
     *
     * @param array<int, array<string, string>> $rows
     * @param array<string, string> $imagePool
     * @param array{updateExisting?: bool, userId?: ?int} $options
     * @return array{total: int, created: int, updated: int, skipped: int, images_attached: int, errors: list<string>, warnings: list<string>}
     */
    public function importCompanies(array $rows, array $imagePool = [], array $options = []): array
    {
        $updateExisting = $options['updateExisting'] ?? true;
        $userId = $options['userId'] ?? auth()->id();

        $stats = [
            'total' => count($rows),
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'images_attached' => 0,
            'errors' => [],
            'warnings' => [],
        ];

        foreach ($rows as $index => $row) {
            $lineNum = $index + 2;

            $name = $row['name'] ?? $row['company_name'] ?? '';
            if (blank($name)) {
                $stats['skipped']++;
                $stats['errors'][] = "Row #{$lineNum}: Company name is missing. Skipped.";
                continue;
            }

            $slug = filled($row['slug'] ?? null) ? Str::slug($row['slug']) : Str::slug($name);

            $company = Company::where('slug', $slug)->orWhere('name', $name)->first();

            if ($company && ! $updateExisting) {
                $stats['skipped']++;
                $stats['warnings'][] = "Row #{$lineNum}: Company '{$name}' already exists. Skipped.";
                continue;
            }

            $isFeatured = $this->parseBoolean($row['is_featured'] ?? $row['featured'] ?? false);
            $isActive = $this->parseBoolean($row['is_active'] ?? $row['active'] ?? true);
            $sortOrder = isset($row['sort_order']) && is_numeric($row['sort_order']) ? (int) $row['sort_order'] : 0;
            $country = filled($row['country'] ?? null) ? trim($row['country']) : 'Sri Lanka';
            $industry = filled($row['industry'] ?? null) ? trim($row['industry']) : null;
            $website = filled($row['website'] ?? null) ? trim($row['website']) : null;
            $desc = filled($row['description'] ?? null) ? Purifier::clean($row['description']) : null;

            try {
                DB::beginTransaction();

                if (! $company) {
                    $uniqueSlug = $slug;
                    $counter = 1;
                    while (Company::where('slug', $uniqueSlug)->exists()) {
                        $uniqueSlug = "{$slug}-{$counter}";
                        $counter++;
                    }

                    $company = Company::create([
                        'name' => $name,
                        'slug' => $uniqueSlug,
                        'description' => $desc,
                        'website' => $website,
                        'country' => $country,
                        'industry' => $industry,
                        'is_featured' => $isFeatured,
                        'is_active' => $isActive,
                        'sort_order' => $sortOrder,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                    $stats['created']++;
                } else {
                    $company->update([
                        'name' => $name,
                        'description' => $desc ?? $company->description,
                        'website' => $website ?? $company->website,
                        'country' => $country ?? $company->country,
                        'industry' => $industry ?? $company->industry,
                        'is_featured' => $isFeatured,
                        'is_active' => $isActive,
                        'sort_order' => $sortOrder,
                        'updated_by' => $userId,
                    ]);
                    $stats['updated']++;
                }

                // Match and attach logo
                $logoAttached = $this->attachCompanyLogo($company, $row, $imagePool);
                $stats['images_attached'] += $logoAttached;

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                $stats['skipped']++;
                $stats['errors'][] = "Row #{$lineNum} ('{$name}'): " . $e->getMessage();
            }
        }

        return $stats;
    }

    /**
     * Attach matched images to a Product.
     *
     * @param array<string, string> $row
     * @param array<string, string> $pool
     */
    protected function attachProductImages(Product $product, array $row, array $pool): int
    {
        if (empty($pool)) {
            return 0;
        }

        $matchedPaths = [];

        // 1. Check explicit 'images' or 'image' column in CSV
        $explicitImages = $row['images'] ?? $row['image'] ?? '';
        if (filled($explicitImages)) {
            $filesList = array_map('trim', explode(',', $explicitImages));
            foreach ($filesList as $item) {
                $key = strtolower(basename($item));
                if (isset($pool[$key])) {
                    $matchedPaths[$pool[$key]] = true;
                }
            }
        }

        // 2. Prefix matching with job_number (e.g. JB-1001.jpg, JB-1001_1.jpg, JB-1001-box.png)
        if (filled($product->job_number)) {
            $jnLower = strtolower(trim($product->job_number));
            foreach ($pool as $filename => $path) {
                $base = pathinfo($filename, PATHINFO_FILENAME);
                if ($base === $jnLower || str_starts_with($base, $jnLower . '_') || str_starts_with($base, $jnLower . '-')) {
                    $matchedPaths[$path] = true;
                }
            }
        }

        // 3. Match with product slug (e.g. luxury-tea-box.jpg)
        $slugLower = strtolower($product->slug);
        foreach ($pool as $filename => $path) {
            $base = pathinfo($filename, PATHINFO_FILENAME);
            if ($base === $slugLower || str_starts_with($base, $slugLower . '_') || str_starts_with($base, $slugLower . '-')) {
                $matchedPaths[$path] = true;
            }
        }

        $attachedCount = 0;
        foreach (array_keys($matchedPaths) as $filePath) {
            if (! file_exists($filePath)) {
                continue;
            }

            try {
                // Strip EXIF and convert to WebP raster for consistency & security
                $img = $this->imageManager->read($filePath);
                $cleanFilename = Str::uuid()->toString() . '.webp';
                $cleanPath = storage_path('app/temp_' . $cleanFilename);

                $img->toWebp(85)->save($cleanPath);

                $product->addMedia($cleanPath)
                    ->usingFileName($cleanFilename)
                    ->toMediaCollection('gallery');

                if (file_exists($cleanPath)) {
                    @unlink($cleanPath);
                }

                $attachedCount++;
            } catch (\Throwable $e) {
                // Fallback: try adding file directly if intervention failed
                try {
                    $product->addMedia($filePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery');
                    $attachedCount++;
                } catch (\Throwable) {
                    // Ignore individual image failure
                }
            }
        }

        return $attachedCount;
    }

    /**
     * Attach matched logo to a Company.
     *
     * @param array<string, string> $row
     * @param array<string, string> $pool
     */
    protected function attachCompanyLogo(Company $company, array $row, array $pool): int
    {
        if (empty($pool)) {
            return 0;
        }

        $matchedPath = null;

        // 1. Check explicit 'logo' or 'image' column in CSV
        $explicitLogo = $row['logo'] ?? $row['image'] ?? '';
        if (filled($explicitLogo)) {
            $key = strtolower(trim(basename($explicitLogo)));
            if (isset($pool[$key])) {
                $matchedPath = $pool[$key];
            }
        }

        // 2. Match with company slug or name
        if (! $matchedPath) {
            $slugLower = strtolower($company->slug);
            $nameClean = strtolower(Str::slug($company->name));

            foreach ($pool as $filename => $path) {
                $base = pathinfo($filename, PATHINFO_FILENAME);
                if ($base === $slugLower || $base === $nameClean || str_starts_with($base, $slugLower . '_') || str_starts_with($base, $nameClean . '_')) {
                    $matchedPath = $path;
                    break;
                }
            }
        }

        if (! $matchedPath || ! file_exists($matchedPath)) {
            return 0;
        }

        try {
            $ext = strtolower(pathinfo($matchedPath, PATHINFO_EXTENSION));

            if ($ext === 'svg') {
                $cleanContent = $this->svgSanitizer->sanitize(file_get_contents($matchedPath));
                $cleanFilename = Str::uuid()->toString() . '.svg';
                $cleanPath = storage_path('app/temp_' . $cleanFilename);
                file_put_contents($cleanPath, $cleanContent);

                $company->addMedia($cleanPath)
                    ->usingFileName($cleanFilename)
                    ->toMediaCollection('logo');

                @unlink($cleanPath);
            } else {
                $img = $this->imageManager->read($matchedPath);
                $cleanFilename = Str::uuid()->toString() . '.webp';
                $cleanPath = storage_path('app/temp_' . $cleanFilename);
                $img->toWebp(90)->save($cleanPath);

                $company->addMedia($cleanPath)
                    ->usingFileName($cleanFilename)
                    ->toMediaCollection('logo');

                @unlink($cleanPath);
            }

            return 1;
        } catch (\Throwable) {
            return 0;
        }
    }

    /**
     * Parse specifications string into list of ['key' => ..., 'value' => ...].
     * Supports formats:
     * - "Material: Greyboard; Finishing: Matte; GSM: 350"
     * - "Material: Greyboard | Finishing: Matte"
     * - JSON array string: [{"key":"Material","value":"Kraft"}]
     *
     * @return list<array{key: string, value: string}>
     */
    public function parseSpecsString(string $raw): array
    {
        if (blank($raw)) {
            return [];
        }

        $trimmed = trim($raw);

        // Check if JSON
        if (str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']')) {
            $decoded = json_decode($trimmed, true);
            if (is_array($decoded)) {
                $specs = [];
                foreach ($decoded as $item) {
                    if (is_array($item) && isset($item['key'], $item['value'])) {
                        $specs[] = [
                            'key' => Str::limit(trim((string) $item['key']), 80),
                            'value' => Str::limit(trim((string) $item['value']), 120),
                        ];
                    }
                }
                return $specs;
            }
        }

        // Delimiter could be semicolon or pipe or newline
        $delimiters = [';', '|', "\n"];
        $chosenDelimiter = ';';
        foreach ($delimiters as $d) {
            if (str_contains($raw, $d)) {
                $chosenDelimiter = $d;
                break;
            }
        }

        $parts = explode($chosenDelimiter, $raw);
        $specs = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if (blank($part)) {
                continue;
            }

            if (str_contains($part, ':')) {
                [$k, $v] = explode(':', $part, 2);
            } elseif (str_contains($part, '=')) {
                [$k, $v] = explode('=', $part, 2);
            } else {
                $k = 'Specification';
                $v = $part;
            }

            $key = trim($k);
            $val = trim($v);

            if (filled($key) && filled($val)) {
                $specs[] = [
                    'key' => Str::limit($key, 80),
                    'value' => Str::limit($val, 120),
                ];
            }
        }

        return $specs;
    }

    /**
     * Sync companies to product from comma-separated names or slugs.
     */
    protected function syncProductCompanies(Product $product, string $companiesString): void
    {
        $names = array_filter(array_map('trim', explode(',', $companiesString)));
        if (empty($names)) {
            return;
        }

        $companyIds = [];
        $order = 0;
        foreach ($names as $name) {
            $slug = Str::slug($name);
            $company = Company::where('slug', $slug)->orWhere('name', $name)->first();

            if (! $company) {
                // Auto create company if not exists
                $company = Company::create([
                    'name' => $name,
                    'slug' => $slug,
                    'country' => 'Sri Lanka',
                    'is_active' => true,
                ]);
            }

            $companyIds[$company->id] = [
                'relationship_note' => 'Client Partner',
                'sort_order' => $order++,
            ];
        }

        if (! empty($companyIds)) {
            $product->companies()->syncWithoutDetaching($companyIds);
        }
    }

    /**
     * Convert string / mixed to boolean.
     */
    protected function parseBoolean(mixed $val): bool
    {
        if (is_bool($val)) {
            return $val;
        }

        $str = strtolower(trim((string) $val));

        return in_array($str, ['1', 'true', 'yes', 'y', 'on', 'active'], true);
    }
}
