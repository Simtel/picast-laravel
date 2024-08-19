<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Images;
use App\Services\Notifications\TelegramChannelNotification;
use File;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Storage;

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
     * @param Request $request
     * @param TelegramChannelNotification $telegramChannelNotification
     * @return RedirectResponse
     */
    public function store(Request $request, TelegramChannelNotification $telegramChannelNotification): RedirectResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $file = $request->file('image');
        if ($file instanceof UploadedFile) {
            $imageName = time() . '.' . $file->extension();
            $directory = 'images/'.date('m-Y');
            Storage::disk('s3')->put($directory.'/'. $imageName, File::get($file));
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

            /*  $telegramChannelNotification->sendImageToChannel(
                  public_path('images') . '/' . $imageName,
                  $imageName
              );*/
        }


        return back()->with('success', 'You have successfully upload image.');
    }

    public function show(Images $image): Factory|View|Application
    {
        return view(
            'personal.images.show',
            [
                'image' => $image,
            ]
        );
    }
}
