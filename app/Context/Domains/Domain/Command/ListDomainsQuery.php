<?php

declare(strict_types=1);

namespace App\Context\Domains\Domain\Command;

use App\Context\Common\Infrastructure\CommandInterface;
use App\Context\User\Domain\Model\User;

final class ListDomainsQuery implements CommandInterface
{
    public function __construct(
        private readonly User $user,
        private readonly ?string $search = null,
        private readonly string $sortBy = 'name',
        private readonly string $sortDirection = 'asc',
        private readonly int $perPage = 15,
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function getSortDirection(): string
    {
        return $this->sortDirection;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }
}
