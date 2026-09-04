<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Controller;

use App\Context\Common\Domain\Models\Images;
use App\Context\User\Application\Query\ImageListingQuery;
use App\Context\User\Application\Service\ImageUploadService;
use App\Context\User\Infrastructure\Request\Personal\Images\Store;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ImagesController extends Controller
{
    public function __construct(
        private readonly ImageUploadService $imageUploadService,
        private readonly ImageListingQuery $imageListingQuery,
    ) {
    }

    /**
     * @return Factory|View|Application
     */
    public function create(): Factory|View|Application
    {
        return view('personal.images.create');
    }

    /**
     * @return Factory|View|Application
     */
    public function index(Request $request): Factory|View|Application
    {
        $images = $this->imageListingQuery->handle(
            (int)$request->user()->id,
            $request->query()
        );

        return view('personal.images.index', ['images' => $images]);
    }

    public function store(Store $request): RedirectResponse
    {
        $file = $request->file('image');

        if ($file instanceof \Illuminate\Http\UploadedFile) {
            $this->imageUploadService->upload($file, (int)$request->user()->id);
        }

        return back()->with('success', 'You have successfully upload image.');
    }

    public function show(Images $image): Factory|View|Application
    {
        $image->incrementViews();

        return view(
            'personal.images.show',
            [
                'image' => $image,
            ]
        );
    }
}
