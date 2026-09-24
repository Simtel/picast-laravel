<?php

declare(strict_types=1);

namespace App\Context\Tools\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

final class TextDiffController extends Controller
{
    public function index(): View
    {
        Log::debug('[TextDiffController.index] рендер страницы сравнения текстов');

        return view('personal.tools.text-diff.index');
    }
}
