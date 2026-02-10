<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Domain\Model;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'link',
        'date',
        'guid',
    ];
}
