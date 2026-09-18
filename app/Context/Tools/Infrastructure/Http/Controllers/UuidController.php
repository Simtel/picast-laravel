<?php

declare(strict_types=1);

namespace App\Context\Tools\Infrastructure\Http\Controllers;

use App\Context\Tools\Application\Service\UuidService;
use App\Context\Tools\Infrastructure\Request\UuidGenerateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

final class UuidController extends Controller
{
    public function __construct(
        private readonly UuidService $uuidService,
    ) {
    }

    public function index(UuidGenerateRequest $request): View
    {
        $version = (string) $request->string('version', 'v4');

        if (!$this->uuidService->hasVersion($version)) {
            $version = 'v4';
        }

        $count = $request->integer('count', 1);

        $result = $this->uuidService->generate($version, $count);

        Log::debug('[UuidController.index] запрос', [
            'version' => $version,
            'count' => $count,
            'generated' => count($result),
        ]);

        return view('personal.tools.uuid.index', [
            'versions' => $this->uuidService->getVersions(),
            'selectedVersion' => $version,
            'count' => $count,
            'result' => $result,
        ]);
    }
}
