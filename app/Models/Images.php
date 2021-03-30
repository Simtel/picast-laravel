<?php

namespace App\Models;

use App\Models\Traits\ImagesModelTraits;

use Illuminate\Database\Eloquent\Model;


/**
 * App\ImagesController
 *
 * @property int $id
 * @property string $filename
 * @property string $thumb
 * @property int $width
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Images newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Images newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Images query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Images whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Images whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Images whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Images whereThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Images whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Images whereWidth($value)
 * @mixin \Eloquent
 * @property int $check
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Images whereCheck($value)
 * @property int $user_id
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Images whereUserId($value)
 */
class Images extends Model
{
    use ImagesModelTraits;
    
    /**
     * Подрубаем пользователя
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
