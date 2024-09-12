<?php

namespace App\Models\Youtube;

use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $title
 * @property string $code
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideoStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideoStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideoStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideoStatus whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideoStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideoStatus whereTitle($value)
 * @mixin \Eloquent
 */
class YouTubeVideoStatus extends Model
{
    protected $table = 'youtube_video_statuses';


}
