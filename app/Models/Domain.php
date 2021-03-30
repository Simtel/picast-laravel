<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Domain
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property ind $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Domain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain query()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Whois[] $whois
 * @property-read int|null $whois_count
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereUserId($value)
 * @property string $expire_at
 * @property string $owner
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereOwner($value)
 * @method static \Database\Factories\DomainFactory factory(...$parameters)
 */
class Domain extends Model
{
    use HasFactory;

    protected  $fillable = ['name','user_id'];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function whois()
    {
        return $this->hasMany(Whois::class);

    }
}
