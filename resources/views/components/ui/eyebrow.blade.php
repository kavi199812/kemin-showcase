@props(['text' => ''])

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 text-gold-500 font-sans font-semibold text-[11px] uppercase tracking-[0.3em]']) }}>
    <svg class="w-3.5 h-3.5 text-gold-500 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor">
        <circle cx="8" cy="8" r="6" stroke-width="1.75" />
        <circle cx="8" cy="8" r="2.25" fill="currentColor" />
    </svg>
    <span>{{ $text ?: $slot }}</span>
</div>
