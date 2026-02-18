<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Domain\Model;

use App\Context\Tournaments\Domain\Factory\TournamentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $link
 * @property Carbon|null $date
 * @property Carbon|null $date_end
 * @property string|null $city
 * @property string|null $organizer
 * @property string $guid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereDateEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereGuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereOrganizer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereUpdatedAt($value)
 * @method static \App\Context\Tournaments\Domain\Factory\TournamentFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class Tournament extends Model
{
    /** @use HasFactory<TournamentFactory> */
    use HasFactory;

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
        'city',
        'organizer',
        'guid',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'date_end' => 'date',
    ];

    protected static string $factory = TournamentFactory::class;

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

    public function getDate(): ?Carbon
    {
        return $this->date;
    }

    public function getDateEnd(): ?Carbon
    {
        return $this->date_end;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getOrganizer(): ?string
    {
        return $this->organizer;
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
