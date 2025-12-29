<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('images', static function (Blueprint $table) {
            if (!Schema::hasColumn('images', 'views_count')) {
                $table->unsignedBigInteger('views_count')->default(0)->after('disk');
            }
        });
    }

    public function down(): void
    {
        Schema::table('images', static function (Blueprint $table) {
            $table->dropColumn('views_count');
        });
    }
};
