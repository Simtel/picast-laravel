<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Model;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $video_id
 * @property int $format_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Context\Youtube\Domain\Model\VideoFormats $format
 * @property-read \App\Context\Youtube\Domain\Model\Video $video
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoDownloadQueue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoDownloadQueue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoDownloadQueue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoDownloadQueue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoDownloadQueue whereFormatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoDownloadQueue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoDownloadQueue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoDownloadQueue whereVideoId($value)
 * @mixin Eloquent
 */
class VideoDownloadQueue extends Model
{
    protected $fillable = ['video_id', 'format_id'];

    protected $table = 'youtube_video_download_queue';

    public function getId(): int
    {
        return $this->id;
    }

    public function getVideoId(): int
    {
        return $this->video_id;
    }

    public function getFormatId(): int
    {
        return $this->format_id;
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
     * @return BelongsTo<Video,$this>
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * @return BelongsTo<VideoFormats,$this>
     */
    public function format(): BelongsTo
    {
        return $this->belongsTo(VideoFormats::class);
    }
}
