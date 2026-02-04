<?php

declare(strict_types=1);

namespace App\Context\Webcams\Application\Services;

use App\Context\Webcams\Application\Contracts\WebcamServiceInterface;
use App\Context\Webcams\Application\Dto\CreateWebcamDto;
use App\Context\Webcams\Application\Dto\UpdateWebcamDto;
use App\Context\Webcams\Domain\Model\Webcam;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WebcamService implements WebcamServiceInterface
{
    private const CACHE_KEY = 'webcams_ulyanovsk';
    private const CACHE_TTL = 300; // 5 минут

    /**
     * Получить все активные веб-камеры Ульяновска
     *
     * @return Collection<int, Webcam>
     */
    public function getAllActiveWebcams(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, static function () {
            Log::info('Загрузка веб-камер из базы данных');
            return Webcam::whereActive()
                ->orderBy('location')
                ->get();
        });
    }

    /**
     * Получить веб-камеру по ID
     */
    public function getWebcamById(int $id): ?Webcam
    {
        return Webcam::find($id);
    }

    /**
     * Создать новую веб-камеру
     */
    public function createWebcam(CreateWebcamDto $dto): Webcam
    {
        $webcam = Webcam::create([
            'name' => $dto->getName(),
            'location' => $dto->getLocation(),
            'stream_url' => $dto->getStreamUrl(),
            'preview_url' => $dto->getPreviewUrl(),
            'description' => $dto->getDescription(),
            'is_active' => $dto->isActive(),
        ]);

        $this->clearCache();
        Log::info("Создана новая веб-камера: {$webcam->getName()}");

        return $webcam;
    }

    /**
     * Обновить веб-камеру
     */
    public function updateWebcam(int $id, UpdateWebcamDto $dto): ?Webcam
    {
        $webcam = $this->getWebcamById($id);

        if (!$webcam) {
            return null;
        }

        $webcam->update([
            'name' => $dto->getName(),
            'location' => $dto->getLocation(),
            'stream_url' => $dto->getStreamUrl(),
            'preview_url' => $dto->getPreviewUrl(),
            'description' => $dto->getDescription(),
            'is_active' => $dto->isActive(),
        ]);

        $this->clearCache();
        Log::info("Обновлена веб-камера: {$webcam->getName()}");

        return $webcam->fresh();
    }

    /**
     * Удалить веб-камеру
     */
    public function deleteWebcam(int $id): bool
    {
        $webcam = $this->getWebcamById($id);

        if (!$webcam) {
            return false;
        }

        $webcam->delete();
        $this->clearCache();
        Log::info("Удалена веб-камера: {$webcam->getName()}");

        return true;
    }

    /**
     * Получить веб-камеры по локации
     *
     * @return Collection<int, Webcam>
     */
    public function getWebcamsByLocation(string $location): Collection
    {
        return Webcam::where('location', 'like', "%{$location}%")
            ->whereActive()
            ->orderBy('name')
            ->get();
    }

    /**
     * Очистить кэш
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
