<?php

declare(strict_types=1);

namespace App\Context\Youtube\Infrastructure\Controller;

use App\Context\Youtube\Application\Query\VideoListingQuery;
use App\Context\Youtube\Application\Service\RefreshVideoFormatsService;
use App\Context\Youtube\Application\Service\VideoActionService;
use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Infrastructure\Request\QueueDownloadRequest;
use App\Context\Youtube\Infrastructure\Request\YouTubeUrlRequest;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class YouTubeVideoController extends Controller
{
    public function __construct(
        private readonly VideoActionService $videoActionService,
        private readonly VideoListingQuery $videoListingQuery,
        private readonly RefreshVideoFormatsService $refreshVideoFormatsService
    ) {
        $this->authorizeResource(Video::class, 'video');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $videos = $this->videoListingQuery->listByUser((int)$request->user()->id, paginate: true);
        return view('personal.youtube_videos.index', ['videos' => $videos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view('personal.youtube_videos.create');
    }

    /**
     * @param YouTubeUrlRequest $request
     * @return Application|RedirectResponse
     */
    public function store(YouTubeUrlRequest $request): Application|RedirectResponse
    {
        $this->videoActionService->create((int)$request->user()->id, $request->string('url')->toString());

        return redirect()->route('youtube.index');
    }

    /**
     * @throws Exception
     */
    public function refreshFormats(Video $video): Application|RedirectResponse
    {
        $this->refreshVideoFormatsService->refresh($video);

        return redirect()->route('youtube.index');
    }

    public function destroy(Video $video): RedirectResponse
    {
        $video->delete();
        return redirect()->route('youtube.index');
    }

    public function queueDownload(Video $video, QueueDownloadRequest $request): RedirectResponse
    {
        $this->videoActionService->queueDownload($video, $request->integer('format_id'));

        return redirect()->route('youtube.index');
    }
}
