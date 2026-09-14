<?php

declare(strict_types=1);

namespace App\Context\Tools\Infrastructure\Http\Controllers;

use App\Context\Tools\Application\Service\RandomStringService;
use App\Context\Tools\Infrastructure\Request\RandomStringGenerateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class RandomStringController extends Controller
{
    public function __construct(
        private readonly RandomStringService $randomStringService,
    ) {
    }

    public function index(RandomStringGenerateRequest $request): View
    {
        $length = $request->integer('length', 16);
        $uppercase = $request->boolean('uppercase', true);
        $lowercase = $request->boolean('lowercase', true);
        $digits = $request->boolean('digits', true);
        $symbols = $request->boolean('symbols', false);

        $result = $this->randomStringService->generate($length, $uppercase, $lowercase, $digits, $symbols);

        return view('personal.tools.random-string.index', [
            'length' => $length,
            'uppercase' => $uppercase,
            'lowercase' => $lowercase,
            'digits' => $digits,
            'symbols' => $symbols,
            'result' => $result,
        ]);
    }
}
