<?php

declare(strict_types=1);

namespace App\Context\Tools\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class ToolsController extends Controller
{
    public function index(): View
    {
        return view('personal.tools.index');
    }
}
