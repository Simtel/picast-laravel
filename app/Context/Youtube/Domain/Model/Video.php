<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Model;

use Alaouy\Youtube\Facades\Youtube;
use App\Context\Youtube\Domain\Factory\VideoFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $url
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $title
 * @property string $thumb
 * @property int|null $status_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Context\Youtube\Domain\Model\VideoFile> $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Context\Youtube\Domain\Model\VideoFormats> $formats
 * @property-read int|null $formats_count
 * @property-read \App\Context\Youtube\Domain\Model\VideoStatus|null $status
 * @method static \App\Context\Youtube\Domain\Factory\VideoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereUserId($value)
 * @mixin \Eloquent
 */
final class Video extends Model
{
    /** @use HasFactory<VideoFactory> */
    use HasFactory;

    protected $table = 'youtube_videos';
    /**
     * @var array<string, mixed>
     */
    protected $attributes = ['title' => '', 'thumb' => ''];
    protected $fillable = ['user_id', 'url', 'created_at', 'updated_at', 'status_id'];

    protected static string $factory = VideoFactory::class;

    /**
     * @return HasMany<VideoFormats, $this>
     */
    public function formats(): HasMany
    {
        return $this->hasMany(VideoFormats::class, 'video_id');
    }


    /**
     * @return string
     * @throws Exception
     */
    public function getVideoId(): string
    {
        return Youtube::parseVidFromURL($this->url);
    }

    /**
     * @return BelongsTo<VideoStatus,$this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(VideoStatus::class, 'status_id');
    }

    /**
     * @return HasMany<VideoFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(VideoFile::class);
    }

    public function delete(): bool|null
    {
        foreach ($this->files as $file) {
            $file->deleteFile();
        }
        return parent::delete();
    }

    public function setDownloadedStatus(): void
    {
        $this->status_id = VideoStatus::whereCode('downloaded')->first()?->id;
        $this->save();
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function hasFormats(): bool
    {
        return !$this->formats->isEmpty();
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updated_at;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
