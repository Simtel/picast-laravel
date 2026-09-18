@extends('layouts.personal')
@section('title', 'UUID-генератор')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">UUID-генератор</h1>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-fingerprint text-primary"></i>
                    <span>Параметры</span>
                </div>
                {{ Html::form('GET', route('tools.uuid.index'))->open() }}
                <div class="card-body">
                    <div class="mb-3">
                        {{ Html::label('Версия UUID', 'version')->class('form-label fw-semibold') }}
                        {{ Html::select('version', array_map(static fn (array $v): string => $v['label'], $versions), $selectedVersion)->attribute('id', 'version')->class('form-select') }}
                    </div>
                    <div class="mb-3">
                        {{ Html::label('Количество (1–100)', 'count')->class('form-label fw-semibold') }}
                        {{ Html::number('count', $count)->attribute('min', '1')->attribute('max', '100')->class('form-control') }}
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
                    <i class="fa fa-fingerprint text-primary"></i>
                    <span>Результат</span>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <textarea id="uuid-result" class="form-control" rows="10" readonly
                                  tabindex="-1" aria-label="Сгенерированные UUID">{{ implode(PHP_EOL, $result) }}</textarea>
                        <button type="button" class="btn btn-outline-primary" data-copy-target="uuid-result" title="Скопировать">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted d-block mt-2 mb-2">UUID генерируются на сервере при загрузке страницы. UUID — 128-битное число; всего возможно 16³² = 2¹²⁸ ≈ 3,4×10³⁸ комбинаций.</small>
                    <button type="button" class="btn btn-primary" id="uuid-refresh">
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
            const resultTextarea = document.getElementById('uuid-result');
            const refreshButton = document.getElementById('uuid-refresh');
            const copyButton = document.querySelector('[data-copy-target="uuid-result"]');

            if (refreshButton) {
                refreshButton.addEventListener('click', () => {
                    window.location.reload();
                });
            }

            if (copyButton && resultTextarea) {
                copyButton.addEventListener('click', () => {
                    navigator.clipboard.writeText(resultTextarea.value).then(() => {
                        copyButton.classList.replace('btn-outline-primary', 'btn-success');
                        setTimeout(() => copyButton.classList.replace('btn-success', 'btn-outline-primary'), 1500);
                    });
                });
            }
        })();
    </script>
@endpush