<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Class Whois
 *
 * @package App\Models
 * @property int $_id
 * @property $domain_id
 * @property string $text
 * @property-read User $users
 * @method static Builder|Whois newModelQuery()
 * @method static Builder|Whois newQuery()
 * @method static Builder|Whois query()
 * @mixin Eloquent
 */
class Whois extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['title','domain_id','text'];

    /**
     * @var string
     */
    protected $connection = 'mongodb';

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
