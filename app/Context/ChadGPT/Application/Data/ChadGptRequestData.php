<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Application\Data;

use Spatie\LaravelData\Data;

final class ChadGptRequestData extends Data
{
    /**
     * @param array<int, array{role: string, content: string}>|null $history
     * @param string[]|null $images
     */
    public function __construct(
        public readonly string $model,
        public readonly string $userMessage,
        public readonly ?float $temperature = null,
        public readonly ?int $maxTokens = null,
        public readonly ?array $history = null,
        public readonly ?array $images = null,
    ) {
    }
}
