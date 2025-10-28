<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Domain\Model;

use App\Context\User\Domain\Model\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $words_used
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversationWordStat whereWordsUsed($value)
 * @mixin \Eloquent
 */
class ChadGptConversationWordStat extends Model
{
    protected $table = 'chadgpt_conversations_word_stat';

    protected $fillable = [
        'user_id',
        'words_used',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'words_used' => 'integer',
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
}
