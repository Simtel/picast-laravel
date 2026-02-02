<?php

declare(strict_types=1);

namespace App\Context\Webcams\Infrastructure\Controllers;

use App\Context\Webcams\Application\Contracts\WebcamServiceInterface;
use App\Context\Webcams\Application\Dto\WebcamDto;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class WebcamWebController extends Controller
{
    public function __construct(
        private readonly WebcamServiceInterface $webcamService
    ) {
    }

    /**
     * Показать все веб-камеры
     */
    public function index(): Response
    {
        try {
            $webcams = $this->webcamService->getAllActiveWebcams();

            $webcamDtos = $webcams->map(static fn ($webcam) => WebcamDto::fromModel($webcam));

            Log::info('Загружена страница веб-камер', ['count' => $webcamDtos->count()]);

            return response()->view('webcams.index', [
                'webcams' => $webcamDtos,
                'title' => 'Веб-камеры Ульяновска онлайн'
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при загрузке страницы веб-камер', ['error' => $e->getMessage()]);

            return response()->view('webcams.index', [
                'webcams' => collect(),
                'title' => 'Веб-камеры Ульяновска онлайн',
                'error' => 'Произошла ошибка при загрузке камер. Попробуйте позже.'
            ]);
        }
    }

    /**
     * Показать одну веб-камеру
     */
    public function show(int $id): Response
    {
        try {
            $webcam = $this->webcamService->getWebcamById($id);

            if (!$webcam) {
                return response()->view('webcams.show', [
                    'webcam' => null,
                    'title' => 'Веб-камера не найдена',
                    'error' => 'Запрашиваемая веб-камера не найдена.'
                ], 404);
            }

            $webcamDto = WebcamDto::fromModel($webcam);

            Log::info('Загружена страница веб-камеры', ['id' => $id]);

            return response()->view('webcams.show', [
                'webcam' => $webcamDto,
                'title' => $webcamDto->getName()
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при загрузке страницы веб-камеры', ['id' => $id, 'error' => $e->getMessage()]);

            return response()->view('webcams.show', [
                'webcam' => null,
                'title' => 'Ошибка',
                'error' => 'Произошла ошибка при загрузке камеры.'
            ], 500);
        }
    }
}
