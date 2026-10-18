<?php

namespace App\Livewire\Forms;

use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;
use Mews\Purifier\Facades\Purifier;

class CompanyFormObject extends Form
{
    #[Locked]
    public ?int $id = null;

    public string $name = '';

    public string $slug = '';

    public ?string $description = '';

    public ?string $website = '';

    public ?string $country = '';

    public ?string $industry = '';

    public bool $is_featured = false;

    public bool $is_active = true;

    public int $sort_order = 0;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('companies', 'name')->ignore($this->id)],
            'slug' => ['required', 'string', 'max:140', Rule::unique('companies', 'slug')->ignore($this->id)],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
            'country' => ['nullable', 'string', 'max:80'],
            'industry' => ['nullable', 'string', 'max:80'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer'],
        ];
    }

    public function setCompany(Company $company): void
    {
        $this->id = $company->id;
        $this->name = $company->name;
        $this->slug = $company->slug;
        $this->description = $company->description ?? '';
        $this->website = $company->website ?? '';
        $this->country = $company->country ?? '';
        $this->industry = $company->industry ?? '';
        $this->is_featured = (bool) $company->is_featured;
        $this->is_active = (bool) $company->is_active;
        $this->sort_order = (int) $company->sort_order;
    }

    public function generateSlug(): void
    {
        if (blank($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function store(?int $userId = null): Company
    {
        $this->validate();

        $purifiedDescription = filled($this->description) ? Purifier::clean($this->description) : null;

        return Company::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $purifiedDescription,
            'website' => $this->website ?: null,
            'country' => $this->country ?: null,
            'industry' => $this->industry ?: null,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }

    public function update(?int $userId = null): Company
    {
        $this->validate();

        $company = Company::findOrFail($this->id);
        $purifiedDescription = filled($this->description) ? Purifier::clean($this->description) : null;

        $company->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $purifiedDescription,
            'website' => $this->website ?: null,
            'country' => $this->country ?: null,
            'industry' => $this->industry ?: null,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'updated_by' => $userId,
        ]);

        return $company;
    }
}
