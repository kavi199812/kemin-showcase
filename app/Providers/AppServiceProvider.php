<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());

        Password::defaults(function () {
            return Password::min(12)->uncompromised();
        });

        Gate::before(function ($user, $ability) {
            if ($user && isset($user->is_admin) && $user->is_admin) {
                return true;
            }

            return null;
        });
    }
}
