<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Products Management — Admin')]
class ProductList extends Component
{
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public ?string $category = null;

    public ?int $deletingProductId = null;

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $productId): void
    {
        $product = Product::findOrFail($productId);
        Gate::authorize('update', $product);

        $product->is_active = ! $product->is_active;
        $product->save();

        $status = $product->is_active ? 'activated' : 'deactivated';
        $this->dispatch('notify', message: "Product '{$product->name}' {$status}.");
    }

    public function toggleFeatured(int $productId): void
    {
        $product = Product::findOrFail($productId);
        Gate::authorize('update', $product);

        $product->is_featured = ! $product->is_featured;
        $product->save();

        $status = $product->is_featured ? 'marked as featured' : 'removed from featured';
        $this->dispatch('notify', message: "Product '{$product->name}' {$status}.");
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingProductId = $id;
        $this->dispatch('open-modal', 'confirm-delete-product');
    }

    public function deleteProduct(): void
    {
        if ($this->deletingProductId) {
            $product = Product::findOrFail($this->deletingProductId);
            Gate::authorize('delete', $product);

            $name = $product->name;
            $product->delete();

            $this->deletingProductId = null;
            $this->dispatch('close-modal', 'confirm-delete-product');
            $this->dispatch('notify', message: "Product '{$name}' was deleted.");
        }
    }

    public function render(): View
    {
        Gate::authorize('viewAny', Product::class);

        $categories = Category::orderBy('name')->get();

        $query = Product::with(['category', 'media', 'companies'])
            ->withCount('companies');

        if (filled($this->q)) {
            $query->search($this->q);
        }

        if (filled($this->category)) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $this->category));
        }

        $products = $query->orderBy('sort_order')->latest('id')->paginate(15);

        return view('livewire.admin.products.product-list', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
