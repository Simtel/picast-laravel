<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Controller\Api;

/**
 * @OA\Info(
 *     title="Picast Laravel API",
 *     version="1.0.0",
 *     description="API для управления доменами и YouTube видео",
 *     contact={
 *         "name": "API Support",
 *         "email": "support@picast.com"
 *     }
 * )
 * @OA\Server(
 *     url="http://localhost",
 *     description="Локальный сервер разработки"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization",
 *     description="Bearer token для аутентификации"
 * )
 */

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

final class DomainsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Domain::class, 'domain');
    }

    /**
     * Show all user domains
     *
     * Show all user domains without whois history
     */
    #[OA\Get(
        path: '/api/v1/domains',
        summary: 'Get all user domains',
        security: [['sanctum' => []]],
        tags: ['Domains'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of user domains',
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

    /**
     * Show once domain info
     *
     * Show once domain info with whois
     */
    #[OA\Get(
        path: '/api/v1/domains/{id}',
        summary: 'Get domain details',
        security: [['sanctum' => []]],
        tags: ['Domains'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Domain ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Domain details with whois',
                content: new OA\JsonContent(ref: '#/components/schemas/DomainResource')
            ),
            new OA\Response(response: 404, description: 'Domain not found')
        ]
    )]
    public function show(Domain $domain): DomainResource
    {
        return new DomainResource($domain);
    }

    /**
     * Create domain (not implemented)
     */
    public function create(): JsonResponse
    {
        return response()->json(['message' => 'Not action.'], 403);
    }

    /**
     * Edit domain (not implemented)
     */
    #[OA\Put(
        path: '/api/v1/domains/{id}/edit',
        summary: 'Edit domain (not implemented)',
        security: [['sanctum' => []]],
        tags: ['Domains'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Domain ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 403, description: 'Not implemented')
        ]
    )]
    public function edit(Domain $domain): JsonResponse
    {
        return response()->json(['message' => 'Not action.'], 403);
    }

    /**
     * Store new domain
     *
     * Store new domain for authenticated user
     */
    #[OA\Post(
        path: '/api/v1/domains',
        summary: 'Create new domain',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', description: 'Domain name', type: 'string', example: 'example.com')
                ]
            )
        ),
        tags: ["Domains"],
        responses: [
            new OA\Response(response: 200, description: 'Domain created successfully'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function store(DomainRequest $request): JsonResponse
    {
        Domain::create(
            [
                'name' => $request->get('name'),
                'user_id' => Auth::id()
            ]
        );

        return  response()->json(['success' => true]);
    }


    /**
     * Update domain WHOIS info
     *
     * Update domain WHOIS information and expiration date
     *
     * @throws ConnectionException
     * @throws ServerMismatchException
     * @throws WhoisException
     *
     * @return array{success: bool}
     */
    #[OA\Put(
        path: '/api/v1/domains/{id}',
        summary: 'Update domain WHOIS info',
        security: [['sanctum' => []]],
        tags: ['Domains'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Domain ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'WHOIS info updated successfully'),
            new OA\Response(response: 404, description: 'Domain not found')
        ]
    )]
    public function update(Domain $domain): array
    {
        $whois = Whois::loadDomainInfo($domain->name);
        WhoisModel::create(
            [
                'domain_id' => $domain->id,
                'text' => $whois->getResponse()->text,
            ]
        );
        $domain->expire_at = Carbon::createFromTimestamp($whois->expirationDate);
        $domain->updated_at = Carbon::now();
        $domain->save();
        return ['success' => true];
    }


    /**
     * Delete domain
     *
     * Delete domain and associated WHOIS history
     *
     * @return array{success: bool}
     */
    #[OA\Delete(
        path: '/api/v1/domains/{id}',
        summary: 'Delete domain',
        security: [['sanctum' => []]],
        tags: ['Domains'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Domain ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Domain deleted successfully'),
            new OA\Response(response: 404, description: 'Domain not found')
        ]
    )]
    public function destroy(Domain $domain): array
    {
        $domain->delete();
        return ['success' => true];
    }
}
