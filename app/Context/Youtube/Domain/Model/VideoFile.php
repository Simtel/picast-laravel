<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Model;

use App\Context\Youtube\Domain\Factory\VideoFileFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Log;

/**
 * @property int $id
 * @property int $video_id
 * @property string $file_link
 * @property string $size
 * @property int $format_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Context\Youtube\Domain\Model\Video $video
 * @method static \App\Context\Youtube\Domain\Factory\VideoFileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFile whereFileLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFile whereFormatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFile whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFile whereVideoId($value)
 * @mixin Eloquent
 */
final class VideoFile extends Model
{
    /** @use HasFactory<VideoFileFactory> */
    use HasFactory;

    protected $table = 'youtube_video_files';

    protected static string $factory = VideoFileFactory::class;


    public function getFileUrl(): string
    {
        if ($this->file_link === '') {
            return '';
        }
        $host = is_string(config('SELECTEL_PUBLIC'))
            ? rtrim((string)config('SELECTEL_PUBLIC'), '/')
            : strval(config('app.url'));
        return $host . '/videos/' . $this->file_link;
    }

    public function getSize(): string
    {
        if ($this->size === '') {
            return '';
        }
        return Number::fileSize((int)$this->size, 2);
    }

    public function deleteFile(): void
    {
        $filePath = 'videos/' . $this->file_link;
        if (!Storage::disk('s3')->exists($filePath)) {
            Log::debug('File not found on S3: ' . $filePath);
            return;
        }
        Storage::disk('s3')->delete($filePath);
    }


    /**
     * @return BelongsTo<Video,$this>
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }
}
