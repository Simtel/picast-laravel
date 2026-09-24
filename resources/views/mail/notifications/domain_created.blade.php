@extends('mail.layout')

@section('title', 'Домен добавлен в A&S Tech')

@section('content')
    <h1 style="margin:0 0 16px;font-size:22px;line-height:1.3;font-weight:700;color:#e8eefc;">
        Домен добавлен
    </h1>

    <p style="margin:0 0 16px;color:#93a1bd;">
        Домен <strong style="color:#22d3ee;">{{ $domain->name }}</strong> был добавлен в систему.
        Мы начнём отслеживать его WHOIS-данные и сообщим, когда подойдёт срок продления.
    </p>

    @include('mail.partials.button', ['url' => route('domains.index'), 'label' => 'Перейти к домену'])

    <p style="margin:0;color:#93a1bd;">
        Спасибо, что пользуетесь нашим сервисом!
    </p>
@endsection
