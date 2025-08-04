<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $created_by
 * @property string $code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder<static>|InviteCode newModelQuery()
 * @method static Builder<static>|InviteCode newQuery()
 * @method static Builder<static>|InviteCode query()
 * @method static Builder<static>|InviteCode whereCode($value)
 * @method static Builder<static>|InviteCode whereCreatedAt($value)
 * @method static Builder<static>|InviteCode whereCreatedBy($value)
 * @method static Builder<static>|InviteCode whereId($value)
 * @method static Builder<static>|InviteCode whereUpdatedAt($value)
 * @mixin Eloquent
 */
class InviteCode extends Model
{
    protected $fillable = ['created_by', 'code'];
}
