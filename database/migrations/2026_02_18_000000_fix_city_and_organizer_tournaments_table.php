<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tournaments', static function (Blueprint $table) {
            // Проверяем, существуют ли колонки перед добавлением
            if (!Schema::hasColumn('tournaments', 'city')) {
                $table->string('city')->nullable()->after('date_end');
            }
            if (!Schema::hasColumn('tournaments', 'organizer')) {
                $table->string('organizer')->nullable()->after('city');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', static function (Blueprint $table) {
            $table->dropColumn(['city', 'organizer']);
        });
    }
};
