<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteRequest;
use App\Mail\InviteUserNotify;
use App\Models\InviteCode;
use Auth;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class InviteController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('personal.invite');
    }

    /**
     * @param InviteRequest $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function invite(InviteRequest $request): RedirectResponse
    {
        $code = InviteCode::create(
            [
                'created_by' => Auth::id(),
                'code' => str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT)
            ]
        );
        Mail::to($request->get('email'))->send(new InviteUserNotify($code->code, $request->get('name')));
        return redirect()->route('personal');
    }
}
