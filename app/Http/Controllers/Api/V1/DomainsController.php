<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DomainResourse;
use App\Models\Domain;
use App\OpenApi\Responses\V1\DomainResponse;
use App\OpenApi\Responses\V1\ErrorForbiddenResponse;
use App\OpenApi\Responses\V1\ListDomainsResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class DomainsController extends Controller
{
    /**
     * Show all user domains
     *
     * Show all user domains without whois history
     *
     * @return AnonymousResourceCollection
     */
    #[OpenApi\Operation]
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
    #[OpenApi\Operation]
    #[OpenApi\Response(factory: DomainResponse::class)]
    public function show(Domain $domain): DomainResourse
    {
        return new DomainResourse($domain);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|Response
     */
    public function create()
    {

    }


    public function store(DomainRequest $request)
    {

    }


    public function edit(int $id)
    {
        return null;
    }


    public function update(Request $request, Domain $domain)
    {

    }


    public function destroy(Domain $domain)
    {
        $domain->delete();
        return ['success' => true];
    }
}
