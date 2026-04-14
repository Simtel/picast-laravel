<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Application\Dto;

final readonly class TournamentGroupDto
{
    /**
     * @param int $id
     * @param int $tournamentId
     * @param int $number
     * @param string $name
     * @param int $registrations
     */
    public function __construct(
        public int $id,
        public int $tournamentId,
        public int $number,
        public string $name,
        public int $registrations,
    ) {
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public static function fromArray(array $attributes): self
    {
        return new self(
            id: $attributes['id'],
            tournamentId: $attributes['tournament_id'],
            number: $attributes['number'],
            name: $attributes['name'],
            registrations: $attributes['registrations'],
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTournamentId(): int
    {
        return $this->tournamentId;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRegistrations(): int
    {
        return $this->registrations;
    }
}
