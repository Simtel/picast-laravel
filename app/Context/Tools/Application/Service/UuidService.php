<?php

declare(strict_types=1);

namespace App\Context\Tools\Application\Service;

use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;
use Throwable;

final class UuidService
{
    private const array VERSIONS = [
        'v1' => ['label' => 'UUID v1 (по времени)'],
        'v4' => ['label' => 'UUID v4 (случайный)'],
        'v6' => ['label' => 'UUID v6 (по времени)'],
        'v7' => ['label' => 'UUID v7 (время + случайный)'],
    ];

    /**
     * @return array<string, array{label: string}>
     */
    public function getVersions(): array
    {
        return self::VERSIONS;
    }

    public function hasVersion(string $version): bool
    {
        return isset(self::VERSIONS[$version]);
    }

    /**
     * @return array<int, string>
     */
    public function generate(string $version, int $count): array
    {
        if (!$this->hasVersion($version)) {
            Log::warning('[UuidService.generate] неизвестная версия UUID', ['version' => $version]);

            throw new InvalidArgumentException(sprintf('Unsupported UUID version: %s', $version));
        }

        $factory = match ($version) {
            'v1' => static fn (): Uuid => Uuid::v1(),
            'v4' => static fn (): Uuid => Uuid::v4(),
            'v6' => static fn (): Uuid => Uuid::v6(),
            'v7' => static fn (): Uuid => Uuid::v7(),
            default => throw new InvalidArgumentException(sprintf('Unsupported UUID version: %s', $version)),
        };

        $result = [];

        try {
            for ($i = 0; $i < $count; ++$i) {
                $result[] = (string) $factory();
            }

            Log::info('[UuidService.generate] UUID сгенерированы', ['version' => $version, 'count' => $count]);
        } catch (Throwable $exception) {
            Log::error('[UuidService.generate] ошибка генерации UUID', [
                'version' => $version,
                'count' => $count,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return $result;
    }
}
