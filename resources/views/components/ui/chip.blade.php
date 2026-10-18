@props([
    'active' => false,
    'theme' => 'dark', // 'dark' (on dark page) or 'light' (on light background)
])

@php
$baseClasses = 'inline-flex items-center gap-1.5 px-4 py-1.5 text-[11px] font-sans font-semibold tracking-wider uppercase transition-all duration-200 cursor-pointer rounded-full border';

if ($theme === 'dark') {
    $stateClasses = $active
        ? 'bg-white text-ink-900 border-white shadow-sm'
        : 'bg-transparent text-ink-300 border-ink-700 hover:border-gold-500 hover:text-white';
} else {
    $stateClasses = $active
        ? 'bg-ink-900 text-white border-ink-900 shadow-sm'
        : 'bg-transparent text-ink-500 border-ink-300 hover:border-ink-900 hover:text-ink-900';
}
@endphp

<button type="button" {{ $attributes->merge(['class' => $baseClasses . ' ' . $stateClasses]) }}>
    {{ $slot }}
</button>
