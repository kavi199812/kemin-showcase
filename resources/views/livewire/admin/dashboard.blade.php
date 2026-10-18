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

    <!-- Database & Hosting Sync Status (hPanel / GitHub) -->
    <div class="bg-ink-900 border {{ $pendingMigrationsCount > 0 ? 'border-gold-500/80 ring-1 ring-gold-500/40' : 'border-ink-800' }} rounded-sm p-4 sm:p-5 relative overflow-hidden transition-all">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full {{ $pendingMigrationsCount > 0 ? 'bg-gold-500/20 text-gold-400 border border-gold-500/40' : 'bg-success/15 text-success border border-success/30' }} flex items-center justify-center shrink-0 mt-0.5">
                    @if($pendingMigrationsCount > 0)
                        <svg class="w-5 h-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-display font-bold uppercase text-sm sm:text-base tracking-wider text-white">
                            Database Schema &amp; Deployment Sync
                        </h3>
                        @if($pendingMigrationsCount > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-gold-500 text-ink-950 animate-bounce">
                                {{ $pendingMigrationsCount }} Update(s) Pending
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-success/20 text-success border border-success/30">
                                Up to Date
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-ink-400 mt-1">
                        @if($pendingMigrationsCount > 0)
                            New tables or columns were pulled from GitHub. Run migrations now to update the database schema without using SSH.
                        @else
                            All database migrations are synced. Use this whenever you pull new updates from GitHub to hPanel.
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <button
                    type="button"
                    wire:click="clearCache"
                    wire:loading.attr="disabled"
                    class="px-3 py-2 bg-ink-800 hover:bg-ink-700 text-ink-300 hover:text-white border border-ink-700 rounded-sm text-xs uppercase tracking-wider font-semibold transition-all cursor-pointer flex items-center gap-1.5"
                    title="Clear application, route, and view caches"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span wire:loading.remove wire:target="clearCache">Clear Cache</span>
                    <span wire:loading wire:target="clearCache">Clearing...</span>
                </button>

                <button
                    type="button"
                    wire:click="runMigrations"
                    wire:confirm="Are you sure you want to run pending database migrations to update tables and columns?"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 {{ $pendingMigrationsCount > 0 ? 'bg-gold-500 hover:bg-gold-400 text-ink-950 font-bold shadow-lg shadow-gold-500/20' : 'bg-gold-500/10 hover:bg-gold-500/20 text-gold-400 border border-gold-500/40' }} rounded-sm text-xs uppercase tracking-wider font-semibold transition-all cursor-pointer flex items-center gap-2"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span wire:loading.remove wire:target="runMigrations">
                        {{ $pendingMigrationsCount > 0 ? 'Update Database Now' : 'Run Migrations' }}
                    </span>
                    <span wire:loading wire:target="runMigrations" class="flex items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Updating Database...
                    </span>
                </button>
            </div>
        </div>

        @if($migrationOutput)
            <div class="mt-4 pt-3 border-t border-ink-800">
                <div class="flex items-center justify-between text-[11px] text-ink-400 mb-1">
                    <span class="font-mono uppercase tracking-wider">Migration Output Console</span>
                    <button type="button" wire:click="$set('migrationOutput', '')" class="text-ink-500 hover:text-ink-300">Dismiss</button>
                </div>
                <pre class="bg-ink-950 border border-ink-800 rounded p-3 text-xs font-mono text-gold-400/90 whitespace-pre-wrap leading-relaxed max-h-40 overflow-y-auto">{{ $migrationOutput }}</pre>
            </div>
        @endif
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
