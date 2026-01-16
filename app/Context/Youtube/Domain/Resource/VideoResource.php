<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Resource;

use App\Context\Youtube\Domain\Model\Video;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'VideoResource',
    title: 'Video Resource',
    description: 'Ресурс видео',
    required: ['id', 'url'],
    properties: [
        new OA\Property(property: 'id', description: 'ID видео', type: 'integer'),
        new OA\Property(property: 'url', description: 'URL видео', type: 'string'),
    ],
    type: 'object'
)]
final class VideoResource extends JsonResource
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
            'url' => $video->getUrl(),
        ];
    }
}
