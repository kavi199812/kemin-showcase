<?php

namespace App\Livewire\Showcase;

use App\Models\Company;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.showcase')]
#[Title('Partner Companies — Kemin Exports')]
class CompanyIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public ?string $industry = null;

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function updatedIndustry(): void
    {
        $this->resetPage();
    }

    public function filterIndustry(?string $ind): void
    {
        $this->industry = $ind;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->q = '';
        $this->industry = null;
        $this->resetPage();
    }

    public function render(): View
    {
        // Fetch unique industries
        $industries = Company::active()
            ->whereNotNull('industry')
            ->distinct()
            ->pluck('industry')
            ->filter()
            ->values();

        $query = Company::active()
            ->with(['media', 'products'])
            ->withCount('products');

        if (filled($this->q)) {
            $query->search($this->q);
        }

        if (filled($this->industry)) {
            $query->where('industry', $this->industry);
        }

        $companies = $query
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(16);

        return view('livewire.showcase.company-index', [
            'companies' => $companies,
            'industries' => $industries,
        ]);
    }
}
