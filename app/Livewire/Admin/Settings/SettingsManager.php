<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use App\Services\PinService;
use App\Services\SettingsService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Settings & Security — Admin')]
class SettingsManager extends Component
{
    // PIN update
    public string $newPin = '';

    public string $newPinConfirmation = '';

    public int $pinLength = 6;

    // General Showcase Settings
    public string $heroLine1 = '';

    public string $heroLine2 = '';

    public string $tagline = '';

    public int $presenterSessionHours = 8;

    // Security & Lockout Settings
    public int $lockoutMaxAttempts = 5;

    public int $lockoutWindowMinutes = 15;

    public int $lockoutMinutes = 15;

    public int $globalLockoutThreshold = 30;

    public function mount(SettingsService $settings): void
    {
        Gate::authorize('manage', Setting::class);

        $this->pinLength = (int) $settings->get('pin_length', 4);
        $this->heroLine1 = (string) $settings->get('hero_line_1', 'Kemin Exports');
        $this->heroLine2 = (string) $settings->get('hero_line_2', 'Products Catalog');
        $this->tagline = (string) $settings->get('tagline', 'Packaging & Print Manufacturer, Sri Lanka');
        $this->presenterSessionHours = (int) $settings->get('presenter_session_hours', 8);

        $this->lockoutMaxAttempts = (int) $settings->get('lockout_max_attempts', 5);
        $this->lockoutWindowMinutes = (int) $settings->get('lockout_window_minutes', 15);
        $this->lockoutMinutes = (int) $settings->get('lockout_minutes', 15);
        $this->globalLockoutThreshold = (int) $settings->get('global_lockout_threshold', 30);
    }

    public function updatePin(PinService $pinService): void
    {
        $this->validate([
            'newPin' => ['required', 'string', 'digits_between:4,12', 'same:newPinConfirmation'],
        ]);

        if ($pinService->isTrivialPin($this->newPin)) {
            $this->addError('newPin', 'This PIN is too simple or predictable (e.g. repetitive or sequential digits). Please choose a more secure numeric PIN.');

            return;
        }

        try {
            $pinService->updatePin($this->newPin);
            $this->pinLength = strlen($this->newPin);
            $this->newPin = '';
            $this->newPinConfirmation = '';
            $this->dispatch('notify', message: 'Presenter PIN updated successfully. All active sessions have been invalidated.');
        } catch (\Throwable $e) {
            $this->addError('newPin', $e->getMessage());
        }
    }

    public function saveShowcaseSettings(SettingsService $settings): void
    {
        $this->validate([
            'heroLine1' => ['required', 'string', 'max:60'],
            'heroLine2' => ['required', 'string', 'max:80'],
            'tagline' => ['required', 'string', 'max:120'],
            'presenterSessionHours' => ['required', 'integer', 'min:1', 'max:24'],
        ]);

        $settings->setMany([
            'hero_line_1' => $this->heroLine1,
            'hero_line_2' => $this->heroLine2,
            'tagline' => $this->tagline,
            'presenter_session_hours' => $this->presenterSessionHours,
        ]);

        $this->dispatch('notify', message: 'Showcase content settings saved.');
    }

    public function saveLockoutSettings(SettingsService $settings): void
    {
        $this->validate([
            'lockoutMaxAttempts' => ['required', 'integer', 'min:1', 'max:20'],
            'lockoutWindowMinutes' => ['required', 'integer', 'min:1', 'max:60'],
            'lockoutMinutes' => ['required', 'integer', 'min:1', 'max:120'],
            'globalLockoutThreshold' => ['required', 'integer', 'min:10', 'max:200'],
        ]);

        $settings->setMany([
            'lockout_max_attempts' => $this->lockoutMaxAttempts,
            'lockout_window_minutes' => $this->lockoutWindowMinutes,
            'lockout_minutes' => $this->lockoutMinutes,
            'global_lockout_threshold' => $this->globalLockoutThreshold,
        ]);

        $this->dispatch('notify', message: 'Security threshold settings saved.');
    }

    public function runDatabaseBackup(): void
    {
        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            $this->dispatch('notify', message: 'Database backup completed successfully.');
        } catch (\Throwable $e) {
            $this->dispatch('notify', message: 'Backup notice: '.substr($e->getMessage(), 0, 100), type: 'danger');
        }
    }

    public function render(SettingsService $settings): View
    {
        return view('livewire.admin.settings.settings-manager', [
            'sessionVersion' => $settings->get('presenter_session_version', 1),
            'authMode' => config('app.showcase_admin_auth', 'password'),
        ]);
    }
}
