<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteUserNotify extends Mailable
{
    use Queueable;
    use SerializesModels;

    public string $code;

    public string $name;

    /**
     * Create a new message instance.
     *
     * @param string $code
     * @param string $name
     */
    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): InviteUserNotify
    {
        return $this->subject('Вы приглашены в сервис')->view('mail.personal.invite');
    }
}
