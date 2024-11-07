<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Controller\Api;

use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Domain\Model\Whois as WhoisModel;
use App\Context\Domains\Infrastructure\Facades\Whois;
use App\Context\Domains\Infrastructure\Request\DomainRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DomainResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DomainsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Domain::class, 'domain');
    }

    /**
     * Show all user domains
     *
     * Show all user domains without whois history
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $domains = Domain::whereUserId(Auth()->id())->get();

        return DomainResource::collection($domains);
    }

    /**
     * Show once domain info
     *
     * Show once domain info with whois
     *
     * @param Domain $domain Domain ID
     * @return DomainResource
     */
    public function show(Domain $domain): DomainResource
    {
        return new DomainResource($domain);
    }

    /**
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        return response()->json(['message' => 'Not action.'], 403);
    }

    /**
     * @param Domain $domain
     * @return JsonResponse
     */
    public function edit(Domain $domain): JsonResponse
    {
        return response()->json(['message' => 'Not action.'], 403);
    }

    /**
     * @param DomainRequest $request
     * @return JsonResponse
     */
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
     * @param Domain $domain
     * @return bool[]
     */
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
        $domain->save();
        return ['success' => true];
    }


    /**
     * @param Domain $domain
     * @return bool[]
     */
    public function destroy(Domain $domain): array
    {
        $domain->delete();
        return ['success' => true];
    }
}
