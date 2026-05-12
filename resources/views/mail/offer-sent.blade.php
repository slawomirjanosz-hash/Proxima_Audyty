<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nowa oferta ENESA</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: "Segoe UI", Arial, sans-serif; font-size: 14px; color: #1a2e3d; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: linear-gradient(130deg, #002b5b 0%, #0e89d8 60%, #1ba84a 100%); padding: 36px 40px 32px; text-align: center; }
        .header-logo { font-size: 30px; font-weight: 900; color: #fff; letter-spacing: 2px; margin-bottom: 6px; }
        .header-sub { color: rgba(255,255,255,.80); font-size: 13px; }
        .header-icon { font-size: 46px; margin: 18px 0 10px; }
        .header-title { color: #fff; font-size: 22px; font-weight: 800; }
        .header-number { display: inline-block; margin-top: 10px; background: rgba(255,255,255,.18); color: #fff; padding: 4px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; letter-spacing: .5px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 16px; font-weight: 700; margin-bottom: 14px; color: #0f2330; }
        .text { color: #3b5567; line-height: 1.7; margin-bottom: 22px; }
        .offer-card { border: 1px solid #c9dcea; border-radius: 12px; overflow: hidden; margin-bottom: 28px; }
        .offer-card-header { background: #f3f8fc; padding: 14px 20px; border-bottom: 1px solid #d4e6f1; }
        .offer-card-header span { font-size: 11px; font-weight: 700; color: #6b8aa3; text-transform: uppercase; letter-spacing: .6px; }
        .offer-card-body { padding: 18px 20px; }
        .offer-row { display: flex; gap: 10px; margin-bottom: 12px; align-items: flex-start; }
        .offer-row:last-child { margin-bottom: 0; }
        .offer-icon { width: 24px; text-align: center; font-size: 16px; flex-shrink: 0; }
        .offer-label { font-size: 11px; color: #7a99b2; width: 110px; flex-shrink: 0; padding-top: 2px; }
        .offer-value { font-size: 14px; font-weight: 600; color: #0f2330; }
        .cta-wrap { text-align: center; margin: 28px 0; }
        .cta-btn { display: inline-block; padding: 14px 38px; background: linear-gradient(130deg, #002b5b, #0e89d8); color: #fff; text-decoration: none; font-weight: 800; font-size: 15px; border-radius: 10px; letter-spacing: .3px; }
        .cta-note { margin-top: 10px; font-size: 12px; color: #7a99b2; }
        .features { display: table; width: 100%; border-collapse: separate; border-spacing: 10px; margin-bottom: 22px; }
        .feature { display: table-cell; width: 33.33%; background: #f8fbff; border-radius: 10px; padding: 14px; text-align: center; vertical-align: top; }
        .feature-icon { font-size: 22px; margin-bottom: 6px; }
        .feature-title { font-size: 11px; font-weight: 700; color: #0f2330; margin-bottom: 4px; }
        .feature-desc { font-size: 11px; color: #6b8aa3; line-height: 1.4; }
        .divider { height: 1px; background: #e8eef3; margin: 22px 0; }
        .footer { background: #1a2e3d; padding: 22px 40px; text-align: center; }
        .footer p { color: #7fa3be; font-size: 11px; line-height: 1.8; }
        .footer strong { color: #9fc8e4; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <div class="header-logo">ENESA</div>
        <div class="header-sub">Energy Audit &amp; Solutions</div>
        <div class="header-icon">📋</div>
        <div class="header-title">Masz nową ofertę!</div>
        @if($offer->offer_number)
        <div class="header-number">{{ $offer->offer_number }}</div>
        @endif
    </div>

    <div class="body">

        <div class="greeting">
            Dzień dobry, {{ $offer->customer_name ?? ($offer->company?->name ?? 'Kliencie') }}!
        </div>

        <p class="text">
            Przygotowaliśmy dla Ciebie ofertę na realizację usług audytu energetycznego.
            Zaloguj się do Strefy Klienta, aby ją przejrzeć, pobrać jako PDF lub zaakceptować.
        </p>

        <div class="offer-card">
            <div class="offer-card-header"><span>Szczegóły oferty</span></div>
            <div class="offer-card-body">
                @if($offer->offer_title)
                <div class="offer-row">
                    <div class="offer-icon">📄</div>
                    <div class="offer-label">Tytuł oferty</div>
                    <div class="offer-value">{{ $offer->offer_title }}</div>
                </div>
                @endif
                @if($offer->offer_number)
                <div class="offer-row">
                    <div class="offer-icon">🔢</div>
                    <div class="offer-label">Numer oferty</div>
                    <div class="offer-value">{{ $offer->offer_number }}</div>
                </div>
                @endif
                @if($offer->offer_date)
                <div class="offer-row">
                    <div class="offer-icon">📅</div>
                    <div class="offer-label">Data oferty</div>
                    <div class="offer-value">{{ $offer->offer_date->format('d.m.Y') }}</div>
                </div>
                @endif
                @if($offer->total_price)
                <div class="offer-row">
                    <div class="offer-icon">💰</div>
                    <div class="offer-label">Wartość</div>
                    <div class="offer-value">{{ number_format($offer->total_price, 2, ',', ' ') }} PLN netto</div>
                </div>
                @endif
            </div>
        </div>

        <div class="cta-wrap">
            <a href="{{ url('/strefa-klienta') }}" class="cta-btn">Przejdź do Strefy Klienta</a>
            <div class="cta-note">W Strefie Klienta możesz przejrzeć ofertę, pobrać PDF oraz ją zaakceptować.</div>
        </div>

        <table class="features">
            <tr>
                <td class="feature">
                    <div class="feature-icon">📥</div>
                    <div class="feature-title">Pobierz PDF</div>
                    <div class="feature-desc">Oferta dostępna do pobrania w formacie PDF</div>
                </td>
                <td class="feature">
                    <div class="feature-icon">✅</div>
                    <div class="feature-title">Akceptacja online</div>
                    <div class="feature-desc">Zatwierdź ofertę jednym kliknięciem w systemie</div>
                </td>
                <td class="feature">
                    <div class="feature-icon">💬</div>
                    <div class="feature-title">Czat z ekspertem</div>
                    <div class="feature-desc">Masz pytania? Napisz do nas przez chat</div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <p style="color:#4c6373; font-size:12px; line-height:1.6; text-align:center;">
            Ta wiadomość została wysłana automatycznie przez system ENESA.<br>
            Jeśli masz pytania, skontaktuj się z nami: <a href="mailto:kontakt@enesa.pl" style="color:#0e89d8;">kontakt@enesa.pl</a>
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
