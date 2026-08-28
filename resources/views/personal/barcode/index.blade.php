@extends('layouts.personal')
@section('title','Генератор штрих-кодов')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Генератор штрих-кодов</h1>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-qrcode text-primary"></i>
                    <span>Параметры</span>
                </div>
                {{ Html::form('GET', route('barcode.index'))->open() }}
                <div class="card-body">
                    @if ($errorMessage)
                        <div class="alert alert-danger d-flex align-items-start gap-2">
                            <i class="fa fa-circle-exclamation mt-1"></i>
                            <span>{{ $errorMessage }}</span>
                        </div>
                    @endif
                    <div class="mb-3">
                        {{ Html::label('Тип штрих-кода', 'type')->class('form-label fw-semibold') }}
                        {{ Html::select('type', $types->map(static fn (array $t): string => $t['label'])->toArray(), $selectedType)->class('form-select') }}
                    </div>
                    <div class="mb-3">
                        {{ Html::label('Данные', 'text')->class('form-label fw-semibold') }}
                        {{ Html::text('text', $text)->attribute('placeholder', 'Например: TEST-12345')->class('form-control') }}
                        <small class="form-text text-muted">Для EAN-13, EAN-8, UPC используйте только цифры подходящей длины.</small>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                {{ Html::label('Масштаб (1–5)', 'scale')->class('form-label fw-semibold') }}
                                {{ Html::number('scale', $scale)->attribute('min', '1')->attribute('max', '5')->class('form-control') }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                {{ Html::label('Высота, px (20–200)', 'height')->class('form-label fw-semibold') }}
                                {{ Html::number('height', $height)->attribute('min', '20')->attribute('max', '200')->class('form-control') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-end">
                    {{ Html::submit('Сгенерировать')->class('btn btn-primary') }}
                </div>
                {{ Html::form()->close() }}
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-image text-primary"></i>
                    <span>Результат</span>
                </div>
                <div class="card-body text-center">
                    @if ($barcodeDataUri)
                        <img src="{{ $barcodeDataUri }}" alt="Штрих-код" class="img-fluid mb-3">
                        <p class="mb-1 text-break"><strong>Данные:</strong> {{ $text }}</p>
                        <a href="{{ $barcodeDataUri }}" download="barcode.png" class="btn btn-outline-primary">
                            <i class="fa fa-download"></i> Скачать PNG
                        </a>
                    @else
                        <p class="text-muted mb-0">Заполните форму и нажмите «Сгенерировать».</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
