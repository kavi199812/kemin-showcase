<?php

namespace App\Livewire\Showcase;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.showcase')]
#[Title('Products Catalog — Kemin Exports')]
class ProductIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public ?string $category = null;

    #[Url]
    public string $sort = 'featured';

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function filterCategory(?string $slug): void
    {
        $this->category = $slug;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->q = '';
        $this->category = null;
        $this->sort = 'featured';
        $this->resetPage();
    }

    public function render(): View
    {
        $categories = Category::has('activeProducts')
            ->withCount('activeProducts')
            ->orderBy('sort_order')
            ->get();

        $query = Product::active()
            ->with(['category', 'media', 'companies'])
            ->withCount('companies');

        if (filled($this->q)) {
            $query->search($this->q);
        }

        if (filled($this->category)) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', $this->category);
            });
        }

        match ($this->sort) {
            'name_asc' => $query->orderBy('name', 'asc'),
            'newest' => $query->latest('id'),
            default => $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('name'),
        };

        $products = $query->paginate(12);

        return view('livewire.showcase.product-index', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
