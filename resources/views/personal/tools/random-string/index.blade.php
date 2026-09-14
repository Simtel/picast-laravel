@extends('layouts.personal')
@section('title', 'Генератор случайных строк')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Генератор случайных строк</h1>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-shuffle text-primary"></i>
                    <span>Параметры</span>
                </div>
                {{ Html::form('GET', route('tools.random-string.index'))->open() }}
                <div class="card-body">
                    <div class="mb-3">
                        {{ Html::label('Длина (1–128)', 'length')->class('form-label fw-semibold') }}
                        {{ Html::number('length', $length)->attribute('min', '1')->attribute('max', '128')->class('form-control') }}
                    </div>
                    <div class="mb-3">
                        <span class="form-label fw-semibold d-block">Набор символов</span>
                        <small class="form-text text-muted d-block mb-2">Отметьте хотя бы один набор.</small>
                        <div class="form-check mb-1">
                            {{ Html::checkbox('uppercase', $uppercase, '1')->class('form-check-input')->id('uppercase') }}
                            {{ Html::label('Заглавные буквы (A–Z)', 'uppercase')->class('form-check-label') }}
                        </div>
                        <div class="form-check mb-1">
                            {{ Html::checkbox('lowercase', $lowercase, '1')->class('form-check-input')->id('lowercase') }}
                            {{ Html::label('Строчные буквы (a–z)', 'lowercase')->class('form-check-label') }}
                        </div>
                        <div class="form-check mb-1">
                            {{ Html::checkbox('digits', $digits, '1')->class('form-check-input')->id('digits') }}
                            {{ Html::label('Цифры (0–9)', 'digits')->class('form-check-label') }}
                        </div>
                        <div class="form-check mb-1">
                            {{ Html::checkbox('symbols', $symbols, '1')->class('form-check-input')->id('symbols') }}
                            {{ Html::label('Символы (!@#$%^&*…)', 'symbols')->class('form-check-label') }}
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
                    <i class="fa fa-key text-primary"></i>
                    <span>Результат</span>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" id="random-string-result" class="form-control" value="{{ $result }}"
                               readonly tabindex="-1" aria-label="Сгенерированная строка">
                        <button type="button" class="btn btn-outline-primary" data-copy-target="random-string-result" title="Скопировать">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted d-block mt-2 mb-2">Строка генерируется на сервере при загрузке страницы.</small>
                    <button type="button" class="btn btn-primary" id="random-string-refresh">
                        <i class="fa fa-refresh"></i> Сгенерировать заново
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const resultInput = document.getElementById('random-string-result');
            const refreshButton = document.getElementById('random-string-refresh');
            const copyButton = document.querySelector('[data-copy-target="random-string-result"]');

            if (refreshButton && resultInput) {
                refreshButton.addEventListener('click', () => {
                    window.location.reload();
                });
            }

            if (copyButton && resultInput) {
                copyButton.addEventListener('click', () => {
                    navigator.clipboard.writeText(resultInput.value).then(() => {
                        copyButton.classList.replace('btn-outline-primary', 'btn-success');
                        setTimeout(() => copyButton.classList.replace('btn-success', 'btn-outline-primary'), 1500);
                    });
                });
            }
        })();
    </script>
@endpush