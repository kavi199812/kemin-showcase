<?php

namespace App\Livewire\Showcase;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Services\SearchService;
use App\Services\SettingsService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.showcase')]
#[Title('Showcase — Kemin Exports')]
class Home extends Component
{
    #[Url]
    public string $q = '';

    public function search(): mixed
    {
        if (filled($this->q)) {
            return $this->redirectRoute('showcase.products.index', ['q' => trim($this->q)], navigate: true);
        }

        return null;
    }

    public function render(SettingsService $settings, SearchService $searchService): View
    {
        $stats = json_decode($settings->get('stats_json', '[]'), true) ?: [];

        $featuredProducts = Product::active()
            ->featured()
            ->with(['category', 'media', 'companies'])
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $featuredCompanies = Company::active()
            ->featured()
            ->with(['media', 'products'])
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $categories = Category::withCount('activeProducts')
            ->orderBy('sort_order')
            ->get();

        // If search term is typed directly on home screen, calculate live previews
        $liveProducts = [];
        $liveCompanies = [];
        if (mb_strlen(trim($this->q)) >= 2) {
            $liveProducts = Product::active()
                ->search($this->q)
                ->with(['category', 'media'])
                ->take(4)
                ->get();

            $liveCompanies = Company::active()
                ->search($this->q)
                ->with(['media', 'products'])
                ->take(4)
                ->get();
        }

        return view('livewire.showcase.home', [
            'heroLine1' => $settings->get('hero_line_1', 'PRECISION'),
            'heroLine2' => $settings->get('hero_line_2', 'Meets Craftsmanship'),
            'tagline' => $settings->get('tagline', 'Packaging & Print Manufacturer, Sri Lanka'),
            'stats' => $stats,
            'featuredProducts' => $featuredProducts,
            'featuredCompanies' => $featuredCompanies,
            'categories' => $categories,
            'liveProducts' => $liveProducts,
            'liveCompanies' => $liveCompanies,
        ]);
    }
}
