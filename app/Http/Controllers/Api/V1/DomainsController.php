<?php

namespace App\Http\Controllers\Api\V1;

use App\Facades\Whois;
use App\Http\Controllers\Controller;
use App\Http\Requests\DomainRequest;
use App\Http\Resources\Api\V1\DomainResourse;
use App\Models\Domain;
use App\Models\Whois as WhoisModel;
use App\OpenApi\Parameters\AuthParameters;
use App\OpenApi\Responses\V1\DomainResponse;
use App\OpenApi\Responses\V1\ErrorForbiddenResponse;
use App\OpenApi\Responses\V1\ListDomainsResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
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
    #[OpenApi\Operation(tags: ['domains'])]
    #[OpenApi\Parameters(factory: AuthParameters::class)]
    #[OpenApi\Response(factory: ListDomainsResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: ErrorForbiddenResponse::class, statusCode: 401)]
    public function index(): AnonymousResourceCollection
    {

        $domains = Domain::whereUserId(Auth()->id())->get();

        return DomainResourse::collection($domains);
    }

    /**
     * Show once domain info
     *
     * Show once domain info with whois
     *
     * @param Domain $domain Domain ID
     * @return DomainResourse
     */
    #[OpenApi\Operation(tags: ['domains'])]
    #[OpenApi\Parameters(factory: AuthParameters::class)]
    #[OpenApi\Response(factory: DomainResponse::class)]
    #[OpenApi\Response(factory: ErrorForbiddenResponse::class, statusCode: 401)]
    public function show(Domain $domain): DomainResourse
    {
        return new DomainResourse($domain);
    }

    /**
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        return response()->json(['message' => 'Not action.'], 403);
    }

    /**
     * @return JsonResponse
     */
    public function edit(): JsonResponse
    {
        return response()->json(['message' => 'Not action.'], 403);
    }

    /**
     * @param DomainRequest $request
     * @return array
     */
    public function store(DomainRequest $request): array
    {
        Domain::create(
            [
                'name' => $request->get('name'),
                'user_id' => Auth::id()
            ]
        );

        return ['success' => true];
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
