<?php

namespace App\Http\Controllers\Personal;

use App\Facades\Domains\WhoisService;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteOldWhois;
use App\Models\Domains\Domain;
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
        WhoisService::deleteOldWhois($deleteOldWhois->string('delete_old_whois'));
        return redirect()->route('domains.show', ['domain' => $domain->id]);
    }
}
