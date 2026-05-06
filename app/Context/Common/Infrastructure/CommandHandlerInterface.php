<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): mixed;
}
