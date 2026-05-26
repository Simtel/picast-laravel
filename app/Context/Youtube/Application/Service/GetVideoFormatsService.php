<?php

declare(strict_types=1);

namespace App\Context\Youtube\Application\Service;

use App\Context\Youtube\Domain\Data\FormatVideoData;
use App\Context\Youtube\Domain\Model\Video;
use Exception;
use Illuminate\Support\Facades\Process;
use JsonException;
use RuntimeException;

class GetVideoFormatsService
{
    /**
     * @return FormatVideoData[]
     * @throws Exception
     */
    public function getVideoFormats(Video $video): array
    {
        $videoUrl = $this->buildVideoUrl($video->getVideoId());
        $jsonResult = $this->executeCommand($videoUrl);
        $videoInfo = $this->decodeJson($jsonResult);

        if (!isset($videoInfo['formats']) || !is_array($videoInfo['formats'])) {
            throw new RuntimeException('Не удалось получить информацию о видео');
        }

        return $this->extractFormats($videoInfo['formats']);
    }


    private function buildVideoUrl(string $videoId): string
    {
        return "https://www.youtube.com/watch?v=" . $videoId;
    }

    /**
     * @throws RuntimeException
     */
    private function executeCommand(string $videoUrl): string
    {
        $command = "youtube-dl --dump-json " . escapeshellarg($videoUrl);
        return Process::run($command)->output();
    }

    /**
     * @return mixed[]
     * @throws JsonException
     */
    private function decodeJson(string $jsonResult): array
    {
        try {
            $jsonResult = json_decode($jsonResult, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new JsonException('Не удалось декодировать JSON:' . $jsonResult);
        }
        return !is_array($jsonResult) ? throw new JsonException('Не удалось декодировать JSON') : $jsonResult;
    }

    /**
     * @param array<int, array<string, mixed>> $formats
     * @return FormatVideoData[]
     */
    private function extractFormats(array $formats): array
    {
        $formatsData = [];
        foreach ($formats as $format) {
            if (isset($format['height']) && $format['height'] >= 720) {
                $formatsData[] = FormatVideoData::from([
                    'formatId' => intval($format['format_id']),
                    'formatNote' => $format['format_note'] ?? '',
                    'videoExt' => $format['video_ext'] ?? '',
                    'vCodec' => $format['vcodec'] ?? '',
                    'resolution' => $format['resolution'] ?? '',
                ]);
            }
        }

        return $formatsData;
    }
}
