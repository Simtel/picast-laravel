<?php

declare(strict_types=1);

namespace App\Context\Tools\Infrastructure\Http\Controllers;

use App\Context\Tools\Application\Service\HashService;
use App\Context\Tools\Infrastructure\Request\HashRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

final class HashController extends Controller
{
    public function __construct(
        private readonly HashService $hashService,
    ) {
    }

    public function index(HashRequest $request): View
    {
        $algorithm = $request->string('algorithm', 'md5')->toString();

        if (!$this->hashService->hasAlgorithm($algorithm)) {
            $algorithm = 'md5';
        }

        $text = $request->string('text')->toString();
        $result = $text !== '' ? $this->hashService->hash($algorithm, $text) : null;

        Log::debug('[HashController.index] запрос обработан', [
            'algorithm' => $algorithm,
            'textLength' => strlen($text),
            'hasResult' => $result !== null,
        ]);

        return view('personal.tools.hash.index', [
            'algorithms' => $this->hashService->getAlgorithms(),
            'selectedAlgorithm' => $algorithm,
            'text' => $text,
            'result' => $result,
        ]);
    }
}
