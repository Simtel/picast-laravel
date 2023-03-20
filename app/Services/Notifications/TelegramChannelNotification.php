<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Objects\Message;

class TelegramChannelNotification
{
    /**
     * @throws TelegramSDKException
     */
    public function sendTextToChannel(string $message): Message
    {
        return \Telegram::setAsyncRequest(true)->sendMessage(
            ['chat_id' => env('TELEGRAM_MAIN_CHANNEL'), 'text' => $message]
        );
    }

    public function sendImageToChannel(string $filePath, string $name): Message
    {
        return \Telegram::setAsyncRequest(true)->sendPhoto(
            ['chat_id' => env('TELEGRAM_MAIN_CHANNEL'), 'photo' => InputFile::create($filePath, $name)]
        );
    }
}
