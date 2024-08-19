<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => 'edit youtube']);
        $roles = Role::all();
        foreach ($roles as $role) {
            $role->givePermissionTo('edit youtube');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $roles = Role::all();
        foreach ($roles as $role) {
            $role->revokePermissionTo('edit youtube');
        }
    }
};
