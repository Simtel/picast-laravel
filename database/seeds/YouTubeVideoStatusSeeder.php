<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Context\Youtube\Domain\Model\VideoStatus;
use DB;
use Illuminate\Database\Seeder;


class YouTubeVideoStatusSeeder extends Seeder
{
    private array $statuses = [
        'new'         => [
            'title' => 'Новый',
            'code'  => 'new',
        ],
        'queue'       => [
            'title' => 'В очереди',
            'code'  => 'queue',
        ],
        'downloading' => [
            'title' => 'Загружается',
            'code'  => 'downloading',
        ],
        'downloaded'  => [
            'title' => 'Скачен',
            'code'  => 'downloaded',
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [];
        $existsStatuses = VideoStatus::pluck('title', 'code')->all();
        foreach ($this->statuses as $code => $status) {
            if (!array_key_exists($code, $existsStatuses)) {
                $rows[] = ['title' => $status['title'], 'code' => $status['code']];
            }
        }
        DB::table('youtube_video_statuses')->insert($rows);
    }
}
