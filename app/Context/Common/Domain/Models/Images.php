<?php

declare(strict_types=1);

namespace App\Context\Common\Domain\Models;

use App\Context\User\Domain\Model\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $filename
 * @property string $thumb
 * @property int $width
 * @property int $check
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $directory
 * @property string $disk
 * @property-read User $user
 * @method static Builder<static>|Images newModelQuery()
 * @method static Builder<static>|Images newQuery()
 * @method static Builder<static>|Images query()
 * @method static Builder<static>|Images whereCheck($value)
 * @method static Builder<static>|Images whereCreatedAt($value)
 * @method static Builder<static>|Images whereDirectory($value)
 * @method static Builder<static>|Images whereDisk($value)
 * @method static Builder<static>|Images whereFilename($value)
 * @method static Builder<static>|Images whereId($value)
 * @method static Builder<static>|Images whereThumb($value)
 * @method static Builder<static>|Images whereUpdatedAt($value)
 * @method static Builder<static>|Images whereUserId($value)
 * @method static Builder<static>|Images whereWidth($value)
 * @mixin Eloquent
 */
final class Images extends Model
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
            $host = is_string(config('SELECTEL_PUBLIC'))
                ? rtrim((string)config('SELECTEL_PUBLIC'), '/')
                : strval(config('app.url'));
            return $host . '/' . $this->directory . '/' . $this->filename;
        }
        return public_path('images') . '/' . $this->filename;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
