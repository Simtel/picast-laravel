@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Настройки пользователя</h1>

        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{ Html::form('POST', route('settings.password'))->open() }}

        <div class="form-group">
            {{ Html::label('Новый пароль', 'password') }}
            {{ Html::password('password')->class('form-control')}}
        </div>

        {{ Html::submit('Обновить пароль')->class('btn btn-primary')}}

        {{ Html::form()->close() }}
        <br>
        <br>
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