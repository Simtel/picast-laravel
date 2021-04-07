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
 * @method static Builder|InviteCode whereCode($value)
 * @method static Builder|InviteCode whereCreatedAt($value)
 * @method static Builder|InviteCode whereCreatedBy($value)
 * @method static Builder|InviteCode whereId($value)
 * @method static Builder|InviteCode whereUpdatedAt($value)
 */
class InviteCode extends Model
{
    use HasFactory;

    protected $fillable = ['created_by', 'code'];
}
