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
        Schema::table('chadgpt_conversations_word_stat', static function (Blueprint $table) {
            $table->date('stat_date')->nullable()->after('words_used');
        });

        DB::table('chadgpt_conversations_word_stat')->update(['stat_date' => now()->startOfMonth()]);

        Schema::table('chadgpt_conversations_word_stat', static function (Blueprint $table) {
            $table->date('stat_date')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chadgpt_conversations_word_stat', static function (Blueprint $table) {
            $table->dropColumn('stat_date');
        });
    }
};
