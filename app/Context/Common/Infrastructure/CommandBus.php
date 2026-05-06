<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure;

use Illuminate\Support\Facades\App;

class CommandBus
{
    /**
     * @var array<string, string>
     */
    private array $handlers = [];

    public function register(string $commandClass, string $handlerClass): void
    {
        $this->handlers[$commandClass] = $handlerClass;
    }

    /**
     * @param object $command
     * @return mixed
     */
    public function execute(object $command): mixed
    {
        $commandClass = get_class($command);

        if (!isset($this->handlers[$commandClass])) {
            throw new \RuntimeException("Handler not found for {$commandClass}");
        }

        return App::make($this->handlers[$commandClass])->handle($command);
    }
}
