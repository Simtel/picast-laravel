<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class YouTubeVideoController
 *
 * @property int $user_id
 * @property string $url
 * @property bool $is_download
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo query()
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereIsDownload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YouTubeVideo whereUserId($value)
 * @mixin \Eloquent
 */
class YouTubeVideo extends Model
{
    use HasFactory;
}
