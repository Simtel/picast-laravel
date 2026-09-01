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
    </div>
@endsection
