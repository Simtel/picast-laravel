<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Domain\Command;

use App\Common\CommandInterface;
use App\Context\User\Domain\Model\User;

class CreateChatConversationCommand implements CommandInterface
{
    public function __construct(
        private readonly User $user,
        private readonly string $model,
        private readonly string $userMessage,
        private readonly string $response,
        private readonly int $userWordsCount,
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getUserWordsCount(): int
    {
        return $this->userWordsCount;
    }


}
