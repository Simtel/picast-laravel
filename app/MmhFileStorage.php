<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MmhFileStorage
 * Старая таблица с изображениями
 *
 * @property int $file_id
 * @property string $filename
 * @property int $is_private
 * @property int $gallery_id
 * @property int $album_id
 * @property string $file_title
 * @property int $viewer_clicks
 * @property int $cloud
 * @property string $thumb
 * @property int $width
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MmhFileStorage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MmhFileStorage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MmhFileStorage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MmhFileStorage whereAlbumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MmhFileStorage whereCloud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MmhFileStorage whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MmhFileStorage whereFileTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MmhFileStorage whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MmhFileStorage whereGalleryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MmhFileStorage whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MmhFileStorage whereThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MmhFileStorage whereViewerClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MmhFileStorage whereWidth($value)
 * @mixin \Eloquent
 */
class MmhFileStorage extends Model
{
    protected $primaryKey = 'file_id';
    public $timestamps = false;
    protected $table = 'mmh_file_storage';
}
