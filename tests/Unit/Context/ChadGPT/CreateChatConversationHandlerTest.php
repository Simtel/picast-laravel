<?php

declare(strict_types=1);

namespace Tests\Unit\Context\ChadGPT;

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

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = new CreateChatConversationHandler();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_creates_conversation_with_correct_data(): void
    {
        $userId = 123;
        $model = 'gpt-4';
        $userMessage = 'Hello, how are you?';
        $aiResponse = 'I am fine, thank you!';
        $tokensCount = 10;

        $user = User::factory()->create(['id' => $userId]);

        $command = Mockery::mock(CreateChatConversationCommand::class);
        $command->shouldReceive('getUser')->andReturn($user);
        $command->shouldReceive('getModel')->andReturn($model);
        $command->shouldReceive('getUserMessage')->andReturn($userMessage);
        $command->shouldReceive('getResponse')->andReturn($aiResponse);
        $command->shouldReceive('getUserTokensCount')->andReturn($tokensCount);

        Auth::shouldReceive('id')->andReturn($userId);

        $this->handler->handle($command);


        $this->assertDatabaseHas('chadgpt_conversations', [
            'user_id' => $userId,
            'model' => $model,
            'user_message' => $userMessage,
            'ai_response' => $aiResponse,
            'used_tokens_count' => $tokensCount,
        ]);
    }

    public function test_creates_new_word_stat_if_not_exists(): void
    {
        $userId = 456;
        $tokensCount = 15;

        $user = User::factory()->create(['id' => $userId]);

        $command = Mockery::mock(CreateChatConversationCommand::class);
        $command->shouldReceive('getUser')->andReturn($user);
        $command->shouldReceive('getModel')->andReturn('gpt-3.5');
        $command->shouldReceive('getUserMessage')->andReturn('Test message');
        $command->shouldReceive('getResponse')->andReturn('Test response');
        $command->shouldReceive('getUserTokensCount')->andReturn($tokensCount);

        Auth::shouldReceive('id')->andReturn($userId);


        $this->handler->handle($command);


        $this->assertDatabaseHas('chadgpt_conversations_word_stat', [
            'user_id' => $userId,
            'tokens_used' => $tokensCount,
        ]);
    }

    public function test_updates_existing_word_stat(): void
    {
        $userId = 789;
        $initialTokensCount = 50;
        $additionalTokensCount = 25;

        $user = User::factory()->make(['id' => $userId]);
        $user->save();

        ChadGptConversationWordStat::create([
            'user_id' => $user->getId(),
            'tokens_used' => $initialTokensCount,
            'stat_date' => now()->startOfMonth(),
        ]);

        $command = Mockery::mock(CreateChatConversationCommand::class);
        $command->shouldReceive('getUser')->andReturn($user);
        $command->shouldReceive('getModel')->andReturn('gpt-4');
        $command->shouldReceive('getUserMessage')->andReturn('Another message');
        $command->shouldReceive('getResponse')->andReturn('Another response');
        $command->shouldReceive('getUserTokensCount')->andReturn($additionalTokensCount);

        Auth::shouldReceive('id')->andReturn($userId);

        $this->handler->handle($command);

        $wordStat = ChadGptConversationWordStat::where('user_id', $userId)->first();
        $this->assertEquals($initialTokensCount + $additionalTokensCount, $wordStat->tokens_used);
    }

    public function test_handles_zero_words_count(): void
    {
        $userId = 111;
        $tokensCount = 0;

        $user = User::factory()->create(['id' => $userId]);

        $command = Mockery::mock(CreateChatConversationCommand::class);
        $command->shouldReceive('getUser')->andReturn($user);
        $command->shouldReceive('getModel')->andReturn('gpt-3.5');
        $command->shouldReceive('getUserMessage')->andReturn('');
        $command->shouldReceive('getResponse')->andReturn('Empty response');
        $command->shouldReceive('getUserTokensCount')->andReturn($tokensCount);

        Auth::shouldReceive('id')->andReturn($userId);


        $this->handler->handle($command);


        $this->assertDatabaseHas('chadgpt_conversations', [
            'user_id' => $userId,
            'used_tokens_count' => 0,
        ]);

        $wordStat = ChadGptConversationWordStat::where('user_id', $userId)->first();
        $this->assertEquals(0, $wordStat->tokens_used);
    }

    public function test_handles_large_words_count(): void
    {
        $userId = 222;
        $largeTokensCount = 10000;

        $user = User::factory()->create(['id' => $userId]);
        ;

        $command = Mockery::mock(CreateChatConversationCommand::class);
        $command->shouldReceive('getUser')->andReturn($user);
        $command->shouldReceive('getModel')->andReturn('gpt-4');
        $command->shouldReceive('getUserMessage')->andReturn('Very long message');
        $command->shouldReceive('getResponse')->andReturn('Very long response');
        $command->shouldReceive('getUserTokensCount')->andReturn($largeTokensCount);

        Auth::shouldReceive('id')->andReturn($userId);


        $this->handler->handle($command);


        $this->assertDatabaseHas('chadgpt_conversations', [
            'user_id' => $userId,
            'used_tokens_count' => $largeTokensCount,
        ]);

        $wordStat = ChadGptConversationWordStat::where('user_id', $userId)->first();
        $this->assertEquals($largeTokensCount, $wordStat->tokens_used);
    }

    public function test_handles_multiple_consecutive_calls(): void
    {
        $userId = 333;
        $firstTokensCount = 10;
        $secondTokensCount = 20;
        $thirdTokensCount = 30;

        $user = User::factory()->create(['id' => $userId]);

        Auth::shouldReceive('id')->andReturn($userId);


        $command1 = Mockery::mock(CreateChatConversationCommand::class);
        $command1->shouldReceive('getUser')->andReturn($user);
        $command1->shouldReceive('getModel')->andReturn('gpt-4');
        $command1->shouldReceive('getUserMessage')->andReturn('First message');
        $command1->shouldReceive('getResponse')->andReturn('First response');
        $command1->shouldReceive('getUserTokensCount')->andReturn($firstTokensCount);


        $command2 = Mockery::mock(CreateChatConversationCommand::class);
        $command2->shouldReceive('getUser')->andReturn($user);
        $command2->shouldReceive('getModel')->andReturn('gpt-3.5');
        $command2->shouldReceive('getUserMessage')->andReturn('Second message');
        $command2->shouldReceive('getResponse')->andReturn('Second response');
        $command2->shouldReceive('getUserTokensCount')->andReturn($secondTokensCount);


        $command3 = Mockery::mock(CreateChatConversationCommand::class);
        $command3->shouldReceive('getUser')->andReturn($user);
        $command3->shouldReceive('getModel')->andReturn('gpt-4');
        $command3->shouldReceive('getUserMessage')->andReturn('Third message');
        $command3->shouldReceive('getResponse')->andReturn('Third response');
        $command3->shouldReceive('getUserTokensCount')->andReturn($thirdTokensCount);


        $this->handler->handle($command1);
        $this->handler->handle($command2);
        $this->handler->handle($command3);


        $conversationsCount = ChadGptConversation::where('user_id', $userId)->count();
        $this->assertEquals(3, $conversationsCount);

        $wordStat = ChadGptConversationWordStat::where('user_id', $userId)->first();
        $expectedTotal = $firstTokensCount + $secondTokensCount + $thirdTokensCount;
        $this->assertEquals($expectedTotal, $wordStat->tokens_used);
    }

    public function test_handles_special_characters_in_messages(): void
    {
        $userId = 444;
        $specialMessage = 'Test with special chars: <>&"\'';
        $specialResponse = 'Response with émojis 🎉 and unicode ñ';

        $user = User::factory()->create(['id' => $userId]);

        $command = Mockery::mock(CreateChatConversationCommand::class);
        $command->shouldReceive('getUser')->andReturn($user);
        $command->shouldReceive('getModel')->andReturn('gpt-4');
        $command->shouldReceive('getUserMessage')->andReturn($specialMessage);
        $command->shouldReceive('getResponse')->andReturn($specialResponse);
        $command->shouldReceive('getUserTokensCount')->andReturn(5);

        Auth::shouldReceive('id')->andReturn($userId);


        $this->handler->handle($command);


        $this->assertDatabaseHas('chadgpt_conversations', [
            'user_id' => $userId,
            'user_message' => $specialMessage,
            'ai_response' => $specialResponse,
        ]);
    }
}
