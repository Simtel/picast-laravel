<?php

namespace App\Context\Youtube\Application\Service;

use App\Context\Youtube\Domain\Dto\FormatVideoDto;
use App\Context\Youtube\Domain\Model\Video;
use Exception;
use JsonException;
use RuntimeException;

class GetVideoFormatsService
{
    /**
     * @return FormatVideoDto[]
     * @throws Exception
     */
    public function getVideoFormats(Video $video): array
    {
        $videoUrl = $this->buildVideoUrl($video->getVideoId());
        $jsonResult = $this->executeCommand($videoUrl);
        $videoInfo = $this->decodeJson($jsonResult);

        return $this->extractFormats($videoInfo);
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
        $output = shell_exec($command);

        if ($output === null || $output === false) {
            throw new RuntimeException('Ошибка при выполнении команды youtube-dl');
        }

        return $output;
    }

    /**
     * @return array<string, mixed>
     * @throws JsonException
     */
    private function decodeJson(string $jsonResult): array
    {
        $jsonResult = json_decode($jsonResult, true, 512, JSON_THROW_ON_ERROR);
        return !is_array($jsonResult) ? throw new JsonException('Не удалось декодировать JSON') : $jsonResult;
    }

    /**
     * @param array<string, mixed> $videoInfo
     * @return FormatVideoDto[]
     * @throws RuntimeException
     */
    private function extractFormats(array $videoInfo): array
    {
        if (!isset($videoInfo['formats']) || !is_array($videoInfo['formats'])) {
            throw new RuntimeException('Не удалось получить информацию о видео');
        }

        $formats = [];

        foreach ($videoInfo['formats'] as $format) {
            if (isset($format['height']) && $format['height'] >= 720) {
                $formats[] = new FormatVideoDto(
                    $format['format_id'],
                    $format['format_note'] ?? '',
                    $format['video_ext'] ?? '',
                    $format['vcodec'] ?? '',
                    $format['resolution'] ?? ''
                );
            }
        }

        return $formats;
    }
}
