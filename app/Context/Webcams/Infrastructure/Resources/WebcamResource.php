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
     * @return array
     */
    public function toArray($request): array
    {
        if ($this->resource instanceof WebcamDto) {
            return $this->resource->toArray();
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'stream_url' => $this->stream_url,
            'preview_url' => $this->preview_url,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
