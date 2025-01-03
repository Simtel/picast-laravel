<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Mail;

use App\Context\Domains\Domain\Model\Domain;
use App\Context\User\Domain\Model\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExpireDomainNotify extends Mailable
{
    use Queueable;
    use SerializesModels;

    public Domain $domain;

    public User $user;

    /**
     * ExpireDomainNotify constructor.
     * @param Domain $domain
     * @param User $user
     */
    public function __construct(Domain $domain, User $user)
    {
        $this->domain = $domain;
        $this->user = $user;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this->subject('Информация о вашем домене')->view('mail.jobs.domain_expire_notify');
    }
}
