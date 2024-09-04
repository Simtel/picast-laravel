<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DomainCollectionResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @php-stan-ignore-next-line
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data1' => $this->collection,
        ];
    }
}
