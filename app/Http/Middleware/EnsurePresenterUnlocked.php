<?php

namespace App\Http\Middleware;

use App\Services\SettingsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePresenterUnlocked
{
    public function __construct(
        protected SettingsService $settings
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Authenticated admin always passes
        if ($request->user() && $request->user()->is_admin) {
            return $next($request);
        }

        // 2. Check presenter session
        $unlockedAt = session('presenter.unlocked_at');
        $sessionVersion = session('presenter.version');

        if (! $unlockedAt || ! $sessionVersion) {
            return redirect()->route('landing')->with('status', 'Please enter your access PIN to view the showcase.');
        }

        $sessionHours = (int) $this->settings->get('presenter_session_hours', 8);
        $maxLifetimeSeconds = $sessionHours * 3600;

        if ((now()->timestamp - (int) $unlockedAt) > $maxLifetimeSeconds) {
            session()->forget(['presenter.unlocked_at', 'presenter.version']);

            return redirect()->route('landing')->with('status', 'Showcase session expired. Please enter PIN again.');
        }

        $currentVersion = (int) $this->settings->get('presenter_session_version', 1);
        if ((int) $sessionVersion !== $currentVersion) {
            session()->forget(['presenter.unlocked_at', 'presenter.version']);

            return redirect()->route('landing')->with('status', 'Security credentials updated. Please re-enter PIN.');
        }

        return $next($request);
    }
}
