<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        try {
            \App\Models\Product::ensureUnder500kProductsSeeded();
        } catch (\Throwable) {
            // Bỏ qua nếu bảng chưa được migrate
        }
    }
}
