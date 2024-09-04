<?php

namespace App\Providers;

use App\Events\DomainCreated;
use App\Events\YouTubeVideoCreated;
use App\Listeners\GetWhoisDomain;
use App\Listeners\YouTubeVideoCreateListener;
use App\Models\Domains\Domain;
use App\Models\Youtube\YouTubeVideo;
use App\Observers\DomainObserver;
use App\Observers\YouTubeVideoObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        DomainCreated::class       => [
            GetWhoisDomain::class
        ],
        YouTubeVideoCreated::class => [
            YouTubeVideoCreateListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        Domain::observe(DomainObserver::class);
        YouTubeVideo::observe(YouTubeVideoObserver::class);
        //
    }
}
