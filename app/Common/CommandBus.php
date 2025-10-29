<?php

declare(strict_types=1);

namespace App\Common;

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

    public function execute(object $command): void
    {
        $commandClass = get_class($command);

        if (!isset($this->handlers[$commandClass])) {
            throw new \RuntimeException("Handler not found for {$commandClass}");
        }

        App::make($this->handlers[$commandClass])->handle($command);
    }
}
