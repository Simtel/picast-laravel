<?php
/**
 * Created by PhpStorm.
 * User: simtel
 * Date: 13.12.18
 * Time: 16:13
 */

namespace App\Models\Traits;

use Auth;
use Cache;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Image;
use Storage;
use Symfony\Component\HttpFoundation\File\File;

trait ImagesModelTraits
{

    /**
     * получить адресс изображения включая домен
     * @return string
     */
    public function getFullPath(): string
    {
        return env('APP_FILES_URL') . '' . $this->filename;
    }

    /**
     * Путь до миниатюры
     * @return string
     */
    public function getThumbFullPath(): string
    {
        return env('APP_FILES_URL') . '' . $this->thumb;
    }

    /**
     * получаем количество активных изображений
     * @return int
     */
    public static function getCountAvaliable(): int
    {

        if (!Cache::has('cntImages')) {
            Cache::put('cntImages', self::where('check', 1)->count(), 60);
        }
        return Cache::get('cntImages');
    }

    /**
     * Сохранение нового файла
     * @param UploadedFile $file
     * @return bool
     */
    public function saveUploadFile(UploadedFile $file): bool
    {
        $path = $this->getFolderUploads();
        $this->filename = $this->getNewFileName($file);
        $uploadFile = $file->move($path, $file->getClientOriginalName());
        $this->storeFileCloud($uploadFile, $this->filename);
        $thumb = $this->createThumbnail($uploadFile);
        if ($thumb instanceof Image) {
            $this->thumb = $this->getNewThumbFileName($file);
            $this->storeFileCloud($thumb->basePath(), $this->thumb);
        }
        $this->check = 1;
        if (Auth::check()) {
            $this->user_id = (int)Auth::id();
        }
        return $this->save();

    }

    /**
     * Получим имя нового файла, фактически путь в облаке
     * @param UploadedFile $file
     * @return string
     */
    protected function getNewFileName(UploadedFile $file): string
    {
        $ext = $file->getClientOriginalExtension();
        return 'img/' . date('Y') . '/' . date('M') . '/' . md5($file->getClientOriginalName()) . '.' . $ext;
    }

    /**
     * Получим имя миниатюры, фактически путь в облаке
     * @param UploadedFile $file
     * @return string
     */
    protected function getNewThumbFileName(UploadedFile $file): string
    {
        $ext = $file->getClientOriginalExtension();
        return 'img/' . date('Y') . '/' . date('M') . '/thumb/' . md5($file->getClientOriginalName()) . '.' . $ext;
    }

    /**
     * Папка для загрузки изображений
     * @return string
     */
    protected function getFolderUploads(): string
    {
        return env('TMP_FILE_UPLOADS');
    }

    /**
     * Сохраним файл в облако
     * @param string $file
     * @param string $newfile
     * @return string|false
     */
    protected function storeFileCloud(string $file, string $newfile)
    {
        $storage = Storage::disk('selectel');
        return $storage->putFile($newfile, $file);
    }

    /**
     * Создание миниатюры
     * @param File $file
     * @return Image
     */
    protected function createThumbnail(File $file): Image
    {
        $img = \Image::make($file->getRealPath());
        $this->width = $img->width();
        $img->resize(env('THUMB_WIDTH'), env('THUMB_HEIGHT'));
        $img->save($this->getFolderUploads() . '/' . md5($file->getFilename()) . '_thumb.' . $img->extension);
        return $img;
    }

}