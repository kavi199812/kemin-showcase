<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Company;
use App\Models\PinAttempt;
use App\Models\Product;
use App\Services\SettingsService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

#[Layout('layouts.admin')]
#[Title('Admin Dashboard — Kemin Exports')]
class Dashboard extends Component
{
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

        return view('livewire.admin.dashboard', [
            'productsCount' => $productsCount,
            'activeProductsCount' => $activeProductsCount,
            'companiesCount' => $companiesCount,
            'categoriesCount' => $categoriesCount,
            'attempts24h' => $attempts24h,
            'failedAttempts24h' => $failedAttempts24h,
            'recentPinAttempts' => $recentPinAttempts,
            'recentActivities' => $recentActivities,
            'pinLength' => $settings->get('pin_length', 6),
            'authMode' => config('app.showcase_admin_auth', 'password'),
        ]);
    }
}
