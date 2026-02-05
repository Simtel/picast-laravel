<?php

declare(strict_types=1);

namespace Tests\Feature\ChadGPT;

use App\Context\ChadGPT\Domain\Command\CreateChatConversationCommand;
use App\Context\ChadGPT\Domain\Model\ChadGptConversation;
use App\Context\ChadGPT\Domain\Model\ChadGptConversationWordStat;
use App\Context\ChadGPT\Infrastructure\Handlers\CreateChatConversationHandler;
use App\Context\User\Domain\Model\User;
use Auth;
use Mockery;
use Tests\TestCase;

class CreateChatConversationHandlerTest extends TestCase
{
    private CreateChatConversationHandler $handler;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = new CreateChatConversationHandler();
        /** @var User $user */
        $user = User::factory()->create();
        $this->user = $user;
    }

    public function test_full_flow_with_real_models(): void
    {
        Auth::login($this->user);


        $command = Mockery::mock(CreateChatConversationCommand::class);
        $command->shouldReceive('getUser')->andReturn($this->user);
        $command->shouldReceive('getModel')->andReturn('gpt-4');
        $command->shouldReceive('getUserMessage')->andReturn('Hello AI');
        $command->shouldReceive('getResponse')->andReturn('Hello Human');
        $command->shouldReceive('getUserWordsCount')->andReturn(2);

        $this->handler->handle($command);

        $this->assertDatabaseCount('chadgpt_conversations', 1);
        $this->assertDatabaseCount('chadgpt_conversations_word_stat', 1);

        $conversation = ChadGptConversation::first();
        $this->assertEquals($this->user->id, $conversation->user_id);
        $this->assertEquals('gpt-4', $conversation->model);

        $wordStat = ChadGptConversationWordStat::first();
        $this->assertEquals(2, $wordStat->words_used);
    }
}
