<?php

namespace Hak\PaymentMpu;

use Illuminate\Support\ServiceProvider;
use Hak\PaymentMpu\Interfaces\GatewayInterface;

class GatewayServiceProvider extends ServiceProvider
{
    public function boot()
    {
         if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/mpu.php' => config_path('mpu.php'),
            ], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mpu.php', 'mpu');

        $this->app->singleton(GatewayInterface::class, function ($app) {
            return new Gateway();
        });
    }
}