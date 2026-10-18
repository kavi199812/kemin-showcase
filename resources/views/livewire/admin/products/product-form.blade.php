<div class="max-w-5xl mx-auto space-y-8">
    <div class="flex items-center justify-between pb-4 border-b border-ink-800">
        <div>
            <x-ui.eyebrow text="PACKAGING CATALOG" class="mb-1" />
            <h1 class="font-display font-bold uppercase text-2xl sm:text-3xl text-white">
                {{ $isEditing ? 'Edit Packaging Model' : 'Create Packaging Model' }}
            </h1>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs uppercase tracking-wider text-ink-400 hover:text-white transition-colors">
            &larr; Back to Products
        </a>
    </div>

    <form wire:submit="save" class="space-y-8">
        <!-- 1. Core Information -->
        <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 sm:p-8 space-y-6">
            <h3 class="text-sm font-sans font-semibold uppercase tracking-wider text-gold-400 pb-2 border-b border-ink-800">
                1. General Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Product Name *
                    </label>
                    <x-ui.input
                        id="name"
                        type="text"
                        wire:model.live.debounce.300ms="form.name"
                        placeholder="e.g. Matte Black Rigid Magnetic Gift Box"
                        :error="$errors->first('form.name')"
                    />
                    @error('form.name') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        URL Slug *
                    </label>
                    <x-ui.input
                        id="slug"
                        type="text"
                        wire:model="form.slug"
                        placeholder="e.g. matte-black-rigid-magnetic-gift-box"
                        :error="$errors->first('form.slug')"
                    />
                    @error('form.slug') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Category
                    </label>
                    <select
                        id="category_id"
                        wire:model="form.category_id"
                        class="w-full bg-ink-800 border border-ink-700 rounded-sm px-4 py-2.5 text-sm text-white focus:outline-none focus:border-gold-500 cursor-pointer"
                    >
                        <option value="">None / Unassigned</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('form.category_id') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Display Sort Order
                    </label>
                    <x-ui.input
                        id="sort_order"
                        type="number"
                        wire:model="form.sort_order"
                        placeholder="0"
                    />
                </div>
            </div>

            <!-- Short Description -->
            <div>
                <label for="short_description" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                    Short Description (Showcase Cards - Max 200 chars)
                </label>
                <textarea
                    id="short_description"
                    rows="2"
                    wire:model="form.short_description"
                    maxlength="200"
                    placeholder="Brief 1-2 sentence specification summary..."
                    class="w-full bg-ink-800 border border-ink-700 rounded-sm p-3 text-sm text-white focus:outline-none focus:border-gold-500"
                ></textarea>
                <div class="flex justify-between text-[11px] text-ink-500 mt-1">
                    <span>Clean summary displayed in showcase grids.</span>
                    <span>{{ strlen($form->short_description ?? '') }} / 200</span>
                </div>
            </div>

            <!-- Full Rich Description -->
            <div>
                <label for="description" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                    Detailed Specification Description (HTML Purified)
                </label>
                <textarea
                    id="description"
                    rows="6"
                    wire:model="form.description"
                    placeholder="<p>Full manufacturing details, certifications, board specifications, and closures...</p>"
                    class="w-full bg-ink-800 border border-ink-700 rounded-sm p-3 font-mono text-xs text-white focus:outline-none focus:border-gold-500"
                ></textarea>
                <p class="text-[11px] text-ink-500 mt-1">
                    Allowed tags: p, br, strong, em, ul, ol, li, h3, h4, a[href]. Automatically sanitized by HTMLPurifier.
                </p>
            </div>

            <!-- Status Toggles -->
            <div class="flex flex-wrap items-center gap-6 pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model="form.is_active"
                        class="rounded-xs bg-ink-800 border-ink-700 text-gold-500 focus:ring-gold-500/30"
                    />
                    <span class="text-xs uppercase tracking-wider font-semibold text-white">Active in Showcase</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model="form.is_featured"
                        class="rounded-xs bg-ink-800 border-ink-700 text-gold-500 focus:ring-gold-500/30"
                    />
                    <span class="text-xs uppercase tracking-wider font-semibold text-gold-400">Featured Highlight</span>
                </label>
            </div>
        </div>

        <!-- 2. Technical Specifications (Dynamic Key/Value) -->
        <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-ink-800">
                <h3 class="text-sm font-sans font-semibold uppercase tracking-wider text-gold-400">
                    2. Technical Specifications Table
                </h3>
                <button
                    type="button"
                    wire:click="addSpecRow"
                    class="px-3 py-1 bg-ink-800 hover:bg-ink-700 border border-ink-700 text-xs font-semibold uppercase text-gold-400 rounded-xs cursor-pointer"
                >
                    + Add Spec Row
                </button>
            </div>

            <div class="space-y-3">
                @foreach($form->specs as $index => $spec)
                    <div class="flex items-center gap-3" wire:key="spec-{{ $index }}">
                        <input
                            type="text"
                            wire:model="form.specs.{{ $index }}.key"
                            placeholder="Specification Key (e.g. Material)"
                            class="flex-1 bg-ink-800 border border-ink-700 rounded-sm px-3 py-2 text-xs text-white placeholder-ink-500 focus:outline-none focus:border-gold-500"
                        />
                        <input
                            type="text"
                            wire:model="form.specs.{{ $index }}.value"
                            placeholder="Value (e.g. 1500gsm Greyboard)"
                            class="flex-1 bg-ink-800 border border-ink-700 rounded-sm px-3 py-2 text-xs text-white placeholder-ink-500 focus:outline-none focus:border-gold-500"
                        />
                        <button
                            type="button"
                            wire:click="removeSpecRow({{ $index }})"
                            class="p-2 text-ink-500 hover:text-danger cursor-pointer transition-colors"
                            title="Remove specification"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 3. Photo Gallery Upload (Intervention Image Sanitized) -->
        <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 sm:p-8 space-y-6">
            <h3 class="text-sm font-sans font-semibold uppercase tracking-wider text-gold-400 pb-2 border-b border-ink-800">
                3. Photo Gallery (Up to 20 images &middot; WebP/JPG/PNG &middot; 4:3 Ratio Recommended)
            </h3>

            <!-- Existing Media Gallery -->
            @if(!empty($existingMedia))
                <div>
                    <h4 class="text-xs uppercase tracking-wider text-ink-400 mb-3">Current Gallery Images</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
                        @foreach($existingMedia as $media)
                            <div class="relative group bg-white rounded-xs overflow-hidden border border-neutral-300 aspect-4/3" wire:key="med-{{ $media['id'] }}">
                                <img src="{{ $media['url'] }}" alt="" class="w-full h-full object-cover">
                                <button
                                    type="button"
                                    wire:click="deleteMediaItem({{ $media['id'] }})"
                                    class="absolute inset-0 bg-ink-950/80 opacity-0 group-hover:opacity-100 flex items-center justify-center text-danger transition-opacity cursor-pointer"
                                    title="Delete image"
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Upload New Photos -->
            <div>
                <label class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                    Upload New Images (Stripped of EXIF and Re-encoded)
                </label>
                <input
                    type="file"
                    wire:model="newPhotos"
                    multiple
                    accept="image/jpeg,image/png,image/webp"
                    class="block w-full text-xs text-ink-400 file:mr-4 file:py-2 file:px-4 file:rounded-xs file:border-0 file:text-xs file:font-semibold file:uppercase file:bg-ink-800 file:text-gold-400 hover:file:bg-ink-700 cursor-pointer"
                />
                @error('newPhotos.*') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- 4. Partner Companies Mapping (Trust Logos) -->
        <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 sm:p-8 space-y-4">
            <h3 class="text-sm font-sans font-semibold uppercase tracking-wider text-gold-400 pb-2 border-b border-ink-800">
                4. Which Companies Receive This Packaging? (Trust Proof)
            </h3>
            <p class="text-xs text-ink-400">
                Check partner brands that order this product. Add relationship notes or partnership start year to show in client sales presentations.
            </p>

            <div class="space-y-3 pt-2">
                @foreach($companies as $company)
                    <div
                        class="p-4 rounded-sm border transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4 {{ !empty($form->company_relationships[$company->id]['selected']) ? 'bg-ink-800/80 border-gold-500/50' : 'bg-ink-900 border-ink-800' }}"
                        wire:key="rel-{{ $company->id }}"
                    >
                        <label class="flex items-center gap-3 cursor-pointer min-w-[200px]">
                            <input
                                type="checkbox"
                                wire:model.live="form.company_relationships.{{ $company->id }}.selected"
                                class="rounded-xs bg-ink-800 border-ink-700 text-gold-500 focus:ring-gold-500/30"
                            />
                            <span class="text-sm font-semibold text-white">{{ $company->name }}</span>
                        </label>

                        @if(!empty($form->company_relationships[$company->id]['selected']))
                            <div class="flex items-center gap-3 flex-1 max-w-lg">
                                <input
                                    type="text"
                                    wire:model="form.company_relationships.{{ $company->id }}.relationship_note"
                                    placeholder="e.g. Exclusive Luxury Ayurvedic Gift Line"
                                    class="flex-1 bg-ink-900 border border-ink-700 rounded-sm px-3 py-1.5 text-xs text-white placeholder-ink-500"
                                />
                                <input
                                    type="number"
                                    wire:model="form.company_relationships.{{ $company->id }}.since_year"
                                    placeholder="Since Year (e.g. 2021)"
                                    class="w-32 bg-ink-900 border border-ink-700 rounded-sm px-3 py-1.5 text-xs text-white placeholder-ink-500"
                                />
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Submit & Actions -->
        <div class="flex items-center justify-end gap-4 pt-4 border-t border-ink-800">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 text-xs uppercase tracking-wider text-ink-400 hover:text-white">
                Cancel
            </a>
            <x-ui.button type="submit" variant="accent">
                {{ $isEditing ? 'Save Packaging Changes' : 'Create Packaging Model' }}
            </x-ui.button>
        </div>
    </form>
</div>
