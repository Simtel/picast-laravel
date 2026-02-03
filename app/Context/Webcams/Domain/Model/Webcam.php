<?php

declare(strict_types=1);

namespace App\Context\Webcams\Domain\Model;

use App\Context\Webcams\Infrastructure\Factory\WebcamFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $location
 * @property string $stream_url
 * @property string $preview_url
 * @property string $description
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \App\Context\Webcams\Infrastructure\Factory\WebcamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static> whereActive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webcam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webcam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webcam query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webcam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webcam whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webcam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webcam whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webcam whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webcam whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webcam wherePreviewUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webcam whereStreamUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Webcam whereUpdatedAt($value)
 * @mixin Eloquent
 */
final class Webcam extends Model
{
    /** @use HasFactory<WebcamFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'stream_url',
        'preview_url',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static string $factory = WebcamFactory::class;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getStreamUrl(): string
    {
        return $this->stream_url;
    }

    public function getPreviewUrl(): string
    {
        return $this->preview_url;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updated_at;
    }

    /**
     * @param Builder<Webcam> $builder
     * @return Builder<Webcam>
     */
    public function scopeWhereActive(Builder $builder): Builder
    {
        return $builder->where('is_active', true);
    }
}
