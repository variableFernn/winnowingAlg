<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Services\WinnowingService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register WinnowingService sebagai singleton
        $this->app->singleton(WinnowingService::class, function ($app) {
            return new WinnowingService(
                config('winnowing.k_gram', 5),
                config('winnowing.window_size', 4)
            );
        });
    }

    public function boot(): void
    {
        Paginator::useBootstrap();
    }
}