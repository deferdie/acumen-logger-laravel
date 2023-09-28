<?php

namespace AcumenLogger;

use Illuminate\Support\ServiceProvider;

class ClientExceptionLoggerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('acumenlogs-logger', function () {
            return new AcumenLogger();
        });
    }
}
