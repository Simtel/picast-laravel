<?php

namespace App\Models;

use Database\Factories\DomainFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Domain
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Domain newModelQuery()
 * @method static Builder|Domain newQuery()
 * @method static Builder|Domain query()
 * @method static Builder|Domain whereCreatedAt($value)
 * @method static Builder|Domain whereId($value)
 * @method static Builder|Domain whereName($value)
 * @method static Builder|Domain whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|Whois[] $whois
 * @property-read int|null $whois_count
 * @method static Builder|Domain whereUserId($value)
 * @property string $expire_at
 * @property string $owner
 * @method static Builder|Domain whereExpireAt($value)
 * @method static Builder|Domain whereOwner($value)
 * @method static DomainFactory factory(...$parameters)
 * @property-read User $user
 */
class Domain extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id', 'expire_at'];

    protected $hidden = ['created_at', 'updated_at'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function whois(): HasMany
    {
        return $this->hasMany(Whois::class);
    }
}
