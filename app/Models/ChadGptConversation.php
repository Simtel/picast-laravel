<?php

declare(strict_types=1);

namespace App\Models;

use App\Context\User\Domain\Model\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChadGptConversation extends Model
{
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
