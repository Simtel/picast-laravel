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
        Schema::table('chadgpt_conversations', static function (Blueprint $table) {
            $table->renameColumn('used_words_count', 'used_tokens_count');
        });

        Schema::table('chadgpt_conversations_word_stat', static function (Blueprint $table) {
            $table->renameColumn('words_used', 'tokens_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chadgpt_conversations_word_stat', static function (Blueprint $table) {
            $table->renameColumn('tokens_used', 'words_used');
        });

        Schema::table('chadgpt_conversations', static function (Blueprint $table) {
            $table->renameColumn('used_tokens_count', 'used_words_count');
        });
    }
};
