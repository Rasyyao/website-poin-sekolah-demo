<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('websiteName', website_name());
        });

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
