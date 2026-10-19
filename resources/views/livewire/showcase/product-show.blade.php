<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-16">
    <!-- Breadcrumb Strip -->
    <nav class="flex items-center gap-2 text-xs uppercase tracking-widest text-ink-500 font-sans">
        <a href="{{ route('showcase.home') }}" wire:navigate class="hover:text-white transition-colors">Showcase</a>
        <span>/</span>
        <a href="{{ route('showcase.products.index') }}" wire:navigate class="hover:text-white transition-colors">Products</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('showcase.products.index', ['category' => $product->category->slug]) }}" wire:navigate class="text-gold-400 hover:text-gold-300">
                {{ $product->category->name }}
            </a>
        @endif
        <span>/</span>
        <span class="text-white truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <!-- Main Product Detail Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 xl:gap-14 items-start">
        <!-- Left: Photo Gallery (Sticky on desktop) -->
        <div class="lg:col-span-7 space-y-4">
            <!-- Active Image Container -->
            <div class="relative aspect-4/3 w-full bg-white rounded-sm overflow-hidden border border-neutral-200/80 shadow-lg group">
                <img
                    src="{{ $galleryUrls[$selectedImageIndex]['card'] ?? asset('images/product-placeholder.webp') }}"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-contain p-4 cursor-zoom-in"
                    wire:click="triggerLightbox"
                />

                <button
                    type="button"
                    wire:click="triggerLightbox"
                    class="absolute bottom-4 right-4 bg-ink-950/80 hover:bg-ink-950 text-white text-[11px] font-sans font-semibold uppercase tracking-wider px-3 py-1.5 rounded-sm border border-gold-500/40 backdrop-blur-xs flex items-center gap-2 shadow-lg cursor-pointer transition-all"
                >
                    <svg class="w-4 h-4 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                    </svg>
                    <span>Inspect High-Res</span>
                </button>
            </div>

            <!-- Thumbnail Strip if more than 1 image -->
            @if(count($galleryUrls) > 1)
                <div class="flex items-center gap-3 overflow-x-auto pb-2">
                    @foreach($galleryUrls as $idx => $img)
                        <button
                            type="button"
                            wire:click="selectImage({{ $idx }})"
                            class="relative w-20 h-16 shrink-0 bg-white rounded-sm overflow-hidden border-2 transition-all cursor-pointer {{ $selectedImageIndex === $idx ? 'border-gold-500 scale-105 shadow-md' : 'border-ink-800 hover:border-ink-600 opacity-60 hover:opacity-100' }}"
                        >
                            <img src="{{ $img['thumb'] }}" alt="Thumbnail {{ $idx + 1 }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right: Specifications & Details -->
        <div class="lg:col-span-5 space-y-8">
            <div>
                <div class="flex items-center gap-3 flex-wrap mb-3">
                    @if($product->category)
                        <x-ui.eyebrow :text="$product->category->name" />
                    @endif
                    @if($product->job_number)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xs bg-ink-900 border border-gold-500/40 text-gold-400 font-mono text-xs font-semibold tracking-wider shadow-xs">
                            <span class="text-ink-400 text-[10px] uppercase font-sans tracking-widest">Job No:</span>
                            <span>#{{ $product->job_number }}</span>
                        </span>
                    @endif
                </div>

                <h1 class="font-display font-bold uppercase text-3xl sm:text-4xl lg:text-5xl text-white tracking-tight leading-tight">
                    {{ $product->name }}
                </h1>

                @if($product->short_description)
                    <p class="mt-4 text-ink-300 text-sm sm:text-base leading-relaxed font-light">
                        {{ $product->short_description }}
                    </p>
                @endif
            </div>

            <!-- Specifications Table -->
            @if(!empty($product->specs) && is_array($product->specs))
                <div class="border border-ink-800 rounded-sm bg-ink-900/60 p-5 space-y-3">
                    <h3 class="text-xs font-sans font-semibold uppercase tracking-widest text-gold-400 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span>
                        Technical Specifications
                    </h3>
                    <div class="divide-y divide-ink-800">
                        @foreach($product->specs as $spec)
                            @if(!empty($spec['key']))
                                <div class="py-2.5 flex items-center justify-between text-xs">
                                    <span class="text-ink-400 uppercase tracking-wider font-medium">{{ $spec['key'] }}</span>
                                    <span class="text-white font-semibold text-right">{{ $spec['value'] ?? '—' }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Rich Text Description -->
            @if($product->description)
                <div class="pt-4 border-t border-ink-800">
                    <h3 class="text-xs font-sans font-semibold uppercase tracking-widest text-ink-400 mb-3">
                        Manufacturing &amp; Material Details
                    </h3>
                    <x-rich-text :content="$product->description" />
                </div>
            @endif
        </div>
    </div>

    <!-- Associated Partner Companies Section (Trust Badges) -->
    <div class="pt-12 border-t border-ink-800">
        <div class="mb-8">
            <x-ui.eyebrow text="CLIENT VERIFICATION &amp; TRUST" class="mb-2" />
            <h2 class="font-display font-bold uppercase text-2xl sm:text-3xl text-white">
                Brands Receiving This Packaging
            </h2>
            <p class="text-xs uppercase tracking-widest text-ink-400 mt-1">
                Direct export and domestic client partnerships
            </p>
        </div>

        @if($companies->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($companies as $company)
                    <div class="bg-white rounded-sm p-6 border border-neutral-200 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 shrink-0 bg-neutral-50 rounded-xs flex items-center justify-center p-2 border border-neutral-100">
                                <img
                                    src="{{ $company->getLogoUrl('thumb') }}"
                                    alt="{{ $company->name }} logo"
                                    class="max-h-12 w-auto object-contain"
                                />
                            </div>
                            <div>
                                <h4 class="font-sans font-semibold text-sm text-ink-950">
                                    <a href="{{ route('showcase.companies.show', $company->slug) }}" wire:navigate class="hover:text-gold-600 transition-colors">
                                        {{ $company->name }}
                                    </a>
                                </h4>
                                @if($company->industry)
                                    <p class="text-[11px] text-ink-500 uppercase tracking-wider mt-0.5">
                                        {{ $company->industry }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Pivot Note & Relationship -->
                        <div class="mt-4 pt-3 border-t border-neutral-100 flex items-center justify-between text-xs text-ink-600">
                            <span class="italic text-[11px] font-normal truncate max-w-[200px]">
                                {{ $company->pivot->relationship_note ?: 'Direct Client' }}
                            </span>
                            @if($company->pivot->since_year)
                                <span class="text-[10px] uppercase font-semibold text-gold-600 bg-gold-400/20 px-2 py-0.5 rounded-xs">
                                    Since {{ $company->pivot->since_year }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center border border-dashed border-ink-800 rounded-sm text-ink-500 text-xs">
                Private internal release or unassigned corporate partner specification.
            </div>
        @endif
    </div>
</div>
