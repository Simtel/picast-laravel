<?php

declare(strict_types=1);

namespace App\Context\User\Application\Service;

use App\Context\Common\Domain\Models\Images;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class ImageUploadService
{
    public function upload(UploadedFile $file, int $userId): Images
    {
        $imageName = time() . '.' . $file->extension();
        $directory = 'images/' . date('m-Y');

        Storage::disk('s3')->put($directory . '/' . $imageName, $file->getContent());

        $image = Images::create(
            [
                'user_id' => $userId,
                'filename' => $imageName,
                'directory' => $directory,
                'thumb' => '',
                'width' => 0,
                'check' => 1,
                'disk' => 's3',
            ]
        );

        Log::info('[ImageUploadService.upload] файл загружен', [
            'name' => $imageName,
            'user' => $userId,
        ]);

        return $image;
    }
}
