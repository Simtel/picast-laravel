<?php

declare(strict_types=1);

namespace Tests\Unit\Context;

use App\Context\Common\Domain\Models\InviteCode;
use App\Context\User\Domain\Model\User;
use Tests\TestCase;

final class InviteCodeTest extends TestCase
{
    public function test_invite_code_has_fillable_attributes(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var InviteCode $inviteCode */
        $inviteCode = InviteCode::create(
            [
                'created_by' => $user->getId(),
                'code' => '123456'
            ]
        );

        self::assertEquals($user->getId(), $inviteCode->created_by);
        self::assertEquals('123456', $inviteCode->code);
        self::assertInstanceOf(InviteCode::class, $inviteCode);
    }
}
