@extends('layouts.app_auth')

@section('title', 'Подтверждение email — A&S Tech')

@section('content')
    <div class="login-card">
        <div class="login-glow" aria-hidden="true"></div>

        <h2>Подтверждение email</h2>
        <p class="login-subtitle">Проверьте вашу почту</p>

        @if (session('resent'))
            <div class="alert alert-success">
                Новая ссылка для подтверждения отправлена на ваш email.
            </div>
        @endif

        <p class="auth-text">
            Перед продолжением проверьте почту — мы отправили письмо со ссылкой
            для подтверждения адреса.
        </p>

        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="btn-login">
                <i class="fa-solid fa-paper-plane"></i> Отправить ещё раз
            </button>
        </form>

        <div class="auth-links">
            <a href="{{ route('logout') }}">Выйти</a>
        </div>
    </div>
@endsection
