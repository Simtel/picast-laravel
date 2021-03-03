<?php

namespace Tests\Feature;

use App\Models\Images;
use Tests\TestCase;


class ImageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testFindImage()
    {
        $image = Images::find(2000);
        $this->assertInstanceOf('App\Models\Images', $image, 'хуйня случается');
    }

    public function testGallery()
    {
        $response =$this->call('GET','gallery');
        $response->assertViewHas('images');
    }
}
