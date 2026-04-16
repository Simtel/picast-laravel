<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Handlers;

use App\Common\CommandHandlerInterface;
use App\Common\CommandInterface;
use App\Context\Domains\Domain\Command\ListDomainsQuery;
use App\Context\Domains\Domain\Model\Domain;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListDomainsQueryHandler implements CommandHandlerInterface
{
    /**
     * @param ListDomainsQuery $query
     * @return LengthAwarePaginator<int, Domain>
     */
    public function handle(CommandInterface $query): LengthAwarePaginator
    {
        $domainsQuery = Domain::whereUserId($query->getUser()->getId());

        // Поиск по имени домена
        if ($query->getSearch() !== null && $query->getSearch() !== '') {
            $domainsQuery->where('name', 'like', '%' . $query->getSearch() . '%');
        }

        // Сортировка
        $sortBy = $query->getSortBy();
        $allowedSortColumns = ['name', 'created_at', 'updated_at', 'expire_at'];

        if (!in_array($sortBy, $allowedSortColumns, true)) {
            $sortBy = 'name';
        }

        $sortDirection = strtolower($query->getSortDirection()) === 'desc' ? 'desc' : 'asc';
        $domainsQuery->orderBy($sortBy, $sortDirection);

        return $domainsQuery->paginate($query->getPerPage());
    }
}
