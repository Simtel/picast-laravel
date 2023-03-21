<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class SettingsController extends Controller
{
    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): Factory|View|Application
    {
        $token = $request->user()?->api_token;
        return view('personal.settings', ['token' => $token]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function token(Request $request): RedirectResponse
    {
        if ($request->user() === null) {
            return redirect()->route('login');
        }

        $request->user()->forceFill([
            'api_token' => Str::random(60),
        ])->save();

        return redirect()->route('settings');
    }

    /**
     * @param ChangePasswordRequest $request
     * @return RedirectResponse
     */
    public function password(ChangePasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user === null) {
            throw new BadRequestException('Not found user');
        }
        $user->fill(
            [
                'password' => bcrypt($request->string('new_password'))
            ]
        )->save();

        return redirect()->route('settings');
    }
}
