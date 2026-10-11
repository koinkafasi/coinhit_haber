<?php

namespace App\Providers;

use App\Services\OmniRoute\OmniRouteService;
use Illuminate\Support\ServiceProvider;

class OmniRouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__, 2).'/config/omniroute.php', 'omniroute'
        );

        $this->app->singleton(OmniRouteService::class, function () {
            return new OmniRouteService();
        });
    }

    public function boot(): void
    {
        //
    }
}