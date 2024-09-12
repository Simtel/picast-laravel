<?php

namespace App\Repositories;

use App\Models\Youtube\YouTubeVideoStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class YouTubeVideoStatusRepository
{
    /**
     * Найти статус видео по ID
     *
     * @param int $id
     * @return YouTubeVideoStatus
     * @throws ModelNotFoundException
     */
    public function findById(int $id): YouTubeVideoStatus
    {
        $status = YouTubeVideoStatus::find($id);
        if (!$status) {
            throw new ModelNotFoundException("Статус видео с ID {$id} не найден.");
        }
        return $status;
    }

    /**
     * Найти статус видео по заголовку
     *
     * @param string $title
     * @return YouTubeVideoStatus
     * @throws ModelNotFoundException
     */
    public function findByTitle(string $title): YouTubeVideoStatus
    {
        $status = YouTubeVideoStatus::where('title', $title)->first();
        if (!$status) {
            throw new ModelNotFoundException("Статус видео с заголовком '{$title}' не найден.");
        }
        return $status;
    }

    /**
     * Найти статус видео по коду
     *
     * @param string $code
     * @return YouTubeVideoStatus
     * @throws ModelNotFoundException
     */
    public function findByCode(string $code): YouTubeVideoStatus
    {
        $status = YouTubeVideoStatus::where('code', $code)->first();
        if ($status === null) {
            throw new ModelNotFoundException("Статус видео с кодом '{$code}' не найден.");
        }
        return $status;
    }
}
