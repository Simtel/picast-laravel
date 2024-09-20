<?php

declare(strict_types=1);

namespace App\Providers;

use App\Context\Youtube\Application\Listener\YouTubeVideoCreateListener;
use App\Context\Youtube\Domain\Event\YouTubeVideoCreated;
use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Observer\YouTubeVideoObserver;
use App\Events\DomainCreated;
use App\Listeners\GetWhoisDomain;
use App\Models\Domains\Domain;
use App\Observers\DomainObserver;
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
        Video::observe(YouTubeVideoObserver::class);
        //
    }
}
