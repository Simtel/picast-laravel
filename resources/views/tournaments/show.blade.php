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

<!-- Группы турнира -->
<div class="main-content-header mt-4">
    <h2 class="h4">
        <i class="fa fa-users mr-2 text-info"></i>
        Группы турнира
    </h2>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('tournaments.show', ['id' => $tournament->id]) }}" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Поиск по названию группы..." 
                           value="{{ request('search', '') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-hashtag"></i></span>
                    <input type="number" 
                           name="number" 
                           class="form-control" 
                           placeholder="Номер группы" 
                           min="1"
                           value="{{ request('number', '') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa fa-filter mr-1"></i>Фильтр
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('tournaments.show', ['id' => $tournament->id]) }}" class="btn btn-outline-secondary w-100">
                    <i class="fa fa-times mr-1"></i>Сброс
                </a>
            </div>
            <div class="col-md-1">
                <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                <input type="hidden" name="sort_order" value="{{ $sortOrder }}">
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        @if($groups->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fa fa-inbox fa-3x mb-3"></i>
                <p class="mb-0">Группы не найдены</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="border-0">
                                <a href="{{ route('tournaments.show', array_merge(request()->query(), ['id' => $tournament->id, 'sort_by' => 'number', 'sort_order' => ($sortBy === 'number' && $sortOrder === 'asc') ? 'desc' : 'asc'])) }}" class="d-flex align-items-center gap-1 text-decoration-none">
                                    №
                                    @if ($sortBy === 'number')
                                        <i class="fa fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fa fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="border-0">
                                <a href="{{ route('tournaments.show', array_merge(request()->query(), ['id' => $tournament->id, 'sort_by' => 'name', 'sort_order' => ($sortBy === 'name' && $sortOrder === 'asc') ? 'desc' : 'asc'])) }}" class="d-flex align-items-center gap-1 text-decoration-none">
                                    Название группы
                                    @if ($sortBy === 'name')
                                        <i class="fa fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fa fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="border-0">
                                <a href="{{ route('tournaments.show', array_merge(request()->query(), ['id' => $tournament->id, 'sort_by' => 'registrations', 'sort_order' => ($sortBy === 'registrations' && $sortOrder === 'asc') ? 'desc' : 'asc'])) }}" class="d-flex align-items-center gap-1 text-decoration-none">
                                    Регистраций
                                    @if ($sortBy === 'registrations')
                                        <i class="fa fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fa fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($groups as $group)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $group->number }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $group->name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <i class="fa fa-user mr-1"></i>
                                        {{ $group->registrations }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($groups->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Показано {{ $groups->firstItem() ?? 0 }} - {{ $groups->lastItem() ?? 0 }} из {{ $groups->total() }} групп
                </div>
                {{ $groups->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
