<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Resource;

use App\Context\Youtube\Domain\Model\Video;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'VideoFullResource',
    title: 'Video Full Resource',
    description: 'Полный ресурс видео',
    required: ['id', 'url'],
    properties: [
        new OA\Property(property: 'id', description: 'ID видео', type: 'integer'),
        new OA\Property(property: 'title', description: 'Заголовок видео', type: 'string'),
        new OA\Property(property: 'url', description: 'URL видео', type: 'string'),
        new OA\Property(property: 'createdAt', description: 'Дата создания', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updatedAt', description: 'Дата обновления', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
final class VideoFullResource extends JsonResource
{
    /**
     * @param $request
     * @return array{id:int, url:string}
     */
    public function toArray($request): array
    {
        /** @var Video $video */
        $video = $this->resource;
        return [
            'id' => $video->getId(),
            'title' => $video->getTitle(),
            'url' => $video->getUrl(),
            'createdAt' => $video->getCreatedAt()?->toDateTimeString(),
            'updatedAt' => $video->getUpdatedAt()?->toDateTimeString(),
        ];
    }
}
