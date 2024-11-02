<?php

declare(strict_types=1);

namespace Tests\Feature\Common;

use App\Models\Images;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImagesControllersTest extends TestCase
{
    public function test_personal_image_create_page(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('images.create'));
        $response->assertStatus(200);
        $response->assertViewIs('personal.images.create');
        $response->assertSee('Изображения');
        $response->assertSee('Добавить');
        $response->assertSee('Сохранить');
    }

    public function test_personal_image_index_page(): void
    {
        $this->authUserWithPermissions([], ['edit images']);

        $response = $this->get(route('images.index'));
        $response->assertStatus(200);
        $response->assertViewIs('personal.images.index');
        $response->assertSee('Изображения');
        $response->assertSee('Добавить');
    }

    public function test_personal_image_store(): void
    {
        $this->authUserWithPermissions([], ['edit images']);


        $file = UploadedFile::fake()->image('image.jpg');


        Storage::fake('s3');
        Storage::shouldReceive('disk')->once()->with('s3')->andReturnSelf();
        Storage::shouldReceive('put')->once()->andReturnSelf();

        $response = $this->post(route('images.store'), [
            'image' => $file,
            'name' => 'test image'
        ]);
        $response->assertStatus(302);

        $this->assertDatabaseCount(Images::class, 1);
    }

    public function test_personal_image_show_page(): void
    {
        $this->authUserWithPermissions([], ['edit images']);

        $image =  Images::create(
            [
                'user_id' => Auth()->id(),
                'filename' => 'test.jpg',
                'directory' => 'images',
                'thumb' => '',
                'width' => 0,
                'check' => 1,
                'disk'  => 's3'
            ]
        );

        $response = $this->get(route('images.show', ['image' => $image->id]));
        $response->assertStatus(200);
        $response->assertViewIs('personal.images.show');
        $response->assertSee('Изображения');
        $response->assertSee('test.jpg');
        $response->assertSee('Добавить');
    }
}
