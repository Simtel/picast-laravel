<?php

namespace App\Http\Controllers\Personal;

use App\Facades\Whois;
use App\Http\Controllers\Controller;
use App\Http\Requests\DomainRequest;
use App\Models\Domain;
use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;


class DomainsController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|Response
     */
    public function index()
    {
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
     * @param int $id
     * @return Application|Factory|View|Response
     */
    public function show(int $id)
    {
        $domain = Domain::find($id);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $domain = Domain::find($id);
        $whois = Whois::loadDomainInfo($domain->name);
        \App\Models\Whois::create(
            [
                'domain_id' => $domain->id,
                'text' => $whois->getResponse()->text,
            ]
        );
        $domain->expire_at = Carbon::createFromTimestamp($whois->getExpirationDate());
        $domain->save();
        return redirect()->route('domains.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        Domain::destroy([$id]);
        return redirect()->route('domains.index');
    }
}
