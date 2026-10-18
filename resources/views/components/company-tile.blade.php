@props(['company'])

@php
$logoUrl = $company->getLogoUrl('card');
$productsCount = $company->products_count ?? $company->products->count();
@endphp

<a
    href="{{ route('showcase.companies.show', $company->slug) }}"
    wire:navigate
    {{ $attributes->merge(['class' => 'group relative bg-white rounded-sm p-6 flex flex-col items-center justify-between shadow-xs hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-neutral-200/80 min-h-[190px]']) }}
>
    <!-- Logo Container with Grayscale to Color Transition -->
    <div class="w-full flex-1 flex items-center justify-center p-3 overflow-hidden">
        <img
            src="{{ $logoUrl }}"
            alt="{{ $company->name }} logo"
            loading="lazy"
            class="max-h-20 w-auto max-w-[85%] object-contain filter grayscale group-hover:grayscale-0 opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-300 ease-out"
        />
    </div>

    <!-- Company Meta -->
    <div class="w-full pt-3 border-t border-neutral-100 text-center">
        <h4 class="font-sans font-semibold text-[13px] text-ink-950 group-hover:text-gold-600 transition-colors truncate">
            {{ $company->name }}
        </h4>
        <div class="flex items-center justify-center gap-2 mt-1 text-[11px] text-ink-500">
            @if($company->industry)
                <span class="truncate max-w-[130px]">{{ $company->industry }}</span>
                <span>•</span>
            @endif
            <span class="font-medium text-gold-600">{{ $productsCount }} {{ Str::plural('Product', $productsCount) }}</span>
        </div>
    </div>
</a>
