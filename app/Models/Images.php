<?php

namespace App\Models;

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
 * @method static Builder|\App\Images newModelQuery()
 * @method static Builder|\App\Images newQuery()
 * @method static Builder|\App\Images query()
 * @method static Builder|\App\Images whereCreatedAt($value)
 * @method static Builder|\App\Images whereFilename($value)
 * @method static Builder|\App\Images whereId($value)
 * @method static Builder|\App\Images whereThumb($value)
 * @method static Builder|\App\Images whereUpdatedAt($value)
 * @method static Builder|\App\Images whereWidth($value)
 * @mixin Eloquent
 * @property int $check
 * @method static Builder|\App\Images whereCheck($value)
 * @property int $user_id
 * @property-read \App\User $user
 * @method static Builder|\App\Images whereUserId($value)
 */
class Images extends Model
{
    protected $fillable = ['filename', 'user_id', 'thumb', 'width', 'check'];

    /**
     * Подрубаем пользователя
     *
     * @return BelongsTo<User, Images>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
