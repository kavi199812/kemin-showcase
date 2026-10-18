<div class="space-y-8">
    <!-- Header Block -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <x-ui.eyebrow text="ADMINISTRATIVE TERMINAL" class="mb-2" />
            <h1 class="font-display font-bold uppercase text-3xl sm:text-4xl text-white">
                Showcase Operations
            </h1>
            <p class="text-xs uppercase tracking-widest text-ink-400 mt-1">
                Kemin Exports Management &middot; Auth Mode: <span class="text-gold-400 font-bold uppercase">{{ $authMode }}</span>
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.create') }}" class="btn-primary-kemin text-xs py-2 px-5">
                + New Product
            </a>
            <a href="{{ route('admin.companies.create') }}" class="btn-primary-kemin text-xs py-2 px-5">
                + New Company
            </a>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Products -->
        <div class="bg-ink-900 border border-ink-800 rounded-sm p-5 relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-sans font-semibold uppercase tracking-wider text-ink-400">Total Products</span>
                <span class="w-2 h-2 rounded-full bg-gold-500"></span>
            </div>
            <div class="font-display font-bold text-3xl text-white">{{ $productsCount }}</div>
            <div class="text-[11px] text-gold-400 mt-1 font-medium">{{ $activeProductsCount }} Active in Showcase</div>
        </div>

        <!-- Companies -->
        <div class="bg-ink-900 border border-ink-800 rounded-sm p-5 relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-sans font-semibold uppercase tracking-wider text-ink-400">Partner Brands</span>
                <span class="w-2 h-2 rounded-full bg-gold-500"></span>
            </div>
            <div class="font-display font-bold text-3xl text-white">{{ $companiesCount }}</div>
            <div class="text-[11px] text-ink-400 mt-1">Client Trust Logos</div>
        </div>

        <!-- Categories -->
        <div class="bg-ink-900 border border-ink-800 rounded-sm p-5 relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-sans font-semibold uppercase tracking-wider text-ink-400">Categories</span>
                <span class="w-2 h-2 rounded-full bg-gold-500"></span>
            </div>
            <div class="font-display font-bold text-3xl text-white">{{ $categoriesCount }}</div>
            <div class="text-[11px] text-ink-400 mt-1">Packaging Segments</div>
        </div>

        <!-- PIN Security -->
        <div class="bg-ink-900 border border-ink-800 rounded-sm p-5 relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-sans font-semibold uppercase tracking-wider text-ink-400">PIN Attempts (24h)</span>
                <span class="w-2 h-2 rounded-full {{ $failedAttempts24h > 0 ? 'bg-danger' : 'bg-success' }}"></span>
            </div>
            <div class="font-display font-bold text-3xl text-white">{{ $attempts24h }}</div>
            <div class="text-[11px] text-ink-400 mt-1">
                @if($failedAttempts24h > 0)
                    <span class="text-danger font-medium">{{ $failedAttempts24h }} Failed</span>
                @else
                    <span class="text-success font-medium">0 Security Breaches</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Navigation & Logs Split -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Security & PIN Log Card -->
        <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-ink-800">
                <h3 class="font-display text-lg uppercase tracking-wider text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Recent Presenter PIN Access
                </h3>
                <a href="{{ route('admin.settings.index') }}" class="text-xs text-gold-400 hover:underline uppercase tracking-wider">
                    Settings &rarr;
                </a>
            </div>

            <div class="divide-y divide-ink-800">
                @forelse($recentPinAttempts as $attempt)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full {{ $attempt->succeeded ? 'bg-success' : 'bg-danger' }}"></span>
                            <div>
                                <span class="font-mono text-ink-300">{{ $attempt->ip }}</span>
                                <p class="text-[10px] text-ink-500 uppercase tracking-wider">
                                    {{ $attempt->succeeded ? 'Authorized Access' : 'Failed PIN Attempt' }}
                                </p>
                            </div>
                        </div>
                        <span class="text-ink-500 font-mono text-[11px]">
                            {{ $attempt->created_at->diffForHumans() }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-ink-500 py-4 text-center">No PIN security events recorded yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Activity Log Card -->
        <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-ink-800">
                <h3 class="font-display text-lg uppercase tracking-wider text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Recent Audit Activity
                </h3>
                <a href="{{ route('admin.activity.index') }}" class="text-xs text-gold-400 hover:underline uppercase tracking-wider">
                    Full Log &rarr;
                </a>
            </div>

            <div class="divide-y divide-ink-800">
                @forelse($recentActivities as $act)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div>
                            <span class="text-white font-medium capitalize">{{ $act->description }}</span>
                            <p class="text-[10px] text-ink-500 uppercase tracking-wider">
                                {{ class_basename($act->subject_type ?? '') }} &middot; {{ $act->causer?->name ?? 'System' }}
                            </p>
                        </div>
                        <span class="text-ink-500 font-mono text-[11px]">
                            {{ $act->created_at->diffForHumans() }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-ink-500 py-4 text-center">No audit activity logged yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
