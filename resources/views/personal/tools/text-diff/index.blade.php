@extends('layouts.personal')
@section('title', 'Сравнение текстов')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Сравнение текстов</h1>
        <p class="text-muted mb-0">Построчное сравнение двух текстов: добавленные, удалённые и совпадающие строки с нумерацией.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-align-left text-primary"></i>
                    <span>Текст A (было)</span>
                </div>
                <div class="card-body">
                    <label for="text-diff-a" class="visually-hidden">Текст A</label>
                    <textarea id="text-diff-a" class="form-control font-monospace" rows="12" spellcheck="false">A&amp;S Tech
Версия 1
Laravel, PHP
Почта: old@example.com</textarea>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-align-left text-primary"></i>
                    <span>Текст B (стало)</span>
                </div>
                <div class="card-body">
                    <label for="text-diff-b" class="visually-hidden">Текст B</label>
                    <textarea id="text-diff-b" class="form-control font-monospace" rows="12" spellcheck="false">A&amp;S Tech
Версия 2
Laravel, PHP, Vue
Почта: new@example.com</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body d-flex flex-column gap-3">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <button type="button" id="text-diff-run" class="btn btn-primary">
                    <i class="fa fa-align-left"></i> Сравнить
                </button>
                <div id="text-diff-summary" class="d-flex flex-wrap gap-2"></div>
            </div>

            <div id="text-diff-error" class="alert alert-danger py-2 d-none" role="alert"></div>

            <div id="text-diff-result" class="table-responsive"></div>

            <small class="form-text text-muted">Сравнение построчное: <span class="text-success">+</span> — строка добавлена, <span class="text-danger">−</span> — удалена. Совпадающие строки показаны без подсветки.</small>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/assets/js/text-diff/text-diff-page.js')
@endpush
