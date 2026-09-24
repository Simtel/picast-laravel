@extends('mail.layout')

@section('title', 'Срок регистрации домена истекает')

@section('content')
    <h1 style="margin:0 0 16px;font-size:22px;line-height:1.3;font-weight:700;color:#e8eefc;">
        Добрый день, {{ $user->name }}!
    </h1>

    <p style="margin:0 0 16px;color:#93a1bd;">
        Срок регистрации вашего домена подходит к концу.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 8px;">
        <tr>
            <td style="padding:16px 20px;border-radius:12px;border:1px solid #1e2a44;background-color:#111a2e;">
                <div style="font-family:'Segoe UI',Arial,sans-serif;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;color:#63708c;margin-bottom:6px;">Домен</div>
                <div style="font-family:'Space Grotesk','Segoe UI',Arial,sans-serif;font-size:20px;font-weight:700;color:#e8eefc;margin-bottom:10px;">{{ $domain->name }}</div>
                <div style="font-family:'Segoe UI',Arial,sans-serif;font-size:14px;color:#93a1bd;">
                    Истекает: <strong style="color:#f59e0b;">{{ $domain->expire_at?->format('d.m.Y') }}</strong>
                </div>
            </td>
        </tr>
    </table>

    @include('mail.partials.button', ['url' => route('domains.index'), 'label' => 'Открыть личный кабинет'])

    <p style="margin:0;color:#93a1bd;">
        Не забудьте продлить домен у своего регистратора.
    </p>
@endsection
