<?php

declare(strict_types=1);

namespace App\Context\Youtube\Infrastructure\Controller;

use App\Context\Youtube\Application\Service\RefreshVideoFormatsService;
use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Infrastructure\Repository\YouTubeVideoStatusRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\YouTubeUrlRequest;
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
    public function __construct(
        private readonly YouTubeVideoStatusRepository $statusRepository,
        private readonly RefreshVideoFormatsService $refreshVideoFormatsService
    ) {
        $this->authorizeResource(Video::class, 'video');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|Response
     */
    public function index(): View|Factory|Response|Application
    {
        $videos = Video::whereUserId(Auth()->id())->paginate(15);
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
        Video::create(
            [
                'url'       => $request->get('url'),
                'user_id'   => Auth::id(),
                'status_id' => $this->statusRepository->findByCode('new')->id,
            ]
        );

        return redirect()->route('youtube.index');
    }

    /**
     * @throws Exception
     */
    public function refreshFormats(Video $video): Response|Redirector|Application|RedirectResponse
    {
        $this->refreshVideoFormatsService->refresh($video);

        return redirect()->route('youtube.index');
    }

    public function destroy(Video $video): RedirectResponse
    {
        $video->delete();
        return redirect()->route('youtube.index');
    }
}
