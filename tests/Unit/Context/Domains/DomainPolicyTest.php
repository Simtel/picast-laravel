<?php

declare(strict_types=1);

namespace Context\Domains;

use App\Context\Domains\Application\Policy\DomainPolicy;
use App\Context\Domains\Domain\Model\Domain;
use App\Context\User\Domain\Model\User;
use Tests\TestCase;

class DomainPolicyTest extends TestCase
{
    public function test_view_any(): void
    {
        $user = User::factory()->create();
        $policy = new DomainPolicy();
        self::assertTrue($policy->viewAny($user));
    }

    public function test_view(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->make(['user_id' => $user->getId()]);

        $policy = new DomainPolicy();
        self::assertTrue($policy->view($user, $domain));
    }

    public function test_create(): void
    {
        $user = User::factory()->create();
        $policy = new DomainPolicy();
        self::assertTrue($policy->create($user));
    }

    public function test_update(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->make(['user_id' => $user->getId()]);

        $policy = new DomainPolicy();
        self::assertTrue($policy->update($user, $domain));
    }

    public function test_delete(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->make(['user_id' => $user->getId()]);

        $policy = new DomainPolicy();
        self::assertTrue($policy->delete($user, $domain));
    }

    public function test_restore(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->make(['user_id' => $user->getId()]);

        $policy = new DomainPolicy();
        self::assertFalse($policy->restore($user, $domain));
    }

    public function test_force_delete(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->make(['user_id' => $user->getId()]);

        $policy = new DomainPolicy();
        self::assertFalse($policy->forceDelete($user, $domain));
    }

}
