<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Tools;

use App\Context\Tools\Application\Service\UuidService;
use InvalidArgumentException;
use Tests\TestCase;

final class UuidServiceTest extends TestCase
{
    private UuidService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UuidService();
    }

    public function test_generate_v4_returns_valid_uuid(): void
    {
        $result = $this->service->generate('v4', 1);

        self::assertCount(1, $result);
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $result[0],
        );
    }

    public function test_generate_v7_returns_valid_uuid(): void
    {
        $result = $this->service->generate('v7', 1);

        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $result[0],
        );
    }

    public function test_generate_returns_requested_count_and_unique(): void
    {
        $result = $this->service->generate('v4', 5);

        self::assertCount(5, $result);
        self::assertCount(5, array_unique($result));
    }

    public function test_generate_throws_on_unknown_version(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->generate('v9', 1);
    }

    public function test_has_version(): void
    {
        self::assertTrue($this->service->hasVersion('v4'));
        self::assertFalse($this->service->hasVersion('v9'));
    }
}
