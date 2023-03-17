<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;

class TelegramChannelNotification
{

    /**
     * @throws TelegramSDKException
     */
    public function sendToChannel(string $message): Message
    {
        return \Telegram::setAsyncRequest(true)->sendMessage(
            ['chat_id' => env('TELEGRAM_MAIN_CHANNEL'), 'text' => $message]
        );
    }
}
