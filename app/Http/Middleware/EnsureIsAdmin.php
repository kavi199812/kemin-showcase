<?php

namespace App\Http\Middleware;

use App\Services\SettingsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    public function __construct(
        protected SettingsService $settings
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $mode = config('app.showcase_admin_auth', 'password');

        // Mode: PIN-based admin access
        if ($mode === 'pin') {
            $unlockedAt = session('presenter.unlocked_at');
            $sessionVersion = session('presenter.version');

            if (! $unlockedAt || ! $sessionVersion) {
                return redirect()->route('landing')->with('status', 'Please enter your admin PIN to access settings.');
            }

            $sessionHours = (int) $this->settings->get('presenter_session_hours', 8);
            if ((now()->timestamp - (int) $unlockedAt) > ($sessionHours * 3600)) {
                session()->forget(['presenter.unlocked_at', 'presenter.version']);

                return redirect()->route('landing')->with('status', 'Session expired. Please re-enter your PIN.');
            }

            $currentVersion = (int) $this->settings->get('presenter_session_version', 1);
            if ((int) $sessionVersion !== $currentVersion) {
                session()->forget(['presenter.unlocked_at', 'presenter.version']);

                return redirect()->route('landing')->with('status', 'Security credentials updated. Please re-enter PIN.');
            }

            return $next($request);
        }

        // Mode: Password-based admin access (default)
        if (! $request->user()) {
            return redirect()->guest(route('login'));
        }

        if (! $request->user()->is_admin) {
            abort(403, 'Unauthorized. Admin privileges required.');
        }

        return $next($request);
    }
}
