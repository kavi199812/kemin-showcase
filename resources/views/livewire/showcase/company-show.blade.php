<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-14">
    <!-- Breadcrumb Strip -->
    <nav class="flex items-center gap-2 text-xs uppercase tracking-widest text-ink-500 font-sans">
        <a href="{{ route('showcase.home') }}" wire:navigate class="hover:text-white transition-colors">Showcase</a>
        <span>/</span>
        <a href="{{ route('showcase.companies.index') }}" wire:navigate class="hover:text-white transition-colors">Companies</a>
        <span>/</span>
        <span class="text-white truncate max-w-xs">{{ $company->name }}</span>
    </nav>

    <!-- Company Profile Header Card -->
    <div class="bg-gradient-to-br from-ink-900 to-ink-950 border border-ink-800 rounded-sm p-6 sm:p-10 shadow-xl relative overflow-hidden">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <!-- White Logo Tile for Maximum Contrast -->
                <div class="w-24 h-24 sm:w-28 sm:h-28 shrink-0 bg-white rounded-xs p-4 flex items-center justify-center border border-neutral-200 shadow-md">
                    <img
                        src="{{ $company->getLogoUrl('card') }}"
                        alt="{{ $company->name }} logo"
                        class="max-h-20 w-auto object-contain"
                    />
                </div>

                <div>
                    <div class="flex items-center gap-2 mb-2">
                        @if($company->industry)
                            <span class="text-[10px] font-sans font-semibold uppercase tracking-widest text-gold-400 bg-gold-500/10 border border-gold-500/30 px-2.5 py-0.5 rounded-xs">
                                {{ $company->industry }}
                            </span>
                        @endif
                        @if($company->country)
                            <span class="text-[10px] font-sans font-medium uppercase tracking-widest text-ink-400">
                                {{ $company->country }}
                            </span>
                        @endif
                    </div>

                    <h1 class="font-display font-bold uppercase text-3xl sm:text-4xl text-white tracking-tight">
                        {{ $company->name }}
                    </h1>

                    @if($company->website)
                        <a
                            href="{{ $company->website }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 mt-2 text-xs text-ink-400 hover:text-gold-400 transition-colors font-sans"
                        >
                            <span>{{ parse_url($company->website, PHP_URL_HOST) ?: $company->website }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            <div class="text-left md:text-right">
                <span class="text-xs uppercase tracking-widest text-ink-400 block mb-1">Supplied Products</span>
                <span class="font-display font-bold text-3xl text-gold-400">{{ $products->count() }}</span>
            </div>
        </div>

        @if($company->description)
            <div class="mt-8 pt-6 border-t border-ink-800 max-w-3xl">
                <p class="text-ink-300 text-sm leading-relaxed font-light">
                    {{ $company->description }}
                </p>
            </div>
        @endif
    </div>

    <!-- Products Supplied to This Company -->
    <div>
        <div class="mb-8">
            <x-ui.eyebrow text="SUPPLY RECORD &amp; MANUFACTURED GOODS" class="mb-2" />
            <h2 class="font-display font-bold uppercase text-2xl sm:text-3xl text-white">
                Packaging Manufactured for {{ $company->name }}
            </h2>
            <p class="text-xs uppercase tracking-widest text-ink-400 mt-1">
                Showing all packaging specifications supplied to this partner
            </p>
        </div>

        @if($products->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach($products as $product)
                    <div class="flex flex-col" wire:key="comp-prod-{{ $product->id }}">
                        <x-product-card :product="$product" />

                        <!-- Relationship Note Strip -->
                        @if($product->pivot->relationship_note || $product->pivot->since_year)
                            <div class="bg-ink-800/90 border-x border-b border-ink-700 p-3 rounded-b-xs text-xs flex items-center justify-between text-ink-300">
                                <span class="italic text-[11px] truncate max-w-[200px]">
                                    {{ $product->pivot->relationship_note ?: 'Direct Client' }}
                                </span>
                                @if($product->pivot->since_year)
                                    <span class="text-[10px] font-semibold uppercase text-gold-400">
                                        Since {{ $product->pivot->since_year }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center border border-dashed border-ink-800 rounded-sm text-ink-500 text-xs">
                No active packaging items currently mapped to this partner profile.
            </div>
        @endif
    </div>
</div>
