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
                'disk'  => 's3'
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
                'disk'  => 'local'
            ]
        );

        self::assertStringContainsString('/public/images/test.jpg', $image->getPath());
    }
}
