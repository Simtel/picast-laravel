<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('you_tube_videos', 'youtube_videos');
        Schema::rename('you_tube_videos_formats', 'youtube_videos_formats');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('youtube_videos', 'you_tube_videos');
        Schema::rename('youtube_videos_formats', 'you_tube_videos_formats');
    }
};
