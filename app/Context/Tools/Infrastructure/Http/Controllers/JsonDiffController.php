<?php

declare(strict_types=1);

namespace App\Context\Tools\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

final class JsonDiffController extends Controller
{
    public function index(): View
    {
        Log::debug('[JsonDiffController.index] рендер страницы сравнения JSON');

        return view('personal.tools.json-diff.index');
    }
}
