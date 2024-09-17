<?php

namespace App\Models\Youtube;

use Alaouy\Youtube\Facades\Youtube;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class YouTubeVideoController
 *
 * @property int $user_id
 * @property string $url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder|Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Video newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Video query()
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereIsDownload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereUserId($value)
 * @property string $title
 * @property string $thumb
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereFileLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereTitle($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Youtube\VideoFormats> $formats
 * @property-read int|null $formats_count
 * @property int|null $status_id
 * @property-read \App\Models\Youtube\VideoStatus|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereStatusId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Youtube\VideoFile> $files
 * @property-read int|null $files_count
 * @mixin \Eloquent
 */
class Video extends Model
{
    protected $table = 'youtube_videos';
    /**
     * @var array<string, mixed>
     */
    protected $attributes = ['title' => '', 'thumb' => ''];
    protected $fillable = ['user_id', 'url', 'created_at', 'updated_at', 'status_id'];


    /**
     * @return HasMany<VideoFormats>
     */
    public function formats(): HasMany
    {
        return $this->hasMany(VideoFormats::class, 'video_id');
    }


    /**
     * @throws Exception
     */
    public function getVideoId(): string
    {
        return Youtube::parseVidFromURL($this->url);
    }

    /**
     * @return BelongsTo<VideoStatus,Video >
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(VideoStatus::class, 'status_id');
    }

    /**
     * @return HasMany<VideoFile>
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
}
