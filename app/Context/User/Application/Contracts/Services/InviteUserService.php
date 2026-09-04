<?php

declare(strict_types=1);

namespace App\Context\User\Application\Contracts\Services;

use App\Context\Common\Domain\Models\InviteCode;

interface InviteUserService
{
    /**
     * @param int $createdBy
     * @param string $name
     * @param string $email
     * @return InviteCode
     */
    public function invite(int $createdBy, string $name, string $email): InviteCode;
}
