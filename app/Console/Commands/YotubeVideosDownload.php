<?php

namespace App\Console\Commands;

use Alaouy\Youtube\Facades\Youtube;
use App\Models\YouTubeVideo;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Storage;
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
            $videoId = Youtube::parseVidFromURL($video->url);
            $videoInfo = Youtube::getVideoInfo($videoId);
            $video->title = $videoInfo->snippet->title;
            $this->output->info($video->title);
            $video->save();

            $yt = new YoutubeDl();

            $progressBar = $this->output->createProgressBar();
            $progressBar->setBarWidth(50);
            $progressBar->setFormat(
                ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%  %size% %speed%'
            );
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
                    $percentNumber = (int)rtrim($percentage, '%');
                    $progressBar->setProgress($percentNumber);
                    $progressBar->setPlaceholderFormatter('size', function ($value) use ($size) {
                        return $size;
                    });
                    $progressBar->setPlaceholderFormatter('speed', function ($value) use ($speed) {
                        return $speed ?? '';
                    });
                }
            );

            $collection = $yt->download(
                Options::create()
                    ->downloadPath(Storage::disk('local')->path('public/videos'))
                    ->output($videoId . '.%(ext)s')
                    ->url($video->url)
                //->format('bestvideo[height<=1080]+bestaudio/best[height<=1080]')
            );

            foreach ($collection->getVideos() as $element) {
                if ($element->getError() !== null) {
                    $this->output->error($element->getError());
                } else {
                    $filePath = 'public/videos' . '/' . $videoId . '.' . $element->getExt();
                    $this->output->info($filePath);
                    $this->copyFileToS3(
                        $filePath,
                        'videos/' . $element->getFilename(),
                        $this->output
                    );
                    $this->output->writeln('');
                    $this->output->writeln('');
                }
            }
        }
        $this->output->success('Закончили скачивание');
    }

    public function copyFileToS3(string $localFilePath, string $s3FilePath, OutputStyle $output): void
    {
        if (Storage::disk('local')->exists($localFilePath)) {
            $content = Storage::disk('local')->get($localFilePath);
            if ($content !== null) {
                Storage::disk('s3')->put($s3FilePath, $content);
                return;
            }
        }

        $output->error('Файл не найден!');
    }
}
