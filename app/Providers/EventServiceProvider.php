<?php

namespace App\Providers;

use App\Events\DomainCreated;
use App\Listeners\GetWhoisDomain;
use App\Models\Domain;
use App\Observers\DomainObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
       DomainCreated::class => [
            GetWhoisDomain::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Domain::observe(DomainObserver::class);
        //
    }
}
