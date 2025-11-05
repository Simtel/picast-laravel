<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Application\Dto;

class ChadGptRequest
{
    public function __construct(
        private readonly string $model,
        private readonly string $userMessage,
    ) {
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }


}
