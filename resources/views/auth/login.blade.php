@extends('layouts.app_auth')

@section('title', 'Вход — A&S Tech')

@section('content')
    <div class="login-card">
        <div class="login-glow" aria-hidden="true"></div>

        <h2>Вход в систему</h2>
        <p class="login-subtitle">Личный кабинет A&amp;S Tech</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}

            <div class="field">
                <label for="email" class="form-label">Email</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope"></i>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="you@example.com"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                </div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password" class="form-label">Пароль</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Введите пароль"
                        required
                    >
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                <label class="form-check-label" for="remember">Запомнить меня</label>
            </div>

            <button type="submit" class="btn-login">
                <i class="fa-solid fa-right-to-bracket"></i> Войти
            </button>
        </form>

        <div class="auth-links">
            <a href="{{ route('password.request') }}">Забыли пароль?</a>
            <span>Нет аккаунта? <a href="{{ route('register') }}">Зарегистрироваться</a></span>
        </div>
    </div>
@endsection
