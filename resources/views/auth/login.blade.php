@extends('layouts.app_auth')

@section('content')
    <div class="text-center">
        <form class="form-signin"  method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}
            <h1 class="h3 mb-3 font-weight-normal">Войти</h1>
            <label for="inputEmail" class="sr-only">Email</label>
            <input type="email" id="inputEmail" class="form-control"  name="email" placeholder="Введите email адрес "  value="{{ old('email') }}" required autofocus>
            <label for="inputPassword" class="sr-only">Пароль</label>
            <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Пароль" required>
            <div class="checkbox mb-3">
                <label>
                    <input type="checkbox" value="remember-me"> Запомнить меня
                </label>
            </div>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>

        </form>
    </div>
@endsection
