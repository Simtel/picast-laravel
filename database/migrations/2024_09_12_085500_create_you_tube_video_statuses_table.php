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
        Schema::create('youtube_video_statuses', static function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code');
        });

        Schema::table('youtube_videos', static function (Blueprint $table) {
            $table->bigInteger('status_id')->unsigned()->nullable();
            $table->foreign('status_id')
                ->references('id')
                ->on('youtube_video_statuses')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('youtube_video_statuses');

        Schema::table('youtube_videos', static function (Blueprint $table) {
            $table->dropColumn('status_id');
        });
    }
};
