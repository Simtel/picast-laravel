<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('model_has_roles')
            ->where('model_type', 'App\Models\User')
            ->update(['model_type' => 'App\Context\User\Domain\Model\User']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('model_has_roles')
            ->where('model_type', 'App\Context\User\Domain\Model\User')
            ->update(['model_type' => 'App\Models\User']);
    }
};
