<?php

declare(strict_types=1);

namespace App\Context\Domains\Domain\Model;

use App\Context\Domains\Domain\Factory\DomainFactory;
use App\Context\Domains\Infrastructure\Notification\DomainDeleted;
use App\Context\User\Domain\Model\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

/**
 * Class Domain
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Domain newModelQuery()
 * @method static Builder|Domain newQuery()
 * @method static Builder|Domain query()
 * @method static Builder|Domain whereCreatedAt($value)
 * @method static Builder|Domain whereId($value)
 * @method static Builder|Domain whereName($value)
 * @method static Builder|Domain whereUpdatedAt($value)
 * @property-read Collection|Whois[] $whois
 * @property-read int|null $whois_count
 * @method static Builder|Domain whereUserId($value)
 * @property Carbon $expire_at
 * @property string $owner
 * @method static Builder|Domain whereExpireAt($value)
 * @method static Builder|Domain whereOwner($value)
 * @method static DomainFactory factory(...$parameters)
 * @property User $user
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @mixin Eloquent
 */
class Domain extends Model
{
    /** @use HasFactory<DomainFactory> */
    use HasFactory;
    use Notifiable;

    protected $fillable = ['name', 'user_id', 'expire_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected static string $factory = DomainFactory::class;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Whois, $this>
     */
    public function whois(): HasMany
    {
        return $this->hasMany(Whois::class);
    }

    /**
     * @param DomainDeleted $notification
     * @return string[]|string
     */
    public function routeNotificationForMail(Notification $notification): array|string
    {
        return $this->getUser()->getEmail();
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
