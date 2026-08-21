<?php

declare(strict_types=1);

namespace Tests\Unit\Context;

use App\Context\Common\Domain\Models\Images;
use Illuminate\Support\Facades\File;
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

    public function test_get_path_for_s3_with_public_url(): void
    {
        $user = $this->createUserWithPermissions([], ['edit images']);

        config()->set('filesystems.disks.s3.public-url', 'https://cdn.example.com');

        /** @var Images $image */
        $image = Images::create(
            [
                'user_id' => $user->getId(),
                'filename' => 'photo.jpg',
                'directory' => 'images/08-2026',
                'thumb' => '',
                'width' => 0,
                'check' => 1,
                'disk' => 's3',
                'views_count' => 0
            ]
        );

        self::assertEquals('https://cdn.example.com/images/08-2026/photo.jpg', $image->getPath());
    }

    public function test_get_path_for_s3_falls_back_to_app_url(): void
    {
        $user = $this->createUserWithPermissions([], ['edit images']);

        config()->set('filesystems.disks.s3.public-url', null);

        /** @var Images $image */
        $image = Images::create(
            [
                'user_id' => $user->getId(),
                'filename' => 'photo.jpg',
                'directory' => 'images/08-2026',
                'thumb' => '',
                'width' => 0,
                'check' => 1,
                'disk' => 's3',
                'views_count' => 0
            ]
        );

        $expected = rtrim(strval(config('app.url')), '/') . '/images/08-2026/photo.jpg';

        self::assertEquals($expected, $image->getPath());
    }

    public function test_get_formatted_size_for_s3_known_extension(): void
    {
        $user = $this->createUserWithPermissions([], ['edit images']);

        /** @var Images $image */
        $image = Images::create(
            [
                'user_id' => $user->getId(),
                'filename' => 'photo.png',
                'directory' => 'images',
                'thumb' => '',
                'width' => 0,
                'check' => 1,
                'disk' => 's3',
                'views_count' => 0
            ]
        );

        self::assertEquals('3.2 MB', $image->getFormattedSize());
    }

    public function test_get_formatted_size_for_s3_unknown_extension(): void
    {
        $user = $this->createUserWithPermissions([], ['edit images']);

        /** @var Images $image */
        $image = Images::create(
            [
                'user_id' => $user->getId(),
                'filename' => 'photo.bmp',
                'directory' => 'images',
                'thumb' => '',
                'width' => 0,
                'check' => 1,
                'disk' => 's3',
                'views_count' => 0
            ]
        );

        self::assertEquals('2.0 MB', $image->getFormattedSize());
    }

    public function test_get_formatted_size_returns_unknown_for_missing_local_file(): void
    {
        $user = $this->createUserWithPermissions([], ['edit images']);

        /** @var Images $image */
        $image = Images::create(
            [
                'user_id' => $user->getId(),
                'filename' => 'missing.jpg',
                'directory' => 'images',
                'thumb' => '',
                'width' => 0,
                'check' => 1,
                'disk' => 'local',
                'views_count' => 0
            ]
        );

        self::assertEquals('Неизвестно', $image->getFormattedSize());
    }

    public function test_get_formatted_size_for_existing_local_file(): void
    {
        $user = $this->createUserWithPermissions([], ['edit images']);

        $directory = public_path('images');
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, recursive: true);
        }

        $filePath = $directory . '/size-test.bin';
        File::put($filePath, str_repeat('a', 2048));

        try {
            /** @var Images $image */
            $image = Images::create(
                [
                    'user_id' => $user->getId(),
                    'filename' => 'size-test.bin',
                    'directory' => 'images',
                    'thumb' => '',
                    'width' => 0,
                    'check' => 1,
                    'disk' => 'local',
                    'views_count' => 0
                ]
            );

            self::assertEquals('2 KB', $image->getFormattedSize());
        } finally {
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }
    }
}
