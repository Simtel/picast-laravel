<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

class TelegramMessage
{
    public function __construct(private readonly string $message)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
