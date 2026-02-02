@extends('layouts.personal')
@section('title', $title ?? 'Веб-камера')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('webcams.index') }}">
                                <i class="fas fa-video mr-1"></i>Веб-камеры
                            </a>
                        </li>
                        @if($webcam)
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ $webcam->getName() }}
                            </li>
                        @endif
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if(isset($error))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ $error }}
                </div>
            </div>
        </div>
    @endif

    @if($webcam)
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="position-relative">
                        <img src="{{ $webcam->getPreviewUrl() }}" 
                             class="card-img-top" 
                             alt="{{ $webcam->getName() }}"
                             style="height: 400px; object-fit: cover;">
                        <div class="webcam-overlay">
                            <span class="badge badge-success badge-lg">
                                <i class="fas fa-circle mr-2"></i>ОНЛАЙН
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h1 class="h3 card-title mb-0">{{ $webcam->getName() }}</h1>
                            <div class="webcam-actions">
                                <a href="{{ $webcam->getStreamUrl() }}" 
                                   target="_blank" 
                                   class="btn btn-primary"
                                   title="Открыть прямую трансляцию">
                                    <i class="fas fa-play mr-2"></i>Смотреть трансляцию
                                </a>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="card-text">
                                    <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                    <strong>Локация:</strong><br>
                                    {{ $webcam->getLocation() }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="card-text">
                                    <i class="fas fa-clock text-primary mr-2"></i>
                                    <strong>Обновлено:</strong><br>
                                    {{ \Carbon\Carbon::parse($webcam->getUpdatedAt())->format('d.m.Y H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h5 class="text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Описание
                            </h5>
                            <p class="card-text">{{ $webcam->getDescription() }}</p>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Информация:</strong> Камера работает в режиме реального времени. 
                            Для получения лучшего качества изображения рекомендуется открыть прямую трансляцию 
                            в новом окне.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list mr-2"></i>Дополнительная информация
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-muted">Статус камеры</h6>
                            <span class="badge badge-success">
                                <i class="fas fa-wifi mr-1"></i>Активна
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-muted">Качество изображения</h6>
                            <p class="small text-muted mb-0">
                                <i class="fas fa-image mr-1"></i>
                                Высокое разрешение
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">Время обновления</h6>
                            <p class="small text-muted mb-0">
                                <i class="fas fa-sync mr-1"></i>
                                Каждые 30 секунд
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">Источник</h6>
                            <p class="small text-muted mb-0">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                <a href="{{ $webcam->getStreamUrl() }}" target="_blank" class="text-decoration-none">
                                    Открыть на источнике
                                </a>
                            </p>
                        </div>

                        <hr>

                        <div class="text-center">
                            <button type="button" 
                                    class="btn btn-outline-primary btn-sm"
                                    onclick="refreshPreview()">
                                <i class="fas fa-refresh mr-1"></i>Обновить превью
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-share-alt mr-2"></i>Поделиться
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group w-100" role="group">
                            <button type="button" 
                                    class="btn btn-outline-primary"
                                    onclick="copyToClipboard('{{ $webcam->getStreamUrl() }}')">
                                <i class="fas fa-link mr-1"></i>Скопировать ссылку
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-video text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3 text-muted">Веб-камера не найдена</h3>
                    <p class="text-muted">Запрашиваемая веб-камера не существует или была удалена.</p>
                    <a href="{{ route('webcams.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left mr-2"></i>Вернуться к списку камер
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<style>
.webcam-overlay {
    position: absolute;
    top: 15px;
    left: 15px;
}

.badge-lg {
    font-size: 1rem;
    padding: 0.5rem 1rem;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-success i {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.card-img-top {
    transition: transform 0.3s ease-in-out;
}

.card-img-top:hover {
    transform: scale(1.02);
}
</style>

<script>
function refreshPreview() {
    const img = document.querySelector('.card-img-top');
    const timestamp = new Date().getTime();
    const currentSrc = img.src;
    img.src = currentSrc.split('?')[0] + '?t=' + timestamp;
    
    // Показать уведомление
    showNotification('Превью обновлено', 'success');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showNotification('Ссылка скопирована в буфер обмена', 'success');
    }).catch(function(err) {
        console.error('Не удалось скопировать: ', err);
        showNotification('Ошибка при копировании ссылки', 'error');
    });
}

function showNotification(message, type = 'info') {
    // Создаем элемент уведомления
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    // Автоматически скрыть через 3 секунды
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

// Автоматическое обновление превью каждые 60 секунд
setInterval(function() {
    const img = document.querySelector('.card-img-top');
    if (img) {
        const currentSrc = img.src;
        const timestamp = new Date().getTime();
        img.src = currentSrc.split('?')[0] + '?t=' + timestamp;
    }
}, 60000);
</script>
@endsection