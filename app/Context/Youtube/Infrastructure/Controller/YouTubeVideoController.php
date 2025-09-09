<?php

declare(strict_types=1);

namespace App\Context\Youtube\Infrastructure\Controller;

use App\Context\Youtube\Application\Service\RefreshVideoFormatsService;
use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Model\VideoDownloadQueue;
use App\Context\Youtube\Domain\Model\VideoFormats;
use App\Context\Youtube\Infrastructure\Repository\YouTubeVideoStatusRepository;
use App\Context\Youtube\Infrastructure\Request\YouTubeUrlRequest;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class YouTubeVideoController extends Controller
{
    public function __construct(
        private readonly YouTubeVideoStatusRepository $statusRepository,
        private readonly RefreshVideoFormatsService $refreshVideoFormatsService
    ) {
        $this->authorizeResource(Video::class, 'video');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        $videos = Video::whereUserId(Auth()->id())->paginate(15);
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
        Video::create(
            [
                'url' => $request->get('url'),
                'user_id' => Auth::id(),
                'status_id' => $this->statusRepository->findByCode('new')->id,
            ]
        );

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

    public function queueDownload(Video $video, Request $request): RedirectResponse
    {
        /** @var VideoFormats $format */
        $format = VideoFormats::where(
            [
                'id' => $request->integer('video_formats'),
                'video_id' => $video->id
            ]
        )->first();


        VideoDownloadQueue::create(['video_id' => $video->id, 'format_id' => $format->getId()]);
        return redirect()->route('youtube.index');
    }
}
