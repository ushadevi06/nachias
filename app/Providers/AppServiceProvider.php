<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Helpers\Helper;
use App\Models\Setting;

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

        // 🔹 Your existing code (UNCHANGED)
        View::composer('*', function ($view) {

            $webSettings = Cache::rememberForever('web_settings', function () {
                return Setting::first();
            });

            $view->with('web_settings', $webSettings);
        });
    }

}
