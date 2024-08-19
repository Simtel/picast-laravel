<?php

namespace App\Console\Commands;

use Alaouy\Youtube\Facades\Youtube;
use App\Models\YouTubeVideo;
use Exception;
use Illuminate\Console\Command;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

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
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $videos = YouTubeVideo::whereIsDownload(false)->get();
        foreach ($videos as $video) {
            if ($video->url === '') {
                continue;
            }
            $this->output->info('Обработка видео:' . $video->url);
            $videoInfo = Youtube::getVideoInfo(Youtube::parseVidFromURL($video->url));
            $video->title = $videoInfo->snippet->title;
            $this->output->info($video->title);
            $video->save();

            $yt = new YoutubeDl();

            $progressBar = $this->output->createProgressBar();
            $progressBar->setBarWidth(50);
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
            $progressBar->start(100); // 100 - это % завершения, от 0 до 100
            $yt->onProgress(
                function (
                    ?string $progressTarget,
                    string $percentage,
                    string $size,
                    ?string $speed,
                    ?string $eta,
                    ?string $totalTime
                ) use ($progressBar): void {
                    // Преобразование процента в числовое значение
                    $percentNumber = (int)rtrim($percentage, '%');

                    // Обновление прогресс-бара на основе процента
                    $progressBar->setProgress($percentNumber);
                }
            );

            $collection = $yt->download(
                Options::create()
                    ->downloadPath(public_path('videos'))
                    ->url($video->url)
                    ->format('mp4')
            );


            foreach ($collection->getVideos() as $element) {
                if ($element->getError() !== null) {
                    $this->output->error($element->getError());
                } else {
                    $this->output->writeln('');
                    $this->output->writeln('');
                }
            }
        }
        $this->output->success('Закончили скачивание');
    }
}
