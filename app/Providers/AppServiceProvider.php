<?php

namespace App\Providers;

use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
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
            if (! Cache::get('app.boot_schema_checked_v1')) {
                \App\Models\User::ensureCustomerFeaturesSchema();
                \App\Models\Product::ensureUnder500kProductsSeeded();
                Cache::put('app.boot_schema_checked_v1', true, now()->addHours(12));
            }
        } catch (\Throwable) {
            // Bỏ qua nếu bảng chưa được migrate
        }

        View::composer('*', function ($view): void {
            static $cachedWishlistIds = null;

            if ($cachedWishlistIds === null) {
                $cachedWishlistIds = [];
                try {
                    if (Auth::check()) {
                        $cachedWishlistIds = Wishlist::query()
                            ->where('user_id', Auth::id())
                            ->pluck('product_id')
                            ->map(fn ($id) => (int) $id)
                            ->all();
                    }
                } catch (\Throwable) {
                    $cachedWishlistIds = [];
                }
            }

            $view->with('wishlistProductIds', $cachedWishlistIds);
        });
    }
}
