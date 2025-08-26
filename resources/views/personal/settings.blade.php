@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Настройки пользователя</h1>
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

        <!-- Форма редактирования профиля -->
        <h2 class="h2">Личная информация</h2>
        <hr>
        {{ Html::form('POST', route('settings.profile'))->open() }}

        <div class="form-group">
            {{ Html::label('Имя', 'name') }}
            {{ Html::text('name', $user->name)->class('form-control') }}
        </div>

        <div class="form-group">
            {{ Html::label('E-mail', 'email') }}
            {{ Html::email('email', $user->email)->class('form-control') }}
        </div>

        <div class="form-group">
            {{ Html::label('Дата рождения', 'birth_date') }}
            {{ Html::date('birth_date', $user->birth_date?->format('Y-m-d'))->class('form-control') }}
            <small class="form-text text-muted">Укажите вашу дату рождения (необязательно)</small>
        </div>

        {{ Html::submit('Обновить профиль')->class('btn btn-primary') }}

        {{ Html::form()->close() }}
        
        <br>
        <br>
        
        <!-- Форма смены пароля -->
        <h2 class="h2">Изменить пароль</h2>
        <hr>
        {{ Html::form('POST', route('settings.password'))->open() }}

        <div class="form-group">
            {{ Html::label('Новый пароль', 'password') }}
            {{ Html::password('password')->class('form-control')}}
        </div>

        {{ Html::submit('Обновить пароль')->class('btn btn-primary')}}

        {{ Html::form()->close() }}
        
        <br>
        <br>
        
        <!-- API Токен -->
        <h2 class="h2">API Токен</h2>
        <hr>
        {{ Html::form()->route('settings.token')->open() }}

        <div class="form-group">
            {{ Html::label('Токен', 'token') }}
            {{ Html::text('password', $token)->class('form-control')->attribute('readonly') }}
        </div>

        {{ Html::submit('Получить новый токен')->class('btn btn-primary')}}

        {{ Html::form()->close() }}
    </main>
@endsection