<x-layouts.auth title="Admin Login">
    <div class="mb-6 text-center">
        <h2 class="font-display font-medium text-2xl uppercase tracking-wider text-white">
            Admin Authentication
        </h2>
        <p class="mt-1 text-xs text-ink-300">
            Sign in with authorized administrator credentials.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-3 rounded-sm bg-danger/10 border border-danger/40 text-danger text-xs">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if (session('status'))
        <div class="mb-6 p-3 rounded-sm bg-gold-500/10 border border-gold-500/40 text-gold-400 text-xs">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-xs uppercase tracking-wider font-semibold text-ink-300 mb-2">
                Email Address
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="w-full bg-ink-800 border border-ink-700 rounded-sm px-4 py-2.5 text-sm text-white focus:outline-none focus:border-gold-500 focus:ring-1 focus:ring-gold-500/50 transition-colors"
                placeholder="admin@keminexports.com"
            />
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-xs uppercase tracking-wider font-semibold text-ink-300">
                    Password
                </label>
            </div>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="w-full bg-ink-800 border border-ink-700 rounded-sm px-4 py-2.5 text-sm text-white focus:outline-none focus:border-gold-500 focus:ring-1 focus:ring-gold-500/50 transition-colors"
                placeholder="••••••••••••"
            />
        </div>

        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input
                    type="checkbox"
                    name="remember"
                    class="rounded-xs bg-ink-800 border-ink-700 text-gold-500 focus:ring-gold-500/30"
                />
                <span class="text-xs text-ink-300">Remember session</span>
            </label>
        </div>

        <div class="pt-2">
            <button
                type="submit"
                class="w-full bg-gold-500 hover:bg-gold-400 text-ink-950 font-semibold py-3 px-4 rounded-sm uppercase tracking-widest text-xs transition-colors duration-200 cursor-pointer shadow-lg shadow-gold-500/10"
            >
                Authenticate &rarr;
            </button>
        </div>
    </form>
</x-layouts.auth>
