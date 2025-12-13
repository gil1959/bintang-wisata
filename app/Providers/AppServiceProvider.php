<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $keys = [
                'hero_title',
                'hero_subtitle',
                'hero_image',
            ];

            $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');

            $view->with('siteSettings', [
                'hero_title' => $settings['hero_title'] ?? 'Paket Tour Spesial untuk Liburan Tak Terlupakan!',
                'hero_subtitle' => $settings['hero_subtitle'] ?? 'Liburan Tanpa Batas! Jelajahi Destinasi Impian dengan Paket Tour Kami',
                'hero_image' => $settings['hero_image'] ?? '/images/hero-default.jpg',
            ]);
        });
    }
}
