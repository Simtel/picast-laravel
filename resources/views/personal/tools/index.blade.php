@extends('layouts.personal')
@section('title','Инструменты')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Инструменты</h1>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-qrcode text-primary"></i>
                        <span class="fw-semibold">Генератор штрих-кодов</span>
                    </div>
                    <p class="text-muted mb-0">Создание штрих-кодов в различных форматах: EAN, UPC, Code 128, Code 39 и другие.</p>
                    <a href="{{ route('tools.barcode.index') }}" class="btn btn-primary mt-auto">
                        <i class="fa fa-arrow-right"></i> Открыть
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-clock text-primary"></i>
                        <span class="fw-semibold">Конвертер времени</span>
                    </div>
                    <p class="text-muted mb-0">Конвертация Unix timestamp в дату и обратно: текущее время, ISO 8601, GMT, локальный часовой пояс, разница между датами.</p>
                    <a href="{{ route('tools.timestamp.index') }}" class="btn btn-primary mt-auto">
                        <i class="fa fa-arrow-right"></i> Открыть
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-shuffle text-primary"></i>
                        <span class="fw-semibold">Генератор случайных строк</span>
                    </div>
                    <p class="text-muted mb-0">Генерация случайных строк из нужного набора символов: заглавные и строчные буквы, цифры, символы.</p>
                    <a href="{{ route('tools.random-string.index') }}" class="btn btn-primary mt-auto">
                        <i class="fa fa-arrow-right"></i> Открыть
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-hashtag text-primary"></i>
                        <span class="fw-semibold">Хэш-генератор</span>
                    </div>
                    <p class="text-muted mb-0">Вычисление хэша текстовой строки: MD5, SHA1, SHA256, SHA224, SHA512, SHA384, SHA3, RIPEMD160.</p>
                    <a href="{{ route('tools.hash.index') }}" class="btn btn-primary mt-auto">
                        <i class="fa fa-arrow-right"></i> Открыть
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-fingerprint text-primary"></i>
                        <span class="fw-semibold">UUID-генератор</span>
                    </div>
                    <p class="text-muted mb-0">Генерация UUID версий v1, v4, v6, v7 в нужном количестве. UUID — 128-битный идентификатор (16³² ≈ 3,4×10³⁸ комбинаций).</p>
                    <a href="{{ route('tools.uuid.index') }}" class="btn btn-primary mt-auto">
                        <i class="fa fa-arrow-right"></i> Открыть
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-palette text-primary"></i>
                        <span class="fw-semibold">Конвертер цветов</span>
                    </div>
                    <p class="text-muted mb-0">Конвертация цвета между форматами HEX, RGB, HSL и именованными цветами CSS. Интерактивный выбор цвета пикером.</p>
                    <a href="{{ route('tools.color.index') }}" class="btn btn-primary mt-auto">
                        <i class="fa fa-arrow-right"></i> Открыть
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-code-compare text-primary"></i>
                        <span class="fw-semibold">Сравнение JSON</span>
                    </div>
                    <p class="text-muted mb-0">Сравнение двух JSON-объектов: добавленные, удалённые и изменённые значения по путям.</p>
                    <a href="{{ route('tools.json-diff.index') }}" class="btn btn-primary mt-auto">
                        <i class="fa fa-arrow-right"></i> Открыть
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-align-left text-primary"></i>
                        <span class="fw-semibold">Сравнение текстов</span>
                    </div>
                    <p class="text-muted mb-0">Построчное сравнение двух текстов: добавленные, удалённые и совпадающие строки с нумерацией.</p>
                    <a href="{{ route('tools.text-diff.index') }}" class="btn btn-primary mt-auto">
                        <i class="fa fa-arrow-right"></i> Открыть
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
