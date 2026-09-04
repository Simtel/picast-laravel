<?php

declare(strict_types=1);

namespace App\Context\User\Application\Query;

use App\Context\Common\Domain\Models\Images;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

final class ImageListingQuery
{
    /**
     * @param array<string, mixed> $params
     * @return LengthAwarePaginator<int, Images>
     */
    public function handle(int $userId, array $params): LengthAwarePaginator
    {
        $filter = isset($params['filter']) && is_string($params['filter']) ? $params['filter'] : null;
        $search = isset($params['search']) && is_string($params['search']) ? $params['search'] : '';

        $imagesQuery = Images::whereUserId($userId)
            ->orderBy('created_at', 'desc');

        if ($filter === 'recent') {
            $imagesQuery->where('created_at', '>=', now()->subWeek());
        } elseif ($filter === 'large') {
            $imagesQuery->where('id', '>', 100);
        }

        if ($search !== '') {
            $imagesQuery->where('filename', 'like', "%{$search}%");
        }

        $images = $imagesQuery->paginate(20)->withQueryString();

        Log::debug('[ImageListingQuery] images', [
            'user' => $userId,
            'filter' => $filter,
            'search' => $search,
            'count' => $images->total(),
        ]);

        return $images;
    }
}
