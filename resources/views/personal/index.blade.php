@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Пользователи</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{route('invite')}}" class="btn btn-primary">
                <i class="fa fa-user-plus mr-1"></i>Пригласить
            </a>
        </div>
    </div>

    <!-- Форма поиска -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" action="{{ request()->url() }}">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Поиск по имени или email..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ request()->url() }}" class="btn btn-outline-secondary">Очистить</a>
                        @endif
                    </div>
                </div>
                <input type="hidden" name="sort" value="{{ request('sort', 'created_at') }}">
                <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">
                    <a href="{{ request()->url() }}?sort=name&direction={{ request('direction') == 'asc' && request('sort') == 'name' ? 'desc' : 'asc' }}&search={{ request('search') }}" class="d-flex align-items-center gap-1">
                        Имя
                        @if(request('sort') == 'name')
                            <i class="fa fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fa fa-sort text-muted"></i>
                        @endif
                    </a>
                </th>
                <th scope="col">
                    <a href="{{ request()->url() }}?sort=email&direction={{ request('direction') == 'asc' && request('sort') == 'email' ? 'desc' : 'asc' }}&search={{ request('search') }}" class="d-flex align-items-center gap-1">
                        E-mail
                        @if(request('sort') == 'email')
                            <i class="fa fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fa fa-sort text-muted"></i>
                        @endif
                    </a>
                </th>
                <th scope="col">
                    <a href="{{ request()->url() }}?sort=created_at&direction={{ request('direction') == 'asc' && request('sort') == 'created_at' ? 'desc' : 'asc' }}&search={{ request('search') }}" class="d-flex align-items-center gap-1">
                        Дата регистрации
                        @if(request('sort') == 'created_at')
                            <i class="fa fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fa fa-sort text-muted"></i>
                        @endif
                    </a>
                </th>
                <th scope="col">
                    <a href="{{ request()->url() }}?sort=birth_date&direction={{ request('direction') == 'asc' && request('sort') == 'birth_date' ? 'desc' : 'asc' }}&search={{ request('search') }}" class="d-flex align-items-center gap-1">
                        Дата рождения
                        @if(request('sort') == 'birth_date')
                            <i class="fa fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fa fa-sort text-muted"></i>
                        @endif
                    </a>
                </th>
                <th scope="col">Роли пользователя</th>
                <th scope="col">Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <th scope="row">{{ $users->firstItem() + $loop->iteration - 1 }}</th>
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->created_at->format('d.m.Y H:i')}}</td>
                    <td>{{ $user->birth_date ? $user->birth_date->format('d.m.Y') : 'Не указана' }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge">{{$role->name}}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{route('user.edit',[$user])}}" class="btn btn-sm btn-primary text-white">
                            <i class="fa fa-edit mr-1"></i>Редактировать
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Пагинация -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Показано {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} из {{ $users->total() }} записей
        </div>
        <div>
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
@endsection