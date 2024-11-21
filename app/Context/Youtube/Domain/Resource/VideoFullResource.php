<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Resource;

use App\Context\Youtube\Domain\Model\Video;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoFullResource extends JsonResource
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
