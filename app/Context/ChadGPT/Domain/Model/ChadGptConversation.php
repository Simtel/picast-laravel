<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Domain\Model;

use App\Context\ChadGPT\Domain\Factory\ChadGptConversationFactory;
use App\Context\User\Domain\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $model
 * @property string $user_message
 * @property string $ai_response
 * @property int $used_words_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversation whereAiResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversation whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversation whereUsedWordsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChadGptConversation whereUserMessage($value)
 * @method static \App\Context\ChadGPT\Domain\Factory\ChadGptConversationFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class ChadGptConversation extends Model
{
    /** @use HasFactory<ChadGptConversationFactory> */
    use HasFactory;

    protected static string $factory = ChadGptConversationFactory::class;

    protected $table = 'chadgpt_conversations';

    protected $fillable = [
        'user_id',
        'model',
        'user_message',
        'ai_response',
        'used_words_count',
    ];


    protected $casts = [
        'used_words_count' => 'integer',
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
