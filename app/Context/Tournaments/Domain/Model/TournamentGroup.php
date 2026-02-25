<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Domain\Model;

use App\Context\Tournaments\Domain\Factory\TournamentGroupFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $tournament_id
 * @property int $number
 * @property string $name
 * @property int $registrations
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Tournament $tournament
 * @method static Builder<static>|TournamentGroup newModelQuery()
 * @method static Builder<static>|TournamentGroup newQuery()
 * @method static Builder<static>|TournamentGroup query()
 * @method static Builder<static>|TournamentGroup whereCreatedAt($value)
 * @method static Builder<static>|TournamentGroup whereId($value)
 * @method static Builder<static>|TournamentGroup whereName($value)
 * @method static Builder<static>|TournamentGroup whereNumber($value)
 * @method static Builder<static>|TournamentGroup whereRegistrations($value)
 * @method static Builder<static>|TournamentGroup whereTournamentId($value)
 * @method static Builder<static>|TournamentGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class TournamentGroup extends Model
{
    /** @use HasFactory<TournamentGroupFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tournament_id',
        'number',
        'name',
        'registrations',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tournament_id' => 'integer',
        'number' => 'integer',
        'registrations' => 'integer',
    ];

    /**
     * @return BelongsTo<Tournament, $this>
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTournamentId(): int
    {
        return $this->tournament_id;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRegistrations(): int
    {
        return $this->registrations;
    }
}
