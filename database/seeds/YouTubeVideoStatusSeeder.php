<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class YouTubeVideoStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('youtube_video_statuses')->insert(
            [
                [
                    'title' => 'Новый',
                    'code'  => 'new',
                ],
                [
                    'title' => 'В очереди',
                    'code'  => 'queue',
                ],
                [
                    'title' => 'Загружается',
                    'code'  => 'downloading',
                ],
                [
                    'title' => 'Скачен',
                    'code'  => 'downloaded',
                ]
            ]
        );
    }
}
