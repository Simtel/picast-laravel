<?php


use App\Models\User;
use Illuminate\Support\Str;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = require_once dirname(__DIR__) . '/bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = $user = User::findOrNew(1);

if ($user->api_token === null) {
    $user->api_token = Str::random(60);
    $user->save();
}
