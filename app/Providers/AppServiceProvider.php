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
        $web_settings = Setting::first();
        View::share('web_settings', $web_settings);
        if (!defined('WEB_SETTINGS')) {
            define('WEB_SETTINGS', $web_settings);
        }

        // Force HTTPS when running behind Ngrok proxy
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || str_contains(url('/'), 'ngrok')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }

}
