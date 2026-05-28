<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Http\Controllers\Api;

use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Domain\Model\Whois as WhoisModel;
use App\Context\Domains\Domain\Resource\DomainResource;
use App\Context\Domains\Infrastructure\Facades\Whois;
use App\Context\Domains\Infrastructure\Request\DomainRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(
 *     name="Domains",
 *     description="API для управления доменами"
 * )
 */
final class DomainsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Domain::class, 'domain');
    }

    #[OA\Get(
        path: '/api/v1/domains',
        summary: 'Получить все домены пользователя',
        security: [['sanctum' => []]],
        tags: ['Domains'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список доменов пользователя',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/DomainResource')
                )
            )
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        $domains = Domain::whereUserId(Auth()->id())->get();

        return DomainResource::collection($domains);
    }

    #[OA\Get(
        path: '/api/v1/domains/{id}',
        summary: 'Получить детали домена с WHOIS',
        security: [['sanctum' => []]],
        tags: ['Domains'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID домена',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Информация о домене с историей WHOIS',
                content: new OA\JsonContent(ref: '#/components/schemas/DomainResource')
            ),
            new OA\Response(response: 404, description: 'Домен не найден')
        ]
    )]
    public function show(Domain $domain): DomainResource
    {
        return new DomainResource($domain);
    }

    #[OA\Post(
        path: '/api/v1/domains',
        summary: 'Создать новый домен',
        security: [['sanctum' => []]],
        tags: ['Domains'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', description: 'Имя домена', type: 'string', example: 'example.com')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Домен успешно создан'),
            new OA\Response(response: 422, description: 'Ошибка валидации')
        ]
    )]
    public function store(DomainRequest $request): JsonResponse
    {
        Domain::create([
            'name' => $request->get('name'),
            'user_id' => Auth::id(),
        ]);

        return response()->json(['success' => true]);
    }

    #[OA\Put(
        path: '/api/v1/domains/{id}',
        summary: 'Обновить WHOIS информацию домена',
        security: [['sanctum' => []]],
        tags: ['Domains'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID домена',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'WHOIS информация успешно обновлена'),
            new OA\Response(response: 404, description: 'Домен не найден')
        ]
    )]
    public function update(Domain $domain): JsonResponse
    {
        try {
            $whois = Whois::loadDomainInfo($domain->name);
            WhoisModel::create([
                'domain_id' => $domain->id,
                'text' => $whois->getResponse()->text,
            ]);
            $domain->expire_at = Carbon::createFromTimestamp($whois->expirationDate);
            $domain->updated_at = Carbon::now();
            $domain->save();

            return response()->json(['success' => true]);
        } catch (ConnectionException|ServerMismatchException|WhoisException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении WHOIS информации: ' . $e->getMessage(),
            ], 500);
        }
    }

    #[OA\Delete(
        path: '/api/v1/domains/{id}',
        summary: 'Удалить домен',
        security: [['sanctum' => []]],
        tags: ['Domains'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID домена',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Домен успешно удален'),
            new OA\Response(response: 404, description: 'Домен не найден')
        ]
    )]
    public function destroy(Domain $domain): JsonResponse
    {
        $domain->delete();

        return response()->json(['success' => true]);
    }
}
