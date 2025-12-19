@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Пользователи</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="{{route('invite')}}" class="btn btn-primary">Пригласить</a>
            </div>
        </div>

        <!-- Форма поиска -->
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" action="{{ request()->url() }}">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Поиск по имени или email..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">Найти</button>
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

        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">
                    <a href="{{ request()->url() }}?sort=name&direction={{ request('direction') == 'asc' && request('sort') == 'name' ? 'desc' : 'asc' }}&search={{ request('search') }}" class="text-decoration-none">
                        Имя
                        @if(request('sort') == 'name')
                            <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort text-muted"></i>
                        @endif
                    </a>
                </th>
                <th scope="col">
                    <a href="{{ request()->url() }}?sort=email&direction={{ request('direction') == 'asc' && request('sort') == 'email' ? 'desc' : 'asc' }}&search={{ request('search') }}" class="text-decoration-none">
                        E-mail
                        @if(request('sort') == 'email')
                            <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort text-muted"></i>
                        @endif
                    </a>
                </th>
                <th scope="col">
                    <a href="{{ request()->url() }}?sort=created_at&direction={{ request('direction') == 'asc' && request('sort') == 'created_at' ? 'desc' : 'asc' }}&search={{ request('search') }}" class="text-decoration-none">
                        Дата регистрации
                        @if(request('sort') == 'created_at')
                            <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort text-muted"></i>
                        @endif
                    </a>
                </th>
                <th scope="col">
                    <a href="{{ request()->url() }}?sort=birth_date&direction={{ request('direction') == 'asc' && request('sort') == 'birth_date' ? 'desc' : 'asc' }}&search={{ request('search') }}" class="text-decoration-none">
                        Дата рождения
                        @if(request('sort') == 'birth_date')
                            <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort text-muted"></i>
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
                            <span class="badge badge-secondary">{{$role->name}}</span>
                        @endforeach
                    </td>
                    <td><a href="{{route('user.edit',[$user])}}" class="btn btn-sm btn-primary">Редактировать</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Пагинация -->
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Показано {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} из {{ $users->total() }} записей
            </div>
            <div>
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </main>
@endsection