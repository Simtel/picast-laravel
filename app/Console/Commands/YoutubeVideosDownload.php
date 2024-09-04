<?php

namespace App\Console\Commands;

use Alaouy\Youtube\Facades\Youtube;
use App\Models\Youtube\YouTubeVideo;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Storage;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

class YoutubeVideosDownload extends Command
{
    /**
     * @var string
     */
    protected $signature = 'youtube:download';

    /**
     * @var string
     */
    protected $description = 'Download all videos';


    /**
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
            $this->output->info($video->title);

            $yt = new YoutubeDl();

            $progressBar = $this->output->createProgressBar();
            $progressBar->setBarWidth(50);
            $progressBar->setFormat(
                ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%  %size% %speed%'
            );
            $progressBar->start(100);
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
                    ->format('299')
            );

            foreach ($collection->getVideos() as $element) {
                if ($element->getError() !== null) {
                    $this->output->error($element->getError());
                } else {
                    $fileName = $videoId . '.' . $element->getExt();
                    $filePath = 'public/videos' . '/' . $videoId . '.' . $element->getExt();
                    $this->output->info($filePath);
                    $this->copyFileToS3(
                        $filePath,
                        'videos/' . $fileName,
                        $this->output
                    );
                    $video->is_download = true;
                    $video->file_link = $fileName;
                    $video->size = (string)Storage::disk('local')->size($filePath);
                    $video->save();
                    Storage::disk('local')->delete($filePath);
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
            $content = Storage::disk('local')->readStream($localFilePath);
            if ($content !== null) {
                if (Storage::disk('s3')->exists($s3FilePath)) {
                    return;
                }
                Storage::disk('s3')->put($s3FilePath, $content);

                return;
            }
        }

        $output->error('Файл не найден!');
    }
}
