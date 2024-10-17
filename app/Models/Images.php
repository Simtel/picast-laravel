<?php

declare(strict_types=1);

namespace App\Models;

use App\Context\User\Domain\Model\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\ImagesController
 *
 * @property int $id
 * @property string $filename
 * @property string $thumb
 * @property int $width
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Images newModelQuery()
 * @method static Builder|Images newQuery()
 * @method static Builder|Images query()
 * @method static Builder|Images whereCreatedAt($value)
 * @method static Builder|Images whereFilename($value)
 * @method static Builder|Images whereId($value)
 * @method static Builder|Images whereThumb($value)
 * @method static Builder|Images whereUpdatedAt($value)
 * @method static Builder|Images whereWidth($value)
 * @property int $check
 * @method static Builder|Images whereCheck($value)
 * @property int $user_id
 * @property-read \App\Context\User\Domain\Model\User $user
 * @method static Builder|Images whereUserId($value)
 * @property string $directory
 * @property string $disk
 * @method static Builder|Images whereDirectory($value)
 * @method static Builder|Images whereDisk($value)
 * @mixin Eloquent
 */
class Images extends Model
{
    protected $fillable = ['filename', 'user_id', 'thumb', 'width', 'check', 'directory', 'disk'];

    /**
     * Подрубаем пользователя
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPath(): string
    {
        if ($this->disk === 's3') {
            $host = is_string(env('SELECTEL_PUBLIC'))
                ? rtrim((string)env('SELECTEL_PUBLIC'), '/')
                : config('app.url');
            return $host . '/' . $this->directory . '/' . $this->filename;
        }
        return public_path('images') . '/' . $this->filename;
    }
}
