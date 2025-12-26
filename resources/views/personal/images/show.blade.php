@extends('layouts.personal')
@section('title','Просмотр изображения')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fas fa-image text-primary me-2"></i>
                {{$image->filename}}
            </h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="{{route('images.create')}}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Добавить
                </a>
                <a href="{{route('images.index')}}" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-arrow-left me-1"></i>Назад
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Изображение -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-eye me-2"></i>Предпросмотр
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{$image->getPath()}}" 
                             class="img-fluid rounded shadow-sm" 
                             style="max-height: 600px; object-fit: contain;" 
                             alt="{{$image->filename}}">
                    </div>
                </div>
            </div>

            <!-- Информация и ссылки -->
            <div class="col-lg-4">
                <!-- URL ссылки -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-link me-2"></i>Ссылки
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-file-image me-1"></i>Прямая ссылка на изображение:
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="imageUrl" 
                                       value="{{$image->getPath()}}" 
                                       readonly>
                                <button class="btn btn-outline-primary btn-sm copy-btn" 
                                        type="button" 
                                        data-target="imageUrl"
                                        title="Копировать ссылку">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-globe me-1"></i>Ссылка на страницу:
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="pageUrl" 
                                       value="{{route('images.show',[$image])}}" 
                                       readonly>
                                <button class="btn btn-outline-primary btn-sm copy-btn" 
                                        type="button" 
                                        data-target="pageUrl"
                                        title="Копировать ссылку">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Нажмите кнопку копирования для быстрого копирования ссылки
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Информация об изображении -->
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Информация
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <strong>ID:</strong>
                                <div class="text-muted small">#{{$image->id}}</div>
                            </div>
                            <div class="col-6">
                                <strong>Размер:</strong>
                                <div class="text-muted small">{{$image->getFormattedSize()}}</div>
                            </div>
                            
                            <div class="col-6">
                                <strong>Файл:</strong>
                                <div class="text-muted small" title="{{$image->filename}}">
                                    {{ Str::limit($image->filename, 15) }}
                                </div>
                            </div>
                            <div class="col-6">
                                <strong>Диск:</strong>
                                <div class="text-muted small">
                                    <i class="fas fa-hdd me-1"></i>{{$image->disk}}
                                </div>
                            </div>

                            <div class="col-6">
                                <strong>Проверено:</strong>
                                <div class="text-muted small">
                                    @if($image->check)
                                        <span class="badge bg-success">Да</span>
                                    @else
                                        <span class="badge bg-warning">Нет</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <strong>Ширина:</strong>
                                <div class="text-muted small">{{$image->width}}px</div>
                            </div>

                            <div class="col-6">
                                <strong>Директория:</strong>
                                <div class="text-muted small" title="{{$image->directory}}">
                                    <i class="fas fa-folder me-1"></i>
                                    {{ Str::limit($image->directory, 12) }}
                                </div>
                            </div>
                            <div class="col-6">
                                <strong>Пользователь:</strong>
                                <div class="text-muted small">
                                    <i class="fas fa-user me-1"></i>
                                    {{ $image->getUser()->name ?? 'Неизвестно' }}
                                </div>
                            </div>

                            <div class="col-12">
                                <strong>Создано:</strong>
                                <div class="text-muted small">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $image->created_at?->format('d.m.Y H:i') ?? 'Неизвестно' }}
                                </div>
                            </div>

                            <div class="col-12">
                                <strong>Обновлено:</strong>
                                <div class="text-muted small">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ $image->updated_at?->format('d.m.Y H:i') ?? 'Неизвестно' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Действия -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tools me-2"></i>Действия
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{$image->getPath()}}" 
                               target="_blank" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt me-1"></i>
                                Открыть в новой вкладке
                            </a>
                            <button class="btn btn-outline-secondary download-btn" 
                                    data-url="{{$image->getPath()}}" 
                                    data-filename="{{$image->filename}}">
                                <i class="fas fa-download me-1"></i>
                                Скачать изображение
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast для уведомлений -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="copyToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-check-circle text-success me-2"></i>
                <strong class="me-auto">Успешно</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Ссылка скопирована в буфер обмена!
            </div>
        </div>
    </div>

    <script>
        console.log('Scripts section loaded');
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing...');
            
            // Обработчики для кнопок копирования
            const copyButtons = document.querySelectorAll('.copy-btn');
            console.log('Found copy buttons:', copyButtons.length);
            
            copyButtons.forEach(function(button, index) {
                console.log('Adding click handler to button', index);
                button.addEventListener('click', function(e) {
                    console.log('Copy button clicked:', index);
                    e.preventDefault();
                    const targetId = this.getAttribute('data-target');
                    console.log('Target ID:', targetId);
                    copyToClipboard(targetId);
                });
            });

            // Обработчик для кнопки скачивания
            const downloadButton = document.querySelector('.download-btn');
            console.log('Found download button:', !!downloadButton);
            
            if (downloadButton) {
                downloadButton.addEventListener('click', function(e) {
                    console.log('Download button clicked');
                    e.preventDefault();
                    const url = this.getAttribute('data-url');
                    const filename = this.getAttribute('data-filename');
                    console.log('Download URL:', url, 'Filename:', filename);
                    downloadImage(url, filename);
                });
            }
        });

        function copyToClipboard(elementId) {
            console.log('copyToClipboard called with:', elementId);
            const element = document.getElementById(elementId);
            
            if (!element) {
                console.error('Element not found:', elementId);
                return;
            }
            
            const text = element.value;
            console.log('Text to copy:', text);
            
            if (navigator.clipboard && window.isSecureContext) {
                console.log('Using Clipboard API');
                navigator.clipboard.writeText(text).then(() => {
                    console.log('Clipboard API success');
                    alert('Ссылка скопирована в буфер обмена!');
                }).catch(err => {
                    console.error('Clipboard API error:', err);
                    fallbackCopyTextToClipboard(text);
                });
            } else {
                console.log('Using fallback method');
                fallbackCopyTextToClipboard(text);
            }
        }

        function fallbackCopyTextToClipboard(text) {
            console.log('fallbackCopyTextToClipboard called with:', text);
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                console.log('Fallback copy result:', successful);
                if (successful) {
                    alert('Ссылка скопирована в буфер обмена! (fallback)');
                } else {
                    alert('Ошибка копирования ссылки');
                }
            } catch (err) {
                console.error('Fallback error:', err);
                alert('Ошибка копирования ссылки');
            }
            
            document.body.removeChild(textArea);
        }

        function downloadImage(url, filename) {
            console.log('downloadImage called with:', url, filename);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            console.log('Download initiated');
        }
    </script>
@endsection


