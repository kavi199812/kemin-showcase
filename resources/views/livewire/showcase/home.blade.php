<div class="space-y-20 pb-20">
    <!-- Hero Section -->
    <section class="relative pt-12 md:pt-20 pb-16 overflow-hidden border-b border-ink-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-10">
                <div class="max-w-3xl">
                    <x-ui.section-title
                        eyebrow="EXCLUSIVE CLIENT SHOWCASE"
                        :line1="$heroLine1"
                        :line2="$heroLine2"
                    />
                    <p class="mt-6 text-ink-300 text-base sm:text-lg max-w-2xl font-light leading-relaxed">
                        Explore our bespoke rigid boxes, food-grade tea cartons, corrugated export shippers, and eco-certified packaging manufactured for leading global brands.
                    </p>

                    <!-- Two Action Buttons per Spec: ALL PRODUCTS / ALL COMPANIES -->
                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <x-ui.button
                            variant="accent"
                            :href="route('showcase.products.index')"
                            wire:navigate
                        >
                            All Products &rarr;
                        </x-ui.button>

                        <x-ui.button
                            variant="accent"
                            :href="route('showcase.companies.index')"
                            wire:navigate
                        >
                            All Companies &rarr;
                        </x-ui.button>
                    </div>
                </div>

                <!-- Live Search Box on Home -->
                <div class="w-full lg:max-w-md">
                    <div class="bg-ink-800/80 border border-ink-700 rounded-sm p-6 shadow-xl relative backdrop-blur-xs">
                        <div class="text-[11px] font-sans font-semibold uppercase tracking-widest text-gold-400 mb-2 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-gold-500 animate-pulse"></span>
                            Live Showcase Search
                        </div>
                        <div class="relative">
                            <input
                                type="text"
                                wire:model.live.debounce.250ms="q"
                                wire:keydown.enter="search"
                                placeholder="Search products, materials, or brands..."
                                class="w-full bg-ink-900 border border-ink-700 rounded-sm px-4 py-3 pl-11 text-sm text-white placeholder-ink-500 focus:outline-none focus:border-gold-500 focus:ring-1 focus:ring-gold-500/50"
                            />
                            <svg class="w-5 h-5 text-ink-500 absolute left-3.5 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <div wire:loading wire:target="q" class="absolute right-3.5 top-3.5">
                                <svg class="animate-spin h-5 w-5 text-gold-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Live Search Previews Dropdown -->
                        @if(mb_strlen(trim($q)) >= 2)
                            <div class="mt-4 pt-4 border-t border-ink-700 space-y-4 max-h-80 overflow-y-auto">
                                @if(count($liveProducts) > 0)
                                    <div>
                                        <span class="text-[10px] uppercase font-bold tracking-wider text-ink-400 block mb-2">Matching Products</span>
                                        <div class="space-y-1.5">
                                            @foreach($liveProducts as $prod)
                                                <a href="{{ route('showcase.products.show', $prod->slug) }}" wire:navigate class="flex items-center justify-between p-2 rounded-xs bg-ink-900/60 hover:bg-ink-900 border border-transparent hover:border-gold-500/30 transition-colors text-xs text-white">
                                                    <span class="truncate max-w-[220px]">{{ $prod->name }}</span>
                                                    <span class="text-[10px] text-gold-400 uppercase tracking-wider">{{ $prod->category?->name }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(count($liveCompanies) > 0)
                                    <div>
                                        <span class="text-[10px] uppercase font-bold tracking-wider text-ink-400 block mb-2">Partner Companies</span>
                                        <div class="space-y-1.5">
                                            @foreach($liveCompanies as $comp)
                                                <a href="{{ route('showcase.companies.show', $comp->slug) }}" wire:navigate class="flex items-center justify-between p-2 rounded-xs bg-ink-900/60 hover:bg-ink-900 border border-transparent hover:border-gold-500/30 transition-colors text-xs text-white">
                                                    <span>{{ $comp->name }}</span>
                                                    <span class="text-[10px] text-ink-400">{{ $comp->industry }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(count($liveProducts) === 0 && count($liveCompanies) === 0)
                                    <p class="text-xs text-ink-400 py-2">No direct results for "{{ $q }}". Press enter to view full search.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Statistics Strip -->
    @if(!empty($stats))
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-ink-800 border border-ink-800 rounded-sm overflow-hidden shadow-lg">
                @foreach($stats as $stat)
                    <div class="bg-ink-900 p-6 text-center group hover:bg-ink-800/50 transition-colors">
                        <div class="font-display font-bold text-3xl sm:text-4xl text-gold-400 tracking-tight group-hover:scale-105 transition-transform duration-200">
                            {{ $stat['value'] ?? '' }}
                        </div>
                        <div class="mt-1 text-[11px] font-sans font-medium uppercase tracking-[0.2em] text-ink-400">
                            {{ $stat['label'] ?? '' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Featured Products Showcase Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10 pb-4 border-b border-ink-800">
            <div>
                <x-ui.eyebrow text="SIGNATURE CAPABILITIES" class="mb-2" />
                <h3 class="font-display font-bold uppercase text-3xl sm:text-4xl text-white">
                    Featured Packaging
                </h3>
            </div>
            <a
                href="{{ route('showcase.products.index') }}"
                wire:navigate
                class="btn-primary-kemin text-xs"
            >
                View Full Catalog
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @foreach($featuredProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>

    <!-- Trusted By Industry Leaders (Company Tiles) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10 pb-4 border-b border-ink-800">
            <div>
                <x-ui.eyebrow text="CLIENT CONFIDENCE &amp; TRUST" class="mb-2" />
                <h3 class="font-display font-bold uppercase text-3xl sm:text-4xl text-white">
                    Partner Companies
                </h3>
            </div>
            <a
                href="{{ route('showcase.companies.index') }}"
                wire:navigate
                class="btn-primary-kemin text-xs"
            >
                View All Partners
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($featuredCompanies as $company)
                <x-company-tile :company="$company" />
            @endforeach
        </div>
    </section>

    <!-- Manufacturing Standards Callout -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-ink-950 via-ink-900 to-ink-950 border border-ink-800 rounded-sm p-8 sm:p-12 relative overflow-hidden">
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-gold-600/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-3xl relative z-10">
                <x-ui.eyebrow text="GLOBAL EXPORT STANDARDS" class="mb-3" />
                <h3 class="font-display font-bold text-2xl sm:text-4xl uppercase text-white mb-4">
                    Engineered for International Supply Chains
                </h3>
                <p class="text-ink-300 text-sm sm:text-base leading-relaxed mb-6 font-light">
                    Every packaging run undergoes rigorous quality validation including ECT compression tests, delta-E color spectrophotometry, and certified food-contact safety compliance.
                </p>
                <div class="flex flex-wrap gap-6 pt-2 text-xs uppercase tracking-widest text-gold-400 font-sans font-semibold">
                    <span class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span>
                        ISO 9001:2015
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span>
                        FSC Certified Paper
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span>
                        FDA Food-Contact Safe
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span>
                        100% Recyclable Options
                    </span>
                </div>
            </div>
        </div>
    </section>
</div>
