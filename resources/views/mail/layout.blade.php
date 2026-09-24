<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark light">
    <title>@yield('title', 'A&S Tech')</title>
</head>
<body style="margin:0;padding:0;background-color:#060912;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#060912;">
    <tr>
        <td align="center" style="padding:36px 16px;">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;max-width:600px;background-color:#0d1322;border:1px solid #1e2a44;border-radius:18px;overflow:hidden;">
                <tr>
                    <td style="height:4px;font-size:0;line-height:0;background-color:#6366f1;background-image:linear-gradient(90deg,#22d3ee 0%,#6366f1 50%,#a855f7 100%);">&nbsp;</td>
                </tr>

                <tr>
                    <td style="padding:28px 36px 4px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="width:44px;height:44px;text-align:center;vertical-align:middle;border-radius:12px;background-color:#6366f1;background-image:linear-gradient(135deg,#6366f1 0%,#a855f7 60%,#22d3ee 130%);font-family:'Segoe UI',Arial,sans-serif;font-size:13px;font-weight:700;color:#ffffff;">A&amp;S</td>
                                <td style="padding-left:12px;font-family:'Segoe UI',Arial,sans-serif;font-size:18px;font-weight:700;color:#e8eefc;">A&amp;S Tech</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:20px 36px 8px;font-family:'Segoe UI',Arial,sans-serif;font-size:15px;line-height:1.65;color:#93a1bd;">
                        @yield('content')
                    </td>
                </tr>

                <tr>
                    <td style="padding:24px 36px 30px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="border-top:1px solid #1e2a44;padding-top:20px;font-family:'Segoe UI',Arial,sans-serif;font-size:12px;line-height:1.6;color:#63708c;">
                                    <p style="margin:0 0 6px;">Домены · Медиа · Турниры · AI · Инструменты</p>
                                    <p style="margin:0;">&copy; {{ date('Y') }} A&amp;S Tech. Все права защищены.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
