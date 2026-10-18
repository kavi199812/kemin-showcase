@props([
    'eyebrow' => null,
    'line1' => '',
    'line2' => null,
    'align' => 'left',
])

@php
$alignClasses = match($align) {
    'center' => 'text-center items-center',
    'right' => 'text-right items-end',
    default => 'text-left items-start',
};
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col ' . $alignClasses]) }}>
    @if($eyebrow)
        <x-ui.eyebrow :text="$eyebrow" class="mb-3" />
    @endif

    <h2 class="font-display font-bold uppercase tracking-tight text-white text-4xl sm:text-5xl md:text-6xl lg:text-7xl leading-[0.95]">
        {{ $line1 }}
    </h2>

    @if($line2)
        <span class="font-display font-light text-ink-300 text-2xl sm:text-3xl md:text-4xl lg:text-5xl tracking-normal mt-1 leading-[1.05]">
            {{ $line2 }}
        </span>
    @endif
</div>
