<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Controller;

use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Infrastructure\Request\DeleteOldWhois;
use App\Context\Domains\Infrastructure\Facades\WhoisService;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;

class WhoisController extends Controller
{
    /**
     * @param int $id
     * @param DeleteOldWhois $deleteOldWhois
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function deleteOldWhois(int $id, DeleteOldWhois $deleteOldWhois): RedirectResponse
    {
        $domain = Domain::find($id);
        if ($domain === null) {
            return redirect()->route('personal');
        }
        $this->authorize('update', $domain);
        WhoisService::deleteOldWhois($deleteOldWhois->string('delete_old_whois')->toString());
        return redirect()->route('domains.show', ['domain' => $domain->id]);
    }
}