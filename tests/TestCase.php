<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Auth;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;


    public function loginAdmin(): void
    {
        /** @var User $user */
        $user = User::find(1);
        Auth::login($user);
    }
}
