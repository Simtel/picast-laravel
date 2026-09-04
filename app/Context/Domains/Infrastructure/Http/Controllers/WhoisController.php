<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Http\Controllers;

use App\Context\Domains\Application\Contract\WhoisService;
use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Infrastructure\Request\DeleteOldWhois;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

final class WhoisController extends Controller
{
    public function __construct(
        private readonly WhoisService $whoisService,
    ) {
    }

    /**
     * @param Domain $domain
     * @param DeleteOldWhois $deleteOldWhois
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function deleteOldWhois(Domain $domain, DeleteOldWhois $deleteOldWhois): RedirectResponse
    {
        $this->authorize('update', $domain);

        $sub = $deleteOldWhois->string('delete_old_whois')->toString();
        $this->whoisService->deleteOldWhois($domain, $sub);

        Log::debug('[WhoisController.deleteOldWhois] очистка whois для домена', [
            'domain' => $domain->getId(),
            'sub' => $sub,
        ]);

        return redirect()->route('domains.show', ['domain' => $domain->getId()]);
    }
}
