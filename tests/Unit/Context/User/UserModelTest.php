<?php

declare(strict_types=1);

namespace Tests\Unit\Context\User;

use App\Context\User\Domain\Model\User;
use Event;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    public function test_user_has_domains(): void
    {
        Event::fake();
        /** @var User $user */
        $user = User::factory()->hasDomains(2)->create();

        self::assertCount(2, $user->domains()->get());
    }

    public function test_user_get_methods(): void
    {
        $user = User::factory()->create(['email' => 'test@mail.com']);

        self::assertEquals('test@mail.com', $user->getEmail());
    }
}
