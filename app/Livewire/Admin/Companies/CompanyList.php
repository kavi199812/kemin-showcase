<?php

namespace App\Livewire\Admin\Companies;

use App\Models\Company;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Companies Management — Admin')]
class CompanyList extends Component
{
    use WithPagination;

    #[Url]
    public string $q = '';

    public ?int $deletingCompanyId = null;

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $companyId): void
    {
        $company = Company::findOrFail($companyId);
        Gate::authorize('update', $company);

        $company->is_active = ! $company->is_active;
        $company->save();

        $status = $company->is_active ? 'activated' : 'deactivated';
        $this->dispatch('notify', message: "Company '{$company->name}' {$status}.");
    }

    public function toggleFeatured(int $companyId): void
    {
        $company = Company::findOrFail($companyId);
        Gate::authorize('update', $company);

        $company->is_featured = ! $company->is_featured;
        $company->save();

        $status = $company->is_featured ? 'marked as featured' : 'removed from featured';
        $this->dispatch('notify', message: "Company '{$company->name}' {$status}.");
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingCompanyId = $id;
        $this->dispatch('open-modal', 'confirm-delete-company');
    }

    public function deleteCompany(): void
    {
        if ($this->deletingCompanyId) {
            $company = Company::findOrFail($this->deletingCompanyId);
            Gate::authorize('delete', $company);

            $name = $company->name;
            $company->delete();

            $this->deletingCompanyId = null;
            $this->dispatch('close-modal', 'confirm-delete-company');
            $this->dispatch('notify', message: "Company '{$name}' was deleted.");
        }
    }

    public function render(): View
    {
        Gate::authorize('viewAny', Company::class);

        $query = Company::with(['media', 'products'])
            ->withCount('products');

        if (filled($this->q)) {
            $query->search($this->q);
        }

        $companies = $query
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(15);

        return view('livewire.admin.companies.company-list', [
            'companies' => $companies,
        ]);
    }
}
