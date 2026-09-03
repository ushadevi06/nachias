<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
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
        Storage::extend('google', function ($app, $config) {
            $client = new \Google\Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);
            
            $service = new \Google\Service\Drive($client);
            $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, '', [
                'useHasDir' => true,
                'sharedFolderId' => $config['folderId'] ?? null,
            ]);
            
            return new \Illuminate\Filesystem\FilesystemAdapter(
                new \League\Flysystem\Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }

}
