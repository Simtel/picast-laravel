<!-- handoff:task:c4ea991a-2f82-40f6-b2ef-46a13e703083 -->

# Implementation Plan: Генерация штрих-кодов

Branch: master
Created: 2026-08-28

## Original Request
Нужно создать новый раздел, в котором пользователь сможет генерировать различные штрих-коды для тестирования.

## Settings
- Testing: no
- Logging: verbose
- Docs: no

## Roadmap Linkage
Milestone: "none"
Rationale: Роадмап не существует — связь пропущена.

## Tasks

### Phase 1: Зависимости и контроллер

- [x] Task 1: Установить библиотеку `picqer/php-barcode-generator` (^3.3).
  - Доставка: пакет должен быть в `composer.json` (require) и в `composer.lock`.
  - Реализация: выполнить внутри php-контейнера `composer require picqer/php-barcode-generator:^3.3` (через `make cli` или `docker compose exec php composer require ...`). GD в образе уже установлен — PNG-рендер будет работать.
  - Проверка: `docker compose exec php php -r "require 'vendor/autoload.php'; echo (new Picqer\Barcode\Renderers\PngRenderer())->render((new Picqer\Barcode\Types\TypeCode128())->getBarcode('12345'), 100, 50) ? 'ok' : 'fail';"` → выводит `ok`.
  - Логирование: вывести `INFO [composer] установлен picqer/php-barcode-generator <версия>`, при ошибке установки — `ERROR [composer] не удалось установить picqer/php-barcode-generator: {error}`.
  - Файлы: `composer.json`, `composer.lock`

