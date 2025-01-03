<?php

declare(strict_types=1);

namespace App\Providers;

use App\Context\Domains\Application\Contract\WhoisService;
use App\Context\Domains\Application\Contract\WhoisUpdater;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Iodev\Whois\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $singletons = [
        WhoisUpdater::class => \App\Context\Domains\Application\Service\WhoisUpdater::class,
        WhoisService::class => \App\Context\Domains\Application\Service\WhoisService::class
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
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
