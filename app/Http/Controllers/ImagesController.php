<?php

namespace App\Http\Controllers;

use App\Models\Images;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use URL;

class ImagesControlleryt extends Controller
{


    /**
     * @return Factory|View
     */
    public function gallery()
    {
        $images = Images::where('check', 1)->orderBy('id', 'desc')->take(40)->paginate(15);
        return view('gallery.gallery', ['images' => $images]);
    }

    /**
     * Страница с изображением
     * @param $id
     * @return Factory|View
     */
    public function show($id)
    {
        $image = Images::find($id);
        return view('gallery.showimage', ['image' => $image]);
    }

    /**
     * Cохранение изображения
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'upload_file' => 'required'
        ]);
        $files = $request->file('upload_file');
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $image = new Images();
                if($image->saveUploadFile($file)) {
                    return redirect()->route('home');
                }
            }
        }
    }

    /**
     * Получить последние изображения
     * @param Request $request
     * @return Images[]|Collection
     */
    public function last(Request $request)
    {
        $out = [];
        $start = (int)$request->get('start') ?: 0;
        $images = Images::where('check', 1)->orderBy('id', 'desc')->offset($start)->limit(20)->get();
        if (!empty($images)) {
            foreach ($images as $image) {
                if ($image instanceof Images) {
                    $out[] = [
                        'id' => $image->id,
                        'page_url' => URL::route('show_image', ['id' => $image->id]),
                        'image_src' => $image->getFullPath(),
                        'thumb_src' => $image->getThumbFullPath()
                    ];
                }
            }
        }
        return $out;
    }


}
