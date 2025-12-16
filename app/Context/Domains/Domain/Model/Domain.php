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
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $expire_at
 * @property string|null $owner
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read User $user
 * @property-read Collection<int, \App\Context\Domains\Domain\Model\Whois> $whois
 * @property-read int|null $whois_count
 * @method static \App\Context\Domains\Domain\Factory\DomainFactory factory($count = null, $state = [])
 * @method static Builder<static>|Domain newModelQuery()
 * @method static Builder<static>|Domain newQuery()
 * @method static Builder<static>|Domain query()
 * @method static Builder<static>|Domain whereCreatedAt($value)
 * @method static Builder<static>|Domain whereExpireAt($value)
 * @method static Builder<static>|Domain whereId($value)
 * @method static Builder<static>|Domain whereName($value)
 * @method static Builder<static>|Domain whereOwner($value)
 * @method static Builder<static>|Domain whereUpdatedAt($value)
 * @method static Builder<static>|Domain whereUserId($value)
 * @mixin Eloquent
 */
final class Domain extends Model
{
    /** @use HasFactory<DomainFactory> */
    use HasFactory;
    use Notifiable;

    protected $fillable = ['name', 'user_id', 'expire_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'expire_at' => 'date',
    ];
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
     * @return string
     */
    public function routeNotificationForMail(Notification $notification): string
    {
        return $this->getUser()->getEmail();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCreatedAt(): ?Carbon
    {
        $a = $this->created_at;
        return $this->created_at;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updated_at;
    }

    public function getExpireAt(): ?Carbon
    {
        $b = $this->expire_at;
        return $this->expire_at;
    }


}
