<?php

declare(strict_types=1);

namespace Tests\Unit\Context;

use App\Context\Common\Domain\Models\Images;
use Tests\TestCase;

final class ImagesTest extends TestCase
{
    public function test_a_image_has_user(): void
    {
        $user = $this->createUserWithPermissions([], ['edit images']);

        /** @var Images $image */
        $image =  Images::create(
            [
                'user_id' => $user->getId(),
                'filename' => 'test.jpg',
                'directory' => 'images',
                'thumb' => '',
                'width' => 0,
                'check' => 1,
                'disk'  => 's3',
                'views_count' => 0
            ]
        );

        self::assertEquals($user->getId(), $image->getUser()->getId());
    }

    public function test_a_image_with_local_disk(): void
    {
        $user = $this->createUserWithPermissions([], ['edit images']);

        /** @var Images $image */
        $image =  Images::create(
            [
                'user_id' => $user->getId(),
                'filename' => 'test.jpg',
                'directory' => 'images',
                'thumb' => '',
                'width' => 0,
                'check' => 1,
                'disk'  => 'local',
                'views_count' => 0
            ]
        );

        self::assertStringContainsString('/public/images/test.jpg', $image->getPath());
    }

    public function test_a_image_has_views_count_attribute(): void
    {
        $user = $this->createUserWithPermissions([], ['edit images']);

        /** @var Images $image */
        $image = Images::create(
            [
                'user_id' => $user->getId(),
                'filename' => 'test.jpg',
                'directory' => 'images',
                'thumb' => '',
                'width' => 0,
                'check' => 1,
                'disk' => 's3',
                'views_count' => 5
            ]
        );

        self::assertEquals(5, $image->views_count);
        self::assertArrayHasKey('views_count', $image->getAttributes());
    }

    public function test_a_image_views_count_defaults_to_zero(): void
    {
        $user = $this->createUserWithPermissions([], ['edit images']);

        /** @var Images $image */
        $image = Images::create(
            [
                'user_id' => $user->getId(),
                'filename' => 'test.jpg',
                'directory' => 'images',
                'thumb' => '',
                'width' => 0,
                'check' => 1,
                'disk' => 's3'
            ]
        );

        self::assertEquals(0, $image->views_count);
    }

    public function test_increment_views_count(): void
    {
        $user = $this->createUserWithPermissions([], ['edit images']);

        /** @var Images $image */
        $image = Images::create(
            [
                'user_id' => $user->getId(),
                'filename' => 'test.jpg',
                'directory' => 'images',
                'thumb' => '',
                'width' => 0,
                'check' => 1,
                'disk' => 's3',
                'views_count' => 0
            ]
        );

        self::assertEquals(0, $image->views_count);

        $image->incrementViews();

        self::assertEquals(1, $image->fresh()->views_count);

        $image->incrementViews();
        $image->incrementViews();

        self::assertEquals(3, $image->fresh()->views_count);
    }
}
