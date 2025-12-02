<?php

declare(strict_types=1);

namespace App\Http\Controllers\Personal;

use App\Context\Common\Domain\Models\InviteCode;
use App\Context\User\Infrastructure\Mail\InviteUserNotify;
use App\Http\Controllers\Controller;
use App\Http\Requests\InviteRequest;
use Auth;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

final class InviteController extends Controller
{
    /**
     * InviteController constructor.
     */
    public function __construct()
    {
        $this->middleware(['can:invite user']);
    }


    /**
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
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
                'code'       => str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT)
            ]
        );
        Mail::to($request->get('email'))->send(
            new InviteUserNotify($code->code, $request->string('name')->toString())
        );
        return redirect()->route('personal');
    }
}
