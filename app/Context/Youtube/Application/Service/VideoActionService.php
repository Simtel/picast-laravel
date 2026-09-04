<?php

declare(strict_types=1);

namespace App\Context\Youtube\Application\Service;

use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Model\VideoDownloadQueue;
use App\Context\Youtube\Domain\Model\VideoFormats;
use App\Context\Youtube\Infrastructure\Repository\YouTubeVideoStatusRepository;
use Illuminate\Support\Facades\Log;

final class VideoActionService
{
    public function __construct(
        private readonly YouTubeVideoStatusRepository $statusRepository,
    ) {
    }

    public function create(int $userId, string $url): Video
    {
        $video = Video::create(
            [
                'url' => $url,
                'user_id' => $userId,
                'status_id' => $this->statusRepository->findByCode('new')->id,
            ]
        );

        Log::info('[VideoActionService.create] видео добавлено', [
            'url' => $url,
            'user' => $userId,
        ]);

        return $video;
    }

    public function queueDownload(Video $video, int $formatId): void
    {
        $format = VideoFormats::where([
            'id' => $formatId,
            'video_id' => $video->getId(),
        ])->firstOrFail();

        VideoDownloadQueue::create([
            'video_id' => $video->getId(),
            'format_id' => $format->getId(),
        ]);

        Log::info('[VideoActionService.queueDownload] в очередь', [
            'video' => $video->getId(),
            'format' => $format->getId(),
        ]);
    }
}
