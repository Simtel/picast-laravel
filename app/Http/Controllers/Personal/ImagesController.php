<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Images;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class ImagesController extends Controller
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
     */
    public function index(): Factory|View|Application
    {
        $images = Images::whereUserId(Auth()->id())->paginate(15);
        return view('personal.images.index', ['images' => $images]);
    }

    /**
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $file = $request->file('image');
        if ($file instanceof UploadedFile) {
            $imageName = time().'.'.$file->extension();

            $file->move(public_path('images'), $imageName);
            Images::create(
                [
                    'user_id' => Auth()->id(),
                    'filename' => $imageName,
                    'thumb' => '',
                    'width' => 0,
                    'check' => 1
                ]
            );
        }


        return back()->with('success', 'You have successfully upload image.');
    }
}
