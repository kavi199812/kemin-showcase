<x-layouts.auth title="Confirm Password">
    <div class="mb-6 text-center">
        <h2 class="font-display font-medium text-2xl uppercase tracking-wider text-white">
            Security Confirmation
        </h2>
        <p class="mt-1 text-xs text-ink-300">
            This is a secure area of the application. Please confirm your password before continuing.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-3 rounded-sm bg-danger/10 border border-danger/40 text-danger text-xs">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <label for="password" class="block text-xs uppercase tracking-wider font-semibold text-ink-300 mb-2">
                Administrator Password
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                autofocus
                class="w-full bg-ink-800 border border-ink-700 rounded-sm px-4 py-2.5 text-sm text-white focus:outline-none focus:border-gold-500 focus:ring-1 focus:ring-gold-500/50"
                placeholder="••••••••••••"
            />
        </div>

        <div class="pt-3">
            <button
                type="submit"
                class="w-full bg-gold-500 hover:bg-gold-400 text-ink-950 font-semibold py-3 px-4 rounded-sm uppercase tracking-widest text-xs transition-colors duration-200 cursor-pointer"
            >
                Confirm Password &rarr;
            </button>
        </div>
    </form>
</x-layouts.auth>
