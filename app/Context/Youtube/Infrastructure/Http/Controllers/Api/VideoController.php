<?php

declare(strict_types=1);

namespace App\Context\Youtube\Infrastructure\Http\Controllers\Api;

use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Model\VideoDownloadQueue;
use App\Context\Youtube\Domain\Model\VideoFormats;
use App\Context\Youtube\Domain\Resource\VideoFullResource;
use App\Context\Youtube\Domain\Resource\VideoResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(
 *     name="Videos",
 *     description="API для управления YouTube видео"
 * )
 */
final class VideoController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Video::class, 'video');
    }

    #[OA\Get(
        path: '/api/v1/videos',
        summary: 'Получить все видео пользователя',
        security: [['sanctum' => []]],
        tags: ['Videos'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список видео пользователя',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/VideoResource')
                )
            )
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        $videos = Video::whereUserId(Auth()->id())->get();

        return VideoResource::collection($videos);
    }

    #[OA\Get(
        path: '/api/v1/videos/{id}',
        summary: 'Получить детали видео',
        security: [['sanctum' => []]],
        tags: ['Videos'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID видео',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Информация о видео',
                content: new OA\JsonContent(ref: '#/components/schemas/VideoFullResource')
            ),
            new OA\Response(response: 404, description: 'Видео не найдено')
        ]
    )]
    public function show(Video $video): VideoFullResource
    {
        return new VideoFullResource($video);
    }

    #[OA\Post(
        path: '/api/v1/videos',
        summary: 'Добавить видео в очередь на скачивание',
        security: [['sanctum' => []]],
        tags: ['Videos'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['url'],
                properties: [
                    new OA\Property(property: 'url', description: 'URL YouTube видео', type: 'string', example: 'https://youtube.com/watch?v=VIDEO_ID')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Видео добавлено в очередь'),
            new OA\Response(response: 422, description: 'Ошибка валидации')
        ]
    )]
    public function store(\Illuminate\Http\Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'required|url',
        ]);

        /** @var array{url: string} $validated */

        // Получаем репозиторий статуса
        $statusRepository = app(\App\Context\Youtube\Infrastructure\Repository\YouTubeVideoStatusRepository::class);
        $status = $statusRepository->findByCode('new');

        $video = Video::create([
            'url' => $validated['url'],
            'user_id' => Auth::id(),
            'status_id' => $status->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => new VideoResource($video),
        ]);
    }

    #[OA\Put(
        path: '/api/v1/videos/{id}',
        summary: 'Обновить информацию о видео',
        security: [['sanctum' => []]],
        tags: ['Videos'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID видео',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Видео обновлено'),
            new OA\Response(response: 404, description: 'Видео не найдено')
        ]
    )]
    public function update(\Illuminate\Http\Request $request, Video $video): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
        ]);

        $video->update($validated);

        return response()->json([
            'success' => true,
            'data' => new VideoFullResource($video),
        ]);
    }

    #[OA\Post(
        path: '/api/v1/videos/{id}/queue-download',
        summary: 'Добавить видео в очередь скачивания',
        security: [['sanctum' => []]],
        tags: ['Videos'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID видео',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['format_id'],
                properties: [
                    new OA\Property(property: 'format_id', description: 'ID формата видео', type: 'integer', example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Добавлено в очередь'),
            new OA\Response(response: 422, description: 'Ошибка валидации')
        ]
    )]
    public function queueDownload(\Illuminate\Http\Request $request, Video $video): JsonResponse
    {
        $validated = $request->validate([
            'format_id' => 'required|integer|exists:video_formats,id',
        ]);

        /** @var array{format_id: int} $validated */

        // Проверяем, что формат принадлежит видео
        $format = VideoFormats::where([
            'id' => $validated['format_id'],
            'video_id' => $video->id,
        ])->firstOrFail();

        VideoDownloadQueue::create([
            'video_id' => $video->id,
            'format_id' => $format->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Видео добавлено в очередь скачивания',
        ]);
    }

    #[OA\Delete(
        path: '/api/v1/videos/{id}',
        summary: 'Удалить видео',
        security: [['sanctum' => []]],
        tags: ['Videos'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID видео',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Видео удалено'),
            new OA\Response(response: 404, description: 'Видео не найдено')
        ]
    )]
    public function destroy(Video $video): JsonResponse
    {
        $video->delete();

        return response()->json(['success' => true]);
    }
}
