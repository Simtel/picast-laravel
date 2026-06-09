<?php

declare(strict_types=1);


use App\Context\User\Domain\Model\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Bootstrap\HandleExceptions;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = require dirname(__DIR__) . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

HandleExceptions::flushState();

$user = $user = User::findOrNew(1);

if ($user->tokens()->count() === 0) {
    $user->createToken('test-token');
}
