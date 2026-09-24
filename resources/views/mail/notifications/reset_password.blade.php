@extends('mail.layout')

@section('title', 'Сброс пароля — A&S Tech')

@section('content')
    <h1 style="margin:0 0 16px;font-size:22px;line-height:1.3;font-weight:700;color:#e8eefc;">
        Сброс пароля
    </h1>

    <p style="margin:0 0 16px;color:#93a1bd;">
        Здравствуйте, {{ $user->name }}! Мы получили запрос на сброс пароля для вашего аккаунта.
    </p>

    @include('mail.partials.button', ['url' => $url, 'label' => 'Сбросить пароль'])

    <p style="margin:0 0 16px;font-size:13px;line-height:1.6;color:#63708c;">
        Ссылка действительна {{ $expire }} минут. Если вы не запрашивали сброс пароля,
        просто проигнорируйте это письмо — ваш пароль останется прежним.
    </p>

    <p style="margin:0;font-size:13px;line-height:1.6;color:#63708c;">
        Если кнопка не работает, скопируйте ссылку в браузер:<br>
        <a href="{{ $url }}" target="_blank" style="color:#a5b4fc;text-decoration:none;word-break:break-all;">{{ $url }}</a>
    </p>
@endsection
