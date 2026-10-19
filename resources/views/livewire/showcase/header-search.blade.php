<div
    class="relative"
    x-data="{ focused: false }"
    @click.outside="focused = false"
    @keydown.escape.window="focused = false"
>
    <!-- Search Bar Input Container -->
    <div class="relative flex items-center transition-all duration-200 w-44 lg:w-56 focus-within:w-64 lg:focus-within:w-72">
        <input
            type="text"
            wire:model.live.debounce.250ms="q"
            wire:keydown.enter="search"
            @focus="focused = true"
            placeholder="Search showcase..."
            class="w-full bg-ink-900/90 hover:bg-ink-900 border border-ink-700/80 hover:border-ink-600 focus:border-gold-500/80 rounded-sm py-1.5 pl-8 pr-7 text-xs text-white placeholder-ink-400 focus:outline-none focus:ring-1 focus:ring-gold-500/30 transition-all shadow-inner"
        />

        <!-- Search Icon -->
        <span class="absolute left-2.5 pointer-events-none text-ink-400">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </span>

        <!-- Loading Spinner -->
        <div wire:loading wire:target="q" class="absolute right-2.5">
            <svg class="animate-spin h-3.5 w-3.5 text-gold-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        </div>

        <!-- Clear Button -->
        @if(filled($q))
            <button
                type="button"
                wire:click="clear"
                wire:loading.remove
                wire:target="q"
                class="absolute right-2 text-ink-400 hover:text-white transition-colors cursor-pointer p-0.5"
                title="Clear"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
    </div>

    <!-- Live Results Dropdown -->
    @if(mb_strlen(trim($q)) >= 2)
        <div
            x-show="focused || $wire.q.length >= 2"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="absolute right-0 mt-2 w-80 sm:w-96 bg-ink-950/98 border border-gold-500/30 rounded-sm shadow-2xl backdrop-blur-md z-50 overflow-hidden divide-y divide-ink-800/80"
        >
            <!-- Dropdown Header -->
            <div class="px-3.5 py-2 bg-ink-900/60 flex items-center justify-between">
                <span class="text-[10px] font-sans font-semibold uppercase tracking-widest text-gold-400 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-gold-400 animate-pulse"></span>
                    Live Matches
                </span>
                <span class="text-[10px] text-ink-400 font-mono">
                    "{{ Str::limit(trim($q), 15) }}"
                </span>
            </div>

            <div class="max-h-80 overflow-y-auto divide-y divide-ink-800/60 p-1.5 space-y-2">
                <!-- Products Match Section -->
                @if($liveProducts->isNotEmpty())
                    <div class="space-y-1">
                        <span class="text-[9px] uppercase font-bold tracking-wider text-ink-400 px-2.5 pt-1 block">
                            Products ({{ $liveProducts->count() }})
                        </span>
                        @foreach($liveProducts as $prod)
                            <a
                                href="{{ route('showcase.products.show', $prod->slug) }}"
                                wire:navigate
                                @click="focused = false"
                                class="flex items-center gap-2.5 p-2 rounded-xs hover:bg-ink-900 border border-transparent hover:border-gold-500/25 transition-all group"
                            >
                                <div class="w-9 h-9 shrink-0 bg-white rounded-xs overflow-hidden border border-ink-700/60 flex items-center justify-center p-0.5">
                                    <img src="{{ $prod->getCoverUrl('thumb') }}" alt="" class="w-full h-full object-contain">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs text-white group-hover:text-gold-300 font-medium truncate">
                                        {{ $prod->name }}
                                    </div>
                                    @if($prod->category)
                                        <div class="text-[10px] text-ink-400 uppercase tracking-wider truncate">
                                            {{ $prod->category->name }}
                                        </div>
                                    @endif
                                </div>
                                <svg class="w-3.5 h-3.5 text-ink-500 group-hover:text-gold-400 shrink-0 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- Companies Match Section -->
                @if($liveCompanies->isNotEmpty())
                    <div class="space-y-1 pt-1">
                        <span class="text-[9px] uppercase font-bold tracking-wider text-ink-400 px-2.5 pt-1 block">
                            Companies ({{ $liveCompanies->count() }})
                        </span>
                        @foreach($liveCompanies as $comp)
                            <a
                                href="{{ route('showcase.companies.show', $comp->slug) }}"
                                wire:navigate
                                @click="focused = false"
                                class="flex items-center gap-2.5 p-2 rounded-xs hover:bg-ink-900 border border-transparent hover:border-gold-500/25 transition-all group"
                            >
                                <div class="w-9 h-9 shrink-0 bg-white rounded-xs overflow-hidden border border-ink-700/60 flex items-center justify-center p-1">
                                    <img src="{{ $comp->getLogoUrl('thumb') }}" alt="" class="max-h-full max-w-full object-contain">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs text-white group-hover:text-gold-300 font-medium truncate">
                                        {{ $comp->name }}
                                    </div>
                                    <div class="text-[10px] text-ink-400 truncate">
                                        {{ $comp->industry ?? $comp->country ?? 'Partner' }}
                                    </div>
                                </div>
                                <svg class="w-3.5 h-3.5 text-ink-500 group-hover:text-gold-400 shrink-0 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- Empty State -->
                @if($liveProducts->isEmpty() && $liveCompanies->isEmpty())
                    <div class="py-6 text-center text-ink-400 text-xs">
                        No matches found for <span class="text-white">"{{ $q }}"</span>
                    </div>
                @endif
            </div>

            <!-- View All Results Footer -->
            <button
                type="button"
                wire:click="search"
                class="w-full px-3.5 py-2.5 bg-ink-900/90 hover:bg-gold-500/10 text-gold-400 hover:text-gold-300 text-[11px] font-sans font-semibold uppercase tracking-wider flex items-center justify-between cursor-pointer transition-colors"
            >
                <span>View all results for "{{ Str::limit(trim($q), 20) }}"</span>
                <span class="text-xs">&rarr;</span>
            </button>
        </div>
    @endif
</div>
