<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Application\Dto;

use Illuminate\Support\Carbon;

final readonly class TournamentDetailDto
{
    /**
     * @param int $id
     * @param string $title
     * @param string $link
     * @param Carbon|null $date
     * @param Carbon|null $dateEnd
     * @param string|null $city
     * @param string|null $organizer
     * @param string $guid
     * @param TournamentGroupDto[] $groups
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $link,
        public ?Carbon $date,
        public ?Carbon $dateEnd,
        public ?string $city,
        public ?string $organizer,
        public string $guid,
        public array $groups,
    ) {
    }

    /**
     * @param array<string, mixed> $attributes
     * @param TournamentGroupDto[] $groups
     */
    public static function fromArray(array $attributes, array $groups = []): self
    {
        return new self(
            id: $attributes['id'],
            title: $attributes['title'],
            link: $attributes['link'],
            date: $attributes['date'] ? Carbon::parse($attributes['date']) : null,
            dateEnd: $attributes['date_end'] ? Carbon::parse($attributes['date_end']) : null,
            city: $attributes['city'] ?? null,
            organizer: $attributes['organizer'] ?? null,
            guid: $attributes['guid'],
            groups: $groups,
        );
    }
}
