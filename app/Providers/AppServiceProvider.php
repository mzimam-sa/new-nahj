<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        Paginator::defaultView('pagination::default');

        $explicitPlatform = config('lrs-nelc-xapi.platform');
        $fallbackPlatform = $explicitPlatform ?: config('lrs-nelc-xapi.lms_url') ?: config('app.url');

        if (blank(config('lrs-nelc-xapi.platform_in_arabic')) && filled($fallbackPlatform)) {
            config(['lrs-nelc-xapi.platform_in_arabic' => $fallbackPlatform]);
        }

        if (blank(config('lrs-nelc-xapi.platform_in_english')) && filled($fallbackPlatform)) {
            config(['lrs-nelc-xapi.platform_in_english' => $fallbackPlatform]);
        }

        if (filled($explicitPlatform)) {
            config([
                'lrs-nelc-xapi.platform_in_arabic' => $explicitPlatform,
                'lrs-nelc-xapi.platform_in_english' => $explicitPlatform,
            ]);
        }
    }
}
