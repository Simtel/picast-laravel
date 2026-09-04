<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Controller;

use App\Context\User\Application\Contracts\Services\InviteUserService;
use App\Context\User\Infrastructure\Request\InviteRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class InviteController extends Controller
{
    public function __construct(
        private readonly InviteUserService $inviteUserService,
    ) {
        $this->middleware(['can:invite user']);
    }

    /**
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return view('personal.invite');
    }

    public function invite(InviteRequest $request): RedirectResponse
    {
        $this->inviteUserService->invite(
            (int)$request->user()->id,
            $request->string('name')->toString(),
            $request->string('email')->toString(),
        );

        return redirect()->route('personal');
    }
}
