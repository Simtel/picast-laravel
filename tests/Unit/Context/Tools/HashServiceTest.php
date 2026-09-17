<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Tools;

use App\Context\Tools\Application\Service\HashService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class HashServiceTest extends TestCase
{
    private HashService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HashService();
    }

    #[DataProvider('knownHashProvider')]
    public function test_hash_returns_known_value(string $algorithm, string $expected): void
    {
        $result = $this->service->hash($algorithm, 'hello');

        self::assertSame($expected, $result);
    }

    #[DataProvider('delegatedAlgorithmProvider')]
    public function test_hash_delegates_to_php_hash(string $algorithm): void
    {
        $algo = $this->service->getAlgorithms()[$algorithm]['algo'];

        self::assertSame(hash($algo, 'hello'), $this->service->hash($algorithm, 'hello'));
    }

    public function test_has_algorithm_returns_true_for_known_key(): void
    {
        self::assertTrue($this->service->hasAlgorithm('md5'));
    }

    public function test_has_algorithm_returns_false_for_unknown_key(): void
    {
        self::assertFalse($this->service->hasAlgorithm('unknown'));
    }

    public function test_hash_throws_for_unknown_algorithm(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->hash('unknown', 'hello');
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function knownHashProvider(): array
    {
        return [
            'md5' => ['md5', '5d41402abc4b2a76b9719d911017c592'],
            'sha1' => ['sha1', 'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d'],
            'sha256' => ['sha256', '2cf24dba5fb0a30e26e83b2ac5b9e29e1b161e5c1fa7425e73043362938b9824'],
            'sha3' => ['sha3', '3338be694f50c5f338814986cdf0686453a888b84f424d792af4b9202398f392'],
            'ripemd160' => ['ripemd160', '108f07b8382412612c048d07d13f814118445acd'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function delegatedAlgorithmProvider(): array
    {
        return [
            'sha224' => ['sha224'],
            'sha384' => ['sha384'],
            'sha512' => ['sha512'],
        ];
    }
}
