<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddEditImagesPerm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => 'edit images']);
        $admin = Role::findByName('admin');
        $admin->givePermissionTo('edit images');
        $member = Role::findByName('member');
        $member->givePermissionTo('edit images');
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
