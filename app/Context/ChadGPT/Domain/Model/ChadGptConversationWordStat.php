<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Domain\Model;

use App\Context\User\Domain\Model\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $words_used
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat whereWordsUsed($value)
 * @property Carbon $stat_date
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat whereStatDate($value)
 * @mixin \Eloquent
 */
class ChadGptConversationWordStat extends Model
{
    protected $table = 'chadgpt_conversations_word_stat';

    protected $fillable = [
        'user_id',
        'words_used',
        'stat_date',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'words_used' => 'integer',
        'stat_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getWordsUsed(): int
    {
        return $this->words_used;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updated_at;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getStatDate(): Carbon
    {
        return $this->stat_date;
    }


}
