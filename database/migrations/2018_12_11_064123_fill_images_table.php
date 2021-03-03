<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FillImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $olds = \App\MmhFileStorage::where('cloud', 1)->get();
        $cnt = 0;
        foreach ($olds as $old) {
            if ($old instanceof \App\MmhFileStorage) {
                $output->writeln('Сохраняем изображение ' . $old->filename);
                $new = new \App\Models\Images();
                $new->filename = $old->filename;
                $new->thumb = $old->thumb;
                $new->width = $old->width;

                if (!$new->save()) {
                    $output->writeln('Не удалось сохранить изображение');
                } else {
                    $cnt++;
                }

            }
        }
        $output->writeln('Сохранили изображений ' . $cnt);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\Images::truncate();
    }
}
