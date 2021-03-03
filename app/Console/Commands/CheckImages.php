<?php

namespace App\Console\Commands;

use App\Models\Images;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class CheckImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Скрипт для проверки наличия изображений в хранилище';

    /**
     * @var
     */
    protected $images;

    /**
     * @var ProgressBar
     */
    protected $progress;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Check images');
        $this->loadImages();
        $this->workImages();
        return true;
    }

    /**
     * Получаем все изображения
     */
    protected function loadImages(): void
    {
        $this->images = Images::all();
        if (empty($this->images)) {
            $this->info('Нет изображений для проверки');
            die();
        }
        $this->progress = $this->output->createProgressBar(count($this->images));
    }

    /**
     * Работаем с изображениями
     */
    protected function workImages(): void
    {

        $this->progress->start();
        foreach ($this->images as $image) {
            if ($image instanceof Images) {
                try {
                    $image->check = $this->checkImage($image);
                    $image->save();
                } catch (GuzzleException $e) {
                    $this->progress->setMessage($e->getMessage());
                }
                $this->progress->advance();
            }
        }
        $this->progress->finish();
    }

    /**
     * Проверка изображения на наличие
     * @param Images $image
     * @return bool
     * @throws GuzzleException
     */
    protected function checkImage(Images $image): bool
    {
        $client = new Client();
        try {
            $res = $client->request('GET', $image->getFullPath(), ['http_errors' => false]);
            if ($res->getStatusCode() === 200) {
                return $this->checkThumbImage($image);
            }
        } catch (RequestException $e) {
            $this->error($e->getMessage());
        }
        return false;
    }

    /**
     * Проверка миниатюры
     * @param Images $image
     * @return bool
     * @throws GuzzleException
     */
    protected function checkThumbImage(Images $image): bool
    {
        $client = new Client();
        try {
            $res = $client->request('GET', $image->getThumbFullPath(), ['http_errors' => false]);
            if ($res->getStatusCode() === 200) {
                return true;
            }
        } catch (RequestException $e) {
            $this->error($e->getMessage());
        }
        return false;
    }
}
