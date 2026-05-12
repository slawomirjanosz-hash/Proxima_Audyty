<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informacja o wniosku rejestracyjnym</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: "Segoe UI", Arial, sans-serif; font-size: 14px; color: #1a2e3d; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: linear-gradient(130deg, #374151 0%, #6b7280 100%); padding: 32px 36px 28px; text-align: center; }
        .header h1 { color: #fff; font-size: 26px; font-weight: 800; letter-spacing: -.3px; }
        .header p { color: rgba(255,255,255,.82); font-size: 13px; margin-top: 4px; }
        .body { padding: 32px 36px; }
        .greeting { font-size: 16px; font-weight: 700; margin-bottom: 12px; color: #0f2330; }
        .intro { color: #3b5567; line-height: 1.6; margin-bottom: 22px; }
        .info-box { background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; }
        .info-box .company-name { font-size: 15px; font-weight: 700; color: #991b1b; margin-bottom: 4px; }
        .info-box .nip { font-size: 12px; color: #b45309; font-family: "Courier New", monospace; }
        .notice { background: #f8fafc; border-left: 4px solid #6b7280; border-radius: 6px; padding: 10px 14px; font-size: 12px; color: #374151; line-height: 1.6; margin-bottom: 20px; }
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
        <div class="greeting">
            Dzień dobry{{ $registration->first_name ? ', ' . $registration->first_name : '' }},
        </div>

        <p class="intro">
            Informujemy, że Twój wniosek rejestracyjny w systemie <strong>ENESA</strong> dla poniższej firmy
            nie został zaakceptowany przez administratora.
        </p>

        <div class="info-box">
            <div class="company-name">{{ $registration->name }}</div>
            <div class="nip">NIP: {{ $registration->nip }}</div>
        </div>

        <div class="notice">
            Jeśli uważasz, że to pomyłka lub chcesz uzyskać więcej informacji, skontaktuj się z nami
            bezpośrednio odpowiadając na tę wiadomość.
        </div>

        <p style="font-size:13px; color:#4c6373; line-height:1.6;">
            Dziękujemy za zainteresowanie usługami ENESA.
        </p>
    </div>

    <div class="footer">
        <p><strong>ENESA</strong> – Energy Audit Systems<br>
        Ta wiadomość została wygenerowana automatycznie.</p>
    </div>
</div>
</body>
</html>
