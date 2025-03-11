<?php

declare(strict_types=1);


use App\Context\User\Domain\Model\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Support\Str;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = require dirname(__DIR__) . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

HandleExceptions::flushState();

$user = $user = User::findOrNew(1);

if ($user->api_token === null) {
    $user->api_token = Str::random(60);
    $user->save();
}