- [x] Task 2: Создать `BarcodeController` (final class, `declare(strict_types=1)`, namespace `App\Context\User\Infrastructure\Controller`, extends `App\Http\Controllers\Controller`).
  - Доставка: метод `index(Request $request): View` на `GET /personal/barcode` обрабатывает query-параметры (`type`, `text`, `scale`, `height`) и возвращает view `personal.barcode.index`.
  - Реализация: в контроллере определить приватную константу `TYPES` — массив вида `'code128' => ['class' => TypeCode128::class, 'label' => 'Code 128']` с минимум такими типами: `code128` (TypeCode128), `code39` (TypeCode39), `code93` (TypeCode93), `ean13` (TypeEan13), `ean8` (TypeEan8), `itf14` (TypeITF14), `upca` (TypeUpcA), `upce` (TypeUpcE), `codabar` (TypeCodabar), `code11` (TypeCode11), `standard25` (TypeStandard2of5), `interleaved25` (TypeInterleaved25), `msi` (TypeMsi), `postnet` (TypePostnet), `planet` (TypePlanet), `rms4cc` (TypeRms4cc), `kix` (TypeKix), `pharmacode` (TypePharmacode). Импорты типов — из `Picqer\Barcode\Types\`.
  - Валидация (без редиректа, чтобы не терять query-параметры): `Validator::make($request->all(), [...])` с правилами `type => [nullable, Rule::in(array_keys(TYPES))]`, `text => [required_with:type, string, max:255]`, `scale => [integer, between:1,5]`, `height => [integer, between:20,200]`. Если валидация провалилась — `$errorMessage = $validator->errors()->first()`.
  - Генерация: `$barcode = (new $typeClass())->getBarcode($text);` → `(new PngRenderer())->render($barcode, $barcode->getWidth() * $scale, $height)` → data URI `'data:image/png;base64,' . base64_encode($png)`. Оборачивать в try/catch `Throwable` — ошибки библиотеки (недопустимые символы/длина, например у EAN/UPC) превращать в `$errorMessage` с текстом исключения.
  - Передавать в view: `types` (для select), `selectedType`, `text`, `scale`, `height`, `barcodeDataUri` (null если не генерировался), `errorMessage` (null если нет).
  - Логирование: `DEBUG [BarcodeController.index] вход, type={type} textLength={len} scale={scale} height={height}`; `INFO [BarcodeController.index] штрих-код сгенерирован (type={type}, textLength={len})`; `ERROR [BarcodeController.index] ошибка генерации: {error}`.
  - Проверка: `vendor/bin/phpstan analyse app/Context/User/Infrastructure/Controller/BarcodeController.php` без ошибок.
  - Файлы: `app/Context/User/Infrastructure/Controller/BarcodeController.php`

### Phase 2: Маршрут, меню и представление

- [x] Task 3: Добавить маршрут и пункт меню (depends on 2).
  - Доставка: `GET /personal/barcode` доступен авторизованным пользователям (внутри внешней группы `personal` с middleware `auth`); пункт «Штрих-коды» виден в сайдбаре всем авторизованным.
  - Реализация:
    - В `routes/web.php` внутри внешней группы `['middleware' => 'auth', 'prefix' => 'personal']` добавить: `Route::get('/barcode', [BarcodeController::class, 'index'])->name('barcode.index');` + `use App\Context\User\Infrastructure\Controller\BarcodeController;`. Без permission — как у ChadGPT.
    - В `resources/views/personal/sidebars/sidebar.blade.php` добавить `<li class="nav-item">` (без `@can`) с иконкой `fa fa-qrcode`, активным состоянием `{{ request()->routeIs('barcode.*') ? 'active' : '' }}` и `href="{{ route('barcode.index') }}"`, текст «Штрих-коды».
  - Логирование: `DEBUG [web] добавлен маршрут barcode.index`; `DEBUG [sidebar] добавлен пункт меню barcode`.
  - Проверка: `docker compose exec php php artisan route:list | grep barcode` показывает `barcode.index`.
  - Файлы: `routes/web.php`, `resources/views/personal/sidebars/sidebar.blade.php`

- [x] Task 4: Создать view `resources/views/personal/barcode/index.blade.php` (depends on 2, 3).
  - Доставка: страница с формой выбора типа штрих-кода и ввода данных; после отправки (GET) показывает сгенерированное изображение.
  - Реализация (по конвенции `personal/settings.blade.php`):
    - `@extends('layouts.personal')`, `@section('title','Генератор штрих-кодов')`, `.main-content-header` с `h1.h2` «Генератор штрих-кодов».
    - Форма `Html::form('GET', route('barcode.index'))->open()` в `.card`: select `type` (`Html::select('type', $types->map(...)->toArray(), $selectedType)` с label «Тип штрих-кода»), input `text` (`form-control`) с label «Данные» и placeholder-примером, числовые input `scale` (1–5, по умолчанию 2) и `height` (20–200, по умолчанию 50), submit-кнопка «Сгенерировать».
    - Блок ошибок: если `$errorMessage` — `<div class="alert alert-danger">`.
    - Результат: если `$barcodeDataUri` — `<img src="{{ $barcodeDataUri }}" alt="Штрих-код">`, под ним текст закодированных данных, и ссылка скачивания `<a href="{{ $barcodeDataUri }}" download="barcode.png">Скачать PNG</a>`.
    - Использовать стандартные классы Bootstrap (`form-label fw-semibold`, `form-control`, `form-select`, `card`, `card-header`, `card-body`, `card-footer bg-transparent`).
  - Логирование: `DEBUG [view] рендер barcode.index (hasImage={hasImage})`.
  - Проверка: страница `GET /personal/barcode` (авторизованным) отдаёт 200; генерация Code 128 и EAN-13 показывает корректное изображение; неверные данные (например, буквы для EAN-13) показывают ошибку без 500.
  - Примечание (реализация): пример EAN-13 из плана `4601234567894` невалиден — у него неверная контрольная цифра. Корректный валидный номер: `4601234567893`. Библиотека проверяет чексумму EAN-13 и для невалидного номера корректно показывает ошибку.
  - Файлы: `resources/views/personal/barcode/index.blade.php`

- [x] Task 5: Проверка качества кода и смоук-тест.
  - Доставка: код проходит `pint` и `phpstan`; раздел работает в браузере.
  - Реализация:
    - `make pint` — форматирование без ошибок.
    - `make phpstan` — уровень max без ошибок.
    - Смоук-тест в браузере: открыть `/personal/barcode`, сгенерировать Code 128 («TEST-12345») и EAN-13 («4601234567893»), убедиться, что изображения отображаются; проверить невалидный ввод (EAN-13 с буквами) — показывается понятная ошибка.
  - Логирование: `INFO [barcode] проверка завершена: pint={pintOk}, phpstan={phpstanOk}`.
  - Файлы: без изменений файлов (валидация результата)

## Commit Plan
- **Commit 1** (после задач 1-2): `feat(barcode): add picqer/php-barcode-generator and BarcodeController`
- **Commit 2** (после задач 3-5): `feat(barcode): add barcode generation section UI`
