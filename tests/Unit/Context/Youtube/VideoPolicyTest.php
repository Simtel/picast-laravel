<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Youtube;

use App\Context\User\Domain\Model\User;
use App\Context\Youtube\Application\Policy\YouTubeVideoPolicy;
use App\Context\Youtube\Domain\Model\Video;
use Tests\TestCase;

class VideoPolicyTest extends TestCase
{
    public function test_view_any(): void
    {
        $user = User::factory()->create();
        $policy = new YouTubeVideoPolicy();
        self::assertTrue($policy->viewAny($user));
    }

    public function test_view(): void
    {
        $user = User::factory()->create();
        $video = Video::factory()->make(['user_id' => $user->getId()]);

        $policy = new YouTubeVideoPolicy();
        self::assertTrue($policy->view($user, $video));
    }

    public function test_create(): void
    {
        $user = User::factory()->create();
        $policy = new YouTubeVideoPolicy();
        self::assertTrue($policy->create($user));
    }

    public function test_update(): void
    {
        $user = User::factory()->create();
        $video = Video::factory()->make(['user_id' => $user->getId()]);

        $policy = new YouTubeVideoPolicy();
        self::assertTrue($policy->update($user, $video));
    }

    public function test_delete(): void
    {
        $user = User::factory()->create();
        $video = Video::factory()->make(['user_id' => $user->getId()]);

        $policy = new YouTubeVideoPolicy();
        self::assertTrue($policy->delete($user, $video));
    }

    public function test_restore(): void
    {
        $user = User::factory()->create();
        $video = Video::factory()->make(['user_id' => $user->getId()]);

        $policy = new YouTubeVideoPolicy();
        self::assertTrue($policy->restore($user, $video));
    }

    public function test_force_delete(): void
    {
        $user = User::factory()->create();
        $video = Video::factory()->make(['user_id' => $user->getId()]);

        $policy = new YouTubeVideoPolicy();
        self::assertTrue($policy->forceDelete($user, $video));
    }

}
