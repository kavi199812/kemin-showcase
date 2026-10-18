<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Company;
use App\Models\PinAttempt;
use App\Models\Product;
use App\Models\Setting;
use App\Services\SettingsService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

#[Layout('layouts.admin')]
#[Title('Admin Dashboard — Kemin Exports')]
class Dashboard extends Component
{
    public string $migrationOutput = '';

    public function runMigrations(): void
    {
        Gate::authorize('manage', Setting::class);

        try {
            Artisan::call('migrate', ['--force' => true]);
            $this->migrationOutput = trim(Artisan::output()) ?: 'Database is up to date. (Nothing to migrate)';

            activity()
                ->causedBy(auth()->user())
                ->log('Admin executed database migrations via Dashboard');

            $this->dispatch('notify', message: 'Database migrations executed successfully.');
        } catch (\Throwable $e) {
            $this->migrationOutput = 'Error: '.$e->getMessage();
            $this->dispatch('notify', message: 'Migration failed: '.substr($e->getMessage(), 0, 80), type: 'danger');
        }
    }

    public function clearCache(): void
    {
        Gate::authorize('manage', Setting::class);

        try {
            Artisan::call('optimize:clear');
            $this->dispatch('notify', message: 'System and configuration caches cleared.');
        } catch (\Throwable $e) {
            $this->dispatch('notify', message: 'Cache clear failed: '.substr($e->getMessage(), 0, 80), type: 'danger');
        }
    }

    public function render(SettingsService $settings): View
    {
        $productsCount = Product::count();
        $activeProductsCount = Product::active()->count();
        $companiesCount = Company::count();
        $categoriesCount = Category::count();

        // 24-hour PIN security attempts
        $attempts24h = PinAttempt::where('created_at', '>=', Carbon::now()->subDay())->count();
        $failedAttempts24h = PinAttempt::where('succeeded', false)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->count();

        // Recent security attempts
        $recentPinAttempts = PinAttempt::latest('created_at')->take(5)->get();

        // Recent activities
        $recentActivities = Activity::with('causer')
            ->latest()
            ->take(6)
            ->get();

        // Check for pending database migrations
        $pendingMigrationsCount = 0;
        try {
            $migrator = app('migrator');
            $files = $migrator->getMigrationFiles(database_path('migrations'));
            $ran = $migrator->getRepository()->getRan();
            $pendingMigrationsCount = count(array_diff(array_keys($files), $ran));
        } catch (\Throwable) {
            $pendingMigrationsCount = 0;
        }

        return view('livewire.admin.dashboard', [
            'productsCount' => $productsCount,
            'activeProductsCount' => $activeProductsCount,
            'companiesCount' => $companiesCount,
            'categoriesCount' => $categoriesCount,
            'attempts24h' => $attempts24h,
            'failedAttempts24h' => $failedAttempts24h,
            'recentPinAttempts' => $recentPinAttempts,
            'recentActivities' => $recentActivities,
            'pendingMigrationsCount' => $pendingMigrationsCount,
            'pinLength' => $settings->get('pin_length', 6),
            'authMode' => config('app.showcase_admin_auth', 'password'),
        ]);
    }
}
