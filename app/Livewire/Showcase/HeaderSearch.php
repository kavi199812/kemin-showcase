<?php

namespace App\Livewire\Showcase;

use App\Models\Company;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class HeaderSearch extends Component
{
    public string $q = '';

    public function search(): mixed
    {
        $term = trim($this->q);
        if ($term !== '') {
            return $this->redirectRoute('showcase.products.index', ['q' => $term], navigate: true);
        }

        return null;
    }

    public function clear(): void
    {
        $this->q = '';
    }

    public function render(): View
    {
        $liveProducts = collect();
        $liveCompanies = collect();

        $term = trim($this->q);
        if (mb_strlen($term) >= 2) {
            $liveProducts = Product::active()
                ->search($term)
                ->with(['category', 'media'])
                ->take(5)
                ->get();

            $liveCompanies = Company::active()
                ->search($term)
                ->with(['media'])
                ->take(3)
                ->get();
        }

        return view('livewire.showcase.header-search', [
            'liveProducts' => $liveProducts,
            'liveCompanies' => $liveCompanies,
        ]);
    }
}
