@extends('layouts.personal')
@section('title','Редактирование пользователя')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Редактировать профиль</h1>

        </div>
        
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{ Html::form('POST',route('user.update',$user))->open()}}

        <div class="form-group">
            {{ Html::label('Имя', 'name') }}
            {{ Html::text('name', $user->name)->class('form-control') }}
        </div>
        <div class="form-group">
            {{ Html::label('E-mail', 'email') }}
            {{ Html::text('email', $user->email)->class('form-control') }}
        </div>

        <div class="form-group">
            {{ Html::label('Дата рождения', 'birth_date') }}
            {{ Html::date('birth_date', $user->birth_date?->format('Y-m-d'))->class('form-control') }}
            <small class="form-text text-muted">Укажите дату рождения пользователя (необязательно)</small>
        </div>

        <div class="form-group">
            {{ Html::label('Роли', 'roles[]') }}
            @foreach($roles as $role)
                {{ Html::checkbox('roles[]', $user->hasRole($role->name),$role->name) }} {{$role->name}}
            @endforeach
        </div>

        {{ Html::submit('Сохранить')->class('btn btn-primary') }}

        {{ Html::form()->close() }}
    </main>

@endsection