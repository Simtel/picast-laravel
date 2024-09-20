<?php

declare(strict_types=1);

namespace App\Contracts\Services;

interface InviteUserService
{
    /**
     * @param string $name
     * @param string $email
     * @return mixed
     */
    public function invite(string $name, string $email): mixed;
}
