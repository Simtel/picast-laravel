@extends('mail.layout')

@section('title', 'Приглашение в A&S Tech')

@section('content')
    <h1 style="margin:0 0 16px;font-size:22px;line-height:1.3;font-weight:700;color:#e8eefc;">
        Добрый день, {{ $name }}!
    </h1>

    <p style="margin:0 0 16px;color:#93a1bd;">
        Вы приглашены в <strong style="color:#e8eefc;">A&amp;S Tech</strong> — платформу для
        WHOIS-мониторинга доменов, управления YouTube-видео, турнирной аналитики,
        AI-ассистента и инструментов разработчика.
    </p>

    <p style="margin:0 0 12px;color:#93a1bd;">Ваш код для регистрации:</p>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 8px;">
        <tr>
            <td style="padding:14px 28px;border-radius:12px;border:1px solid #1e2a44;background-color:#111a2e;font-family:'Space Grotesk','Segoe UI',Arial,sans-serif;font-size:26px;font-weight:700;letter-spacing:0.28em;color:#22d3ee;">
                {{ $code }}
            </td>
        </tr>
    </table>

    @include('mail.partials.button', ['url' => route('register'), 'label' => 'Зарегистрироваться'])

    <p style="margin:0;font-size:13px;line-height:1.6;color:#63708c;">
        Если кнопка не работает, скопируйте ссылку в браузер:<br>
        <a href="{{ route('register') }}" target="_blank" style="color:#a5b4fc;text-decoration:none;">{{ route('register') }}</a>
    </p>
@endsection
