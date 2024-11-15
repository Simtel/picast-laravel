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
        Schema::create('youtube_video_download_queue', static function (Blueprint $table) {
            $table->id();
            $table->bigInteger('video_id')->unsigned();
            $table->foreign('video_id')
                ->references('id')
                ->on('youtube_videos')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('format_id')->unsigned();
            $table->foreign('format_id')
                ->references('id')
                ->on('youtube_videos_formats')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('youtube_video_download_queue');
    }
};
