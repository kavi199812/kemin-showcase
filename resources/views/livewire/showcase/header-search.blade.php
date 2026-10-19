<div
    x-data="{
        open: false,
        openModal() {
            this.open = true;
            document.body.style.overflow = 'hidden';
            this.$nextTick(() => {
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                }
            });
        },
        closeModal() {
            this.open = false;
            document.body.style.overflow = '';
        }
    }"
    @open-global-search.window="openModal()"
    @keydown.window.ctrl.k.prevent="openModal()"
    @keydown.window.meta.k.prevent="openModal()"
    @keydown.escape.window="closeModal()"
    class="inline-block"
>
    <!-- Header Menu Trigger Button -->
    <button
        type="button"
        @click="openModal()"
        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-sm bg-ink-900 hover:bg-ink-800 border border-ink-700/80 hover:border-gold-500/50 text-xs font-sans uppercase tracking-widest text-ink-300 hover:text-white transition-all cursor-pointer group shadow-xs"
        title="Open Live Search (Ctrl+K)"
    >
        <svg class="w-3.5 h-3.5 text-gold-400 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <span>Search</span>
    </button>

    <!-- Full-Screen Backdrop Overlay (Deep, High-Opacity Dark Overlay) -->
    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto flex items-start justify-center pt-8 sm:pt-16 p-4 sm:p-6"
            style="background: rgba(4, 4, 6, 0.95); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);"
            @click.self="closeModal()"
        >
            <!-- Solid Opaque Search Card (No background bleeding through) -->
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-98 -translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-98 -translate-y-2"
                class="w-full max-w-4xl rounded-sm p-6 sm:p-8 relative"
                style="background: #111114; border: 1px solid #2d2d35; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.95), 0 0 0 1px rgba(201, 164, 92, 0.2);"
                @click.stop
            >
                <!-- Card Header -->
                <div class="flex items-center justify-between pb-3 mb-4" style="border-bottom: 1px solid #222228;">
                    <div class="text-xs font-sans font-semibold uppercase tracking-widest text-gold-400 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-gold-500 animate-pulse"></span>
                        <span>Live Showcase Search</span>
                    </div>

                    <button
                        type="button"
                        @click="closeModal()"
                        class="text-ink-300 hover:text-white transition-colors cursor-pointer px-2.5 py-1 rounded-xs hover:bg-ink-800 flex items-center gap-1.5 text-xs font-mono"
                        style="border: 1px solid #2a2a30;"
                        title="Close (ESC)"
                    >
                        <span>ESC</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Search Input Field Container -->
                <div class="relative w-full">
                    <!-- Search Icon (Strictly positioned on left) -->
                    <div style="position: absolute; left: 1.1rem; top: 50%; transform: translateY(-50%); pointer-events: none; display: flex; align-items: center; z-index: 10;">
                        <svg style="width: 1.35rem; height: 1.35rem; color: #c9a45c;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Input with explicit padding preventing icon collision -->
                    <input
                        x-ref="searchInput"
                        type="text"
                        wire:model.live.debounce.200ms="q"
                        wire:keydown.enter="search"
                        placeholder="Search by product name, job number, partner, or specs..."
                        style="width: 100%; height: 3.5rem; background: #17171c; border: 1.5px solid #2e2e38; border-radius: 4px; padding-left: 3.5rem !important; padding-right: 5rem !important; font-size: 1.05rem; color: #ffffff; outline: none; transition: border-color 0.2s, box-shadow 0.2s;"
                        onfocus="this.style.borderColor='#c9a45c'; this.style.boxShadow='0 0 0 3px rgba(201, 164, 92, 0.25)';"
                        onblur="this.style.borderColor='#2e2e38'; this.style.boxShadow='none';"
                    />

                    <!-- Loading / Clear Indicator -->
                    <div style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); display: flex; align-items: center; gap: 0.5rem; z-index: 10;">
                        <div wire:loading wire:target="q">
                            <svg class="animate-spin h-5 w-5 text-gold-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </div>

                        @if(filled($q))
                            <button
                                type="button"
                                wire:click="clear"
                                wire:loading.remove
                                wire:target="q"
                                class="text-xs uppercase font-mono px-2 py-1 rounded-xs transition-colors cursor-pointer"
                                style="background: #23232b; color: #aaa; border: 1px solid #33333d;"
                                onmouseover="this.style.color='#fff'; this.style.background='#2d2d38';"
                                onmouseout="this.style.color='#aaa'; this.style.background='#23232b';"
                                title="Clear search"
                            >
                                Clear
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Live Results Area inside Card -->
                @if(mb_strlen(trim($q)) >= 2)
                    <div class="mt-6 pt-4 space-y-6 max-h-[60vh] overflow-y-auto pr-1" style="border-top: 1px solid #222228;">
                        <!-- Matching Products Section -->
                        @if(count($liveProducts) > 0)
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-sans font-semibold uppercase tracking-widest text-gold-400">
                                        Matching Products ({{ count($liveProducts) }})
                                    </span>
                                    <button
                                        type="button"
                                        wire:click="search"
                                        class="text-xs text-gold-400 hover:text-gold-300 uppercase font-sans font-semibold tracking-wider flex items-center gap-1 cursor-pointer transition-colors"
                                    >
                                        <span>Full Catalog &rarr;</span>
                                    </button>
                                </div>

                                <!-- 2-Column Responsive Product Grid with 2.5x larger image thumbnails -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                    @foreach($liveProducts as $prod)
                                        <a
                                            href="{{ route('showcase.products.show', $prod->slug) }}"
                                            wire:navigate
                                            @click="closeModal()"
                                            class="group cursor-pointer transition-all"
                                            style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem; background: #17171c; border: 1px solid #282830; border-radius: 4px; text-decoration: none;"
                                            onmouseover="this.style.borderColor='rgba(201, 164, 92, 0.5)'; this.style.background='#1c1c22';"
                                            onmouseout="this.style.borderColor='#282830'; this.style.background='#17171c';"
                                        >
                                            <!-- Prominent 2.5x Larger Thumbnail Image Container -->
                                            <div style="width: 88px; height: 70px; flex-shrink: 0; background: #ffffff; border-radius: 4px; display: flex; align-items: center; justify-content: center; padding: 4px; overflow: hidden; border: 1px solid #e0e0e0;">
                                                <img src="{{ $prod->getCoverUrl('thumb') }}" alt="{{ $prod->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                            </div>

                                            <!-- Product Details -->
                                            <div style="flex: 1; min-width: 0;">
                                                <h4 class="group-hover:text-gold-300 transition-colors" style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $prod->name }}
                                                </h4>
                                                <div class="flex items-center gap-2 flex-wrap" style="margin-top: 3px;">
                                                    @if($prod->job_number)
                                                        <span style="font-size: 0.68rem; font-family: monospace; font-weight: 600; background: #23232b; color: #c9a45c; padding: 1px 5px; border-radius: 2px; border: 1px solid #383842;">
                                                            #{{ $prod->job_number }}
                                                        </span>
                                                    @endif
                                                    @if($prod->category)
                                                        <span style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #c9a45c; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $prod->category->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($prod->companies->isNotEmpty())
                                                    <div style="font-size: 0.75rem; color: #8d8d99; margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                        Partner: {{ $prod->companies->pluck('name')->join(', ') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Arrow Icon -->
                                            <div style="color: #666; margin-right: 0.25rem;" class="group-hover:text-gold-400 group-hover:translate-x-0.5 transition-all">
                                                <svg style="width: 1.15rem; height: 1.15rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Partner Companies Section -->
                        @if(count($liveCompanies) > 0)
                            <div class="pt-4" style="border-top: 1px solid #222228;">
                                <span class="text-xs font-sans font-semibold uppercase tracking-widest text-gold-400 block mb-3">
                                    Brand Partners & Companies ({{ count($liveCompanies) }})
                                </span>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($liveCompanies as $comp)
                                        <a
                                            href="{{ route('showcase.companies.show', $comp->slug) }}"
                                            wire:navigate
                                            @click="closeModal()"
                                            class="group cursor-pointer transition-all"
                                            style="display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.75rem; background: #17171c; border: 1px solid #282830; border-radius: 4px; text-decoration: none;"
                                            onmouseover="this.style.borderColor='rgba(201, 164, 92, 0.5)'; this.style.background='#1c1c22';"
                                            onmouseout="this.style.borderColor='#282830'; this.style.background='#17171c';"
                                        >
                                            <div style="width: 56px; height: 44px; flex-shrink: 0; background: #ffffff; border-radius: 4px; display: flex; align-items: center; justify-content: center; padding: 4px; overflow: hidden; border: 1px solid #e0e0e0;">
                                                <img src="{{ $comp->getLogoUrl('thumb') }}" alt="{{ $comp->name }}" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                            </div>
                                            <div style="flex: 1; min-width: 0;">
                                                <div class="group-hover:text-gold-300 transition-colors" style="font-size: 0.85rem; font-weight: 600; color: #ffffff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $comp->name }}
                                                </div>
                                                <div style="font-size: 0.7rem; color: #8d8d99; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px;">
                                                    {{ $comp->industry ?? $comp->country ?? 'Partner' }}
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Empty State -->
                        @if(count($liveProducts) === 0 && count($liveCompanies) === 0)
                            <div class="py-8 text-center space-y-2">
                                <div class="text-sm font-medium text-white">No direct results for "{{ $q }}"</div>
                                <p class="text-xs text-ink-400">Press enter to run a full catalog search across description & technical specifications.</p>
                            </div>
                        @endif

                        <!-- Bottom Action Strip -->
                        <div class="pt-4 flex items-center justify-between text-xs text-ink-400" style="border-top: 1px solid #222228;">
                            <span>Showing top matches</span>
                            <button
                                type="button"
                                wire:click="search"
                                class="text-gold-400 hover:text-gold-300 uppercase tracking-wider font-semibold cursor-pointer flex items-center gap-1.5 transition-colors"
                            >
                                <span>View all results in catalog &rarr;</span>
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Default Suggestions View when empty -->
                    <div class="mt-6 pt-4 space-y-4" style="border-top: 1px solid #222228;">
                        <span class="text-xs font-sans font-semibold uppercase tracking-widest text-ink-400 block">
                            Explore Categories
                        </span>

                        <div class="flex flex-wrap gap-2.5">
                            @foreach($categories as $cat)
                                <a
                                    href="{{ route('showcase.products.index', ['category' => $cat->slug]) }}"
                                    wire:navigate
                                    @click="closeModal()"
                                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xs text-xs font-sans text-ink-300 hover:text-gold-300 transition-all cursor-pointer group"
                                    style="background: #17171c; border: 1px solid #282830; text-decoration: none;"
                                    onmouseover="this.style.borderColor='rgba(201, 164, 92, 0.4)'; this.style.color='#e0c78c';"
                                    onmouseout="this.style.borderColor='#282830'; this.style.color='#b5b5b5';"
                                >
                                    <span>{{ $cat->name }}</span>
                                    <span style="font-size: 0.7rem; color: #666;" class="group-hover:text-gold-400">({{ $cat->active_products_count }})</span>
                                </a>
                            @endforeach
                        </div>

                        <div class="pt-3 flex items-center justify-between text-[11px] text-ink-500">
                            <span>Type 2 or more characters to search products, job numbers, specs, or brand partners</span>
                            <span class="hidden sm:inline">Press ESC to close</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </template>
</div>
