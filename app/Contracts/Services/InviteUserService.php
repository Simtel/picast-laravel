<?php

namespace App\Contracts\Services;

interface InviteUserService
{
    /**
     * @param string $name
     * @param string $email
     * @return mixed
     */
    public function invite(string $name, string $email);
}
