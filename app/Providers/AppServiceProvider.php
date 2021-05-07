<?php

namespace App\Providers;


use App\Contracts\Services\Domains\WhoisUpdater;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Iodev\Whois\Factory;

class AppServiceProvider extends ServiceProvider
{

    public $singletons = [
        WhoisUpdater::class => \App\Services\Domains\WhoisUpdater::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {

        $this->app->singleton('whois', function (Application $app) {
            return Factory::get()->createWhois();
        });

    }
}
