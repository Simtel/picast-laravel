<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Controller;

use App\Context\User\Application\Service\ApiTokenService;
use App\Context\User\Application\Service\ChangePasswordService;
use App\Context\User\Application\Service\ProfileUpdateService;
use App\Context\User\Domain\Model\User;
use App\Context\User\Infrastructure\Request\ChangePasswordRequest;
use App\Context\User\Infrastructure\Request\UpdateProfileRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

final class SettingsController extends Controller
{
    public function __construct(
        private readonly ApiTokenService $apiTokenService,
        private readonly ChangePasswordService $changePasswordService,
        private readonly ProfileUpdateService $profileUpdateService,
    ) {
    }

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function index(Request $request): Factory|View|Application
    {
        /** @var User $user */
        $user = $request->user();

        return view('personal.settings', [
            'tokens' => $user->tokens,
            'user' => $user,
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function token(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $plainTextToken = $this->apiTokenService->create($user);

        return redirect()->route('settings')
            ->with('success', 'Токен создан: <code>' . e($plainTextToken) . '</code> — скопируйте его сейчас, он больше не будет показан.');
    }

    /**
     * @param PersonalAccessToken $token
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteToken(PersonalAccessToken $token, Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->apiTokenService->delete($user, $token->id);

        return redirect()->route('settings')->with('success', 'Токен удалён.');
    }

    /**
     * @param ChangePasswordRequest $request
     * @return RedirectResponse
     */
    public function password(ChangePasswordRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->changePasswordService->change($user, $request->string('password')->toString());

        return redirect()->route('settings')->with('success', 'Пароль успешно обновлен!');
    }

    /**
     * @param UpdateProfileRequest $request
     * @return RedirectResponse
     */
    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->profileUpdateService->update($user, [
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'birth_date' => $request->date('birth_date')?->format('Y-m-d'),
        ]);

        return redirect()->route('settings')->with('success', 'Профиль успешно обновлен!');
    }
}
