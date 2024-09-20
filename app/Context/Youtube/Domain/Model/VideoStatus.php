<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Model;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $title
 * @property string $code
 * @method static \Illuminate\Database\Eloquent\Builder|VideoStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoStatus whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoStatus whereTitle($value)
 * @mixin Eloquent
 */
class VideoStatus extends Model
{
    protected $table = 'youtube_video_statuses';



}
