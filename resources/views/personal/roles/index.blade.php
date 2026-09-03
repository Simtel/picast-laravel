@extends('layouts.personal')
@section('title','Роли и доступ')

@section('content')
    <div class="main-content-header d-flex flex-wrap justify-content-between align-items-center">
        <h1 class="h2">Роли и доступ</h1>
        <a href="{{route('roles.create')}}" class="btn btn-primary">
            <i class="fa fa-plus me-1"></i>Создать роль
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="fa fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2">
            <i class="fa fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-6">
            <form method="GET" action="{{ route('roles.index') }}" class="d-flex gap-2">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Поиск по названию роли..."
                       value="{{ $search ?? '' }}">
                <input type="hidden" name="sort" value="{{ $currentSort ?? 'name' }}">
                <input type="hidden" name="direction" value="{{ $currentDirection ?? 'asc' }}">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fa fa-search"></i>
                </button>
                @if($search ?? false)
                    <a href="{{ route('roles.index', ['sort' => $currentSort, 'direction' => $currentDirection]) }}"
                       class="btn btn-outline-secondary">Сбросить</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th scope="col">
                        <a href="{{ route('roles.index', [
                                'sort' => 'name',
                                'direction' => ($currentSort ?? 'name') === 'name' && ($currentDirection ?? 'asc') === 'asc' ? 'desc' : 'asc',
                                'search' => $search ?? null,
                            ]) }}" class="text-decoration-none text-dark">
                            Роль
                            @if(($currentSort ?? 'name') === 'name')
                                <span class="sort-arrow sort-arrow-{{ ($currentDirection ?? 'asc') === 'asc' ? 'up' : 'down' }} active"></span>
                            @else
                                <span class="sort-arrow sort-arrow-up"></span>
                            @endif
                        </a>
                    </th>
                    <th scope="col">
                        <a href="{{ route('roles.index', [
                                'sort' => 'users_count',
                                'direction' => ($currentSort ?? 'name') === 'users_count' && ($currentDirection ?? 'asc') === 'asc' ? 'desc' : 'asc',
                                'search' => $search ?? null,
                            ]) }}" class="text-decoration-none text-dark">
                            Пользователей
                            @if(($currentSort ?? 'name') === 'users_count')
                                <span class="sort-arrow sort-arrow-{{ ($currentDirection ?? 'asc') === 'asc' ? 'up' : 'down' }} active"></span>
                            @else
                                <span class="sort-arrow sort-arrow-up"></span>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="text-end text-nowrap">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>
                            <span class="badge text-bg-light border">{{$role->name}}</span>
                        </td>
                        <td>{{$role->users_count}}</td>
                        <td class="text-end text-nowrap">
                            <div class="d-flex justify-content-end gap-2 flex-nowrap">
                                <a href="{{route('roles.edit', $role)}}" class="btn btn-sm btn-primary text-white">
                                    <i class="fa fa-shield-halved me-1"></i>Разделы
                                </a>
                                @if(!in_array($role->name, ['admin'], true))
                                    <x-button :route="route('roles.destroy', $role)"
                                              method="DELETE"
                                              class="btn-danger"
                                              icon="trash"
                                              title="Удалить"
                                              :confirm="'Удалить роль «' . $role->name . '»?'"/>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">Роли не найдены.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
