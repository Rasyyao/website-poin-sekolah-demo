<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // Use Tailwind CSS pagination views
        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');

        Gate::before(function ($user, $ability) {
            if ($user->role?->value === 'super_admin') {
                return true;
            }
        });

        // Gate: who can input student points
        Gate::define('input points', function ($user) {
            return $user->canInputPoints();
        });
    }
}
