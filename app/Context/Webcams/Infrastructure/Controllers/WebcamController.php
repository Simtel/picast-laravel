<?php

declare(strict_types=1);

namespace App\Context\Webcams\Infrastructure\Controllers;

use App\Context\Webcams\Application\Contracts\WebcamServiceInterface;
use App\Context\Webcams\Application\Dto\CreateWebcamDto;
use App\Context\Webcams\Application\Dto\UpdateWebcamDto;
use App\Context\Webcams\Application\Dto\WebcamDto;
use App\Context\Webcams\Domain\Model\Webcam;
use App\Context\Webcams\Infrastructure\Resources\WebcamResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class WebcamController extends Controller
{
    public function __construct(
        private readonly WebcamServiceInterface $webcamService
    ) {
    }

    /**
     * Получить все активные веб-камеры
     */
    public function index(): AnonymousResourceCollection
    {
        try {
            $webcams = $this->webcamService->getAllActiveWebcams();

            $webcamDtos = $webcams->map(static fn ($webcam) => WebcamDto::fromModel($webcam));

            Log::info('Получен список веб-камер', ['count' => $webcamDtos->count()]);

            return WebcamResource::collection($webcamDtos);
        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка веб-камер', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Получить веб-камеру по ID
     */
    public function show(Webcam $webcam): WebcamResource|Response
    {
        return new WebcamResource($webcam);
    }

    /**
     * Создать новую веб-камеру
     */
    public function store(Request $request): WebcamResource
    {
        try {
            $dto = CreateWebcamDto::fromArray($request->all());

            $webcam = $this->webcamService->createWebcam($dto);
            $webcamDto = WebcamDto::fromModel($webcam);

            return new WebcamResource($webcamDto);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Ошибка при создании веб-камеры', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Обновить веб-камеру
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $dto = UpdateWebcamDto::fromArray($request->all());

            $webcam = $this->webcamService->updateWebcam($id, $dto);

            if (!$webcam) {
                return new JsonResponse(['message' => 'Веб-камера не найдена'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['success' => true]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении веб-камеры', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Удалить веб-камеру
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->webcamService->deleteWebcam($id);

            if (!$deleted) {
                return new JsonResponse(['message' => 'Веб-камера не найдена'], Response::HTTP_NOT_FOUND);
            }

            return  response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Ошибка при удалении веб-камеры', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Получить веб-камеры по локации
     */
    public function byLocation(Request $request): AnonymousResourceCollection
    {
        try {
            $request->validate([
                'location' => 'required|string|max:255',
            ]);

            $location = $request->input('location');
            $webcams = $this->webcamService->getWebcamsByLocation($location);

            $webcamDtos = $webcams->map(static fn ($webcam) => WebcamDto::fromModel($webcam));

            return WebcamResource::collection($webcamDtos);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error(
                'Ошибка при поиске веб-камер по локации',
                ['location' => $request->input('location'), 'error' => $e->getMessage()]
            );
            throw $e;
        }
    }
}
