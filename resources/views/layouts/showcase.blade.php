<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-ink-900 text-white antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ $title ?? 'Showcase' }} — Kemin Exports</title>

    <!-- Google Fonts Preconnect & Styles -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body
    class="min-h-full flex flex-col font-sans bg-ink-900 text-white selection:bg-gold-500 selection:text-ink-950"
    x-data="{
        idleTimer: null,
        resetIdleTimer() {
            clearTimeout(this.idleTimer);
            // 30 minutes auto-lock
            this.idleTimer = setTimeout(() => {
                document.getElementById('idle-lock-form').submit();
            }, 30 * 60 * 1000);
        }
    }"
    x-init="resetIdleTimer()"
    @mousemove.window.debounce.1000ms="resetIdleTimer()"
    @keydown.window.debounce.1000ms="resetIdleTimer()"
    @click.window.debounce.1000ms="resetIdleTimer()"
>
    <!-- Hidden Idle Auto-Lock Form -->
    <form id="idle-lock-form" action="{{ route('lock') }}" method="POST" class="hidden">
        @csrf
    </form>

    <!-- Sticky Showcase Header -->
    <header class="sticky top-0 z-40 bg-ink-950/95 backdrop-blur-md border-b border-ink-800 transition-all duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <div class="flex items-center gap-6">
                <a href="{{ route('showcase.home') }}" wire:navigate class="flex items-center gap-3 group focus:outline-none focus:ring-1 focus:ring-gold-500">
                    <img src="{{ asset('images/kemin-logo.svg') }}" alt="Kemin Exports" class="h-9 w-auto" />
                </a>
                <span class="hidden md:inline-block h-5 w-px bg-ink-700"></span>
                <span class="hidden md:inline-block text-[10px] font-sans font-semibold uppercase tracking-[0.25em] text-ink-500">
                    Client Showcase
                </span>
            </div>

            <!-- Showcase Navigation -->
            <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                <a
                    href="{{ route('showcase.home') }}"
                    wire:navigate
                    class="px-4 py-2 text-xs font-sans font-semibold uppercase tracking-widest transition-colors {{ request()->routeIs('showcase.home') ? 'text-gold-400 border-b-2 border-gold-500' : 'text-ink-300 hover:text-white' }}"
                >
                    Search
                </a>
                <a
                    href="{{ route('showcase.products.index') }}"
                    wire:navigate
                    class="px-4 py-2 text-xs font-sans font-semibold uppercase tracking-widest transition-colors {{ request()->routeIs('showcase.products.*') ? 'text-gold-400 border-b-2 border-gold-500' : 'text-ink-300 hover:text-white' }}"
                >
                    Products
                </a>
                <a
                    href="{{ route('showcase.companies.index') }}"
                    wire:navigate
                    class="px-4 py-2 text-xs font-sans font-semibold uppercase tracking-widest transition-colors {{ request()->routeIs('showcase.companies.*') ? 'text-gold-400 border-b-2 border-gold-500' : 'text-ink-300 hover:text-white' }}"
                >
                    Companies
                </a>
            </nav>

            <!-- Actions: Lock & Optional Admin -->
            <div class="flex items-center gap-3">
                @if(auth()->check() && auth()->user()->is_admin)
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="px-3 py-1.5 rounded-sm border border-gold-500/50 hover:bg-gold-500/10 text-gold-400 font-sans text-xs uppercase tracking-wider font-semibold transition-colors"
                    >
                        Admin Panel
                    </a>
                @endif

                <form action="{{ route('lock') }}" method="POST" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-4 py-1.5 rounded-sm bg-ink-800 hover:bg-ink-700 border border-ink-700 hover:border-gold-500/50 text-xs font-sans uppercase tracking-wider text-ink-300 hover:text-white transition-all cursor-pointer focus:outline-none focus:ring-1 focus:ring-gold-500"
                        title="End Presenter Session and Lock Showcase"
                    >
                        <svg class="w-3.5 h-3.5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Lock</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Navigation Strip -->
        <div class="md:hidden flex items-center justify-around border-t border-ink-800 bg-ink-950 px-2 py-2">
            <a href="{{ route('showcase.home') }}" wire:navigate class="px-3 py-1 text-[11px] font-semibold uppercase tracking-wider {{ request()->routeIs('showcase.home') ? 'text-gold-400' : 'text-ink-300' }}">Search</a>
            <a href="{{ route('showcase.products.index') }}" wire:navigate class="px-3 py-1 text-[11px] font-semibold uppercase tracking-wider {{ request()->routeIs('showcase.products.*') ? 'text-gold-400' : 'text-ink-300' }}">Products</a>
            <a href="{{ route('showcase.companies.index') }}" wire:navigate class="px-3 py-1 text-[11px] font-semibold uppercase tracking-wider {{ request()->routeIs('showcase.companies.*') ? 'text-gold-400' : 'text-ink-300' }}">Companies</a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Confidential Footer Strip per spec (bg-copper-700) -->
    <footer class="bg-copper-700 py-2.5 px-4 text-center">
        <p class="text-[11px] uppercase tracking-widest font-sans font-medium text-white/90">
            &copy; {{ date('Y') }} Kemin Exports &middot; Internal showcase &middot; Confidential
        </p>
    </footer>

    <!-- Global Lightbox & Toasts -->
    <x-lightbox />
    <x-ui.toast />

    @livewireScripts
</body>
</html>
