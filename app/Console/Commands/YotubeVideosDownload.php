<?php

namespace App\Console\Commands;

use Alaouy\Youtube\Facades\Youtube;
use App\Contracts\Services\Domains\WhoisUpdater;
use App\Models\Domain;
use App\Models\YouTubeVideo;
use Illuminate\Console\Command;

class YotubeVideosDownload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download all videos';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $videos = YouTubeVideo::whereIsDownload(false)->get();
        foreach ($videos as $video) {
            $this->output->info('Обработка видео:' . $video->url);
            $videoInfo = Youtube::getVideoInfo(Youtube::parseVidFromURL($video->url));
            $this->output->info($videoInfo->title);

        }
        $this->output->success('Закончили скачивание');
    }
}
