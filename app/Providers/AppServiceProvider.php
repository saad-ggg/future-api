<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // Import the Paginator class

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
        /**
         * Instruct Laravel to use Bootstrap 5 styling for pagination.
         * This prevents the pagination buttons from appearing oversized (Tailwind default).
         */
        Paginator::useBootstrapFive();
    }
}