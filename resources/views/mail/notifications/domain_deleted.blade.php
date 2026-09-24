@extends('mail.layout')

@section('title', 'Домен удалён из A&S Tech')

@section('content')
    <h1 style="margin:0 0 16px;font-size:22px;line-height:1.3;font-weight:700;color:#e8eefc;">
        Домен удалён
    </h1>

    <p style="margin:0 0 16px;color:#93a1bd;">
        Домен <strong style="color:#f87171;">{{ $domain->name }}</strong> был удалён из системы.
        Его WHOIS-данные больше не отслеживаются.
    </p>

    @include('mail.partials.button', ['url' => route('domains.index'), 'label' => 'Открыть личный кабинет'])

    <p style="margin:0;color:#93a1bd;">
        Спасибо, что пользуетесь нашим сервисом!
    </p>
@endsection
