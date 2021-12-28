<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
 */
class Products extends Model
{
    use HasFactory;
}
