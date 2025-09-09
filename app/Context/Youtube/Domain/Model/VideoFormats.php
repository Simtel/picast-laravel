<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Model;

use App\Context\Youtube\Domain\Factory\VideoFormatFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $video_id
 * @property int $format_id
 * @property string|null $format_note
 * @property string|null $format_ext
 * @property string|null $vcodec
 * @property string|null $resolution
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Context\Youtube\Domain\Model\VideoFile> $files
 * @property-read int|null $files_count
 * @method static \App\Context\Youtube\Domain\Factory\VideoFormatFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFormats newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFormats newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFormats query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFormats whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFormats whereFormatExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFormats whereFormatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFormats whereFormatNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFormats whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFormats whereResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFormats whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFormats whereVcodec($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoFormats whereVideoId($value)
 * @mixin Eloquent
 */
final class VideoFormats extends Model
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
