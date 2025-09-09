<?php

declare(strict_types=1);

namespace App\Context\Youtube\Infrastructure\Jobs;

use App\Context\Youtube\Application\Service\RefreshVideoFormatsService;
use App\Context\Youtube\Domain\Model\Video;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Log;

final class UpdateVideoFormats implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly Video $video,
    ) {
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(RefreshVideoFormatsService $refreshVideoFormatsService): void
    {
        Log::info('Start download video formats:' . $this->video->title);

        $refreshVideoFormatsService->refresh($this->video);
    }
}
