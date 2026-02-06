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
        Schema::create('chadgpt_conversations', static function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('model');
            $table->text('user_message');
            $table->longText('ai_response');
            $table->integer('used_words_count')->default(0);
            $table->timestamps();

            /** @phpstan-ignore-next-line   */
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chadgpt_conversations');
    }
};
