<?php

declare(strict_types=1);

use App\Context\User\Domain\Model\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

class FixAdminRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = User::find(1);
        $role = Role::findByName('admin');
        $user->assignRole($role);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $user = User::find(1);
        $role = Role::findByName('admin');
        $user->removeRole($role);
    }
}
