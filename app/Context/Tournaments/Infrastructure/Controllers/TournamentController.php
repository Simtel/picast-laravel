<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Infrastructure\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\View\Factory;
use App\Context\Tournaments\Application\Services\DancemanagerScraper;
use Illuminate\View\View;

class TournamentController extends Controller
{
    protected DancemanagerScraper $scraper;

    public function __construct(DancemanagerScraper $scraper)
    {
        $this->scraper = $scraper;
    }

    /**
     * @throws GuzzleException
     */
    public function index(): Factory|\Illuminate\Contracts\View\View|View
    {
        $tournaments = $this->scraper->getTournaments(true);
        return view('tournaments.index', compact('tournaments'));
    }
}
