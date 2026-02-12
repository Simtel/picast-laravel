<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Сначала конвертируем формат даты из DD.MM.YYYY в YYYY-MM-DD
        DB::statement("
            UPDATE tournaments
            SET date = CONCAT(
                SUBSTRING(date, 7, 4), '-',
                SUBSTRING(date, 4, 2), '-',
                SUBSTRING(date, 1, 2)
            )
            WHERE date REGEXP '^[0-9]{2}\\.[0-9]{2}\\.[0-9]{4}$'
        ");

        // Затем меняем тип колонки на DATE
        Schema::table('tournaments', static function (Blueprint $table) {
            $table->date('date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Сначала меняем тип колонки обратно на STRING
        Schema::table('tournaments', static function (Blueprint $table) {
            $table->string('date')->nullable()->change();
        });

        // Затем конвертируем формат даты из YYYY-MM-DD обратно в DD.MM.YYYY
        DB::statement("
            UPDATE tournaments
            SET date = CONCAT(
                SUBSTRING(date, 9, 2), '.',
                SUBSTRING(date, 6, 2), '.',
                SUBSTRING(date, 1, 4)
            )
            WHERE date REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$'
        ");
    }
};
