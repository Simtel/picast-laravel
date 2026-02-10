@extends('layouts.personal')
@section('title', 'Турниры')

@section('content')
<div class="main-content-header">
    <h1 class="h2">
        <i class="fa fa-trophy mr-2 text-warning"></i>
        Турниры
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <span class="text-muted">
            Всего турниров: <strong>{{ $tournaments->total() }}</strong>
        </span>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">
                <a href="{{ route('tournaments.index', ['sort_by' => 'title', 'sort_order' => ($sortBy === 'title' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="d-flex align-items-center gap-1">
                    Название
                    @if ($sortBy === 'title')
                        <i class="fa fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                    @else
                        <i class="fa fa-sort text-muted"></i>
                    @endif
                </a>
            </th>
            <th scope="col">
                <a href="{{ route('tournaments.index', ['sort_by' => 'date', 'sort_order' => ($sortBy === 'date' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="d-flex align-items-center gap-1">
                    Дата
                    @if ($sortBy === 'date')
                        <i class="fa fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                    @else
                        <i class="fa fa-sort text-muted"></i>
                    @endif
                </a>
            </th>
            <th scope="col">Ссылка</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($tournaments as $tournament)
            <tr>
                <th scope="row">{{ $tournaments->firstItem() + $loop->iteration - 1 }}</th>
                <td>
                    <strong>{{ $tournament->title }}</strong>
                </td>
                <td>
                    @if($tournament->date)
                        <i class="fa fa-calendar-alt mr-1 text-muted"></i>
                        {{ \Carbon\Carbon::parse($tournament->date)->format('d.m.Y') }}
                    @else
                        <span class="text-muted">Не указана</span>
                    @endif
                </td>
                <td>
                    @if($tournament->link)
                        <a href="{{ $tournament->link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-external-link-alt mr-1"></i>Открыть
                        </a>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<!-- Пагинация -->
<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted">
        Показано {{ $tournaments->firstItem() ?? 0 }} - {{ $tournaments->lastItem() ?? 0 }} из {{ $tournaments->total() }} записей
    </div>
    <div>
        {{ $tournaments->appends(['sort_by' => $sortBy, 'sort_order' => $sortOrder])->links() }}
    </div>
</div>
@endsection