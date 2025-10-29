<?php

declare(strict_types=1);

namespace App\Common;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): void;
}
