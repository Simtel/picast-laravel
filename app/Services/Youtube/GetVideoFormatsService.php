<?php

namespace App\Services\Youtube;

use App\Dto\Youtube\FormatVideoDto;
use Exception;
use RuntimeException;

class GetVideoFormatsService
{
    /**
     * @param string $videoId
     * @return FormatVideoDto[]
     * @throws Exception
     */
    public function getVideoFormats(string $videoId): array
    {
        $formats = [];

        $video_url = "https://www.youtube.com/watch?v=" . $videoId;

        $command = "youtube-dl --dump-json " . escapeshellarg($video_url);

        $json_result = shell_exec($command);

        $video_info = json_decode((string)$json_result, true, 512, JSON_THROW_ON_ERROR);

        if (is_array($video_info) && isset($video_info['formats']) && is_array($video_info['formats'])) {
            foreach ($video_info['formats'] as $format) {
                if (array_key_exists('height', $format) && $format['height'] >= 720) {
                    $formats[] = new FormatVideoDto(
                        $format['format_id'],
                        $format['format_note'] ?? '',
                        $format['video_ext'] ?? '',
                        $format['vcodec'] ?? '',
                        $format['resolution'] ?? '',
                    );
                }
            }
        } else {
            throw new RuntimeException('Не удалось получить информацию о видео');
        }
        return $formats;
    }
}
