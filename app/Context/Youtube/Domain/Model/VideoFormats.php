<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Model;

use App\Context\Youtube\Domain\Factory\VideoFormatFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\VideoFormats
 *
 * @method static \Illuminate\Database\Eloquent\Builder|VideoFormats newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoFormats newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoFormats query()
 * @property int $id
 * @property int $video_id
 * @property int $format_id
 * @property string|null $format_note
 * @property string|null $format_ext
 * @property string|null $vcodec
 * @property string|null $resolution
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|VideoFormats whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoFormats whereFormatExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoFormats whereFormatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoFormats whereFormatNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoFormats whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoFormats whereResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoFormats whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoFormats whereVcodec($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoFormats whereVideoId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, VideoFile> $files
 * @property-read int|null $files_count
 * @mixin Eloquent
 */
class VideoFormats extends Model
{
    /** @use HasFactory<VideoFormatFactory> */
    use HasFactory;

    protected $table = 'youtube_videos_formats';

    protected static string $factory = VideoFormatFactory::class;

    public function delete(): ?bool
    {
        foreach ($this->files as $file) {
            $file->deleteFile();
        }

        return parent::delete();
    }

    /**
     * @return HasMany<VideoFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(VideoFile::class, 'format_id');
    }

    public function getId(): int
    {
        return $this->id;
    }
}
