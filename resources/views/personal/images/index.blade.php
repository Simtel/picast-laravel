@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')

            <!-- Gallery Header -->
            <div class="gallery-header">
                <div>
                    <h1 class="gallery-title">Галерея изображений</h1>
                    <div class="gallery-stats">
                        Всего изображений: {{ $images->total() }}
                        @if($images->hasPages())
                            • Страница {{ $images->currentPage() }} из {{ $images->lastPage() }}
                        @endif
                        • Показано: {{ $images->count() }}
                    </div>
                </div>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{route('images.create')}}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Добавить изображение
                    </a>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4" style="gap: 15px;">
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           id="gallery-search" 
                           placeholder="Поиск по названию...">
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary gallery-filter active" data-filter="all">
                        Все
                    </button>
                    <button type="button" class="btn btn-outline-secondary gallery-filter" data-filter="recent">
                        Недавние
                    </button>
                    <button type="button" class="btn btn-outline-secondary gallery-filter" data-filter="large">
                        Большие
                    </button>
                </div>
            </div>

            <!-- Image Grid -->
            @if($images->count() > 0)
                <div class="gallery-grid">
                    @foreach($images as $image)
                        <div class="gallery-item" data-image-id="{{ $image->id }}">
                            <div class="image-container">
                                <img src="{{ $image->getPath() }}" 
                                     alt="{{ $image->filename }}"
                                     loading="lazy">
                                
                                <!-- Overlay -->
                                <div class="image-overlay">
                                    <div class="overlay-info">
                                        <div class="overlay-filename">{{ $image->filename }}</div>
                                        <div class="overlay-meta">
                                            {{ $image->created_at->format('d.m.Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="action-buttons">
                                    <a href="{{ route('images.show', $image) }}" 
                                       class="btn-icon" 
                                       title="Просмотр">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Image Info -->
                            <div class="image-info">
                                <div class="image-filename" title="{{ $image->filename }}">
                                    {{ $image->filename }}
                                </div>
                                <div class="image-meta">
                                <span class="image-date">
                                    {{ $image->created_at->format('d.m.Y') }}
                                </span>
                                <span class="image-views">
                                    <i class="fas fa-eye"></i> {{ $image->views_count }}
                                </span>
                            </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-images fa-3x text-muted"></i>
                    </div>
                    <h3 class="text-muted mb-3">Изображения не найдены</h3>
                    <p class="text-muted mb-4">Загрузите свои первые изображения, чтобы создать галерею</p>
                    <a href="{{route('images.create')}}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Добавить изображение
                    </a>
                </div>
            @endif

            <!-- Pagination -->
            @if($images->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    <nav aria-label="Навигация по страницам">
                        {{ $images->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
            @endif

@endsection

@push('styles')
    <link rel="stylesheet" href="/css/gallery.css">
@endpush

@push('scripts')
    <script src="/js/gallery.js"></script>
@endpush
