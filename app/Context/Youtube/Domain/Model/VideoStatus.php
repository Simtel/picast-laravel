<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Model;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $code
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoStatus whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VideoStatus whereTitle($value)
 * @mixin Eloquent
 */
class VideoStatus extends Model
{
    protected $table = 'youtube_video_statuses';

    protected $fillable = ['title','code'];

    public $timestamps = false;

}
