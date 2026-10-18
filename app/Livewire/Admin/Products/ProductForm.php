<?php

namespace App\Livewire\Admin\Products;

use App\Livewire\Forms\ProductFormObject;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Layout('layouts.admin')]
#[Title('Edit Product — Admin')]
class ProductForm extends Component
{
    use WithFileUploads;

    public ProductFormObject $form;

    public bool $isEditing = false;

    public ?int $productId = null;

    /**
     * @var array<UploadedFile>
     */
    public array $newPhotos = [];

    /**
     * @var list<array{id: int, url: string, name: string}>
     */
    public array $existingMedia = [];

    public function mount(?int $id = null): void
    {
        if ($id) {
            $product = Product::with(['category', 'companies', 'media'])->findOrFail($id);
            Gate::authorize('update', $product);

            $this->isEditing = true;
            $this->productId = $id;
            $this->form->setProduct($product);
            $this->loadExistingMedia($product);
        } else {
            Gate::authorize('create', Product::class);
            $this->isEditing = false;
            // Preload 2 blank specs rows for convenience
            $this->form->specs = [
                ['key' => 'Material', 'value' => ''],
                ['key' => 'Finishing', 'value' => ''],
            ];
        }
    }

    protected function loadExistingMedia(Product $product): void
    {
        $this->existingMedia = $product->getMedia('gallery')->map(fn (Media $m) => [
            'id' => $m->id,
            'url' => $m->getUrl('card'),
            'name' => $m->file_name,
        ])->all();
    }

    public function updatedFormName(): void
    {
        $this->form->generateSlug();
    }

    public function addSpecRow(): void
    {
        $this->form->addSpec();
    }

    public function removeSpecRow(int $index): void
    {
        $this->form->removeSpec($index);
    }

    public function deleteMediaItem(int $mediaId): void
    {
        $media = Media::findOrFail($mediaId);
        $media->delete();

        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            $this->loadExistingMedia($product);
        }

        $this->dispatch('notify', message: 'Image removed from gallery.');
    }

    public function save(): mixed
    {
        // Validate photos upload rules (Rule 6: photos jpg,jpeg,png,webp <= 10MB, max 20 per product)
        $this->validate([
            'newPhotos.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
        ]);

        $userId = auth()->id();

        if ($this->isEditing) {
            $product = $this->form->update($userId);
            $message = "Product '{$product->name}' updated successfully.";
        } else {
            $product = $this->form->store($userId);
            $message = "Product '{$product->name}' created successfully.";
        }

        // Process new photos using Intervention Image v3 to strip EXIF and save clean raster
        if (! empty($this->newPhotos)) {
            $manager = new ImageManager(new Driver);

            foreach ($this->newPhotos as $photo) {
                $tempPath = $photo->getRealPath();

                // Re-encode image to strip EXIF and sanitize
                $img = $manager->read($tempPath);
                $cleanFilename = Str::uuid()->toString().'.webp';
                $cleanPath = storage_path('app/temp_'.$cleanFilename);

                $img->toWebp(85)->save($cleanPath);

                $product->addMedia($cleanPath)
                    ->usingFileName($cleanFilename)
                    ->toMediaCollection('gallery');

                if (file_exists($cleanPath)) {
                    @unlink($cleanPath);
                }
            }
        }

        $this->dispatch('notify', message: $message);

        return redirect()->route('admin.products.index');
    }

    public function render(): View
    {
        $categories = Category::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();

        return view('livewire.admin.products.product-form', [
            'categories' => $categories,
            'companies' => $companies,
        ]);
    }
}
