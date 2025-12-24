@extends('layouts.personal')
@section('title','Тест галереи')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="image-gallery" style="border: 3px solid red; padding: 20px;">
            <!-- Gallery Header -->
            <div class="gallery-header" style="border: 2px solid blue;">
                <div>
                    <h1 class="gallery-title" style="color: red !important;">ТЕСТ ГАЛЕРЕИ</h1>
                    <div class="gallery-stats" style="color: blue;">
                        Тестовая галерея для проверки стилей
                    </div>
                </div>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{route('images.create')}}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Добавить изображение
                    </a>
                </div>
            </div>

            <!-- Test Grid -->
            <div class="gallery-grid" style="border: 2px solid green; min-height: 300px;">
                @if($images->count() > 0)
                    @foreach($images as $image)
                        <div class="gallery-item" data-image-id="{{ $image->id }}" style="border: 2px solid orange;">
                            <div class="image-container" style="border: 2px solid purple; height: 200px;">
                                <img src="{{ $image->getPath() }}" 
                                     alt="{{ $image->filename }}"
                                     style="border: 2px solid red; max-width: 100%; max-height: 100%; object-fit: cover;"
                                     loading="lazy">
                                
                                <!-- Overlay -->
                                <div class="image-overlay" style="background: rgba(0,0,0,0.5); color: white; padding: 10px;">
                                    <div class="overlay-info">
                                        <div class="overlay-filename">{{ $image->filename }}</div>
                                        <div class="overlay-meta">
                                            {{ $image->created_at->format('d.m.Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Image Info -->
                            <div class="image-info" style="border: 2px solid brown; padding: 10px;">
                                <div class="image-filename" title="{{ $image->filename }}">
                                    {{ $image->filename }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="border: 2px solid red; padding: 20px; text-align: center;">
                        <h3>Нет изображений для тестирования</h3>
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection

@push('styles')
    <link rel="stylesheet" href="/css/gallery.css">
    <style>
        /* Дополнительные стили для тестирования */
        .gallery-item img {
            max-width: 100% !important;
            max-height: 100% !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }
        
        .gallery-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
            grid-gap: 20px !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="/js/gallery.js"></script>
@endpush