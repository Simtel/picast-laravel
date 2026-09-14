<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Tools;

use App\Context\Tools\Application\Service\RandomStringService;
use Tests\TestCase;

final class RandomStringServiceTest extends TestCase
{
    private RandomStringService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RandomStringService();
    }

    public function test_generate_returns_requested_length(): void
    {
        $result = $this->service->generate(32, true, true, true, false);

        self::assertSame(32, strlen($result));
    }

    public function test_generate_uppercase_only(): void
    {
        $result = $this->service->generate(100, true, false, false, false);

        self::assertMatchesRegularExpression('/^[A-Z]+$/', $result);
    }

    public function test_generate_digits_only(): void
    {
        $result = $this->service->generate(100, false, false, true, false);

        self::assertMatchesRegularExpression('/^[0-9]+$/', $result);
    }

    public function test_generate_symbols_only(): void
    {
        $result = $this->service->generate(100, false, false, false, true);

        self::assertMatchesRegularExpression('/^[!@#$%^&*()\-_=+\[\]{}|;:,.<>?]+$/', $result);
    }

    public function test_generate_falls_back_when_no_charset_selected(): void
    {
        $result = $this->service->generate(100, false, false, false, false);

        self::assertMatchesRegularExpression('/^[A-Za-z0-9]+$/', $result);
    }
}
