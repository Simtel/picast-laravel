@extends('layouts.app_auth')

@section('title', 'Подтверждение пароля — A&S Tech')

@section('content')
    <div class="login-card">
        <div class="login-glow" aria-hidden="true"></div>

        <h2>Подтверждение пароля</h2>
        <p class="login-subtitle">Введите пароль, чтобы продолжить</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

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
                        autofocus
                        autocomplete="current-password"
                    >
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-login">
                <i class="fa-solid fa-check"></i> Подтвердить
            </button>
        </form>

        <div class="auth-links">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Забыли пароль?</a>
            @endif
        </div>
    </div>
@endsection
