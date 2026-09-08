@extends('layouts.personal')
@section('title', 'Конвертер времени')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Конвертер времени</h1>
        <p class="text-muted mb-0">Unix timestamp ⇄ дата</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-clock text-primary"></i>
                    <span>Текущее время</span>
                </div>
                <div class="card-body"></div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-arrow-right-arrow-left text-primary"></i>
                    <span>Timestamp → дата</span>
                </div>
                <div class="card-body"></div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-calendar-day text-primary"></i>
                    <span>Дата → timestamp</span>
                </div>
                <div class="card-body"></div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-table-list text-primary"></i>
                    <span>Примеры дат</span>
                </div>
                <div class="card-body"></div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-code-compare text-primary"></i>
                    <span>Разница между датами</span>
                </div>
                <div class="card-body"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
