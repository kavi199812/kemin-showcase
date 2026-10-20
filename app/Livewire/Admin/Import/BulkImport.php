<?php

namespace App\Livewire\Admin\Import;

use App\Models\Company;
use App\Models\Product;
use App\Services\BulkImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Bulk Import — Admin')]
class BulkImport extends Component
{
    use WithFileUploads;

    public string $activeTab = 'products'; // 'products' or 'companies'

    /**
     * @var UploadedFile|null
     */
    public $csvFile = null;

    /**
     * @var UploadedFile|null
     */
    public $zipFile = null;

    /**
     * @var array<UploadedFile>
     */
    public array $imageFiles = [];

    public bool $updateExisting = true;

    public bool $createCategories = true;

    public bool $isProcessing = false;

    /**
     * @var array{total: int, created: int, updated: int, skipped: int, images_attached: int, errors: list<string>, warnings: list<string>}|null
     */
    public ?array $resultSummary = null;

    public function switchTab(string $tab): void
    {
        if (in_array($tab, ['products', 'companies'], true)) {
            $this->activeTab = $tab;
            $this->reset(['csvFile', 'zipFile', 'imageFiles', 'resultSummary']);
            $this->resetErrorBag();
        }
    }

    public function removeSelectedImage(int $index): void
    {
        if (isset($this->imageFiles[$index])) {
            unset($this->imageFiles[$index]);
            $this->imageFiles = array_values($this->imageFiles);
        }
    }

    public function removeZip(): void
    {
        $this->zipFile = null;
    }

    public function removeCsv(): void
    {
        $this->csvFile = null;
    }

    public function clearResults(): void
    {
        $this->resultSummary = null;
    }

    public function import(BulkImportService $importService): void
    {
        if ($this->activeTab === 'products') {
            Gate::authorize('create', Product::class);
        } else {
            Gate::authorize('create', Company::class);
        }

        $this->validate([
            'csvFile' => ['required', 'file', 'mimes:csv,txt', 'max:15360'],
            'zipFile' => ['nullable', 'file', 'mimes:zip', 'max:102400'], // max 100MB
            'imageFiles.*' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,svg,gif', 'max:10240'],
        ], [
            'csvFile.required' => 'Please select a CSV file to upload.',
            'csvFile.mimes' => 'The file must be a valid .csv file.',
            'zipFile.mimes' => 'The archive must be a valid .zip file.',
            'imageFiles.*.mimes' => 'Images must be JPEG, PNG, WebP, or SVG format.',
        ]);

        $this->isProcessing = true;
        $tempDir = null;

        try {
            // Parse CSV
            $csvPath = $this->csvFile->getRealPath();
            $rows = $importService->parseCsv($csvPath);

            if (empty($rows)) {
                $this->addError('csvFile', 'The uploaded CSV file is empty or contains no valid rows.');
                $this->isProcessing = false;
                return;
            }

            // Build image pool from uploaded files and/or extracted zip
            $poolData = $importService->buildImagePool($this->imageFiles, $this->zipFile);
            $imagePool = $poolData['pool'];
            $tempDir = $poolData['tempDir'];

            $options = [
                'updateExisting' => $this->updateExisting,
                'createCategories' => $this->createCategories,
                'userId' => auth()->id(),
            ];

            if ($this->activeTab === 'products') {
                $stats = $importService->importProducts($rows, $imagePool, $options);
            } else {
                $stats = $importService->importCompanies($rows, $imagePool, $options);
            }

            $this->resultSummary = $stats;

            // Reset files after successful run
            $this->reset(['csvFile', 'zipFile', 'imageFiles']);

            $typeLabel = $this->activeTab === 'products' ? 'Products' : 'Companies';
            $this->dispatch('notify', message: "Bulk import for {$typeLabel} completed! Created: {$stats['created']}, Updated: {$stats['updated']}.");
        } catch (\Throwable $e) {
            $this->addError('csvFile', 'Import error: ' . $e->getMessage());
        } finally {
            $importService->cleanupTempDir($tempDir);
            $this->isProcessing = false;
        }
    }

    public function render(): View
    {
        Gate::authorize('viewAny', Product::class);

        return view('livewire.admin.import.bulk-import');
    }
}
