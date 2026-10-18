@props(['content' => ''])

@php
$purified = Purifier::clean((string) $content);
@endphp

<div {{ $attributes->merge(['class' => 'prose prose-invert max-w-none text-ink-300 text-sm leading-relaxed space-y-3 [&_h3]:font-display [&_h3]:text-xl [&_h3]:text-white [&_h3]:uppercase [&_h3]:font-medium [&_h4]:font-sans [&_h4]:text-base [&_h4]:text-gold-400 [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:space-y-1 [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:space-y-1 [&_strong]:text-white [&_strong]:font-semibold [&_a]:text-gold-400 [&_a]:underline hover:[&_a]:text-gold-300']) }}>
    {!! $purified !!}
</div>
