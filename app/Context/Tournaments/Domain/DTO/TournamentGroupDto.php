<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Domain\DTO;

readonly class TournamentGroupDto
{
    public function __construct(
        private int $number,
        private string $name,
    ) {
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getName(): string
    {
        return $this->name;
    }


}
