<?php

namespace App\Livewire\Showcase;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.showcase')]
class ProductShow extends Component
{
    public Product $product;

    public int $selectedImageIndex = 0;

    public function mount(string $slug): void
    {
        $this->product = Product::active()
            ->where('slug', $slug)
            ->with(['category', 'media', 'companies.media'])
            ->firstOrFail();
    }

    public function selectImage(int $index): void
    {
        $this->selectedImageIndex = $index;
    }

    public function triggerLightbox(): void
    {
        $images = $this->product->getMedia('gallery')->map(fn ($m) => $m->getUrl('full'))->values()->all();
        if (empty($images)) {
            $images = [asset('images/product-placeholder.webp')];
        }

        $this->dispatch('open-lightbox', images: $images, index: $this->selectedImageIndex);
    }

    public function render(): View
    {
        $galleryMedia = $this->product->getMedia('gallery');
        $galleryUrls = $galleryMedia->map(fn ($m) => [
            'thumb' => $m->getUrl('thumb'),
            'card' => $m->getUrl('card'),
            'full' => $m->getUrl('full'),
        ])->all();

        if (empty($galleryUrls)) {
            $galleryUrls = [[
                'thumb' => asset('images/product-placeholder.webp'),
                'card' => asset('images/product-placeholder.webp'),
                'full' => asset('images/product-placeholder.webp'),
            ]];
        }

        return view('livewire.showcase.product-show', [
            'galleryUrls' => $galleryUrls,
            'companies' => $this->product->companies,
        ])->title("{$this->product->name} — Kemin Exports Showcase");
    }
}
