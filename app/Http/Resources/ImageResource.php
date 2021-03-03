<?php

namespace App\Http\Resources;

use App\Models\Images;
use Illuminate\Http\Resources\Json\JsonResource;


class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        /**
         * @var Images $this
         */
        return [
            'id' => $this->id,
            'link' => route('show_image', ['id' => $this->id]),
            'image_src' => $this->getFullPath(),
            'thumb_src' => $this->getThumbFullPath(),
            'created_at' => $this->created_at->format('d.m.Y H:i:s'),
        ];
    }
}
