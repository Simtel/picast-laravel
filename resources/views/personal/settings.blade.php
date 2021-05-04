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
        {{ Form::open(['route' => 'settings.password']) }}

        <div class="form-group">
            {{ Form::label('password', 'Старый пароль') }}
            {{ Form::password('password', ['class' => 'form-control']) }}
        </div>
        <div class="form-group">
            {{ Form::label('new_password', 'Новый пароль') }}
            {{ Form::password('new_password', ['class' => 'form-control']) }}
        </div>

        {{ Form::submit('Обновить пароль', array('class' => 'btn btn-primary')) }}

        {{ Form::close() }}
        <br>
        <br>
        <h2 class="h2">API Токен</h2>
        <hr>
        {{ Form::open(['route' => 'settings.token']) }}

        <div class="form-group">
            {{ Form::label('token', 'Токен') }}
            {{ Form::text('password', $token,['class' => 'form-control','readonly' => 'true']) }}
        </div>

        {{ Form::submit('Получить новый токен', array('class' => 'btn btn-primary')) }}

        {{ Form::close() }}
    </main>
@endsection