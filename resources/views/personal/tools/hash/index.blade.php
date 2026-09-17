@extends('layouts.personal')
@section('title', 'Хэш-генератор')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Хэш-генератор</h1>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-hashtag text-primary"></i>
                    <span>Параметры</span>
                </div>
                {{ Html::form('GET', route('tools.hash.index'))->open() }}
                <div class="card-body">
                    <div class="mb-3">
                        {{ Html::label('Алгоритм', 'algorithm')->class('form-label fw-semibold') }}
                        {{ Html::select('algorithm', collect($algorithms)->map(static fn (array $a): string => $a['label'])->toArray(), $selectedAlgorithm)->attribute('id', 'algorithm')->class('form-select') }}
                    </div>
                    <div class="mb-3">
                        {{ Html::label('Текст', 'text')->class('form-label fw-semibold') }}
                        {{ Html::textarea('text', $text)->attribute('id', 'text')->attribute('rows', '3')->attribute('placeholder', 'Введите строку для хэширования')->class('form-control') }}
                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-end">
                    {{ Html::submit('Хэшировать')->class('btn btn-primary') }}
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
                    @if ($result !== null)
                        <div class="input-group">
                            <input type="text" id="hash-result" class="form-control" value="{{ $result }}"
                                   readonly tabindex="-1" aria-label="Результат хэширования">
                            <button type="button" class="btn btn-outline-primary" data-copy-target="hash-result" title="Скопировать">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted d-block mt-2">Хэш вычислен на сервере.</small>
                    @else
                        <p class="text-muted mb-0">Введите текст и нажмите «Хэшировать».</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const resultInput = document.getElementById('hash-result');
            const copyButton = document.querySelector('[data-copy-target="hash-result"]');

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