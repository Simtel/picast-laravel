<?php

declare(strict_types=1);

namespace Tests\Unit\Context\ChadGPT;

use App\Context\ChadGPT\Domain\Model\ChadGptConversation;
use App\Context\User\Domain\Model\User;
use Tests\TestCase;

class ChadGptConversationModelTest extends TestCase
{
    public function test_it_can_be_created_with_factory(): void
    {
        $conversation = ChadGptConversation::factory()->create();

        $this->assertDatabaseHas('chadgpt_conversations', [
            'id' => $conversation->id,
            'user_id' => $conversation->user_id,
            'model' => $conversation->model,
            'user_message' => $conversation->user_message,
            'ai_response' => $conversation->ai_response,
            'used_words_count' => $conversation->used_words_count,
        ]);
    }

    public function test_it_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $conversation = ChadGptConversation::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $conversation->user);
        $this->assertEquals($user->id, $conversation->user->id);
    }

    public function test_fillable_attributes(): void
    {
        $fillable = [
            'user_id',
            'model',
            'user_message',
            'ai_response',
            'used_words_count',
        ];

        $this->assertEquals($fillable, (new ChadGptConversation())->getFillable());
    }

    public function test_casts(): void
    {
        $casts = [
            'used_words_count' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'id' => 'int'
        ];

        $this->assertEquals($casts, new ChadGptConversation()->getCasts());
    }

    public function test_table_name(): void
    {
        $this->assertEquals('chadgpt_conversations', new ChadGptConversation()->getTable());
    }
}
