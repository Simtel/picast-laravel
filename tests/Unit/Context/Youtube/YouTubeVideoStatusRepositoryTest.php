<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Youtube;

use App\Context\Youtube\Domain\Model\VideoStatus;
use App\Context\Youtube\Infrastructure\Repository\YouTubeVideoStatusRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class YouTubeVideoStatusRepositoryTest extends TestCase
{
    /**
     * @throws BindingResolutionException
     */
    public function test_find_by_id(): void
    {

        $videoStatus = VideoStatus::create([
            'title' => 'Test Video Status',
            'code'  => 'test-video-status',
        ]);


        $repository = $this->app->make(YouTubeVideoStatusRepository::class);


        $foundStatus = $repository->findById($videoStatus->id);
        $this->assertEquals($videoStatus->id, $foundStatus->id);
        $this->assertEquals($videoStatus->title, $foundStatus->title);
        $this->assertEquals($videoStatus->code, $foundStatus->code);


        $this->expectException(ModelNotFoundException::class);
        $repository->findById(999);
    }


    public function test_find_by_title(): void
    {

        $videoStatus = VideoStatus::create([
            'title' => 'Test Video Status',
            'code'  => 'test-video-status',
        ]);


        $repository = $this->app->make(YouTubeVideoStatusRepository::class);

        $foundStatus = $repository->findByTitle('Test Video Status');
        $this->assertEquals($videoStatus->id, $foundStatus->id);
        $this->assertEquals($videoStatus->title, $foundStatus->title);
        $this->assertEquals($videoStatus->code, $foundStatus->code);

        $this->expectException(ModelNotFoundException::class);
        $repository->findByTitle('Non-Existent Video Status');
    }


    public function test_find_by_code(): void
    {
        $videoStatus = VideoStatus::create([
            'title' => 'Test Video Status',
            'code'  => 'test-video-status',
        ]);


        $repository = $this->app->make(YouTubeVideoStatusRepository::class);

        $foundStatus = $repository->findByCode('test-video-status');
        $this->assertEquals($videoStatus->id, $foundStatus->id);
        $this->assertEquals($videoStatus->title, $foundStatus->title);
        $this->assertEquals($videoStatus->code, $foundStatus->code);

        $this->expectException(ModelNotFoundException::class);
        $repository->findByCode('non-existent-video-status');
    }
}
