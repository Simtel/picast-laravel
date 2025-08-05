<?php

declare(strict_types=1);

namespace App\Context\Domains\Domain\Model;

use App\Context\Domains\Domain\Factory\WhoisFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $domain_id
 * @property string $text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \App\Context\Domains\Domain\Factory\WhoisFactory factory($count = null, $state = [])
 * @method static Builder<static>|Whois newModelQuery()
 * @method static Builder<static>|Whois newQuery()
 * @method static Builder<static>|Whois query()
 * @method static Builder<static>|Whois whereCreatedAt($value)
 * @method static Builder<static>|Whois whereDomainId($value)
 * @method static Builder<static>|Whois whereId($value)
 * @method static Builder<static>|Whois whereText($value)
 * @method static Builder<static>|Whois whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Whois extends Model
{
    /** @use HasFactory<WhoisFactory> */
    use HasFactory;

    protected $fillable = ['domain_id', 'text'];

    protected $hidden = ['text'];

    protected static string $factory = WhoisFactory::class;
}
