<?php

declare(strict_types=1);

namespace App\Context\Domains\Domain\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * Domain resource for API documentation
 */
#[OA\Schema(
    schema: 'DomainResource',
    description: 'Domain resource with WHOIS info',
    properties: [
        new OA\Property(property: 'id', description: 'Domain ID', type: 'integer'),
        new OA\Property(property: 'name', description: 'Domain name', type: 'string', example: 'example.com'),
        new OA\Property(property: 'expire_at', description: 'Expiration date', type: 'string', format: 'date-time'),
        new OA\Property(property: 'whois', description: 'WHOIS information', type: 'array', items: new OA\Items(type: 'string'))
    ]
)]
/**
 * @property integer $id
 * @property string $name
 * @property string $expire_at
 */
final class DomainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'expire_at' => $this->resource->expire_at,
            'whois' => []
        ];
    }
}
