<?php

namespace App\Providers;

use App\Contracts\SomeServiceContract;
use App\Services\SomeService;
use App\Services\SomeServiceSecond;
use Illuminate\Support\ServiceProvider;

class SomeServiceProvider extends ServiceProvider
{

    public $singletons =[
        SomeServiceContract::class => SomeServiceSecond::class
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
       $this->app->bind('someservice',function ($app) {
            return $app->make(SomeServiceContract::class);
        });
    }
}
