@props([
    'variant' => 'primary', // primary | accent | ghost | danger
    'type' => 'button',
    'href' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-sans transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-gold-500/50 disabled:opacity-50 disabled:cursor-not-allowed';

$variantClasses = match($variant) {
    'accent' => 'bg-gold-500 text-ink-950 hover:bg-gold-400 font-semibold text-[11px] uppercase tracking-wider px-6 py-2.5 rounded-full shadow-lg shadow-gold-500/10 active:scale-[0.98]',
    'ghost' => 'border border-ink-700 hover:border-gold-500/60 text-ink-300 hover:text-white px-5 py-2.5 text-xs uppercase tracking-wider rounded-sm active:scale-[0.98]',
    'danger' => 'bg-danger hover:bg-red-700 text-white font-semibold text-xs uppercase tracking-wider px-5 py-2.5 rounded-sm active:scale-[0.98]',
    default => 'btn-primary-kemin',
};

$classes = $variant === 'primary' ? 'btn-primary-kemin ' . $attributes->get('class') : $baseClasses . ' ' . $variantClasses . ' ' . $attributes->get('class');
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
