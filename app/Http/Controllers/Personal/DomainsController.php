<?php

namespace App\Http\Controllers\Personal;

use App\Contracts\Services\Domains\WhoisUpdater;
use App\Http\Controllers\Controller;
use App\Http\Requests\DomainRequest;
use App\Models\Domain;
use App\Models\Whois;
use Auth;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;


class DomainsController extends Controller
{

    private WhoisUpdater $whoisUpdater;

    public function __construct(WhoisUpdater $whoisUpdater)
    {
        $this->authorizeResource(Domain::class, 'domain');
        $this->whoisUpdater = $whoisUpdater;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|Response
     */
    public function index(): View|Factory|Response|Application
    {
        $domains = Domain::whereUserId(Auth()->id())->paginate(15);
        return view('personal.domains.index', ['domains' => $domains]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|Response
     */
    public function create(): View|Factory|Response|Application
    {
        return view('personal.domains.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DomainRequest $request
     * @return Application|RedirectResponse|Response|Redirector
     */
    public function store(DomainRequest $request): Response|Redirector|Application|RedirectResponse
    {
        Domain::create(
            [
                'name' => $request->get('name'),
                'user_id' => Auth::id()
            ]
        );
        return redirect()->route('domains.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Domain $domain
     * @return Application|Factory|View|Response
     */
    public function show(Domain $domain): View|Factory|Response|Application
    {
        return view('personal.domains.show', ['domain' => $domain, 'whois' => Whois::whereDomainId($domain->id)->paginate(15)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response|null
     */
    public function edit(int $id): ?Response
    {
        return null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Domain $domain
     * @return RedirectResponse
     */
    public function update(Domain $domain): RedirectResponse
    {
        $this->whoisUpdater->update($domain);
        return redirect()->route('domains.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Domain $domain
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(Domain $domain): RedirectResponse
    {
        $domain->delete();
        return redirect()->route('domains.index');
    }
}
