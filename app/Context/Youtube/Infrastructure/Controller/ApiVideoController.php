<?php

declare(strict_types=1);

namespace App\Context\Youtube\Infrastructure\Controller;

use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Resource\VideoFullResource;
use App\Context\Youtube\Domain\Resource\VideoResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

final class ApiVideoController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Video::class, 'video');
    }

    /**
     * Show all user videos
     */
    #[OA\Get(
        path: '/api/v1/video',
        summary: 'Get all user videos',
        security: [['sanctum' => []]],
        tags: ['Videos'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of user videos',
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

    /**
     * Show video details
     */
    #[OA\Get(
        path: '/api/v1/video/{id}',
        summary: 'Get video details',
        security: [['sanctum' => []]],
        tags: ['Videos'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Video ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Video details',
                content: new OA\JsonContent(ref: '#/components/schemas/VideoFullResource')
            ),
            new OA\Response(response: 404, description: 'Video not found')
        ]
    )]
    public function show(Video $video): VideoFullResource
    {
        return new VideoFullResource($video);
    }

    /**
     * Create video (not implemented)
     */
    public function create(): JsonResponse
    {
        return response()->json(['message' => 'Not action.'], 403);
    }

    /**
     * Store new video (not implemented)
     */
    #[OA\Post(
        path: '/api/v1/video',
        summary: 'Create new video (not implemented)',
        security: [['sanctum' => []]],
        tags: ['Videos'],
        responses: [
            new OA\Response(response: 403, description: 'Not implemented')
        ]
    )]
    public function store(): JsonResponse
    {
        return response()->json(['message' => 'Not action.'], 403);
    }

    /**
     * Update video (not implemented)
     */
    #[OA\Put(
        path: '/api/v1/video/{id}',
        summary: 'Update video (not implemented)',
        security: [['sanctum' => []]],
        tags: ['Videos'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Video ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 403, description: 'Not implemented')
        ]
    )]
    public function update(): JsonResponse
    {
        return response()->json(['message' => 'Not action.'], 403);
    }

    /**
     * Delete video (not implemented)
     */
    #[OA\Delete(
        path: '/api/v1/video/{id}',
        summary: 'Delete video (not implemented)',
        security: [['sanctum' => []]],
        tags: ['Videos'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Video ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 403, description: 'Not implemented')
        ]
    )]
    public function destroy(): JsonResponse
    {
        return response()->json(['message' => 'Not action.'], 403);
    }
}
