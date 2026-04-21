<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Witamy w systemie ENESA</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: "Segoe UI", Arial, sans-serif; font-size: 14px; color: #1a2e3d; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: linear-gradient(130deg, #0e89d8 0%, #1ba84a 100%); padding: 32px 36px 28px; text-align: center; }
        .header h1 { color: #fff; font-size: 26px; font-weight: 800; letter-spacing: -.3px; }
        .header p { color: rgba(255,255,255,.82); font-size: 13px; margin-top: 4px; }
        .body { padding: 32px 36px; }
        .greeting { font-size: 16px; font-weight: 700; margin-bottom: 12px; color: #0f2330; }
        .intro { color: #3b5567; line-height: 1.6; margin-bottom: 22px; }
        .credentials-box { background: #f3f8fc; border: 1px solid #c9dcea; border-radius: 10px; padding: 18px 22px; margin-bottom: 24px; }
        .credentials-box .cred-title { font-size: 12px; font-weight: 700; color: #6b8aa3; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 12px; }
        .cred-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .cred-row:last-child { margin-bottom: 0; }
        .cred-icon { width: 28px; text-align: center; font-size: 16px; flex-shrink: 0; }
        .cred-label { font-size: 11px; color: #7a99b2; width: 80px; flex-shrink: 0; }
        .cred-value { font-size: 14px; font-weight: 700; color: #0f2330; font-family: "Courier New", monospace; }
        .cta-wrap { text-align: center; margin: 20px 0 24px; }
        .cta-btn { display: inline-block; padding: 12px 32px; background: linear-gradient(130deg, #0e89d8, #1ba84a); color: #fff; text-decoration: none; font-weight: 800; font-size: 15px; border-radius: 8px; letter-spacing: .2px; }
        .notice { background: #fff8e1; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 10px 14px; font-size: 12px; color: #6b4800; line-height: 1.5; margin-bottom: 20px; }
        .company-info { font-size: 12px; color: #4c6373; background: #f8fbff; border-radius: 8px; padding: 10px 14px; margin-bottom: 24px; }
        .company-info strong { color: #0e4f7a; }
        .footer { background: #1a2e3d; padding: 20px 36px; text-align: center; }
        .footer p { color: #7fa3be; font-size: 11px; line-height: 1.7; }
        .footer strong { color: #9fc8e4; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>ENESA</h1>
        <p>Energy Audit Systems</p>
    </div>

    <div class="body">
        <div class="greeting">Witaj, {{ $user->first_name ?? $user->name }}!</div>

        <p class="intro">
            Zostałeś/aś zarejestrowany/a w systemie <strong>ENESA</strong> jako przedstawiciel firmy
            <strong>{{ $company->name }}</strong>. Poniżej znajdziesz dane potrzebne do pierwszego logowania.
        </p>

        <div class="credentials-box">
            <div class="cred-title">Twoje dane do logowania</div>
            <div class="cred-row">
                <div class="cred-icon">📧</div>
                <div class="cred-label">Login (e-mail)</div>
                <div class="cred-value">{{ $user->email }}</div>
            </div>
            <div class="cred-row">
                <div class="cred-icon">🔑</div>
                <div class="cred-label">Hasło</div>
                <div class="cred-value">{{ $plainPassword }}</div>
            </div>
        </div>

        <div class="cta-wrap">
            <a href="{{ config('app.url') }}" class="cta-btn">Zaloguj się do systemu ENESA</a>
        </div>

        <div class="notice">
            <strong>⚠ Ważne:</strong> Ze względów bezpieczeństwa zalecamy zmianę hasła po pierwszym logowaniu.
            Nie udostępniaj hasła osobom trzecim.
        </div>

        <div class="company-info">
            Konto zostało przypisane do firmy: <strong>{{ $company->name }}</strong>
            @if($company->nip)
                · NIP: <strong>{{ $company->nip }}</strong>
            @endif
        </div>

        <p style="color:#4c6373; font-size:12px; line-height:1.6;">
            Jeśli masz pytania lub napotkasz problemy z logowaniem, skontaktuj się z nami pod adresem
            <a href="mailto:enesa.api@enesa.pl" style="color:#0e89d8;">enesa.api@enesa.pl</a>.
        </p>
    </div>

    <div class="footer">
        <p>
            <strong>ENESA – Energy Audit Systems</strong><br>
            Ta wiadomość została wysłana automatycznie. Prosimy nie odpowiadać na ten e-mail.<br>
            &copy; {{ date('Y') }} ENESA. Wszelkie prawa zastrzeżone.
        </p>
    </div>
</div>
</body>
</html>
