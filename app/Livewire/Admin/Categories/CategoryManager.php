<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Categories Management — Admin')]
class CategoryManager extends Component
{
    public ?int $editingCategoryId = null;

    public string $name = '';

    public string $slug = '';

    public int $sort_order = 0;

    public ?int $deletingCategoryId = null;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80', Rule::unique('categories', 'name')->ignore($this->editingCategoryId)],
            'slug' => ['required', 'string', 'max:100', Rule::unique('categories', 'slug')->ignore($this->editingCategoryId)],
            'sort_order' => ['integer'],
        ];
    }

    public function updatedName(): void
    {
        if (blank($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingCategoryId', 'name', 'slug', 'sort_order']);
        $this->resetValidation();
        $this->sort_order = (Category::max('sort_order') ?? 0) + 1;
        $this->dispatch('open-modal', 'category-modal');
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $category = Category::findOrFail($id);
        Gate::authorize('update', $category);

        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->sort_order = (int) $category->sort_order;

        $this->dispatch('open-modal', 'category-modal');
    }

    public function saveCategory(): void
    {
        $this->validate();

        if ($this->editingCategoryId) {
            $category = Category::findOrFail($this->editingCategoryId);
            Gate::authorize('update', $category);

            $category->update([
                'name' => $this->name,
                'slug' => $this->slug,
                'sort_order' => $this->sort_order,
            ]);

            $message = "Category '{$category->name}' updated.";
        } else {
            Gate::authorize('create', Category::class);

            $category = Category::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'sort_order' => $this->sort_order,
            ]);

            $message = "Category '{$category->name}' created.";
        }

        $this->dispatch('close-modal', 'category-modal');
        $this->dispatch('notify', message: $message);
        $this->reset(['editingCategoryId', 'name', 'slug', 'sort_order']);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingCategoryId = $id;
        $this->dispatch('open-modal', 'confirm-delete-category');
    }

    public function deleteCategory(): void
    {
        if ($this->deletingCategoryId) {
            $category = Category::findOrFail($this->deletingCategoryId);
            Gate::authorize('delete', $category);

            $name = $category->name;
            $category->delete();

            $this->deletingCategoryId = null;
            $this->dispatch('close-modal', 'confirm-delete-category');
            $this->dispatch('notify', message: "Category '{$name}' deleted.");
        }
    }

    public function render(): View
    {
        Gate::authorize('viewAny', Category::class);

        $categories = Category::withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.categories.category-manager', [
            'categories' => $categories,
        ]);
    }
}
