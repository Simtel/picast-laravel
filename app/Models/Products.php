<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Products
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Products newModelQuery()
 * @method static Builder|Products newQuery()
 * @method static Builder|Products query()
 * @method static Builder|Products whereCreatedAt($value)
 * @method static Builder|Products whereId($value)
 * @method static Builder|Products whereName($value)
 * @method static Builder|Products whereUpdatedAt($value)
 * @method static Builder|Products whereUserId($value)
 * @mixin Eloquent
 * @property-read Collection|ProductsUrls[] $urls
 * @property-read int|null $urls_count
 * @property-read User $user
 */
class Products extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];


    /**
     * @return BelongsTo<User, Products>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<ProductsUrls>
     */
    public function urls(): HasMany
    {
        return $this->hasMany(ProductsUrls::class, 'product_id');
    }
}
