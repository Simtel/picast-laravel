<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Controller;

use App\Context\Domains\Application\Contract\WhoisUpdater;
use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Domain\Model\Whois;
use App\Context\Domains\Infrastructure\Request\DomainRequest;
use App\Http\Controllers\Controller;
use Auth;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class DomainsController extends Controller
{
    public function __construct(
        private readonly WhoisUpdater $whoisUpdater,
    ) {
        $this->authorizeResource(Domain::class, 'domain');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        $domains = Domain::whereUserId(Auth()->id())->paginate(15);
        return view('personal.domains.index', ['domains' => $domains]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view('personal.domains.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DomainRequest $request
     * @return Application|RedirectResponse
     */
    public function store(DomainRequest $request): Application|RedirectResponse
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
     * @return View|Factory|Application
     */
    public function show(Domain $domain): View|Factory|Application
    {
        return view(
            'personal.domains.show',
            [
                'domain' => $domain,
                'whois'  => Whois::whereDomainId($domain->id)->paginate(15)
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Domain $domain
     * @return never
     */
    public function edit(Domain $domain): never
    {
        abort(403);
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
