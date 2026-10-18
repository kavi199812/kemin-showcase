@props([
    'variant' => 'dark', // 'dark' | 'white' | 'paper'
])

@php
$classes = match($variant) {
    'white' => 'bg-white text-ink-950 rounded-sm shadow-sm hover:shadow-md transition-shadow duration-200 border border-neutral-200',
    'paper' => 'bg-paper text-ink-900 rounded-sm shadow-sm border border-neutral-200',
    default => 'bg-ink-800 text-white rounded-sm border border-ink-700 shadow-sm',
};
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
