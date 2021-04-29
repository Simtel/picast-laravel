<?php

namespace App\Providers;


use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Iodev\Whois\Factory;

class AppServiceProvider extends ServiceProvider
{

    public $singletons = [

    ];

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

        $this->app->singleton('whois', function (Application $app) {
            return Factory::get()->createWhois();
        });

    }
}
