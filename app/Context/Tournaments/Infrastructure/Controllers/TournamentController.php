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
    public const int GROUPS_PER_PAGE = 25;

    public function index(Request $request): Factory|\Illuminate\Contracts\View\View|View
    {
        $query = Tournament::query();
        $query->whereDate('date', '>', Carbon::now());

        $sortBy = $request->get('sort_by', 'date');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $cities = Tournament::whereDate('date', '>', Carbon::now())->pluck('city')->unique()->filter()->sort();
        $selectedCity = $request->get('city');

        if ($selectedCity) {
            $query->where('city', $selectedCity);
        }

        $tournaments = $query->paginate(10);

        return view('tournaments.index', compact('tournaments', 'sortBy', 'sortOrder', 'cities', 'selectedCity'));
    }

    public function show(Request $request, int $id): Factory|\Illuminate\Contracts\View\View|View
    {
        $tournament = Tournament::findOrFail($id);

        $groupsQuery = $tournament->groups();


        $search = $request->input('search', '');
        if (is_string($search)) {
            $groupsQuery->where('name', 'like', "%{$search}%");
        }

        $number = $request->integer('number', 0);
        if ($number > 0) {
            $groupsQuery->where('number', (int)$number);
        }

        $sortBy = $request->get('sort_by', 'number');
        $sortOrder = $request->get('sort_order', 'asc');
        $groupsQuery->orderBy($sortBy, $sortOrder);

        $groups = $groupsQuery->paginate(self::GROUPS_PER_PAGE)->appends($request->query());

        return view('tournaments.show', compact('tournament', 'groups', 'sortBy', 'sortOrder'));
    }
}
