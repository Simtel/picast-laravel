<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Context\Webcams\Domain\Model\Webcam;
use Illuminate\Database\Seeder;

class WebcamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $webcams = [
            [
                'name' => 'Ульяновск. Улица Минаева, улица 12 сентября',
                'location' => 'Ульяновск, пересечение улиц Минаева и 12 Сентября',
                'stream_url' => 'https://www.webcam-oko.ru/ulyanovsk-ulica-minaeva-ulica-12-sentya/',
                'preview_url' => 'https://www.webcam-oko.ru/wp-content/uploads/2022/08/ulyanovsk-ulica-minaeva-12-.jpg',
                'description' => 'Камера онлайн с видом транспортной развязки',
                'is_active' => true,
            ],
            [
                'name' => 'Ульяновск. Улица Самарская, улица Отрадная',
                'location' => 'Ульяновск, пересечение улиц Самарской и Отрадной',
                'stream_url' => 'https://www.webcam-oko.ru/ulyanovsk-ulica-samarskaya-ulica-otra/',
                'preview_url' => 'https://www.webcam-oko.ru/wp-content/uploads/2022/08/ulyanovsk-ulica-samarskaya-.jpg',
                'description' => 'Стрим с городского перекрестка',
                'is_active' => true,
            ],
            [
                'name' => 'Ульяновск. Проспект Филатова',
                'location' => 'Ульяновск, Проспект Филатова',
                'stream_url' => 'https://www.webcam-oko.ru/ulyanovsk-prospekt-filatova/',
                'preview_url' => 'https://www.webcam-oko.ru/wp-content/uploads/2022/08/ulyanovsk-prospekt-filatova.jpg',
                'description' => 'Камера онлайн на автомагистрали',
                'is_active' => true,
            ],
            [
                'name' => 'Ульяновск. Проспект Нариманова, Юности',
                'location' => 'Ульяновск, Проспект Нариманова, пересечение с улицей Юности',
                'stream_url' => 'https://www.webcam-oko.ru/ulyanovsk-prospekt-narimanova-yunost/',
                'preview_url' => 'https://www.webcam-oko.ru/wp-content/uploads/2022/09/ulyanovsk-prospekt-narimano.jpg',
                'description' => 'Веб камера онлайн на транспортной магистрали',
                'is_active' => true,
            ],
            [
                'name' => 'Ульяновск. Улица Камышинская, улица Жигулевская',
                'location' => 'Ульяновск, пересечение улиц Камышинской и Жигулевской',
                'stream_url' => 'https://www.webcam-oko.ru/ulyanovsk-ulica-kamyshinskaya-ulica-zhi/',
                'preview_url' => 'https://www.webcam-oko.ru/wp-content/uploads/2025/06/ulyanovsk-kamyshinskaya-zhi.jpg',
                'description' => 'Обзорная веб камера онлайн',
                'is_active' => true,
            ],
            [
                'name' => 'Ульяновск. ТРЦ «Аквамолл», река Волга, фонтаны',
                'location' => 'Ульяновск, ТРЦ «Аквамолл», берег реки Волги',
                'stream_url' => 'https://www.webcam-oko.ru/ulyanovsk-trc-akvamoll-reka-volga-f/',
                'preview_url' => 'https://www.webcam-oko.ru/wp-content/uploads/2021/07/ulyanovsk-trc-akvamol4.jpg',
                'description' => 'Онлайн-трансляция с красивым видом города',
                'is_active' => true,
            ],
            [
                'name' => 'Ульяновск. Пушкаревское кольцо, Московское шоссе',
                'location' => 'Ульяновск, Пушкаревское кольцо, Московское шоссе',
                'stream_url' => 'https://www.webcam-oko.ru/ulyanovsk-pushkarevskoe-kolco-mosko/',
                'preview_url' => 'https://www.webcam-oko.ru/wp-content/uploads/2021/08/ulyanovsk-pushkarevskoe4.jpg',
                'description' => 'Камера онлайн на главной улице Засвияжского района города',
                'is_active' => true,
            ],
            [
                'name' => 'Ульяновск. Улица Промышленная',
                'location' => 'Ульяновск, Улица Промышленная',
                'stream_url' => 'https://www.webcam-oko.ru/ulyanovsk-ulica-promyshlennaya/',
                'preview_url' => 'https://www.webcam-oko.ru/wp-content/uploads/2022/10/ulyanovsk-ulica-promyshlenn.jpg',
                'description' => 'Городская веб камера онлайн',
                'is_active' => true,
            ],
            [
                'name' => 'Ульяновск. Улица Кирова, улица Карсунская',
                'location' => 'Ульяновск, пересечение улиц Кирова и Карсунской',
                'stream_url' => 'https://www.webcam-oko.ru/ulyanovsk-ulica-kirova-ulica-karsuns/',
                'preview_url' => 'https://www.webcam-oko.ru/wp-content/uploads/2021/12/ulyanovsk-kirova-karsuns4.jpg',
                'description' => 'Камера видеонаблюдения онлайн с видом перекрестка',
                'is_active' => true,
            ],
            [
                'name' => 'Ульяновск. Улица Ефремова',
                'location' => 'Ульяновск, Улица Ефремова',
                'stream_url' => 'https://www.webcam-oko.ru/ulyanovsk-ulica-efremova/',
                'preview_url' => 'https://www.webcam-oko.ru/wp-content/uploads/2022/08/ulyanovsk-ulica-efremova.jpg',
                'description' => 'Городская веб камера онлайн',
                'is_active' => true,
            ],
            [
                'name' => 'Ульяновск. 1-й пер. Маяковского',
                'location' => 'Ульяновск, 1-й переулок Маяковского',
                'stream_url' => 'https://www.webcam-oko.ru/ulyanovsk-1-y-per-mayakovskogo/',
                'preview_url' => 'https://www.webcam-oko.ru/wp-content/uploads/2021/08/ulyanovsk-1-y-per-ma4.jpg',
                'description' => 'Городская вебкамера онлайн',
                'is_active' => true,
            ],
            [
                'name' => 'Ульяновск. Проспект Авиастроителей, Ульяновский проспект',
                'location' => 'Ульяновск, Проспект Авиастроителей, Ульяновский проспект',
                'stream_url' => 'https://www.webcam-oko.ru/ulyanovsk-prospekt-aviastroiteley-u/',
                'preview_url' => 'https://www.webcam-oko.ru/wp-content/uploads/2022/08/ulyanovsk-prospekt-aviastro.jpg',
                'description' => 'Веб камера онлайн с видом транспортной развязки',
                'is_active' => true,
            ],
            [
                'name' => 'Ульяновск. Сквер «Каштановая аллея», улица 12 Сентября',
                'location' => 'Ульяновск, Сквер «Каштановая аллея», улица 12 Сентября',
                'stream_url' => 'https://www.webcam-oko.ru/ulyanovsk-skver-kashtanovaya-alleya-u/',
                'preview_url' => 'https://www.webcam-oko.ru/wp-content/uploads/2021/08/ulyanovsk-skver-kasht4.jpg',
                'description' => 'Камера онлайн в зеленой зоне города',
                'is_active' => true,
            ],
            [
                'name' => 'Ульяновск. Строительство моста через реку Свияга',
                'location' => 'Ульяновск, строительство моста через реку Свияга',
                'stream_url' => 'https://www.webcam-oko.ru/ulyanovsk-stroitelstvo-mosta-cherez/',
                'preview_url' => 'https://www.webcam-oko.ru/wp-content/uploads/2022/08/ulyanovsk-stroitelstvo-most.jpg',
                'description' => 'Стрим со стройки нового городского моста',
                'is_active' => true,
            ],
        ];

        $count = 0;
        $exists = Webcam::pluck('name', 'stream_url')->all();
        foreach ($webcams as $webcamData) {
            if (!array_key_exists($webcamData['stream_url'], $exists)) {
                ++$count;
                Webcam::create($webcamData);
            }
        }

        $this->command->info('Сидер веб-камер Ульяновска выполнен успешно. Добавлено ' . $count . ' камер.');
    }
}
