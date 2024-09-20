<?php

declare(strict_types=1);

namespace App\Providers;

use App\Context\Youtube\Application\Policy\YouTubeVideoPolicy;
use App\Context\Youtube\Domain\Model\Video;
use App\Models\Domains\Domain;
use App\Policies\DomainPolicy;
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
