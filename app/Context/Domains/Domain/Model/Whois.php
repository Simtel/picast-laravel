<?php

declare(strict_types=1);

namespace App\Context\Domains\Domain\Model;

use App\Models\User;
use Database\Factories\Domains\WhoisFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Whois
 *
 * @package App\Models
 * @property int $_id
 * @property int $domain_id
 * @property string $text
 * @property-read User $users
 * @method static Builder|Whois newModelQuery()
 * @method static Builder|Whois newQuery()
 * @method static Builder|Whois query()
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static WhoisFactory factory(...$parameters)
 * @method static Builder|Whois whereCreatedAt($value)
 * @method static Builder|Whois whereDomainId($value)
 * @method static Builder|Whois whereId($value)
 * @method static Builder|Whois whereText($value)
 * @method static Builder|Whois whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Whois extends Model
{
    /** @use HasFactory<WhoisFactory> */
    use HasFactory;

    protected $fillable = ['domain_id', 'text'];

    protected $hidden = ['text'];
}
