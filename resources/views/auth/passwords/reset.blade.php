@extends('layouts.app_auth')

@section('title', 'Новый пароль — A&S Tech')

@section('content')
    <div class="login-card">
        <div class="login-glow" aria-hidden="true"></div>

        <h2>Новый пароль</h2>
        <p class="login-subtitle">Придумайте новый пароль для аккаунта</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            {{ csrf_field() }}

            <input type="hidden" name="token" value="{{ $token }}">

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
                        value="{{ old('email', $email) }}"
                        required
                        autofocus
                    >
                </div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password" class="form-label">Новый пароль</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Минимум 6 символов"
                        required
                    >
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password-confirm" class="form-label">Подтвердите пароль</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input
                        type="password"
                        id="password-confirm"
                        name="password_confirmation"
                        class="form-control"
                        placeholder="Повторите пароль"
                        required
                    >
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fa-solid fa-shield-halved"></i> Сбросить пароль
            </button>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}">Вернуться ко входу</a>
        </div>
    </div>
@endsection
