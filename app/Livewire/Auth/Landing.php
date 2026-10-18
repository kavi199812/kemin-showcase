<?php

namespace App\Livewire\Auth;

use App\Services\PinService;
use App\Services\SettingsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.landing')]
#[Title('Presenter Unlock — Kemin Exports')]
class Landing extends Component
{
    public int $pinLength = 4;

    public string $pin = '';

    public ?string $errorMessage = null;

    public bool $isLockedOut = false;

    public ?string $lockoutMessage = null;

    public function mount(Request $request, SettingsService $settings, PinService $pinService): mixed
    {
        // If authenticated admin or active presenter session, redirect immediately
        if ($request->user() && $request->user()->is_admin) {
            return redirect()->route('showcase.home');
        }

        $unlockedAt = session('presenter.unlocked_at');
        $sessionVersion = session('presenter.version');
        $currentVersion = (int) $settings->get('presenter_session_version', 1);
        $sessionHours = (int) $settings->get('presenter_session_hours', 8);

        if ($unlockedAt && $sessionVersion === $currentVersion && (now()->timestamp - (int) $unlockedAt) < ($sessionHours * 3600)) {
            return redirect()->route('showcase.home');
        }

        $this->pinLength = (int) $settings->get('pin_length', 4);

        // Check if device or system is locked out
        $lockout = $pinService->checkLockout($request->ip());
        if ($lockout['locked']) {
            $this->isLockedOut = true;
            $this->lockoutMessage = $lockout['message'];
        }

        return null;
    }

    public function verifyPin(Request $request, PinService $pinService, string $submittedPin, ?string $fingerprint = null): mixed
    {
        $submittedPin = trim($submittedPin);

        if (strlen($submittedPin) !== $this->pinLength || ! ctype_digit($submittedPin)) {
            $this->errorMessage = "Please enter a complete {$this->pinLength}-digit numeric PIN.";
            $this->dispatch('pin-error');

            return null;
        }

        $result = $pinService->verify($submittedPin, $request->ip(), $fingerprint);

        if ($result['success']) {
            $this->errorMessage = null;

            return redirect()->route('showcase.home');
        }

        $this->errorMessage = $result['error'] ?? 'Incorrect PIN. Access denied.';
        $this->dispatch('pin-error');

        // Refresh lockout status if needed
        $lockout = $pinService->checkLockout($request->ip(), $fingerprint);
        if ($lockout['locked']) {
            $this->isLockedOut = true;
            $this->lockoutMessage = $lockout['message'];
        }

        return null;
    }

    public function render(SettingsService $settings): View
    {
        return view('livewire.auth.landing', [
            'heroLine1' => $settings->get('hero_line_1', 'Kemin Exports'),
            'heroLine2' => $settings->get('hero_line_2', 'Products Catalog'),
            'tagline' => $settings->get('tagline', 'Packaging & Print Manufacturer, Sri Lanka'),
        ]);
    }
}
