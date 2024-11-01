<?php

declare(strict_types=1);

namespace Tests;

use App\Context\User\Domain\Model\User;
use Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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

    /**
     * @param array<string, mixed> $attributes
     * @param string[] $permissions
     * @return void
     */
    public function authUserWithPermissions(array $attributes, array $permissions): void
    {
        $user = User::factory()->count(1)->create($attributes)->first();
        $role = Role::create(['name' => 'user']);
        foreach ($permissions as $permission) {
            $permission = Permission::findOrCreate($permission);
            $role->givePermissionTo($permission);
        }
        $user->assignRole($role);
        Auth::login($user);
    }

    public function getAuthUser(): ?Authenticatable
    {
        return Auth::user();
    }

    public function getAdminUser(): User
    {
        $user = User::find(1)?->first();
        if ($user === null) {
            throw new \RuntimeException('Not found admin user');
        }

        return $user;
    }
}
