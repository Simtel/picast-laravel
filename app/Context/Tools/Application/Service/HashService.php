<?php

declare(strict_types=1);

namespace App\Context\Tools\Application\Service;

use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

final class HashService
{
    /**
     * Доступные алгоритмы хэширования.
     *
     * @var array<string, array{algo: string, label: string}>
     */
    private const array ALGORITHMS = [
        'md5' => ['algo' => 'md5', 'label' => 'MD5'],
        'sha1' => ['algo' => 'sha1', 'label' => 'SHA1'],
        'sha224' => ['algo' => 'sha224', 'label' => 'SHA224'],
        'sha256' => ['algo' => 'sha256', 'label' => 'SHA256'],
        'sha384' => ['algo' => 'sha384', 'label' => 'SHA384'],
        'sha512' => ['algo' => 'sha512', 'label' => 'SHA512'],
        'sha3' => ['algo' => 'sha3-256', 'label' => 'SHA3'],
        'ripemd160' => ['algo' => 'ripemd160', 'label' => 'RIPEMD160'],
    ];

    /**
     * @return array<string, array{algo: string, label: string}>
     */
    public function getAlgorithms(): array
    {
        return self::ALGORITHMS;
    }

    public function hasAlgorithm(string $key): bool
    {
        return isset(self::ALGORITHMS[$key]);
    }

    public function hash(string $key, string $text): string
    {
        Log::debug('[HashService.hash] начало', ['algorithm' => $key, 'textLength' => strlen($text)]);

        $algo = self::ALGORITHMS[$key]['algo'] ?? null;

        if ($algo === null) {
            Log::error('[HashService.hash] неизвестный алгоритм', ['algorithm' => $key]);

            throw new InvalidArgumentException("Unknown hash algorithm: {$key}");
        }

        try {
            $result = hash($algo, $text);

            Log::info('[HashService.hash] хэш вычислен', [
                'algorithm' => $key,
                'textLength' => strlen($text),
                'resultLength' => strlen($result),
            ]);

            return $result;
        } catch (Throwable $e) {
            Log::error('[HashService.hash] ошибка вычисления', [
                'algorithm' => $key,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
