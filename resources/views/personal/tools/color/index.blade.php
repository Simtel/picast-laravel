@extends('layouts.personal')
@section('title', 'Конвертер цветов')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Конвертер цветов</h1>
        <p class="text-muted mb-0">HEX ⇄ RGB ⇄ HSL ⇄ CSS-название</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-palette text-primary"></i>
                    <span>Цвет</span>
                </div>
                <div class="card-body d-flex flex-column gap-3">
                    <div>
                        <label for="color-picker" class="form-label fw-semibold">Выбор цвета</label>
                        <input type="color" id="color-picker" class="form-control form-control-color" value="#3366cc">
                        <small class="form-text text-muted">Нативный пикер выдаёт HEX; остальные форматы вводятся текстом.</small>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase">Превью</div>
                        <div id="color-preview" class="rounded border d-flex align-items-center justify-content-center text-white"
                             style="min-height: 96px; background-color: #3366cc;">
                            <span id="color-preview-label" class="fw-semibold">#3366cc</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-arrow-right-arrow-left text-primary"></i>
                    <span>Форматы</span>
                </div>
                <div class="card-body">
                    <div id="color-error" class="alert alert-danger py-2 d-none" role="alert"></div>

                    <div class="mb-3">
                        <label for="color-hex" class="form-label fw-semibold">HEX</label>
                        <div class="input-group">
                            <input type="text" id="color-hex" class="form-control" value="#3366cc"
                                   autocomplete="off" spellcheck="false">
                            <button type="button" class="btn btn-outline-primary" data-copy-target="color-hex" title="Скопировать">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="color-rgb" class="form-label fw-semibold">RGB</label>
                        <div class="input-group">
                            <input type="text" id="color-rgb" class="form-control" value="rgb(51, 102, 204)"
                                   autocomplete="off" spellcheck="false">
                            <button type="button" class="btn btn-outline-primary" data-copy-target="color-rgb" title="Скопировать">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="color-hsl" class="form-label fw-semibold">HSL</label>
                        <div class="input-group">
                            <input type="text" id="color-hsl" class="form-control" value="hsl(220, 60%, 50%)"
                                   autocomplete="off" spellcheck="false">
                            <button type="button" class="btn btn-outline-primary" data-copy-target="color-hsl" title="Скопировать">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="color-name" class="form-label fw-semibold">CSS-название</label>
                        <div class="input-group">
                            <input type="text" id="color-name" class="form-control" value=""
                                   placeholder="Нет точного совпадения" autocomplete="off" spellcheck="false">
                            <button type="button" class="btn btn-outline-primary" data-copy-target="color-name" title="Скопировать">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">Точное название есть только у 148 именованных цветов CSS (red, rebeccapurple, …).</small>
                    </div>

                    <small class="form-text text-muted">Поддерживаются #rgb, #rrggbb, rgb(r, g, b), hsl(h, s%, l%) и именованные цвета CSS. Альфа-канал не поддерживается.</small>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/assets/js/color/color-page.js')
@endpush
