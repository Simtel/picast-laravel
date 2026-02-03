<?php

declare(strict_types=1);

namespace App\Context\Webcams\Infrastructure\Resources;

use App\Context\Webcams\Application\Dto\WebcamDto;
use Illuminate\Http\Resources\Json\JsonResource;

class WebcamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        if ($this->resource instanceof WebcamDto) {
            return $this->resource->toArray();
        }

        /** @var \App\Context\Webcams\Domain\Model\Webcam $webcam */
        $webcam = $this->resource;

        return [
            'id' => $webcam->getId(),
            'name' => $webcam->getName(),
            'location' => $webcam->getLocation(),
            'stream_url' => $webcam->getStreamUrl(),
            'preview_url' => $webcam->getPreviewUrl(),
            'description' => $webcam->getDescription(),
            'is_active' => $webcam->isActive(),
            'created_at' => $webcam->getCreatedAt()?->toISOString(),
            'updated_at' => $webcam->getUpdatedAt()?->toISOString(),
        ];
    }
}
