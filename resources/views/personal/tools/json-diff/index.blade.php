@extends('layouts.personal')
@section('title', 'Сравнение JSON')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Сравнение JSON</h1>
        <p class="text-muted mb-0">Сравнение двух JSON-объектов: добавленные, удалённые и изменённые значения по путям.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-code text-primary"></i>
                    <span>JSON A (было)</span>
                </div>
                <div class="card-body">
                    <label for="json-diff-a" class="visually-hidden">JSON A</label>
                    <textarea id="json-diff-a" class="form-control font-monospace" rows="12" spellcheck="false">{
  "name": "A&amp;S Tech",
  "version": 1,
  "tags": ["laravel", "php"],
  "user": {
    "email": "old@example.com",
    "active": true
  }
}</textarea>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-code text-primary"></i>
                    <span>JSON B (стало)</span>
                </div>
                <div class="card-body">
                    <label for="json-diff-b" class="visually-hidden">JSON B</label>
                    <textarea id="json-diff-b" class="form-control font-monospace" rows="12" spellcheck="false">{
  "name": "A&amp;S Tech",
  "version": 2,
  "tags": ["laravel", "php", "vue"],
  "user": {
    "email": "new@example.com",
    "active": true
  }
}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body d-flex flex-column gap-3">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <button type="button" id="json-diff-run" class="btn btn-primary">
                    <i class="fa fa-code-compare"></i> Сравнить
                </button>
                <div id="json-diff-summary" class="d-flex flex-wrap gap-2"></div>
            </div>

            <div id="json-diff-error" class="alert alert-danger py-2 d-none" role="alert"></div>

            <div id="json-diff-result"></div>

            <small class="form-text text-muted">Сравниваются объекты по ключам и массивы по индексам. Путь начинается с <code>$</code>.</small>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/assets/js/json-diff/json-diff-page.js')
@endpush
