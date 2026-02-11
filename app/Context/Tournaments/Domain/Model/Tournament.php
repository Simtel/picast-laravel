<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $link
 * @property string|null $date
 * @property string|null $date_end
 * @property string $guid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereDateEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereGuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        'date_end',
        'guid',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function getDateEnd(): ?string
    {
        return $this->date_end;
    }

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updated_at;
    }


}
