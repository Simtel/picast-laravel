<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Resource;

use App\Context\Youtube\Domain\Model\Video;
use Illuminate\Http\Resources\Json\JsonResource;

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
