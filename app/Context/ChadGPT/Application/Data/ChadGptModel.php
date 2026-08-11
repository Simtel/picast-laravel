<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Application\Data;

use Spatie\LaravelData\Data;

final class ChadGptModel extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $label,
        public readonly bool $isDefault = false,
    ) {
    }
}
