<?php

declare(strict_types=1);

namespace App\Http\Controllers\Personal;

use App\Context\Common\Domain\Models\Images;
use App\Http\Controllers\Controller;
use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Storage;

final class ImagesController extends Controller
{
    /**
     * @return Factory|View|Application
     */
    public function create(): Factory|View|Application
    {
        return view('personal.images.create');
    }

    /**
     * @return Factory|View|Application
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): Factory|View|Application
    {
        $userId = Auth()->id();
        $imagesQuery = Images::whereUserId($userId)
            ->orderBy('created_at', 'desc');

        $filter = request()->get('filter');
        if ($filter === 'recent') {
            $imagesQuery->where('created_at', '>=', now()->subWeek());
        } elseif ($filter === 'large') {
            $imagesQuery->where('id', '>', 100);
        }


        $search = request()->string('search');
        if ($search->isNotEmpty()) {
            $imagesQuery->where('filename', 'like', "%{$search}%");
        }

        $images = $imagesQuery->paginate(20)->withQueryString();

        return view('personal.images.index', [
            'images' => $images,
            'currentFilter' => $filter,
            'currentSearch' => $search
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws FileNotFoundException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $file = $request->file('image');
        if ($file instanceof UploadedFile) {
            $imageName = time() . '.' . $file->extension();
            $directory = 'images/'.date('m-Y');
            Storage::disk('s3')->put($directory.'/'. $imageName, File::get($file->path()));
            Images::create(
                [
                    'user_id' => Auth()->id(),
                    'filename' => $imageName,
                    'directory' => $directory,
                    'thumb' => '',
                    'width' => 0,
                    'check' => 1,
                    'disk'  => 's3'
                ]
            );
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
