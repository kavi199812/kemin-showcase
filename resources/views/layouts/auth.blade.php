<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-ink-950 text-white antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ $title ?? 'Admin Authentication' }} — Kemin Exports</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-ink-950 text-white selection:bg-gold-500 selection:text-ink-950">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="{{ route('landing') }}" class="inline-block focus:outline-none focus:ring-2 focus:ring-gold-500">
            <img class="mx-auto h-12 w-auto" src="{{ asset('images/kemin-logo.svg') }}" alt="Kemin Exports">
        </a>
        <div class="mt-4 flex items-center justify-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span>
            <span class="text-[11px] font-sans font-semibold tracking-[0.3em] uppercase text-gold-500">
                Administrative Terminal
            </span>
            <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-ink-900 py-8 px-6 sm:px-10 border border-ink-800 rounded-sm shadow-2xl relative">
            <div class="absolute -top-px left-8 right-8 h-px bg-gradient-to-r from-transparent via-gold-500/50 to-transparent"></div>
            {{ $slot }}
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('landing') }}" class="text-xs uppercase tracking-widest text-ink-500 hover:text-gold-400 transition-colors">
                &larr; Return to Presenter Portal
            </a>
        </div>
    </div>

    <footer class="mt-auto pt-10 pb-4 text-center">
        <p class="text-[10px] uppercase tracking-widest text-ink-500">
            &copy; {{ date('Y') }} Kemin Exports &middot; Internal Administration &middot; Confidential
        </p>
    </footer>

    <x-ui.toast />
</body>
</html>
