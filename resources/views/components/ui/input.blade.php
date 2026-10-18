@props([
    'disabled' => false,
    'error' => null,
])

@php
$baseClasses = 'w-full bg-ink-800 border rounded-sm px-4 py-2.5 text-sm text-white placeholder-ink-500 transition-colors duration-200 focus:outline-none focus:ring-2 disabled:opacity-50 disabled:cursor-not-allowed';

$borderClasses = $error
    ? 'border-danger focus:border-danger focus:ring-danger/30'
    : 'border-ink-700 focus:border-gold-500 focus:ring-gold-500/30';
@endphp

<input {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $baseClasses . ' ' . $borderClasses]) }}>
