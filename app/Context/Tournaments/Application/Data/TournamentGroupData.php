<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Application\Data;

use Spatie\LaravelData\Data;

class TournamentGroupData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $tournamentId,
        public readonly int $number,
        public readonly string $name,
        public readonly int $registrations,
    ) {
    }
}
