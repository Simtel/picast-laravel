<?php

declare(strict_types=1);

namespace App\Context\Youtube\Infrastructure\Controller;

use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Resource\VideoResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ApiVideoController extends Controller
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

}
