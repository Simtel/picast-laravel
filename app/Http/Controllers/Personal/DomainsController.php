<?php

namespace App\Http\Controllers\Personal;

use App\Contracts\Services\Domains\WhoisUpdater;
use App\Http\Controllers\Controller;
use App\Http\Requests\DomainRequest;
use App\Models\Domain;
use Auth;
use Carbon\Carbon;
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
    public function index()
    {
        $days2 = [];
        $domains = Domain::all();
        foreach ($domains as $domain) {
            $expire_at = new Carbon($domain->expire_at);
            $days = $expire_at->diffInDays(Carbon::now());
            $days2[] = $days;
        }
        dd($days2);
        $domains = Domain::whereUserId(Auth()->id())->get();
        return view('personal.domains.index', ['domains' => $domains]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|Response
     */
    public function create()
    {
        return view('personal.domains.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DomainRequest $request
     * @return Application|RedirectResponse|Response|Redirector
     */
    public function store(DomainRequest $request)
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
    public function show(Domain $domain)
    {
        return view('personal.domains.show', ['domain' => $domain, 'whois' => $domain->whois]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
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
