<?php

declare(strict_types=1);

namespace Tests\Feature\Common;

use App\Context\User\Domain\Model\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class UsersControllerTest extends TestCase
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
            'birth_date' => '1990-01-15',
            'roles' => [
                'member'
            ]
        ];
        $response = $this->post(route('user.update', ['user' => $user]), $data);
        $response->assertStatus(302);
        $response->assertRedirect(route('user.edit', ['user' => $user]));
        $response->assertSessionHas('success', 'Пользователь успешно обновлен!');
        $this->assertDatabaseCount($tableNames['model_has_roles'], 3);
        $this->assertDatabaseHas(User::class, ['email' => 'test@test1.com', 'birth_date' => '1990-01-15']);
    }

    public function test_user_update_with_duplicate_email_validation_error(): void
    {
        $this->loginAdmin();
        $target = $this->createUserWithPermissions([], ['']);
        $other = User::factory()->create(['email' => 'occupied@example.com']);

        $response = $this->post(route('user.update', ['user' => $target]), [
            'name' => $target->getName(),
            'email' => $other->email,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }
}
