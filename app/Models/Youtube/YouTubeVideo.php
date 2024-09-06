<?php

namespace App\Models\Youtube;

use App\Exceptions\NotFoundS3VideoFileException;
use App\Helpers\Files;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Class YouTubeVideoController
 *
 * @property int $user_id
 * @property string $url
 * @property bool $is_download
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo query()
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereIsDownload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereUserId($value)
 * @property string $title
 * @property string $thumb
 * @property string $file_link
 * @property string $size
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereFileLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereTitle($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Youtube\VideoFormats> $formats
 * @property-read int|null $formats_count
 * @mixin \Eloquent
 */
class YouTubeVideo extends Model
{
    protected $table = 'youtube_videos';
    /**
     * @var array<string, mixed>
     */
    protected $attributes = ['is_download' => false, 'title' => '', 'thumb' => '', 'file_link' => '', 'size' => ''];
    protected $fillable = ['user_id', 'url', 'is_download', 'created_at', 'updated_at'];

    public function getFileUrl(): string
    {
        if ($this->file_link === '') {
            return '';
        }
        $host = is_string(env('SELECTEL_PUBLIC'))
            ? rtrim((string)env('SELECTEL_PUBLIC'), '/')
            : config('app.url');
        return $host . '/videos/' . $this->file_link;
    }

    public function getSize(): string
    {
        if ($this->size === '') {
            return '';
        }
        return Files::bytesToHuman((int)$this->size);
    }

    /**
     * @return HasMany<VideoFormats>
     */
    public function formats(): HasMany
    {
        return $this->hasMany(VideoFormats::class, 'video_id');
    }

    /**
     * @throws NotFoundS3VideoFileException
     */
    public function deleteFile(): void
    {
        $filePath = 'videos/' . $this->file_link;
        if (!Storage::disk('s3')->exists($filePath)) {
            throw new NotFoundS3VideoFileException('File not found on S3: ' . $filePath);
        }
        Storage::disk('s3')->delete($filePath);
    }
}
