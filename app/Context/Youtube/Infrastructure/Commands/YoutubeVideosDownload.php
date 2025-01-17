<?php

declare(strict_types=1);

namespace App\Context\Youtube\Infrastructure\Commands;

use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Model\VideoDownloadQueue;
use App\Context\Youtube\Domain\Model\VideoFile;
use App\Context\Youtube\Domain\Model\VideoFormats;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Helper\ProgressBar;
use YoutubeDl\Entity\VideoCollection;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

class YoutubeVideosDownload extends Command implements Isolatable
{
    /**
     * @var string
     */
    protected $signature = 'youtube:download';

    /**
     * @var string
     */
    protected $description = 'Download all videos';


    public function __construct(
        private readonly YoutubeDl $youtubeDl
    ) {
        parent::__construct();
    }


    /**
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $lockKey = 'youtube_download_lock';
        $lock = cache()->lock($lockKey, 600);
        if (!$lock->get()) {
            $this->error('Команда уже выполняется!');
            return;
        }
        $videosQueue = VideoDownloadQueue::all();
        if (count($videosQueue) > 0) {
            $this->output->writeln('В очереди на загрузку: ' . count($videosQueue));
        }

        foreach ($videosQueue as $queue) {
            $video = $queue->video;
            if ($video->getUrl() === '') {
                continue;
            }
            $format = $queue->format;

            $this->output->writeln('Обработка видео:' . $video->getUrl());

            $progressBar = $this->getProgressBar();

            $this->youtubeDl->onProgress(
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

            $collection = $this->youtubeDl->download(
                Options::create()
                    ->downloadPath(Storage::disk('local')->path('public/videos'))
                    ->output($video->getVideoId() . '.%(ext)s')
                    ->url($video->getUrl())
                    ->format((string)$format->format_id)
            );

            $this->getVideos($collection, $video, $format);
            $queue->delete();
        }
        $lock->release();
        $this->output->writeln('Закончили скачивание');
    }

    /**
     * @throws Exception
     */
    private function getVideos(
        VideoCollection $collection,
        Video $video,
        VideoFormats $format
    ): void {
        foreach ($collection->getVideos() as $element) {
            if ($element->getError() !== null) {
                $this->output->error($element->getError());
            } else {
                $fileName = $video->getVideoId() . '.' . $element->getExt();
                $filePath = 'public/videos' . '/' . $video->getVideoId() . '.' . $element->getExt();
                $this->output->writeln($filePath);
                $this->copyFileToS3(
                    $filePath,
                    'videos/' . $fileName,
                    $this->output
                );
                $videoFile = new VideoFile();
                $videoFile->format_id = $format->id;
                $videoFile->video_id = $video->id;
                $videoFile->file_link = $fileName;
                $videoFile->size = (string)Storage::disk('local')->size($filePath);
                $videoFile->save();

                $video->setDownloadedStatus();

                Storage::disk('local')->delete($filePath);

                $this->output->writeln('');
                $this->output->writeln('');
            }
        }
    }

    private function copyFileToS3(
        string $localFilePath,
        string $s3FilePath,
        OutputStyle $output
    ): void {
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

    private function getProgressBar(): ProgressBar
    {
        $progressBar = $this->output->createProgressBar();
        $progressBar->setBarWidth(50);
        $progressBar->setFormat(
            ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%  %size% %speed%'
        );
        $progressBar->start(100);
        return $progressBar;
    }
}
