<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Whois;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\Rule;

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
        return view('personal.domains', ['domains' => $domains]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|Response
     */
    public function create()
    {
        return view('personal.domains_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|RedirectResponse|Response|Redirector
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => [
                'required',
                'max:225',
                Rule::unique('domains')->where(function ($query) use ($request) {
                    return $query->where('name', $request->get('name'))
                        ->where('user_id', Auth::id());
                })
            ]
        ];
        $request->validate($rules);

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
    public function show($id)
    {
        $domain = Domain::find($id);
        $whois = Whois::where('domain_id', $id)->get();
        return view('personal.domains_show', ['domain' => $domain, 'whois' => $whois]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
