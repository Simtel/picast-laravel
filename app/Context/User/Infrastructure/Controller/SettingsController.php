<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Controller;

use App\Context\User\Infrastructure\Request\ChangePasswordRequest;
use App\Context\User\Infrastructure\Request\UpdateProfileRequest;
use App\Http\Controllers\Controller;
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

final class SettingsController extends Controller
{
    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): Factory|View|Application
    {
        $user = $request->user();
        $tokens = $user->tokens ?? collect();
        return view('personal.settings', ['tokens' => $tokens, 'user' => $user]);
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

        $token = $request->user()->createToken('api-token');

        return redirect()->route('settings')
            ->with('success', 'Токен создан: <code>' . e($token->plainTextToken) . '</code> — скопируйте его сейчас, он больше не будет показан.');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteToken(int $id, Request $request): RedirectResponse
    {
        $request->user()?->tokens()->where('id', $id)->delete();

        return redirect()->route('settings')->with('success', 'Токен удалён.');
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
