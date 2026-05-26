<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Application\Data;

use Spatie\LaravelData\Data;

class ChadGptRequestData extends Data
{
    public function __construct(
        public readonly string $model,
        public readonly string $userMessage,
    ) {
    }
}
