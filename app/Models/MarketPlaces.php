<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\MarketPlaces
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|MarketPlaces newModelQuery()
 * @method static Builder|MarketPlaces newQuery()
 * @method static Builder|MarketPlaces query()
 * @method static Builder|MarketPlaces whereCreatedAt($value)
 * @method static Builder|MarketPlaces whereId($value)
 * @method static Builder|MarketPlaces whereName($value)
 * @method static Builder|MarketPlaces whereUpdatedAt($value)
 * @method static Builder|MarketPlaces whereUrl($value)
 * @mixin Eloquent
 */
class MarketPlaces extends Model
{
    protected $fillable = ['name', 'url'];
}
