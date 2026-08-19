<?php

declare(strict_types=1);

namespace Tests\Unit\Context\ChadGPT;

use App\Context\ChadGPT\Domain\Command\CreateChatConversationCommand;
use App\Context\Common\Infrastructure\CommandInterface;
use App\Context\User\Domain\Model\User;
use Tests\TestCase;

class CreateChatConversationCommandTest extends TestCase
{
    public function test_implements_command_interface(): void
    {
        $command = new CreateChatConversationCommand(
            user: User::factory()->make(),
            model: 'gpt-5.6-terra',
            userMessage: 'Привет',
            response: 'Привет!',
            userTokensCount: 42,
        );

        $this->assertInstanceOf(CommandInterface::class, $command);
    }

    public function test_getters_return_constructor_values(): void
    {
        $user = User::factory()->create();

        $command = new CreateChatConversationCommand(
            user: $user,
            model: 'claude-5-sonnet',
            userMessage: 'Вопрос пользователя',
            response: 'Ответ ассистента',
            userTokensCount: 100,
        );

        $this->assertSame($user, $command->getUser());
        $this->assertSame('claude-5-sonnet', $command->getModel());
        $this->assertSame('Вопрос пользователя', $command->getUserMessage());
        $this->assertSame('Ответ ассистента', $command->getResponse());
        $this->assertSame(100, $command->getUserTokensCount());
    }
}
