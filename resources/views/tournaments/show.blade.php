@extends('layouts.personal')
@section('title', $tournament->title)

@section('content')
<div class="main-content-header">
    <h1 class="h2">
        <i class="fa fa-trophy mr-2 text-warning"></i>
        {{ $tournament->title }}
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('tournaments.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left mr-1"></i>Назад к списку
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h5 class="card-title mb-4">Информация о турнире</h5>

                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold">Название</label>
                    <div class="h4">{{ $tournament->title }}</div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold">Дата проведения</label>
                    <div>
                        @if($tournament->date)
                            <span class="badge badge-primary fs-6">
                                <i class="fa fa-calendar-alt mr-1"></i>
                                {{ \Carbon\Carbon::parse($tournament->date)->format('d.m.Y') }}
                            </span>
                            @if($tournament->date_end && $tournament->date_end != $tournament->date)
                                <span class="mx-2">—</span>
                                <span class="badge badge-primary fs-6">
                                    <i class="fa fa-calendar-check mr-1"></i>
                                    {{ \Carbon\Carbon::parse($tournament->date_end)->format('d.m.Y') }}
                                </span>
                            @endif
                        @else
                            <span class="text-muted">Не указана</span>
                        @endif
                    </div>
                </div>

                @if($tournament->city)
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold">Город</label>
                    <div class="h5">
                        <i class="fa fa-map-marker-alt text-danger mr-2"></i>
                        {{ $tournament->city }}
                    </div>
                </div>
                @endif

                @if($tournament->organizer)
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold">Организатор</label>
                    <div class="h5">
                        <i class="fa fa-user text-primary mr-2"></i>
                        {{ $tournament->organizer }}
                    </div>
                </div>
                @endif

                @if($tournament->link)
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold">Страница на DanceManager</label>
                    <div>
                        <a href="{{ $tournament->link }}" target="_blank" class="btn btn-primary">
                            <i class="fa fa-external-link-alt mr-1"></i>
                            Открыть на DanceManager
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title text-muted small text-uppercase fw-bold mb-3">Дополнительно</h6>

                        <div class="mb-2">
                            <small class="text-muted">ID:</small>
                            <code>{{ $tournament->id }}</code>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">GUID:</small>
                            <code>{{ $tournament->guid }}</code>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">Создан:</small>
                            <div>{{ \Carbon\Carbon::parse($tournament->created_at)->format('d.m.Y H:i') }}</div>
                        </div>

                        <div>
                            <small class="text-muted">Обновлён:</small>
                            <div>{{ \Carbon\Carbon::parse($tournament->updated_at)->format('d.m.Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
