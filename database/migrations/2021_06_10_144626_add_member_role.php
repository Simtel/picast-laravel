<?php

declare(strict_types=1);

use App\Context\User\Domain\Model\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddMemberRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role = Role::create(['name' => 'member']);
        Permission::create(['name' => 'domains']);
        foreach (User::all() as $user) {
            $user->assignRole($role);
            $user->givePermissionTo('domains');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
