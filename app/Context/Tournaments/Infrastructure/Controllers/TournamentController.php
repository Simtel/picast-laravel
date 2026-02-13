<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Infrastructure\Controllers;

use App\Context\Tournaments\Domain\Model\Tournament;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function index(Request $request): Factory|\Illuminate\Contracts\View\View|View
    {
        $query = Tournament::query();


        $sortBy = $request->get('sort_by', 'date');
        $sortOrder = $request->get('sort_order', 'asc');

        $query->orderBy($sortBy, $sortOrder);
        $query->whereDate('date', '>', Carbon::now());

        $tournaments = $query->paginate(10);

        return view('tournaments.index', compact('tournaments', 'sortBy', 'sortOrder'));
    }
}
