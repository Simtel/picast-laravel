<?php

declare(strict_types=1);

namespace App\Context\Youtube\Infrastructure\Controller;

use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Resource\VideoFullResource;
use App\Context\Youtube\Domain\Resource\VideoResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ApiVideoController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Video::class, 'video');
    }


    public function index(): AnonymousResourceCollection
    {
        $videos = Video::whereUserId(Auth()->id())->get();

        return VideoResource::collection($videos);
    }


    public function show(Video $video): VideoFullResource
    {
        return new VideoFullResource($video);
    }

}
