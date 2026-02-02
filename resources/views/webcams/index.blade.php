@extends('layouts.personal')
@section('title', $title ?? 'Веб-камеры Ульяновска')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="h2">
                    <i class="fas fa-video mr-2 text-primary"></i>
                    {{ $title }}
                </h1>
                <p class="text-muted">
                    Прямые трансляции с различных точек города Ульяновска в реальном времени
                </p>
            </div>
        </div>
    </div>

    @if(isset($error))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ $error }}
                </div>
            </div>
        </div>
    @endif

    @if($webcams->count() > 0)
        <div class="row">
            <div class="col-12 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">
                        Найдено камер: <strong>{{ $webcams->count() }}</strong>
                    </span>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleView('grid')">
                            <i class="fas fa-th"></i> Сетка
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleView('list')">
                            <i class="fas fa-list"></i> Список
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="webcams-grid">
            @foreach($webcams as $webcam)
                <div class="col-lg-4 col-md-6 mb-4 webcam-card" data-webcam-id="{{ $webcam->getId() }}">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <img src="{{ $webcam->getPreviewUrl() }}" 
                                 class="card-img-top webcam-preview" 
                                 alt="{{ $webcam->getName() }}"
                                 loading="lazy">
                            <div class="webcam-overlay">
                                <span class="badge badge-success">
                                    <i class="fas fa-circle mr-1"></i>ОНЛАЙН
                                </span>
                            </div>
                            <div class="webcam-actions">
                                <a href="{{ $webcam->getStreamUrl() }}" 
                                   target="_blank" 
                                   class="btn btn-primary btn-sm"
                                   title="Открыть прямую трансляцию">
                                    <i class="fas fa-play"></i>
                                </a>
                                <a href="{{ route('webcams.show', $webcam->getId()) }}" 
                                   class="btn btn-outline-primary btn-sm"
                                   title="Подробнее">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $webcam->getName() }}</h5>
                            <p class="card-text text-muted">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $webcam->getLocation() }}
                            </p>
                            <p class="card-text">{{ $webcam->getDescription() }}</p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-clock mr-1"></i>
                                    Обновлено: {{ \Carbon\Carbon::parse($webcam->getUpdatedAt())->format('d.m.Y H:i') }}
                                </small>
                                <div class="webcam-status">
                                    <span class="badge badge-success">
                                        <i class="fas fa-wifi mr-1"></i>Активна
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row" id="webcams-list" style="display: none;">
            @foreach($webcams as $webcam)
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="row no-gutters">
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <img src="{{ $webcam->getPreviewUrl() }}" 
                                         class="card-img" 
                                         alt="{{ $webcam->getName() }}"
                                         style="height: 200px; object-fit: cover;"
                                         loading="lazy">
                                    <div class="webcam-overlay">
                                        <span class="badge badge-success">
                                            <i class="fas fa-circle mr-1"></i>ОНЛАЙН
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $webcam->getName() }}</h5>
                                    <p class="card-text text-muted">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        {{ $webcam->getLocation() }}
                                    </p>
                                    <p class="card-text">{{ $webcam->getDescription() }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <a href="{{ $webcam->getStreamUrl() }}" 
                                               target="_blank" 
                                               class="btn btn-primary">
                                                <i class="fas fa-play mr-1"></i>Смотреть трансляцию
                                            </a>
                                            <a href="{{ route('webcams.show', $webcam->getId()) }}" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-info-circle mr-1"></i>Подробнее
                                            </a>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ \Carbon\Carbon::parse($webcam->getUpdatedAt())->format('d.m.Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-video text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3 text-muted">Веб-камеры не найдены</h3>
                    <p class="text-muted">В настоящее время нет доступных камер для отображения.</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<style>
.webcam-card {
    transition: transform 0.2s ease-in-out;
}

.webcam-card:hover {
    transform: translateY(-5px);
}

.webcam-overlay {
    position: absolute;
    top: 10px;
    left: 10px;
}

.webcam-actions {
    position: absolute;
    bottom: 10px;
    right: 10px;
    opacity: 0;
    transition: opacity 0.2s ease-in-out;
}

.webcam-card:hover .webcam-actions {
    opacity: 1;
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

@media (max-width: 768px) {
    .webcam-actions {
        opacity: 1;
    }
}
</style>

<script>
function toggleView(view) {
    const gridView = document.getElementById('webcams-grid');
    const listView = document.getElementById('webcams-list');
    const buttons = document.querySelectorAll('.btn-group button');
    
    buttons.forEach(btn => {
        btn.classList.remove('active');
    });
    
    if (view === 'grid') {
        gridView.style.display = 'flex';
        listView.style.display = 'none';
        buttons[0].classList.add('active');
    } else {
        gridView.style.display = 'none';
        listView.style.display = 'block';
        buttons[1].classList.add('active');
    }
}

// Автоматическое обновление превью камер каждые 30 секунд
setInterval(function() {
    const images = document.querySelectorAll('.webcam-preview');
    images.forEach(img => {
        const currentSrc = img.src;
        const timestamp = new Date().getTime();
        img.src = currentSrc.split('?')[0] + '?t=' + timestamp;
    });
}, 30000);
</script>
@endsection