<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\ProductsUrls
 *
 * @property int $id
 * @property int $product_id
 * @property int $marketplace_id
 * @property string $url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ProductsUrls newModelQuery()
 * @method static Builder|ProductsUrls newQuery()
 * @method static Builder|ProductsUrls query()
 * @method static Builder|ProductsUrls whereCreatedAt($value)
 * @method static Builder|ProductsUrls whereId($value)
 * @method static Builder|ProductsUrls whereMarketplaceId($value)
 * @method static Builder|ProductsUrls whereProductId($value)
 * @method static Builder|ProductsUrls whereUpdatedAt($value)
 * @method static Builder|ProductsUrls whereUrl($value)
 * @mixin Eloquent
 */
class ProductsUrls extends Model
{
    use HasFactory;
}
