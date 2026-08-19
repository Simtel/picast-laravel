<?php

declare(strict_types=1);

namespace Tests\Unit\Context\ChadGPT;

use App\Context\ChadGPT\Domain\Model\ChadGptConversation;
use App\Context\ChadGPT\Infrastructure\Repository\ConversationRepository;
use App\Context\ChadGPT\Infrastructure\Repository\StatWordsUsedRepository;
use App\Context\User\Domain\Model\User;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ConversationRepositoryTest extends TestCase
{
    private ConversationRepository $repository;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ConversationRepository();
        /** @var User $user */
        $user = User::factory()->create();
        $this->user = $user;
    }

    public function test_find_by_user_returns_only_user_conversations(): void
    {
        ChadGptConversation::factory()->count(3)->create(['user_id' => $this->user->id]);

        $otherUser = User::factory()->create();
        ChadGptConversation::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $result = $this->repository->findBuUser($this->user);

        $this->assertCount(3, $result);
        $userId = $this->user->id;
        $this->assertSame(0, $result->filter(
            static fn ($item): bool => $item->user_id !== $userId
        )->count());
    }

    public function test_find_by_user_returns_conversations_ordered_by_created_at_desc(): void
    {
        $first = ChadGptConversation::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => Carbon::now()->subDays(2),
        ]);
        $second = ChadGptConversation::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => Carbon::now()->subDay(),
        ]);
        $third = ChadGptConversation::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => Carbon::now(),
        ]);

        $result = $this->repository->findBuUser($this->user);

        $this->assertSame([$third->id, $second->id, $first->id], $result->pluck('id')->all());
    }

    public function test_find_by_user_returns_empty_collection_when_no_conversations(): void
    {
        $result = $this->repository->findBuUser($this->user);

        $this->assertTrue($result->isEmpty());
    }

    public function test_delete_by_user_removes_only_user_conversations(): void
    {
        ChadGptConversation::factory()->count(3)->create(['user_id' => $this->user->id]);

        $otherUser = User::factory()->create();
        ChadGptConversation::factory()->create(['user_id' => $otherUser->id]);

        $this->repository->deleteByUser($this->user);

        $this->assertSame(0, ChadGptConversation::where('user_id', $this->user->id)->count());
        $this->assertSame(1, ChadGptConversation::where('user_id', $otherUser->id)->count());
    }

    public function test_delete_by_user_with_no_conversations_does_not_throw(): void
    {
        $this->repository->deleteByUser($this->user);

        $this->assertSame(0, ChadGptConversation::count());
    }

    public function test_stat_words_used_repository_returns_user_stats(): void
    {
        $statRepository = new StatWordsUsedRepository();

        \App\Context\ChadGPT\Domain\Model\ChadGptConversationWordStat::create([
            'user_id' => $this->user->id,
            'stat_date' => Carbon::now()->firstOfMonth(),
            'tokens_used' => 100,
        ]);

        $otherUser = User::factory()->create();
        \App\Context\ChadGPT\Domain\Model\ChadGptConversationWordStat::create([
            'user_id' => $otherUser->id,
            'stat_date' => Carbon::now()->firstOfMonth(),
            'tokens_used' => 999,
        ]);

        $result = $statRepository->findByUser($this->user);

        $this->assertCount(1, $result);
        $this->assertSame(100, $result->first()->tokens_used);
    }
}
