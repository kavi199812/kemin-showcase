<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-ink-950 text-white antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ $title ?? 'Admin' }} — Kemin Showcase Administration</title>

    <link rel="icon" type="image/webp" href="{{ asset('favicon.webp') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full flex flex-col font-sans bg-ink-950 text-white selection:bg-gold-500 selection:text-ink-950">
    <!-- Top Admin Header -->
    <header class="bg-ink-950 border-b border-ink-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/kemin-logo.webp') }}" alt="Kemin Exports" class="h-8 w-auto object-contain">
                </a>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-gold-500/10 text-gold-400 border border-gold-500/30">
                    Admin
                </span>
            </div>

            <!-- Admin Navigation -->
            <nav class="hidden lg:flex items-center space-x-1">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-sm transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-gold-400 bg-ink-900 border-b-2 border-gold-500' : 'text-ink-300 hover:text-white hover:bg-ink-900' }}"
                >
                    Dashboard
                </a>
                <a
                    href="{{ route('admin.products.index') }}"
                    class="px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-sm transition-colors {{ request()->routeIs('admin.products.*') ? 'text-gold-400 bg-ink-900 border-b-2 border-gold-500' : 'text-ink-300 hover:text-white hover:bg-ink-900' }}"
                >
                    Products
                </a>
                <a
                    href="{{ route('admin.companies.index') }}"
                    class="px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-sm transition-colors {{ request()->routeIs('admin.companies.*') ? 'text-gold-400 bg-ink-900 border-b-2 border-gold-500' : 'text-ink-300 hover:text-white hover:bg-ink-900' }}"
                >
                    Companies
                </a>
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-sm transition-colors {{ request()->routeIs('admin.categories.*') ? 'text-gold-400 bg-ink-900 border-b-2 border-gold-500' : 'text-ink-300 hover:text-white hover:bg-ink-900' }}"
                >
                    Categories
                </a>
                <a
                    href="{{ route('admin.settings.index') }}"
                    class="px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-sm transition-colors {{ request()->routeIs('admin.settings.*') ? 'text-gold-400 bg-ink-900 border-b-2 border-gold-500' : 'text-ink-300 hover:text-white hover:bg-ink-900' }}"
                >
                    Settings &amp; PIN
                </a>
                <a
                    href="{{ route('admin.activity.index') }}"
                    class="px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-sm transition-colors {{ request()->routeIs('admin.activity.*') ? 'text-gold-400 bg-ink-900 border-b-2 border-gold-500' : 'text-ink-300 hover:text-white hover:bg-ink-900' }}"
                >
                    Activity Log
                </a>
                <a
                    href="{{ route('admin.bulk-import') }}"
                    class="px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-sm transition-colors {{ request()->routeIs('admin.bulk-import*') ? 'text-gold-400 bg-ink-900 border-b-2 border-gold-500' : 'text-ink-300 hover:text-white hover:bg-ink-900' }}"
                >
                    Bulk Import
                </a>
            </nav>

            <div class="flex items-center gap-4">
                <a
                    href="{{ route('showcase.home') }}"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs uppercase tracking-wider text-ink-300 hover:text-white border border-ink-800 hover:border-ink-700 rounded-sm transition-colors"
                >
                    <svg class="w-3.5 h-3.5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>View Showcase</span>
                </a>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="px-3 py-1.5 bg-ink-900 hover:bg-ink-800 border border-ink-800 hover:border-danger/50 text-xs font-sans uppercase tracking-wider text-ink-400 hover:text-danger rounded-sm transition-all cursor-pointer"
                    >
                        Sign Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Nav bar for Admin -->
        <div class="lg:hidden flex items-center overflow-x-auto border-t border-ink-800 bg-ink-900 px-4 py-2 space-x-2 text-[11px] uppercase tracking-wider">
            <a href="{{ route('admin.dashboard') }}" class="px-2 py-1 {{ request()->routeIs('admin.dashboard') ? 'text-gold-400 font-bold' : 'text-ink-300' }}">Dashboard</a>
            <a href="{{ route('admin.products.index') }}" class="px-2 py-1 {{ request()->routeIs('admin.products.*') ? 'text-gold-400 font-bold' : 'text-ink-300' }}">Products</a>
            <a href="{{ route('admin.companies.index') }}" class="px-2 py-1 {{ request()->routeIs('admin.companies.*') ? 'text-gold-400 font-bold' : 'text-ink-300' }}">Companies</a>
            <a href="{{ route('admin.categories.index') }}" class="px-2 py-1 {{ request()->routeIs('admin.categories.*') ? 'text-gold-400 font-bold' : 'text-ink-300' }}">Categories</a>
            <a href="{{ route('admin.settings.index') }}" class="px-2 py-1 {{ request()->routeIs('admin.settings.*') ? 'text-gold-400 font-bold' : 'text-ink-300' }}">Settings</a>
            <a href="{{ route('admin.activity.index') }}" class="px-2 py-1 {{ request()->routeIs('admin.activity.*') ? 'text-gold-400 font-bold' : 'text-ink-300' }}">Activity</a>
            <a href="{{ route('admin.bulk-import') }}" class="px-2 py-1 {{ request()->routeIs('admin.bulk-import*') ? 'text-gold-400 font-bold' : 'text-ink-300' }}">Bulk Import</a>
        </div>
    </header>

    <!-- Admin Content Body -->
    <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        @if(isset($header))
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 border-b border-ink-800">
                {{ $header }}
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="mt-auto py-4 border-t border-ink-800 bg-ink-950 text-center text-xs text-ink-500">
        &copy; {{ date('Y') }} Kemin Exports Administration &middot; Internal Security System
    </footer>

    <x-lightbox />
    <x-ui.toast />

    @livewireScripts
</body>
</html>
