<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\InviteCode
 *
 * @property integer $id
 * @property integer $created_by
 * @property string $code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|InviteCode newModelQuery()
 * @method static Builder|InviteCode newQuery()
 * @method static Builder|InviteCode query()
 * @mixin Eloquent
 */
class InviteCode extends Model
{
    use HasFactory;

    protected $fillable = ['created_by', 'code'];
}
