<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Header Title & Filter Bar -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-ink-800">
        <div>
            <x-ui.eyebrow text="CLIENT VERIFICATION" class="mb-2" />
            <h1 class="font-display font-bold uppercase text-3xl sm:text-5xl text-white tracking-tight">
                Partner Companies
            </h1>
            <p class="text-xs uppercase tracking-widest text-ink-400 mt-1">
                Showing {{ $companies->total() }} commercial {{ Str::plural('partner', $companies->total()) }}
            </p>
        </div>

        <!-- Search Input -->
        <div class="relative w-full md:w-72">
            <input
                type="text"
                wire:model.live.debounce.250ms="q"
                placeholder="Search companies or brands..."
                class="w-full bg-ink-800 border border-ink-700 rounded-sm px-4 py-2 pl-9 text-xs text-white placeholder-ink-500 focus:outline-none focus:border-gold-500 focus:ring-1 focus:ring-gold-500/50"
            />
            <svg class="w-4 h-4 text-ink-500 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            @if($q)
                <button wire:click="$set('q', '')" class="absolute right-2.5 top-2.5 text-ink-400 hover:text-white">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    </div>

    <!-- Industry Chips -->
    @if($industries->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2 pt-2">
            <x-ui.chip
                :active="empty($industry)"
                wire:click="filterIndustry(null)"
            >
                All Industries
            </x-ui.chip>

            @foreach($industries as $ind)
                <x-ui.chip
                    :active="$industry === $ind"
                    wire:click="filterIndustry('{{ $ind }}')"
                >
                    {{ $ind }}
                </x-ui.chip>
            @endforeach

            @if($industry || $q)
                <button
                    wire:click="clearFilters"
                    class="text-[11px] font-sans text-gold-400 hover:text-gold-300 uppercase tracking-wider underline ml-2 cursor-pointer"
                >
                    Reset Filters
                </button>
            @endif
        </div>
    @endif

    <!-- Loading Indicator -->
    <div wire:loading.delay class="w-full text-center py-4">
        <div class="inline-flex items-center gap-2 text-xs uppercase tracking-widest text-gold-500">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Filtering Companies...</span>
        </div>
    </div>

    <!-- Company Tiles Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        @forelse($companies as $company)
            <x-company-tile :company="$company" wire:key="comp-{{ $company->id }}" />
        @empty
            <div class="col-span-full py-16 text-center border border-dashed border-ink-800 rounded-sm bg-ink-900/50">
                <svg class="w-12 h-12 text-ink-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="font-display uppercase text-lg text-white">No Companies Found</h3>
                <p class="text-xs text-ink-400 mt-1 max-w-sm mx-auto">
                    No partner records match the current filter or search criteria.
                </p>
                <button
                    wire:click="clearFilters"
                    class="mt-4 px-4 py-2 border border-gold-500/50 text-gold-400 text-xs uppercase tracking-wider font-semibold rounded-xs hover:bg-gold-500/10 transition-colors"
                >
                    Clear All Filters
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($companies->hasPages())
        <div class="pt-8 border-t border-ink-800">
            {{ $companies->links() }}
        </div>
    @endif
</div>
