<?php

declare(strict_types=1);

namespace App\Providers;

use App\Context\ChadGPT\Domain\Command\CreateChatConversationCommand;
use App\Context\ChadGPT\Infrastructure\Handlers\CreateChatConversationHandler;
use App\Context\Common\Infrastructure\CommandBus;
use App\Context\Domains\Application\Contract\WhoisService;
use App\Context\Domains\Application\Contract\WhoisUpdater;
use App\Context\Domains\Domain\Command\ListDomainsQuery;
use App\Context\Domains\Infrastructure\Handlers\ListDomainsQueryHandler;
use App\Context\User\Application\Contracts\Services\InviteUserService;
use App\Context\User\Application\Service\InviteUserService as InviteUserServiceImplementation;
use App\Context\User\Domain\Model\User;
use GuzzleHttp\Client;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Application;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Iodev\Whois\Factory;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $singletons = [
        WhoisUpdater::class => \App\Context\Domains\Application\Service\WhoisUpdater::class,
        WhoisService::class => \App\Context\Domains\Application\Service\WhoisService::class,
        InviteUserService::class => InviteUserServiceImplementation::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        ResetPassword::toMailUsing(static function (User $notifiable, string $token): MailMessage {
            return (new MailMessage())
                ->subject('Сброс пароля — A&S Tech')
                ->view('mail.notifications.reset_password', [
                    'user' => $notifiable,
                    'url' => url(route('password.reset', [
                        'token' => $token,
                        'email' => $notifiable->getEmailForPasswordReset(),
                    ], false)),
                    'expire' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire'),
                ]);
        });

        VerifyEmail::toMailUsing(static function (User $notifiable, string $url): MailMessage {
            return (new MailMessage())
                ->subject('Подтверждение email — A&S Tech')
                ->view('mail.notifications.verify_email', [
                    'user' => $notifiable,
                    'url' => $url,
                ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('whois', static function (Application $app) {
            return Factory::get()->createWhois();
        });

        $this->app->singleton(CommandBus::class, static function () {
            $bus = new CommandBus();

            $bus->register(CreateChatConversationCommand::class, CreateChatConversationHandler::class);
            $bus->register(ListDomainsQuery::class, ListDomainsQueryHandler::class);
            return $bus;
        });

        $this->app->singleton(Client::class, static function ($app) {
            return new Client();
        });
    }
}
