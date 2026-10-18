<x-layouts.auth title="Two-Factor Verification">
    <div x-data="{ recovery: false }">
        <div class="mb-6 text-center">
            <h2 class="font-display font-medium text-2xl uppercase tracking-wider text-white">
                Two-Factor Challenge
            </h2>
            <p class="mt-1 text-xs text-ink-300" x-show="!recovery">
                Please enter the authentication code provided by your authenticator application.
            </p>
            <p class="mt-1 text-xs text-ink-300" x-show="recovery" style="display: none;">
                Please confirm access by entering one of your emergency recovery codes.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-3 rounded-sm bg-danger/10 border border-danger/40 text-danger text-xs">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-5">
            @csrf

            <div x-show="!recovery">
                <label for="code" class="block text-xs uppercase tracking-wider font-semibold text-ink-300 mb-2">
                    Authentication Code
                </label>
                <input
                    id="code"
                    type="text"
                    inputmode="numeric"
                    name="code"
                    autofocus
                    x-ref="code"
                    autocomplete="one-time-code"
                    class="w-full bg-ink-800 border border-ink-700 rounded-sm px-4 py-2.5 text-center tracking-[0.4em] font-mono text-lg text-white focus:outline-none focus:border-gold-500 focus:ring-1 focus:ring-gold-500/50"
                    placeholder="123456"
                />
            </div>

            <div x-show="recovery" style="display: none;">
                <label for="recovery_code" class="block text-xs uppercase tracking-wider font-semibold text-ink-300 mb-2">
                    Emergency Recovery Code
                </label>
                <input
                    id="recovery_code"
                    type="text"
                    name="recovery_code"
                    x-ref="recovery_code"
                    autocomplete="one-time-code"
                    class="w-full bg-ink-800 border border-ink-700 rounded-sm px-4 py-2.5 font-mono text-sm text-white focus:outline-none focus:border-gold-500 focus:ring-1 focus:ring-gold-500/50"
                    placeholder="abcd-efgh-ijkl"
                />
            </div>

            <div class="flex items-center justify-between text-xs pt-2">
                <button
                    type="button"
                    class="text-gold-400 hover:text-gold-300 cursor-pointer underline underline-offset-4"
                    x-show="!recovery"
                    @click="recovery = true; $nextTick(() => { $refs.recovery_code.focus() })"
                >
                    Use an emergency recovery code
                </button>

                <button
                    type="button"
                    class="text-gold-400 hover:text-gold-300 cursor-pointer underline underline-offset-4"
                    x-show="recovery"
                    style="display: none;"
                    @click="recovery = false; $nextTick(() => { $refs.code.focus() })"
                >
                    Use an authentication code
                </button>
            </div>

            <div class="pt-3">
                <button
                    type="submit"
                    class="w-full bg-gold-500 hover:bg-gold-400 text-ink-950 font-semibold py-3 px-4 rounded-sm uppercase tracking-widest text-xs transition-colors duration-200 cursor-pointer"
                >
                    Verify &amp; Enter &rarr;
                </button>
            </div>
        </form>
    </div>
</x-layouts.auth>
