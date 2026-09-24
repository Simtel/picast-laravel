@extends('layouts.app_auth')

@section('title', 'Сброс пароля — A&S Tech')

@section('content')
    <div class="login-card">
        <div class="login-glow" aria-hidden="true"></div>

        <h2>Сброс пароля</h2>
        <p class="login-subtitle">Укажите email для восстановления доступа</p>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
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

            <button type="submit" class="btn-login">
                <i class="fa-solid fa-paper-plane"></i> Отправить ссылку
            </button>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}">Вернуться ко входу</a>
        </div>
    </div>
@endsection
