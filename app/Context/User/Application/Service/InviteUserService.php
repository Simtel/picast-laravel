<?php

declare(strict_types=1);

namespace App\Context\User\Application\Service;

use App\Context\Common\Domain\Models\InviteCode;
use App\Context\User\Application\Contracts\Services\InviteUserService as InviteUserServiceContract;
use App\Context\User\Infrastructure\Mail\InviteUserNotify;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Override;

final class InviteUserService implements InviteUserServiceContract
{
    /**
     * @param int $createdBy
     * @param string $name
     * @param string $email
     * @return InviteCode
     */
    #[Override]
    public function invite(int $createdBy, string $name, string $email): InviteCode
    {
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $inviteCode = InviteCode::create(
            [
                'created_by' => $createdBy,
                'code' => $code,
            ]
        );

        Mail::to($email)->send(new InviteUserNotify($inviteCode->code, $name));

        Log::info('[InviteUserService.invite] приглашение создано', [
            'email' => $email,
            'code' => $inviteCode->code,
        ]);

        return $inviteCode;
    }
}
