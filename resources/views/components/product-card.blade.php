@props(['product'])

@php
$coverUrl = $product->getCoverUrl('card');
$categoryName = $product->category?->name;
$companiesCount = $product->companies_count ?? $product->companies->count();
@endphp

<div {{ $attributes->merge(['class' => 'group relative bg-white rounded-sm overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1.5 flex flex-col border border-neutral-200/80']) }}>
    <!-- 4:3 Image Container -->
    <a href="{{ route('showcase.products.show', $product->slug) }}" wire:navigate class="block relative aspect-4/3 w-full overflow-hidden bg-neutral-100">
        <img
            src="{{ $coverUrl }}"
            alt="{{ $product->name }}"
            loading="lazy"
            class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out"
        />
        @if($product->is_featured)
            <div class="absolute top-3 left-3 bg-ink-950/90 backdrop-blur-xs text-gold-400 border border-gold-500/40 text-[9px] uppercase tracking-widest font-semibold px-2.5 py-1 rounded-xs shadow">
                Featured
            </div>
        @endif
    </a>

    <!-- Card Content -->
    <div class="p-5 flex flex-col flex-1 text-center justify-between">
        <div>
            <div class="flex items-center justify-center gap-1.5 flex-wrap mb-1.5">
                @if($categoryName)
                    <span class="text-[10px] font-sans font-semibold tracking-widest uppercase text-ink-500">
                        {{ $categoryName }}
                    </span>
                @endif
                @if($product->job_number)
                    <span class="inline-block text-[9px] font-mono font-semibold tracking-wider text-ink-500 bg-neutral-100 px-1.5 py-0.5 rounded-xs border border-neutral-200" title="Job Number">
                        #{{ $product->job_number }}
                    </span>
                @endif
            </div>

            <h3 class="font-sans font-semibold text-[15px] text-ink-950 group-hover:text-gold-600 transition-colors leading-snug line-clamp-2">
                <a href="{{ route('showcase.products.show', $product->slug) }}" wire:navigate>
                    {{ $product->name }}
                </a>
            </h3>

            @if($product->short_description)
                <p class="mt-2 text-xs text-ink-500 line-clamp-2 font-normal leading-relaxed">
                    {{ $product->short_description }}
                </p>
            @endif
        </div>

        <div class="mt-4 pt-3 border-t border-neutral-100 flex items-center justify-between text-[11px] text-ink-500">
            <span>{{ $companiesCount }} {{ Str::plural('Partner', $companiesCount) }}</span>
            <a href="{{ route('showcase.products.show', $product->slug) }}" wire:navigate class="inline-flex items-center gap-1 font-semibold text-ink-700 group-hover:text-gold-600 transition-colors">
                <span>View Details</span>
                <svg class="w-3.5 h-3.5 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>
