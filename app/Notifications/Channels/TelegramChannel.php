<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Notifications\DomainDeleted;
use App\Services\Notifications\TelegramChannelNotification;
use Telegram\Bot\Exceptions\TelegramSDKException;

readonly class TelegramChannel
{
    public function __construct(private TelegramChannelNotification $telegramChannelNotification)
    {
    }

    /**
     * Send the given notification.
     * @throws TelegramSDKException
     */
    public function send(object $notifiable, DomainDeleted $notification): void
    {
        $message = $notification->toTelegram($notifiable);

        $this->telegramChannelNotification->sendTextToChannel($message->getMessage());
    }
}
