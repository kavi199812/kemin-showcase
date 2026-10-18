<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-display font-bold uppercase text-2xl sm:text-3xl text-white">
                Packaging Categories
            </h1>
            <p class="text-xs uppercase tracking-widest text-ink-400 mt-1">
                Manage packaging classifications and showcase filter chips
            </p>
        </div>

        <button
            type="button"
            wire:click="openCreateModal"
            class="btn-primary-kemin text-xs py-2 px-5 cursor-pointer"
        >
            + Add Category
        </button>
    </div>

    <!-- Categories List Table -->
    <div class="bg-ink-900 border border-ink-800 rounded-sm overflow-hidden shadow-xl">
        <table class="w-full text-left text-xs">
            <thead class="bg-ink-950 border-b border-ink-800 text-[11px] font-sans font-semibold uppercase tracking-wider text-ink-400">
                <tr>
                    <th class="py-3.5 px-4 w-20">Order</th>
                    <th class="py-3.5 px-4">Name</th>
                    <th class="py-3.5 px-4">Slug</th>
                    <th class="py-3.5 px-4 text-center">Products</th>
                    <th class="py-3.5 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-800 text-ink-300">
                @forelse($categories as $category)
                    <tr class="hover:bg-ink-800/40 transition-colors" wire:key="cat-{{ $category->id }}">
                        <td class="py-3 px-4 font-mono text-gold-400">
                            #{{ $category->sort_order }}
                        </td>
                        <td class="py-3 px-4 font-semibold text-white text-sm">
                            {{ $category->name }}
                        </td>
                        <td class="py-3 px-4 font-mono text-ink-400">
                            {{ $category->slug }}
                        </td>
                        <td class="py-3 px-4 text-center font-mono">
                            <span class="px-2 py-0.5 rounded-full bg-ink-800 text-ink-300 border border-ink-700">
                                {{ $category->products_count }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <button
                                type="button"
                                wire:click="openEditModal({{ $category->id }})"
                                class="text-gold-400 hover:text-gold-300 p-1 cursor-pointer"
                                title="Edit Category"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>

                            <button
                                type="button"
                                wire:click="confirmDelete({{ $category->id }})"
                                class="text-danger hover:text-red-400 p-1 cursor-pointer"
                                title="Delete Category"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-ink-500">
                            No categories created yet. Click "+ Add Category" to start.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Create / Edit Modal -->
    <x-ui.modal name="category-modal" maxWidth="md">
        <form wire:submit="saveCategory" class="p-6 space-y-4">
            <h3 class="font-display text-lg uppercase tracking-wider text-white">
                {{ $editingCategoryId ? 'Edit Category' : 'Create Category' }}
            </h3>

            <div>
                <label for="cat-name" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-1.5">
                    Category Name *
                </label>
                <x-ui.input
                    id="cat-name"
                    type="text"
                    wire:model.live.debounce.300ms="name"
                    placeholder="e.g. Rigid Luxury Boxes"
                    :error="$errors->first('name')"
                />
                @error('name') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="cat-slug" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-1.5">
                    URL Slug *
                </label>
                <x-ui.input
                    id="cat-slug"
                    type="text"
                    wire:model="slug"
                    placeholder="e.g. rigid-luxury-boxes"
                    :error="$errors->first('slug')"
                />
                @error('slug') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="cat-sort" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-1.5">
                    Sort Order
                </label>
                <x-ui.input
                    id="cat-sort"
                    type="number"
                    wire:model="sort_order"
                    placeholder="1"
                />
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-ink-800">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'category-modal')"
                    class="px-4 py-2 text-xs uppercase tracking-wider text-ink-400 hover:text-white cursor-pointer"
                >
                    Cancel
                </button>
                <x-ui.button type="submit" variant="accent">
                    {{ $editingCategoryId ? 'Update Category' : 'Create Category' }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    <!-- Delete Modal -->
    <x-ui.modal name="confirm-delete-category" maxWidth="sm">
        <div class="p-6 text-center space-y-4">
            <div class="w-12 h-12 rounded-full bg-danger/10 border border-danger/30 text-danger mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="font-display text-lg uppercase tracking-wider text-white">Delete Category?</h3>
            <p class="text-xs text-ink-400">
                Products attached to this category will become unassigned, but will not be deleted.
            </p>
            <div class="flex items-center justify-center gap-3 pt-2">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'confirm-delete-category')"
                    class="px-4 py-2 bg-ink-800 hover:bg-ink-700 text-white rounded-sm text-xs uppercase tracking-wider cursor-pointer"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="deleteCategory"
                    class="px-4 py-2 bg-danger hover:bg-red-700 text-white rounded-sm text-xs uppercase tracking-wider font-semibold cursor-pointer"
                >
                    Delete Category
                </button>
            </div>
        </div>
    </x-ui.modal>
</div>
