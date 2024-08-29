<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Http\Requests\YouTubeUrlRequest;
use App\Models\YouTubeVideo;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class YouTubeVideoController extends Controller
{
    public function __construct(
    ) {
        $this->authorizeResource(YouTubeVideo::class, 'youTubeVideo');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|Response
     */
    public function index(): View|Factory|Response|Application
    {
        $videos = YouTubeVideo::whereUserId(Auth()->id())->paginate(15);
        return view('personal.youtube_videos.index', ['videos' => $videos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|Response
     */
    public function create(): View|Factory|Response|Application
    {
        return view('personal.youtube_videos.create');
    }

    /**
     * @param YouTubeUrlRequest $request
     * @return Application|RedirectResponse|Response|Redirector
     */
    public function store(YouTubeUrlRequest $request): Response|Redirector|Application|RedirectResponse
    {
        YouTubeVideo::create(
            [
                'url' => $request->get('url'),
                'user_id' => Auth::id()
            ]
        );
        return redirect()->route('youtube.index');
    }
}
