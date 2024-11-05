<?php

declare(strict_types=1);

namespace Tests\Feature\Common;

use App\Context\User\Domain\Model\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    public function test_user_edit_page(): void
    {
        $this->loginAdmin();
        $user = $this->createUserWithPermissions([], ['']);
        $roles = Role::all();
        $response = $this->get(route('user.edit', ['user' => $user]));
        $response->assertStatus(200);
        $response->assertViewIs('personal.user.edit');
        $response->assertViewHas('user', $user);
        $response->assertViewHas('roles', $roles);
    }

    public function test_user_update_roles(): void
    {
        $tableNames = config('permission.table_names');
        if (!is_array($tableNames) || !array_key_exists($tableNames['model_has_roles'], $tableNames)) {
            self::fail('Fail config for permissions');
        }

        $this->loginAdmin();
        $user = $this->createUserWithPermissions([], ['']);
        $this->assertDatabaseCount($tableNames['model_has_roles'], 3);
        $this->assertDatabaseHas($tableNames['model_has_roles'], ['model_id' => $user->id]);
        $data = [
            'name'  => $user->getName(),
            'email' => 'test@test1.com',
            'roles' => [
                'member'
            ]
        ];
        $response = $this->post(route('user.update', ['user' => $user]), $data);
        $response->assertStatus(302);
        $response->assertRedirect(route('user.edit', ['user' => $user]));
        $this->assertDatabaseCount($tableNames['model_has_roles'], 3);
        $this->assertDatabaseHas(User::class, ['email' => 'test@test1.com']);
    }
}
