<?php

namespace App\Jobs;

use App\Models\Youtube\VideoFormats;
use App\Models\Youtube\Video;
use App\Services\Youtube\GetVideoFormatsService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Log;

class UpdateVideoFormats implements ShouldQueue
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
    public function handle(GetVideoFormatsService $getVideoFormatsService): void
    {
        Log::info('Start download video formats:' . $this->video->title);

        $formats = $getVideoFormatsService->getVideoFormats($this->video);
        foreach ($formats as $formatDto) {
            $format = new VideoFormats();
            $format->video_id = $this->video->id;

            $format->format_id = $formatDto->getFormatId();
            $format->format_note = $formatDto->getFormatNote();
            $format->format_ext = $formatDto->getVideoExt();
            $format->vcodec = $formatDto->getVCodec();
            $format->resolution = $formatDto->getResolution();
            $format->save();
        }
    }
}
