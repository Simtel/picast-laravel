<?php

declare(strict_types=1);

namespace App\Context\Webcams\Domain\Model;

use App\Context\Webcams\Infrastructure\Factory\WebcamFactory;
use Eloquent;
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
 * @method static \Illuminate\Database\Eloquent\Builder<static> newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static> newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static> query()
 * @method static \Illuminate\Database\Eloquent\Builder<static> whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static> whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static> whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static> whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static> whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static> whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static> wherePreviewUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static> whereStreamUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static> whereUpdatedAt($value)
 * @mixin Eloquent
 */
final class Webcam extends Model
{
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
     * Scope для получения только активных камер
     */
    public function scopeWhereActive($query)
    {
        return $query->where('is_active', true);
    }
}
