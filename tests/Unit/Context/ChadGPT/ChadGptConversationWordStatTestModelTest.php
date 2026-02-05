<?php

declare(strict_types=1);

namespace Tests\Unit\Context\ChadGPT;

use App\Context\ChadGPT\Domain\Model\ChadGptConversationWordStat;
use App\Context\User\Domain\Model\User;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ChadGptConversationWordStatTestModelTest extends TestCase
{
    public function test_it_can_be_created(): void
    {
        $user = User::factory()->create();

        $stat = new ChadGptConversationWordStat([
            'user_id' => $user->id,
            'words_used' => 150,
            'stat_date' => '2023-10-15',
        ]);

        $stat->save();

        $this->assertDatabaseHas('chadgpt_conversations_word_stat', [
            'id' => $stat->id,
            'user_id' => $user->id,
            'words_used' => 150,
            'stat_date' => '2023-10-15',
        ]);
    }

    public function test_it_belongs_to_user(): void
    {
        $user = User::factory()->create();

        $stat = ChadGptConversationWordStat::create([
            'user_id' => $user->id,
            'words_used' => 100,
            'stat_date' => Carbon::today(),
        ]);

        $this->assertInstanceOf(User::class, $stat->user);
        $this->assertEquals($user->id, $stat->user->id);
    }

    public function test_fillable_attributes(): void
    {
        $fillable = [
            'user_id',
            'words_used',
            'stat_date',
        ];

        $this->assertEquals($fillable, new ChadGptConversationWordStat()->getFillable());
    }

    public function test_casts(): void
    {
        $casts = [
            'user_id' => 'integer',
            'words_used' => 'integer',
            'stat_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'id' => 'int'
        ];

        $this->assertEquals($casts, new ChadGptConversationWordStat()->getCasts());
    }

    public function test_table_name(): void
    {
        $this->assertEquals('chadgpt_conversations_word_stat', new ChadGptConversationWordStat()->getTable());
    }

    public function test_getters(): void
    {
        $user = User::factory()->create();

        $stat = ChadGptConversationWordStat::create([
            'user_id' => $user->id,
            'words_used' => 200,
            'stat_date' => Carbon::today(),
        ]);

        $this->assertEquals($stat->id, $stat->getId());
        $this->assertEquals($user->id, $stat->getUserId());
        $this->assertEquals(200, $stat->getWordsUsed());
        $this->assertInstanceOf(Carbon::class, $stat->getCreatedAt());
        $this->assertInstanceOf(Carbon::class, $stat->getUpdatedAt());
        $this->assertInstanceOf(User::class, $stat->getUser());
        $this->assertInstanceOf(Carbon::class, $stat->getStatDate());
    }
}
