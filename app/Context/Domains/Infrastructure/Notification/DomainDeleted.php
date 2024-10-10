<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Notification;

use App\Context\Domains\Domain\Model\Domain;
use App\Notifications\Channels\TelegramChannel;
use App\Notifications\Channels\TelegramMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DomainDeleted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly Domain $domain,
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', TelegramChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->line($this->domain->name . ' был удален из системы.')
            ->action('Личный кабинет', url('/'))
            ->line('Спасибо что пользуетесь нашим сервисом!');
    }

    public function toTelegram(object $notifiable): TelegramMessage
    {
        return new TelegramMessage('Домен ' . $this->domain->name . ' был удален из системы.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
