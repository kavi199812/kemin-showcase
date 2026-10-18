<?php

namespace App\Livewire\Showcase;

use App\Models\Company;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.showcase')]
class CompanyShow extends Component
{
    public Company $company;

    public function mount(string $slug): void
    {
        $this->company = Company::active()
            ->where('slug', $slug)
            ->with(['media', 'products' => function ($q) {
                $q->where('is_active', true)->with(['category', 'media']);
            }])
            ->firstOrFail();
    }

    public function render(): View
    {
        return view('livewire.showcase.company-show', [
            'products' => $this->company->products,
        ])->title("{$this->company->name} — Kemin Exports Showcase");
    }
}
