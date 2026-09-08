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
                <div class="card-body d-flex flex-column gap-3">
                    <div>
                        <div class="text-muted small text-uppercase">Unix timestamp (сек)</div>
                        <div class="fs-3 fw-bold" id="now-seconds" aria-live="off">&nbsp;</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small text-uppercase">GMT / UTC</div>
                            <div id="now-utc">&nbsp;</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small text-uppercase">Локальное время</div>
                            <div id="now-local">&nbsp;</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-arrow-right-arrow-left text-primary"></i>
                    <span>Timestamp → дата</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="ts-input" class="form-label fw-semibold">Unix timestamp</label>
                        <input type="text" id="ts-input" class="form-control" placeholder="Например: 1752000000 или 1752000000000"
                               autocomplete="off" spellcheck="false">
                        <small class="form-text text-muted">Поддерживаются секунды и миллисекунды (автоопределение по длине числа).</small>
                    </div>
                    <div id="ts-error" class="alert alert-danger py-2 d-none" role="alert"></div>
                    <dl class="row mb-0" id="ts-results">
                        <dt class="col-sm-4 text-muted fw-normal">ISO 8601</dt>
                        <dd class="col-sm-8"><code id="ts-iso">&mdash;</code></dd>
                        <dt class="col-sm-4 text-muted fw-normal">GMT / UTC</dt>
                        <dd class="col-sm-8"><code id="ts-utc">&mdash;</code></dd>
                        <dt class="col-sm-4 text-muted fw-normal">Локальное время</dt>
                        <dd class="col-sm-8"><code id="ts-local">&mdash;</code></dd>
                        <dt class="col-sm-4 text-muted fw-normal">Относительно</dt>
                        <dd class="col-sm-8" id="ts-relative">&mdash;</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-calendar-day text-primary"></i>
                    <span>Дата → timestamp</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="date-input" class="form-label fw-semibold">Дата и время</label>
                        <input type="text" id="date-input" class="form-control"
                               placeholder="Например: 2026-09-08 12:00:00 или 08.09.2026 12:00"
                               autocomplete="off" spellcheck="false">
                        <small class="form-text text-muted">Поддерживаются ISO-строки, «2026-09-08 12:00:00», «08.09.2026», «September 8, 2026» и другие.</small>
                    </div>
                    <div class="mb-3">
                        <label for="date-tz" class="form-label fw-semibold">Часовой пояс</label>
                        <select id="date-tz" class="form-select">
                            <option value="local">Локальный (браузер)</option>
                            <option value="utc">UTC</option>
                        </select>
                    </div>
                    <div id="date-error" class="alert alert-danger py-2 d-none" role="alert"></div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="date-seconds" class="form-label fw-semibold">Секунды</label>
                            <div class="input-group">
                                <input type="text" id="date-seconds" class="form-control" readonly tabindex="-1">
                                <button type="button" class="btn btn-outline-primary" data-copy-target="date-seconds" title="Скопировать">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="date-ms" class="form-label fw-semibold">Миллисекунды</label>
                            <div class="input-group">
                                <input type="text" id="date-ms" class="form-control" readonly tabindex="-1">
                                <button type="button" class="btn btn-outline-primary" data-copy-target="date-ms" title="Скопировать">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-table-list text-primary"></i>
                    <span>Примеры дат</span>
                </div>
                <div class="card-body">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Период</th>
                                <th scope="col">Локальное время</th>
                                <th scope="col">UTC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Начало дня</td>
                                <td><code id="ex-day-local">&mdash;</code></td>
                                <td><code id="ex-day-utc">&mdash;</code></td>
                            </tr>
                            <tr>
                                <td>Начало месяца</td>
                                <td><code id="ex-month-local">&mdash;</code></td>
                                <td><code id="ex-month-utc">&mdash;</code></td>
                            </tr>
                            <tr>
                                <td>Начало года</td>
                                <td><code id="ex-year-local">&mdash;</code></td>
                                <td><code id="ex-year-utc">&mdash;</code></td>
                            </tr>
                        </tbody>
                    </table>
                    <small class="form-text text-muted">Unix timestamp в секундах на начало периода. Обновляется автоматически.</small>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-code-compare text-primary"></i>
                    <span>Разница между датами</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="diff-a" class="form-label fw-semibold">Первая дата</label>
                            <input type="text" id="diff-a" class="form-control" placeholder="Например: 2026-09-08 12:00:00"
                                   autocomplete="off" spellcheck="false">
                        </div>
                        <div class="col-md-5">
                            <label for="diff-b" class="form-label fw-semibold">Вторая дата</label>
                            <input type="text" id="diff-b" class="form-control" placeholder="Например: 08.09.2026"
                                   autocomplete="off" spellcheck="false">
                        </div>
                        <div class="col-md-2">
                            <label for="diff-tz" class="form-label fw-semibold">Часовой пояс</label>
                            <select id="diff-tz" class="form-select">
                                <option value="local">Локальный</option>
                                <option value="utc">UTC</option>
                            </select>
                        </div>
                    </div>
                    <div id="diff-error" class="alert alert-danger py-2 mt-3 d-none" role="alert"></div>
                    <div class="mt-3 d-none" id="diff-results">
                        <span class="text-muted small text-uppercase">Разница</span>
                        <div class="d-flex flex-wrap gap-3 mt-1">
                            <div><strong id="diff-days">&mdash;</strong> <span class="text-muted">дней</span></div>
                            <div><strong id="diff-hours">&mdash;</strong> <span class="text-muted">часов</span></div>
                            <div><strong id="diff-minutes">&mdash;</strong> <span class="text-muted">минут</span></div>
                            <div><strong id="diff-seconds">&mdash;</strong> <span class="text-muted">секунд</span></div>
                            <div class="ms-auto text-end">
                                <div class="small text-muted">Всего секунд</div>
                                <code id="diff-total">&mdash;</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/assets/js/timestamp/timestamp-page.js')
@endpush
