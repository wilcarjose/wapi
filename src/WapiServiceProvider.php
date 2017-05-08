<?php

namespace Wilcar\Wapi;

use Illuminate\Support\ServiceProvider;

class WapiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('wapi', function($app){
            return new Wapi;
        });
    }

    public function boot()
    {
        include __DIR__.'/../vendor/autoload.php';
        require __DIR__ . '/Http/routes.php';
        $this->loadTranslationsFrom(__DIR__.'/resources/lang/', 'wapi');
    }
}
