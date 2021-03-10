<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


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
    protected  $fillable = ['domain_id', 'text'];

}
