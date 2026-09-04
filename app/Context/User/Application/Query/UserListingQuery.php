<?php

declare(strict_types=1);

namespace App\Context\User\Application\Query;

use App\Context\User\Domain\Model\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

final class UserListingQuery
{
    private const int PER_PAGE = 15;

    /**
     * @param array<string, mixed> $params
     * @return LengthAwarePaginator<int, User>
     */
    public function handle(array $params): LengthAwarePaginator
    {
        $search = isset($params['search']) && is_string($params['search']) ? $params['search'] : '';
        $sortColumn = isset($params['sort']) && is_string($params['sort']) ? $params['sort'] : 'created_at';
        $sortDirection = isset($params['direction']) && is_string($params['direction']) ? $params['direction'] : 'desc';

        $query = User::query();

        if ($search !== '') {
            $query->where(static function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allowedColumns = ['name', 'email', 'created_at', 'birth_date'];
        if (in_array($sortColumn, $allowedColumns, true)) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $users = $query->paginate(self::PER_PAGE);

        Log::debug('[UserListingQuery] users', [
            'search' => $search,
            'sort' => $sortColumn,
            'direction' => $sortDirection,
            'count' => $users->total(),
        ]);

        return $users;
    }
}
