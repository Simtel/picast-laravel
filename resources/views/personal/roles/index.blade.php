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

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th scope="col">Роль</th>
                    <th scope="col">Пользователей</th>
                    <th scope="col" class="text-end">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>
                            <span class="badge text-bg-light border">{{$role->name}}</span>
                        </td>
                        <td>{{$role->users_count}}</td>
                        <td class="text-end">
                            <a href="{{route('roles.edit', $role)}}" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-shield-halved me-1"></i>Разделы
                            </a>
                            @if(!in_array($role->name, ['admin'], true))
                                {{ Html::form('DELETE', route('roles.destroy', $role))->id('delete-role-' . $role->id)->open() }}
                                {{ Html::button('<i class="fa fa-trash"></i>')
                                    ->attribute('type', 'submit')
                                    ->class('btn btn-sm btn-outline-danger')
                                    ->attribute('data-confirm', 'Удалить роль «' . $role->name . '»?') }}
                                {{ Html::form()->close() }}
                            @endif
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
