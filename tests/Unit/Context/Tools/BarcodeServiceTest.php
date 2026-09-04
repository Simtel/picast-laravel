<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Tools;

use App\Context\Tools\Application\Service\BarcodeService;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class BarcodeServiceTest extends TestCase
{
    private BarcodeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BarcodeService();
    }

    public function test_with_ean_check_digit_ean13(): void
    {
        $result = $this->service->withEanCheckDigit('590123412345', 13);

        self::assertSame(13, strlen($result));
        self::assertSame('5901234123457', $result);
    }

    public function test_with_ean_check_digit_ean8(): void
    {
        $result = $this->service->withEanCheckDigit('9638507', 8);

        self::assertSame(8, strlen($result));
        self::assertSame('96385074', $result);
    }

    public function test_with_itf14_check_digit(): void
    {
        $result = $this->service->withItf14CheckDigit('1540014128876');

        self::assertSame(14, strlen($result));
        self::assertSame('15400141288763', $result);
    }

    public function test_generate_sample_ean13_has_valid_check_digit(): void
    {
        $sample = $this->service->generateSample('ean13');

        self::assertMatchesRegularExpression('/^\d{13}$/', $sample);
        self::assertSame($sample, $this->service->withEanCheckDigit(substr($sample, 0, 12), 13));
    }

    public function test_generate_sample_ean8_has_valid_check_digit(): void
    {
        $sample = $this->service->generateSample('ean8');

        self::assertMatchesRegularExpression('/^\d{8}$/', $sample);
        self::assertSame($sample, $this->service->withEanCheckDigit(substr($sample, 0, 7), 8));
    }

    public function test_generate_sample_itf14_has_valid_check_digit(): void
    {
        $sample = $this->service->generateSample('itf14');

        self::assertMatchesRegularExpression('/^\d{14}$/', $sample);
        self::assertSame($sample, $this->service->withItf14CheckDigit(substr($sample, 0, 13)));
    }

    #[DataProvider('digitsProvider')]
    public function test_random_digits_length(int $min, ?int $max, int $minLength, int $maxLength): void
    {
        $result = $this->service->randomDigits($min, $max);

        $length = strlen($result);
        self::assertGreaterThanOrEqual($minLength, $length);
        self::assertLessThanOrEqual($maxLength, $length);
        self::assertMatchesRegularExpression('/^\d+$/', $result);
    }

    /**
     * @return array<string, array{int, int|null, int, int}>
     */
    public static function digitsProvider(): array
    {
        return [
            'fixed length' => [5, null, 5, 5],
            'range' => [6, 10, 6, 10],
        ];
    }

    public function test_has_type(): void
    {
        self::assertTrue($this->service->hasType('code128'));
        self::assertTrue($this->service->hasType('ean13'));
        self::assertFalse($this->service->hasType('unknown'));
    }

    public function test_render_returns_png_data_uri(): void
    {
        $uri = $this->service->render('code128', 'TEST-12345', 2, 50);

        self::assertStringStartsWith('data:image/png;base64,', $uri);
    }
}
