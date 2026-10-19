<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Header Title & Filter Bar -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-6 border-b border-ink-800">
        <div>
            <x-ui.eyebrow text="PRODUCTION DIRECTORY" class="mb-2" />
            <h1 class="font-display font-bold uppercase text-3xl sm:text-5xl text-white tracking-tight">
                Job Numbers
            </h1>
            <p class="text-xs uppercase tracking-widest text-ink-400 mt-1 flex items-center gap-2 flex-wrap">
                <span>Showing {{ $products->total() }} packaging {{ Str::plural('record', $products->total()) }}</span>
                <span class="text-ink-600">&bull;</span>
                <span class="text-gold-500 font-mono font-semibold">{{ $totalJobsCount }} Assigned Production Jobs</span>
            </p>
        </div>

        <!-- Search & Sort Controls -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
            <!-- Search Input -->
            <div class="relative w-full sm:w-80">
                <input
                    type="text"
                    wire:model.live.debounce.250ms="q"
                    placeholder="Search by Job No. (e.g. KE-JOB-001 or 001)..."
                    class="w-full bg-ink-800 border border-ink-700 rounded-sm px-4 py-2.5 pl-9 text-xs text-white placeholder-ink-500 focus:outline-none focus:border-gold-500 focus:ring-1 focus:ring-gold-500/50"
                />
                <svg class="w-4 h-4 text-gold-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                @if($q)
                    <button wire:click="$set('q', '')" class="absolute right-2.5 top-2.5 text-ink-400 hover:text-white cursor-pointer" title="Clear">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Sort Dropdown -->
            <div class="relative">
                <select
                    wire:model.live="sort"
                    class="w-full sm:w-auto bg-ink-800 border border-ink-700 rounded-sm px-4 py-2.5 text-xs text-ink-300 focus:outline-none focus:border-gold-500 cursor-pointer"
                >
                    <option value="job_asc">Job No: Ascending (A &rarr; Z)</option>
                    <option value="job_desc">Job No: Descending (Z &rarr; A)</option>
                    <option value="newest">Newest Production First</option>
                    <option value="name_asc">Product Name (A &rarr; Z)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Category Filter Chips -->
    @if($categories->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2 pt-1">
            <x-ui.chip
                :active="empty($category)"
                wire:click="filterCategory(null)"
            >
                All Categories ({{ $products->total() }})
            </x-ui.chip>

            @foreach($categories as $cat)
                <x-ui.chip
                    :active="$category === $cat->slug"
                    wire:click="filterCategory('{{ $cat->slug }}')"
                >
                    {{ $cat->name }} ({{ $cat->active_products_count }})
                </x-ui.chip>
            @endforeach

            @if($category || $q)
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
            <span>Filtering Job Number Directory...</span>
        </div>
    </div>

    <!-- Job Numbers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
        @forelse($products as $product)
            <div
                wire:key="job-card-{{ $product->id }}"
                class="bg-ink-900 border border-ink-800 hover:border-gold-500/50 rounded-sm overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group"
            >
                <!-- Top Job Number Bar with 1-click Copy -->
                <div
                    x-data="{
                        copied: false,
                        copy() {
                            if ('{{ $product->job_number }}') {
                                navigator.clipboard.writeText('{{ $product->job_number }}');
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            }
                        }
                    }"
                    class="bg-ink-950 px-4 py-3 border-b border-ink-800 flex items-center justify-between gap-2"
                >
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-2 h-2 rounded-full {{ $product->job_number ? 'bg-gold-500' : 'bg-ink-600' }}"></span>
                        <span class="text-[10px] font-sans uppercase tracking-widest text-ink-500 font-semibold">JOB REF:</span>
                        @if($product->job_number)
                            <span class="font-mono font-bold text-xs sm:text-sm text-gold-400 tracking-wider truncate" title="{{ $product->job_number }}">
                                #{{ $product->job_number }}
                            </span>
                        @else
                            <span class="font-mono text-xs text-ink-500 italic">Unassigned</span>
                        @endif
                    </div>

                    @if($product->job_number)
                        <button
                            type="button"
                            @click="copy()"
                            class="px-2 py-1 rounded-xs bg-ink-800 hover:bg-ink-700 border border-ink-700 text-[10px] font-mono text-ink-300 hover:text-white transition-colors cursor-pointer shrink-0 flex items-center gap-1"
                            title="Copy Job Number"
                        >
                            <svg x-show="!copied" class="w-3 h-3 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <svg x-show="copied" x-cloak class="w-3 h-3 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-text="copied ? 'Copied' : 'Copy'">Copy</span>
                        </button>
                    @endif
                </div>

                <!-- Product Content Body -->
                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                    <div class="flex items-start gap-4">
                        <!-- Thumbnail Preview -->
                        <a
                            href="{{ route('showcase.products.show', $product->slug) }}"
                            wire:navigate
                            class="w-20 h-20 shrink-0 bg-white rounded-xs overflow-hidden border border-neutral-300 flex items-center justify-center p-1 group-hover:scale-102 transition-transform"
                        >
                            <img
                                src="{{ $product->getCoverUrl('thumb') }}"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-contain"
                                loading="lazy"
                            />
                        </a>

                        <!-- Product Information -->
                        <div class="min-w-0 flex-1">
                            @if($product->category)
                                <span class="text-[10px] font-sans font-semibold uppercase tracking-widest text-gold-500 block mb-1">
                                    {{ $product->category->name }}
                                </span>
                            @endif

                            <h3 class="font-sans font-bold text-sm sm:text-base text-white group-hover:text-gold-300 transition-colors leading-snug line-clamp-2">
                                <a href="{{ route('showcase.products.show', $product->slug) }}" wire:navigate>
                                    {{ $product->name }}
                                </a>
                            </h3>

                            @if($product->companies->isNotEmpty())
                                <div class="mt-1.5 flex items-center gap-1 text-[11px] text-ink-400 line-clamp-1">
                                    <span class="text-ink-500 font-semibold uppercase text-[9px] tracking-wider">Partner:</span>
                                    <span class="text-ink-300 font-medium">{{ $product->companies->pluck('name')->join(', ') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Key Technical Specs Preview -->
                    @if(!empty($product->specs) && is_array($product->specs))
                        <div class="bg-ink-950/70 border border-ink-800/80 rounded-xs p-3 space-y-1.5 text-[11px]">
                            @foreach(array_slice($product->specs, 0, 2) as $spec)
                                @if(!empty($spec['key']))
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-ink-500 font-medium uppercase text-[10px] tracking-wider">{{ $spec['key'] }}:</span>
                                        <span class="text-ink-300 font-semibold truncate text-right max-w-[60%]">{{ $spec['value'] }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <!-- Bottom Action Link -->
                    <div class="pt-3 border-t border-ink-800/60 flex items-center justify-between text-xs">
                        <span class="text-[11px] text-ink-500">
                            {{ $product->companies_count }} {{ Str::plural('Partner', $product->companies_count) }}
                        </span>
                        <a
                            href="{{ route('showcase.products.show', $product->slug) }}"
                            wire:navigate
                            class="inline-flex items-center gap-1.5 font-semibold text-gold-400 hover:text-gold-300 uppercase tracking-wider text-[11px] transition-colors"
                        >
                            <span>View Model</span>
                            <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center border border-dashed border-ink-800 rounded-sm bg-ink-900/50">
                <svg class="w-12 h-12 text-ink-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="font-display uppercase text-lg text-white">No Matching Job Numbers Found</h3>
                <p class="text-xs text-ink-400 mt-1 max-w-sm mx-auto">
                    We could not find any production jobs matching "{{ $q }}". Try searching by another reference number or keyword.
                </p>
                <button
                    wire:click="clearFilters"
                    class="mt-4 px-4 py-2 border border-gold-500/50 text-gold-400 text-xs uppercase tracking-wider font-semibold rounded-xs hover:bg-gold-500/10 transition-colors cursor-pointer"
                >
                    Clear All Filters
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="pt-8 border-t border-ink-800">
            {{ $products->links() }}
        </div>
    @endif
</div>
