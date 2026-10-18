<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex items-center justify-between pb-4 border-b border-ink-800">
        <div>
            <x-ui.eyebrow text="CLIENT DIRECTORY" class="mb-1" />
            <h1 class="font-display font-bold uppercase text-2xl sm:text-3xl text-white">
                {{ $isEditing ? 'Edit Partner Company' : 'Create Partner Company' }}
            </h1>
        </div>
        <a href="{{ route('admin.companies.index') }}" class="text-xs uppercase tracking-wider text-ink-400 hover:text-white transition-colors">
            &larr; Back to Companies
        </a>
    </div>

    <form wire:submit="save" class="space-y-8">
        <!-- 1. Company Profile -->
        <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 sm:p-8 space-y-6">
            <h3 class="text-sm font-sans font-semibold uppercase tracking-wider text-gold-400 pb-2 border-b border-ink-800">
                1. General Profile
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Company / Brand Name *
                    </label>
                    <x-ui.input
                        id="name"
                        type="text"
                        wire:model.live.debounce.300ms="form.name"
                        placeholder="e.g. Dilmah Ceylon Tea"
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
                        placeholder="e.g. dilmah-ceylon-tea"
                        :error="$errors->first('form.slug')"
                    />
                    @error('form.slug') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Industry -->
                <div>
                    <label for="industry" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Industry / Sector
                    </label>
                    <x-ui.input
                        id="industry"
                        type="text"
                        wire:model="form.industry"
                        placeholder="e.g. Luxury Tea & Beverage"
                    />
                </div>

                <!-- Country -->
                <div>
                    <label for="country" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Country of Operation
                    </label>
                    <x-ui.input
                        id="country"
                        type="text"
                        wire:model="form.country"
                        placeholder="e.g. Sri Lanka / Global"
                    />
                </div>

                <!-- Website -->
                <div class="md:col-span-2">
                    <label for="website" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Corporate Website URL
                    </label>
                    <x-ui.input
                        id="website"
                        type="url"
                        wire:model="form.website"
                        placeholder="https://www.example.com"
                        :error="$errors->first('form.website')"
                    />
                    @error('form.website') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                    Company Description &amp; Partnership Scope
                </label>
                <textarea
                    id="description"
                    rows="4"
                    wire:model="form.description"
                    placeholder="Brief background of the brand and why they rely on Kemin packaging..."
                    class="w-full bg-ink-800 border border-ink-700 rounded-sm p-3 text-sm text-white focus:outline-none focus:border-gold-500"
                ></textarea>
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
                    <span class="text-xs uppercase tracking-wider font-semibold text-gold-400">Featured Partner</span>
                </label>
            </div>
        </div>

        <!-- 2. Logo Upload -->
        <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 sm:p-8 space-y-6">
            <h3 class="text-sm font-sans font-semibold uppercase tracking-wider text-gold-400 pb-2 border-b border-ink-800">
                2. Brand Logo (SVG, PNG, WebP or JPG &middot; Max 5 MB)
            </h3>
            <p class="text-xs text-ink-400">
                Logos are displayed on white/paper tiles in the showcase and automatically transition from grayscale to vibrant brand colors on client hover.
            </p>

            <div class="flex flex-col sm:flex-row items-center gap-6">
                <!-- Current Logo Preview -->
                <div class="w-28 h-24 bg-white rounded-xs p-3 border border-neutral-300 flex items-center justify-center shrink-0">
                    @if($newLogo)
                        <span class="text-[10px] text-ink-500 font-semibold uppercase">Pending Upload</span>
                    @elseif($currentLogoUrl)
                        <img src="{{ $currentLogoUrl }}" alt="Logo" class="max-h-full max-w-full object-contain">
                    @else
                        <span class="text-[10px] text-ink-400 uppercase">No Logo</span>
                    @endif
                </div>

                <div class="flex-1 space-y-3 w-full">
                    <input
                        type="file"
                        wire:model="newLogo"
                        accept="image/png,image/jpeg,image/webp,image/svg+xml"
                        class="block w-full text-xs text-ink-400 file:mr-4 file:py-2 file:px-4 file:rounded-xs file:border-0 file:text-xs file:font-semibold file:uppercase file:bg-ink-800 file:text-gold-400 hover:file:bg-ink-700 cursor-pointer"
                    />
                    @error('newLogo') <span class="text-xs text-danger block">{{ $message }}</span> @enderror

                    @if($currentLogoUrl)
                        <button
                            type="button"
                            wire:click="deleteLogo"
                            class="text-xs text-danger hover:underline uppercase tracking-wider cursor-pointer"
                        >
                            Remove Current Logo
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Submit & Actions -->
        <div class="flex items-center justify-end gap-4 pt-4 border-t border-ink-800">
            <a href="{{ route('admin.companies.index') }}" class="px-5 py-2.5 text-xs uppercase tracking-wider text-ink-400 hover:text-white">
                Cancel
            </a>
            <x-ui.button type="submit" variant="accent">
                {{ $isEditing ? 'Save Company Changes' : 'Create Partner Company' }}
            </x-ui.button>
        </div>
    </form>
</div>
