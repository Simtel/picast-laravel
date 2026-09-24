@extends('mail.layout')

@section('title', 'Подтверждение email — A&S Tech')

@section('content')
    <h1 style="margin:0 0 16px;font-size:22px;line-height:1.3;font-weight:700;color:#e8eefc;">
        Подтверждение email
    </h1>

    <p style="margin:0 0 16px;color:#93a1bd;">
        Здравствуйте, {{ $user->name }}! Подтвердите адрес электронной почты,
        чтобы активировать аккаунт A&amp;S Tech.
    </p>

    @include('mail.partials.button', ['url' => $url, 'label' => 'Подтвердить email'])

    <p style="margin:0;font-size:13px;line-height:1.6;color:#63708c;">
        Если вы не создавали аккаунт, просто проигнорируйте это письмо.
    </p>
@endsection
