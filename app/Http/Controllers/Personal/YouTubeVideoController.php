<?php

namespace App\Http\Controllers\Personal;

use Alaouy\Youtube\Facades\Youtube;
use App\Http\Controllers\Controller;
use App\Http\Requests\YouTubeUrlRequest;
use App\Models\Youtube\VideoFormats;
use App\Models\Youtube\YouTubeVideo;
use App\Services\Youtube\GetVideoFormatsService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class YouTubeVideoController extends Controller
{
    public function __construct(private readonly GetVideoFormatsService $getVideoFormatsService)
    {
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
                'url'     => $request->get('url'),
                'user_id' => Auth::id()
            ]
        );
        return redirect()->route('youtube.index');
    }

    /**
     * @throws Exception
     */
    public function refreshFormats(YouTubeVideo $video): Response|Redirector|Application|RedirectResponse
    {
        $videoId = Youtube::parseVidFromURL($video->url);
        $videoInfo = Youtube::getVideoInfo($videoId);
        $video->title = $videoInfo->snippet->title;
        $video->save();
        $formats = $this->getVideoFormatsService->getVideoFormats($videoId);
        foreach ($formats as $formatDto) {
            $format = new VideoFormats();
            $format->video_id = $video->id;

            $format->format_id = $formatDto->getFormatId();
            $format->format_note = $formatDto->getFormatNote();
            $format->format_ext = $formatDto->getVideoExt();
            $format->vcodec = $formatDto->getVCodec();
            $format->resolution = $formatDto->getResolution();
            $format->save();
        }
        return redirect()->route('youtube.index');
    }
}
