<?php

declare(strict_types=1);

namespace App\Context\Webcams\Application\Contracts;

use App\Context\Webcams\Application\Dto\CreateWebcamDto;
use App\Context\Webcams\Application\Dto\UpdateWebcamDto;
use App\Context\Webcams\Domain\Model\Webcam;
use Illuminate\Database\Eloquent\Collection;

interface WebcamServiceInterface
{
    /**
     * Получить все активные веб-камеры
     */
    public function getAllActiveWebcams(): Collection;

    /**
     * Получить веб-камеру по ID
     */
    public function getWebcamById(int $id): ?Webcam;

    /**
     * Создать новую веб-камеру
     */
    public function createWebcam(CreateWebcamDto $dto): Webcam;

    /**
     * Обновить веб-камеру
     */
    public function updateWebcam(int $id, UpdateWebcamDto $dto): ?Webcam;

    /**
     * Удалить веб-камеру
     */
    public function deleteWebcam(int $id): bool;

    /**
     * Получить веб-камеры по локации
     */
    public function getWebcamsByLocation(string $location): Collection;
}
