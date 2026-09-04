<?php

declare(strict_types=1);

namespace App\Context\Youtube\Application\Query;

use App\Context\Youtube\Domain\Model\Video;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

final class VideoListingQuery
{
    /**
     * @param int $userId
     * @param bool $paginate
     * @return LengthAwarePaginator<int, Video>|Collection<int, Video>
     */
    public function listByUser(int $userId, bool $paginate = false): LengthAwarePaginator|Collection
    {
        $query = Video::whereUserId($userId);

        $videos = $paginate ? $query->paginate(15) : $query->get();

        Log::debug('[VideoListingQuery] videos', [
            'user' => $userId,
            'count' => $videos->count(),
        ]);

        return $videos;
    }
}
