<?php

declare(strict_types=1);

namespace App\Context\Tools\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

final class ColorController extends Controller
{
    public function index(): View
    {
        Log::debug('[ColorController.index] рендер страницы конвертера цветов');

        return view('personal.tools.color.index');
    }
}
