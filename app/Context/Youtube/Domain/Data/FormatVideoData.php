<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Data;

use Spatie\LaravelData\Data;

class FormatVideoData extends Data
{
    public function __construct(
        public readonly int $formatId,
        public readonly string $formatNote,
        public readonly string $videoExt,
        public readonly string $vCodec,
        public readonly string $resolution,
    ) {
    }
}
