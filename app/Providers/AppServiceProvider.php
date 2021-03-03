<?php

namespace App\Providers;

use App\Events\DomainCreated;
use App\Models\Domain;
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

        $this->app->singleton('whois', function ($app) {
            return Factory::get()->createWhois();
        });

    }
}
