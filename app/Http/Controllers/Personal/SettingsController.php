<?php

declare(strict_types=1);

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Auth;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $user = $request->user();
        $token = $user?->api_token;
        return view('personal.settings', ['token' => $token, 'user' => $user]);
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
        $user->forceFill([
            'password' => Hash::make($request->string('password')->toString())
        ])->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));
        return redirect()->route('settings')->with('success', 'Пароль успешно обновлен!');
    }

    /**
     * @param UpdateProfileRequest $request
     * @return RedirectResponse
     */
    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user === null) {
            throw new BadRequestException('Пользователь не найден');
        }

        $user->update([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'birth_date' => $request->date('birth_date')?->format('Y-m-d'),
        ]);

        return redirect()->route('settings')->with('success', 'Профиль успешно обновлен!');
    }
}
