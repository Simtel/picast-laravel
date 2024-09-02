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
        Schema::create('you_tube_videos_formats', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('video_id')->unsigned();
            $table->foreign('video_id')
                ->references('id')
                ->on('you_tube_videos')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->integer('format_id')->unsigned();
            $table->string('format_note')->nullable();
            $table->string('format_ext')->nullable();
            $table->string('vcodec')->nullable();
            $table->string('resolution')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('you_tube_videos_formats');
    }
};
