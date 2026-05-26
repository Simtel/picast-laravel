<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Application\Data;

use App\Context\Tournaments\Domain\Model\TournamentGroup;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class TournamentDetailData extends Data
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
        /** @var DataCollection<int, TournamentGroup> */
        public readonly DataCollection $groups,
    ) {
    }

    /**
     * @param array<string, mixed> $attributes
     * @param array<int, array<string, mixed>> $groups
     */
    public static function fromArray(array $attributes, array $groups = []): self
    {
        $groupsArray = [];
        foreach ($groups as $groupData) {
            $groupsArray[] = TournamentGroupData::from($groupData);
        }

        return new self(
            id: $attributes['id'],
            title: $attributes['title'],
            link: $attributes['link'],
            date: isset($attributes['date']) ? Carbon::parse($attributes['date']) : null,
            dateEnd: isset($attributes['date_end']) ? Carbon::parse($attributes['date_end']) : null,
            city: $attributes['city'] ?? null,
            organizer: $attributes['organizer'] ?? null,
            guid: $attributes['guid'],
            groups: new DataCollection(TournamentGroupData::class, $groupsArray),
        );
    }
}
