<?php

namespace App\Repositories;

use App\Models\Youtube\VideoStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class YouTubeVideoStatusRepository
{
    /**
     * Найти статус видео по ID
     *
     * @param int $id
     * @return VideoStatus
     * @throws ModelNotFoundException
     */
    public function findById(int $id): VideoStatus
    {
        $status = VideoStatus::find($id);
        if (!$status) {
            throw new ModelNotFoundException("Статус видео с ID {$id} не найден.");
        }
        return $status;
    }

    /**
     * Найти статус видео по заголовку
     *
     * @param string $title
     * @return VideoStatus
     * @throws ModelNotFoundException
     */
    public function findByTitle(string $title): VideoStatus
    {
        $status = VideoStatus::where('title', $title)->first();
        if (!$status) {
            throw new ModelNotFoundException("Статус видео с заголовком '{$title}' не найден.");
        }
        return $status;
    }

    /**
     * Найти статус видео по коду
     *
     * @param string $code
     * @return VideoStatus
     * @throws ModelNotFoundException
     */
    public function findByCode(string $code): VideoStatus
    {
        $status = VideoStatus::where('code', $code)->first();
        if ($status === null) {
            throw new ModelNotFoundException("Статус видео с кодом '{$code}' не найден.");
        }
        return $status;
    }
}
