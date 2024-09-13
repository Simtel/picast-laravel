<?php

namespace App\Providers;

use App\Models\Domains\Domain;
use App\Models\Youtube\Video;
use App\Policies\DomainPolicy;
use App\Policies\YouTubeVideoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Domain::class => DomainPolicy::class,
        Video::class  => YouTubeVideoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
    }
}
