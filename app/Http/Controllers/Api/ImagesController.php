<?php

namespace App\Http\Controllers\Personal\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImageCollection;
use App\Http\Resources\ImageResource;
use App\Models\Images;
use Illuminate\Http\Request;
use Validator;

class ImagesController extends Controller
{
    /**
     * получить последние изображения
     * @param Request $request
     * @return ImageCollection|array
     */
    public function last(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'start' => 'integer'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->getMessageBag()
            ]);
        }
        $start = $request->get('start') ?: 0;
        $images = Images::where('check', 1)->orderBy('id', 'desc')->offset($start)->take(10)->get();
        return new ImageCollection(ImageResource::collection($images));
    }

    /**
     * Получить изображние по id
     * @param $id
     * @return ImageResource| array
     */
    public function image($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'integer'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->getMessageBag()
            ]);
        }
        $image = Images::find($id);
        if ($image instanceof Images) {
            return new ImageResource($image);
        }
        return response()->json([
            'error' => 'Not found image'
        ]);
    }
}
