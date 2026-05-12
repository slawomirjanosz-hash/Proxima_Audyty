<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dziękujemy za rejestrację</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: "Segoe UI", Arial, sans-serif; font-size: 14px; color: #1a2e3d; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: linear-gradient(130deg, #0e89d8 0%, #1ba84a 100%); padding: 36px 40px 32px; text-align: center; }
        .header-logo { font-size: 30px; font-weight: 900; color: #fff; letter-spacing: 2px; margin-bottom: 6px; }
        .header-sub { color: rgba(255,255,255,.82); font-size: 13px; }
        .header-title { margin-top: 20px; color: #fff; font-size: 20px; font-weight: 700; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 16px; font-weight: 700; margin-bottom: 14px; color: #0f2330; }
        .text { color: #3b5567; line-height: 1.7; margin-bottom: 20px; }
        .info-box { background: #f3f8fc; border: 1px solid #c9dcea; border-radius: 10px; padding: 20px 24px; margin-bottom: 24px; }
        .info-box .info-title { font-size: 11px; font-weight: 700; color: #6b8aa3; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 14px; }
        .info-row { display: flex; gap: 10px; margin-bottom: 10px; }
        .info-row:last-child { margin-bottom: 0; }
        .info-icon { width: 22px; text-align: center; font-size: 15px; flex-shrink: 0; }
        .info-label { font-size: 11px; color: #7a99b2; width: 100px; flex-shrink: 0; padding-top: 1px; }
        .info-value { font-size: 14px; font-weight: 600; color: #0f2330; }
        .steps { margin-bottom: 24px; }
        .step { display: flex; gap: 16px; margin-bottom: 16px; align-items: flex-start; }
        .step-num { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(130deg, #0e89d8, #1ba84a); color: #fff; font-weight: 800; font-size: 14px; text-align: center; line-height: 32px; flex-shrink: 0; }
        .step-text { padding-top: 6px; color: #3b5567; line-height: 1.5; font-size: 13px; }
        .step-text strong { color: #0f2330; }
        .notice { background: #fff8e1; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 12px 16px; font-size: 12px; color: #6b4800; line-height: 1.6; margin-bottom: 24px; }
        .footer { background: #1a2e3d; padding: 22px 40px; text-align: center; }
        .footer p { color: #7fa3be; font-size: 11px; line-height: 1.8; }
        .footer strong { color: #9fc8e4; }
        .divider { height: 1px; background: #e8eef3; margin: 20px 0; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <div class="header-logo">ENESA</div>
        <div class="header-sub">Energy Audit &amp; Solutions</div>
        <div class="header-title">Dziękujemy za rejestrację!</div>
    </div>

    <div class="body">

        <div class="greeting">
            Dzień dobry, {{ $registration->first_name ? $registration->first_name . ' ' . $registration->last_name : $registration->name }}!
        </div>

        <p class="text">
            Dziękujemy za złożenie wniosku rejestracyjnego firmy <strong>{{ $registration->name }}</strong>
            w systemie <strong>ENESA</strong>. Twoje zgłoszenie zostało przyjęte i oczekuje na weryfikację przez nasz zespół.
        </p>

        <div class="info-box">
            <div class="info-title">Dane złożonego wniosku</div>
            <div class="info-row">
                <div class="info-icon">🏢</div>
                <div class="info-label">Firma</div>
                <div class="info-value">{{ $registration->name }}</div>
            </div>
            @if($registration->nip)
            <div class="info-row">
                <div class="info-icon">🔢</div>
                <div class="info-label">NIP</div>
                <div class="info-value">{{ $registration->nip }}</div>
            </div>
            @endif
            @if($registration->city)
            <div class="info-row">
                <div class="info-icon">📍</div>
                <div class="info-label">Miasto</div>
                <div class="info-value">{{ $registration->city }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-icon">📧</div>
                <div class="info-label">E-mail</div>
                <div class="info-value">{{ $registration->email }}</div>
            </div>
            @if($registration->phone)
            <div class="info-row">
                <div class="info-icon">📱</div>
                <div class="info-label">Telefon</div>
                <div class="info-value">{{ $registration->phone }}</div>
            </div>
            @endif
        </div>

        <p style="font-size:13px;font-weight:700;color:#0f2330;margin-bottom:14px;">Co dalej?</p>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-text"><strong>Weryfikacja wniosku</strong> — Nasz zespół sprawdzi podane dane i zweryfikuje Twoją firmę w rejestrach.</div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-text"><strong>Potwierdzenie e-mailem</strong> — Otrzymasz wiadomość z informacją o akceptacji lub odrzuceniu wniosku.</div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-text"><strong>Dostęp do systemu</strong> — Po akceptacji otrzymasz dane do logowania i uzyskasz pełny dostęp do platformy ENESA.</div>
            </div>
        </div>

        <div class="notice">
            <strong>⏱ Czas rozpatrzenia:</strong> Wnioski są zazwyczaj rozpatrywane w ciągu 1–2 dni roboczych.
            Jeśli masz pytania, skontaktuj się z nami pod adresem
            <a href="mailto:kontakt@enesa.pl" style="color:#6b4800;">kontakt@enesa.pl</a>.
        </div>

        <div class="divider"></div>

        <p style="color:#4c6373; font-size:12px; line-height:1.6; text-align:center;">
            Ta wiadomość została wysłana automatycznie przez system ENESA.<br>
            Prosimy nie odpowiadać bezpośrednio na tę wiadomość.
        </p>

    </div>

    <div class="footer">
        <p>
            <strong>ENESA Energy Audit &amp; Solutions</strong><br>
            <a href="{{ config('app.url') }}" style="color:#5ab4df;text-decoration:none;">{{ config('app.url') }}</a><br>
            &copy; {{ date('Y') }} ENESA. Wszelkie prawa zastrzeżone.
        </p>
    </div>

</div>
</body>
</html>
