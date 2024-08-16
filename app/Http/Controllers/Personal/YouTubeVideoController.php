<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\YouTubeVideo;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

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
}
