<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'invite user']);
        $permission->assignRole($role);
        $user = \App\Models\User::find(1);
        $user->givePermissionTo('invite user');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permission = Permission::findByName('invite user');
        Permission::destroy($permission->id);
        $role = Role::findByName('admin');
        $role->delete();
    }
}
