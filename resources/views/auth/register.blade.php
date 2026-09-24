@extends('layouts.app_auth')

@section('title', 'Регистрация — A&S Tech')
@section('shell-class', 'auth-shell--wide')

@section('content')
    <div class="login-card">
        <div class="login-glow" aria-hidden="true"></div>

        <h2>Регистрация</h2>
        <p class="login-subtitle">Создайте аккаунт A&amp;S Tech</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            {{ csrf_field() }}

            <div class="field">
                <label for="name" class="form-label">Имя</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-user"></i>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Ваше имя"
                        value="{{ old('name') }}"
                        required
                        autofocus
                    >
                </div>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

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

            <div class="field">
                <label for="code" class="form-label">Код приглашения</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-key"></i>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        class="form-control @error('code') is-invalid @enderror"
                        placeholder="6-значный код"
                        value="{{ old('code') }}"
                        required
                    >
                </div>
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-login">
                <i class="fa-solid fa-user-plus"></i> Зарегистрироваться
            </button>
        </form>

        <div class="auth-links">
            <span>Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a></span>
        </div>
    </div>
@endsection
