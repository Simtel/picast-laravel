<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('images', 'directory')) {
            Schema::table('images', function (Blueprint $table) {
                $table->string('directory');
            });
        }
        if (!Schema::hasColumn('images', 'disk')) {
            Schema::table('images', function (Blueprint $table) {
                $table->string('disk');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('disk');
        });
    }
};
