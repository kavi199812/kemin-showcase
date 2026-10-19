<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;
use Mews\Purifier\Facades\Purifier;

class ProductFormObject extends Form
{
    #[Locked]
    public ?int $id = null;

    public ?int $category_id = null;

    public string $name = '';

    public string $slug = '';

    public ?string $job_number = '';

    public ?string $short_description = '';

    public ?string $description = '';

    /**
     * @var list<array{key: string, value: string}>
     */
    public array $specs = [];

    public bool $is_featured = false;

    public bool $is_active = true;

    public int $sort_order = 0;

    /**
     * @var array<int, array{selected?: bool, relationship_note?: ?string, since_year?: ?int}>
     */
    public array $company_relationships = [];

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120', Rule::unique('products', 'name')->ignore($this->id)],
            'slug' => ['required', 'string', 'max:140', Rule::unique('products', 'slug')->ignore($this->id)],
            'job_number' => ['nullable', 'string', 'max:80'],
            'short_description' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'specs' => ['nullable', 'array'],
            'specs.*.key' => ['required_with:specs.*.value', 'nullable', 'string', 'max:80'],
            'specs.*.value' => ['required_with:specs.*.key', 'nullable', 'string', 'max:120'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer'],
            'company_relationships' => ['nullable', 'array'],
        ];
    }

    public function setProduct(Product $product): void
    {
        $this->id = $product->id;
        $this->category_id = $product->category_id;
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->job_number = $product->job_number ?? '';
        $this->short_description = $product->short_description ?? '';
        $this->description = $product->description ?? '';
        $rawSpecs = $product->getAttribute('specs');
        /** @var list<array{key: string, value: string}> $specsArray */
        $specsArray = is_array($rawSpecs) ? array_values($rawSpecs) : [];
        $this->specs = $specsArray;
        $this->is_featured = (bool) $product->is_featured;
        $this->is_active = (bool) $product->is_active;
        $this->sort_order = (int) $product->sort_order;

        $relationships = [];
        foreach ($product->companies as $company) {
            /** @var Pivot|null $pivot */
            $pivot = $company->getRelationValue('pivot');
            $relationships[$company->id] = [
                'selected' => true,
                'relationship_note' => $pivot ? (string) $pivot->getAttribute('relationship_note') : null,
                'since_year' => $pivot && $pivot->getAttribute('since_year') !== null ? (int) $pivot->getAttribute('since_year') : null,
            ];
        }
        $this->company_relationships = $relationships;
    }

    public function addSpec(): void
    {
        $this->specs[] = ['key' => '', 'value' => ''];
    }

    public function removeSpec(int $index): void
    {
        unset($this->specs[$index]);
        $this->specs = array_values($this->specs);
    }

    public function generateSlug(): void
    {
        if (blank($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function store(?int $userId = null): Product
    {
        $this->validate();

        // Purify description using HTMLPurifier allow-list
        $purifiedDescription = filled($this->description) ? Purifier::clean($this->description) : null;

        // Filter valid specs
        $cleanSpecs = array_values(array_filter($this->specs, fn ($s) => trim($s['key']) !== ''));

        $product = Product::create([
            'category_id' => $this->category_id ?: null,
            'name' => $this->name,
            'slug' => $this->slug,
            'job_number' => filled($this->job_number) ? trim($this->job_number) : null,
            'short_description' => $this->short_description ?: null,
            'description' => $purifiedDescription,
            'specs' => $cleanSpecs,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        $this->syncCompanies($product);

        return $product;
    }

    public function update(?int $userId = null): Product
    {
        $this->validate();

        $product = Product::findOrFail($this->id);

        $purifiedDescription = filled($this->description) ? Purifier::clean($this->description) : null;
        $cleanSpecs = array_values(array_filter($this->specs, fn ($s) => trim($s['key']) !== ''));

        $product->update([
            'category_id' => $this->category_id ?: null,
            'name' => $this->name,
            'slug' => $this->slug,
            'job_number' => filled($this->job_number) ? trim($this->job_number) : null,
            'short_description' => $this->short_description ?: null,
            'description' => $purifiedDescription,
            'specs' => $cleanSpecs,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'updated_by' => $userId,
        ]);

        $this->syncCompanies($product);

        return $product;
    }

    protected function syncCompanies(Product $product): void
    {
        $syncData = [];
        $order = 0;
        foreach ($this->company_relationships as $companyId => $data) {
            if (! empty($data['selected'])) {
                $syncData[$companyId] = [
                    'relationship_note' => ! empty($data['relationship_note']) ? substr($data['relationship_note'], 0, 120) : null,
                    'since_year' => ! empty($data['since_year']) ? (int) $data['since_year'] : null,
                    'sort_order' => $order++,
                ];
            }
        }

        $product->companies()->sync($syncData);
    }
}
