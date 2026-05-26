<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Application\Data;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class TournamentListData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $link,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public readonly ?Carbon $date,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public readonly ?Carbon $dateEnd,
        public readonly ?string $city,
        public readonly ?string $organizer,
        public readonly string $guid,
        public readonly int $groupsCount,
    ) {
    }
}
