<div class="max-w-4xl mx-auto space-y-10">
    <div>
        <h1 class="font-display font-bold uppercase text-2xl sm:text-3xl text-white">
            Settings &amp; Security Configuration
        </h1>
        <p class="text-xs uppercase tracking-widest text-ink-400 mt-1">
            Manage presentation security PIN, session lifetimes, showcase headers, and database backups
        </p>
    </div>

    <!-- 1. Presenter Security PIN Management -->
    <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between pb-3 border-b border-ink-800">
            <div>
                <h3 class="font-display text-lg uppercase tracking-wider text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Presenter Showcase PIN
                </h3>
                <p class="text-xs text-ink-400 mt-0.5">
                    Current PIN Length: <span class="text-gold-400 font-bold">{{ $pinLength }} digits</span> &middot; Active Session Version: <span class="font-mono text-white">v{{ $sessionVersion }}</span>
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider bg-gold-500/10 text-gold-400 border border-gold-500/30 rounded-xs">
                Argon2id Hashed
            </span>
        </div>

        <form wire:submit="updatePin" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="newPin" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        New Numeric PIN (4 - 12 digits) *
                    </label>
                    <x-ui.input
                        id="newPin"
                        type="password"
                        inputmode="numeric"
                        wire:model="newPin"
                        placeholder="Enter new numeric PIN"
                        :error="$errors->first('newPin')"
                    />
                    @error('newPin') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="newPinConfirmation" class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Confirm New PIN *
                    </label>
                    <x-ui.input
                        id="newPinConfirmation"
                        type="password"
                        inputmode="numeric"
                        wire:model="newPinConfirmation"
                        placeholder="Re-type new numeric PIN"
                    />
                </div>
            </div>

            <div class="bg-ink-800/60 rounded-xs p-3 text-[11px] text-ink-400 border border-ink-800 leading-relaxed">
                <span class="text-gold-400 font-semibold">Security Note:</span> Trivial patterns (e.g. 1234, 1111, sequences) will be rejected. Changing the PIN will immediately terminate all active presenter sessions, requiring sales staff to re-enter the new PIN.
            </div>

            <div class="flex justify-end pt-2">
                <x-ui.button type="submit" variant="accent">
                    Update Security PIN
                </x-ui.button>
            </div>
        </form>
    </div>

    <!-- 2. Showcase Display Content -->
    <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 sm:p-8 space-y-6">
        <h3 class="font-display text-lg uppercase tracking-wider text-white pb-3 border-b border-ink-800">
            Showcase Hero &amp; Presentation Headlines
        </h3>

        <form wire:submit="saveShowcaseSettings" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Hero Heading Line 1 (Oswald 700) *
                    </label>
                    <x-ui.input
                        type="text"
                        wire:model="heroLine1"
                        placeholder="e.g. PRECISION"
                    />
                    @error('heroLine1') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Hero Heading Line 2 (Oswald 300) *
                    </label>
                    <x-ui.input
                        type="text"
                        wire:model="heroLine2"
                        placeholder="e.g. Meets Craftsmanship"
                    />
                    @error('heroLine2') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Sub-Tagline *
                    </label>
                    <x-ui.input
                        type="text"
                        wire:model="tagline"
                        placeholder="e.g. Packaging & Print Manufacturer, Sri Lanka"
                    />
                    @error('tagline') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Presenter Session Lifetime (Hours) *
                    </label>
                    <x-ui.input
                        type="number"
                        wire:model="presenterSessionHours"
                        min="1"
                        max="24"
                    />
                    <span class="text-[11px] text-ink-500 mt-1 block">Default: 8 hours. Presenters auto-lock after 30 mins idle.</span>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <x-ui.button type="submit" variant="accent">
                    Save Showcase Content
                </x-ui.button>
            </div>
        </form>
    </div>

    <!-- 3. Security Lockout Breaker Rules -->
    <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 sm:p-8 space-y-6">
        <h3 class="font-display text-lg uppercase tracking-wider text-white pb-3 border-b border-ink-800">
            Rate Limiting &amp; Global Breaker Thresholds
        </h3>

        <form wire:submit="saveLockoutSettings" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Device Max Failed Attempts
                    </label>
                    <x-ui.input
                        type="number"
                        wire:model="lockoutMaxAttempts"
                        min="1"
                        max="20"
                    />
                    <span class="text-[11px] text-ink-500 mt-1 block">Failures before IP device lockout (Default: 5).</span>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Device Lockout Duration (Minutes)
                    </label>
                    <x-ui.input
                        type="number"
                        wire:model="lockoutMinutes"
                        min="1"
                        max="120"
                    />
                    <span class="text-[11px] text-ink-500 mt-1 block">Device lock duration (Default: 15 mins).</span>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Evaluation Window (Minutes)
                    </label>
                    <x-ui.input
                        type="number"
                        wire:model="lockoutWindowMinutes"
                        min="1"
                        max="60"
                    />
                    <span class="text-[11px] text-ink-500 mt-1 block">Time window for counting attempts (Default: 15 mins).</span>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-ink-300 font-semibold mb-2">
                        Global Breaker Threshold (All IPs)
                    </label>
                    <x-ui.input
                        type="number"
                        wire:model="globalLockoutThreshold"
                        min="10"
                        max="200"
                    />
                    <span class="text-[11px] text-ink-500 mt-1 block">System-wide lock trigger against brute-force (Default: 30).</span>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <x-ui.button type="submit" variant="accent">
                    Save Security Rules
                </x-ui.button>
            </div>
        </form>
    </div>

    <!-- 4. Database & Deployment Maintenance (hPanel / Git Sync) -->
    <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-ink-800">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-display text-lg uppercase tracking-wider text-white">
                        Database Schema &amp; System Maintenance
                    </h3>
                    @if($pendingMigrationsCount > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-gold-500 text-ink-950 animate-bounce">
                            {{ $pendingMigrationsCount }} Update(s) Pending
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-success/20 text-success border border-success/30">
                            Schema Synced
                        </span>
                    @endif
                </div>
                <p class="text-xs text-ink-400 mt-1">
                    Execute pending schema migrations from GitHub to create new tables or columns in hPanel without SSH.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <button
                    type="button"
                    wire:click="clearCache"
                    wire:loading.attr="disabled"
                    class="px-3.5 py-2 bg-ink-800 hover:bg-ink-700 text-ink-300 hover:text-white border border-ink-700 rounded-sm text-xs uppercase tracking-wider font-semibold cursor-pointer transition-all flex items-center gap-1.5"
                    title="Clear configuration, views, and routes cache"
                >
                    <svg class="w-3.5 h-3.5 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span wire:loading.remove wire:target="clearCache">Clear Cache</span>
                    <span wire:loading wire:target="clearCache">Clearing...</span>
                </button>

                <button
                    type="button"
                    wire:click="runDatabaseBackup"
                    wire:loading.attr="disabled"
                    class="px-3.5 py-2 bg-ink-800 hover:bg-ink-700 text-gold-400 border border-gold-500/40 rounded-sm text-xs uppercase tracking-wider font-semibold cursor-pointer transition-all flex items-center gap-1.5"
                >
                    <svg class="w-3.5 h-3.5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span wire:loading.remove wire:target="runDatabaseBackup">Backup DB</span>
                    <span wire:loading wire:target="runDatabaseBackup">Backing Up...</span>
                </button>

                <button
                    type="button"
                    wire:click="runMigrations"
                    wire:confirm="Run pending database migrations to apply new tables and columns?"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 {{ $pendingMigrationsCount > 0 ? 'bg-gold-500 hover:bg-gold-400 text-ink-950 font-bold shadow-lg shadow-gold-500/20' : 'bg-gold-500/10 hover:bg-gold-500/20 text-gold-400 border border-gold-500/40' }} rounded-sm text-xs uppercase tracking-wider font-semibold cursor-pointer transition-all flex items-center gap-2"
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
                        Migrating...
                    </span>
                </button>
            </div>
        </div>

        @if($migrationOutput)
            <div class="pt-2">
                <div class="flex items-center justify-between text-[11px] text-ink-400 mb-1">
                    <span class="font-mono uppercase tracking-wider">Migration Console Output</span>
                    <button type="button" wire:click="$set('migrationOutput', '')" class="text-ink-500 hover:text-ink-300">Dismiss</button>
                </div>
                <pre class="bg-ink-950 border border-ink-800 rounded p-3 text-xs font-mono text-gold-400/90 whitespace-pre-wrap leading-relaxed max-h-48 overflow-y-auto">{{ $migrationOutput }}</pre>
            </div>
        @endif
    </div>
</div>
