<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audyt energetyczny przydzielony</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4f8; font-family: "Segoe UI", Arial, sans-serif; font-size: 14px; color: #1a2e3d; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: linear-gradient(130deg, #1a4d3a 0%, #1ba84a 100%); padding: 36px 40px 32px; text-align: center; }
        .header-logo { font-size: 30px; font-weight: 900; color: #fff; letter-spacing: 2px; margin-bottom: 6px; }
        .header-sub { color: rgba(255,255,255,.80); font-size: 13px; }
        .header-icon { font-size: 46px; margin: 18px 0 10px; }
        .header-title { color: #fff; font-size: 22px; font-weight: 800; }
        .header-status { display: inline-block; margin-top: 10px; background: rgba(255,255,255,.20); color: #fff; padding: 4px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 16px; font-weight: 700; margin-bottom: 14px; color: #0f2330; }
        .text { color: #3b5567; line-height: 1.7; margin-bottom: 22px; }
        .audit-card { border: 1px solid #b8dfc9; border-radius: 12px; overflow: hidden; margin-bottom: 28px; }
        .audit-card-header { background: #f0faf4; padding: 14px 20px; border-bottom: 1px solid #c6e8d3; }
        .audit-card-header span { font-size: 11px; font-weight: 700; color: #2d7a4f; text-transform: uppercase; letter-spacing: .6px; }
        .audit-card-body { padding: 18px 20px; }
        .audit-row { display: flex; gap: 10px; margin-bottom: 12px; align-items: flex-start; }
        .audit-row:last-child { margin-bottom: 0; }
        .audit-icon { width: 24px; text-align: center; font-size: 16px; flex-shrink: 0; }
        .audit-label { font-size: 11px; color: #7a99b2; width: 120px; flex-shrink: 0; padding-top: 2px; }
        .audit-value { font-size: 14px; font-weight: 600; color: #0f2330; }
        .status-badge { display: inline-block; background: #1ba84a; color: #fff; font-size: 12px; font-weight: 700; padding: 3px 12px; border-radius: 20px; }
        .cta-wrap { text-align: center; margin: 28px 0; }
        .cta-btn { display: inline-block; padding: 14px 38px; background: linear-gradient(130deg, #1a4d3a, #1ba84a); color: #fff; text-decoration: none; font-weight: 800; font-size: 15px; border-radius: 10px; letter-spacing: .3px; }
        .cta-note { margin-top: 10px; font-size: 12px; color: #7a99b2; }
        .timeline { margin-bottom: 26px; }
        .timeline-item { display: flex; gap: 14px; margin-bottom: 14px; }
        .timeline-dot { width: 12px; height: 12px; border-radius: 50%; background: #1ba84a; flex-shrink: 0; margin-top: 4px; }
        .timeline-dot.pending { background: #d1d5db; }
        .timeline-line { width: 2px; background: #e5e7eb; margin-left: 5px; height: 18px; margin-top: 0; }
        .timeline-text { font-size: 13px; color: #3b5567; line-height: 1.5; }
        .timeline-text strong { color: #0f2330; }
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
        <div class="header-icon">🔍</div>
        <div class="header-title">Audyt energetyczny przydzielony!</div>
        <div class="header-status">Audyt w toku</div>
    </div>

    <div class="body">

        <div class="greeting">
            Dzień dobry, {{ $recipient->first_name ?? $recipient->name }}!
        </div>

        <p class="text">
            Mamy dla Ciebie ważną informację — do Twojej firmy <strong>{{ $company->name }}</strong>
            został przydzielony audyt energetyczny. Nasz specjalista wkrótce skontaktuje się z Tobą
            w celu ustalenia szczegółów i terminu realizacji.
        </p>

        <div class="audit-card">
            <div class="audit-card-header"><span>Szczegóły audytu</span></div>
            <div class="audit-card-body">
                <div class="audit-row">
                    <div class="audit-icon">📋</div>
                    <div class="audit-label">Tytuł audytu</div>
                    <div class="audit-value">{{ $audit->title }}</div>
                </div>
                @if($audit->audit_type)
                <div class="audit-row">
                    <div class="audit-icon">⚡</div>
                    <div class="audit-label">Typ audytu</div>
                    <div class="audit-value">{{ $audit->audit_type }}</div>
                </div>
                @endif
                <div class="audit-row">
                    <div class="audit-icon">🏢</div>
                    <div class="audit-label">Firma</div>
                    <div class="audit-value">{{ $company->name }}</div>
                </div>
                @if($company->city)
                <div class="audit-row">
                    <div class="audit-icon">📍</div>
                    <div class="audit-label">Lokalizacja</div>
                    <div class="audit-value">{{ $company->city }}</div>
                </div>
                @endif
                <div class="audit-row">
                    <div class="audit-icon">🔄</div>
                    <div class="audit-label">Status</div>
                    <div class="audit-value"><span class="status-badge">{{ $audit->statusLabel() }}</span></div>
                </div>
                <div class="audit-row">
                    <div class="audit-icon">📅</div>
                    <div class="audit-label">Data przydziału</div>
                    <div class="audit-value">{{ now()->format('d.m.Y') }}</div>
                </div>
            </div>
        </div>

        <p style="font-size:13px;font-weight:700;color:#0f2330;margin-bottom:16px;">Przebieg procesu audytu</p>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-text"><strong>Przydzielenie audytu</strong> — Audyt energetyczny został przydzielony do Twojej firmy. ✓</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot pending"></div>
                <div class="timeline-text"><strong>Kontakt ze specjalistą</strong> — Nasz audytor skontaktuje się z Tobą w celu ustalenia szczegółów.</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot pending"></div>
                <div class="timeline-text"><strong>Realizacja audytu</strong> — Przeprowadzenie pełnego audytu energetycznego w Twojej firmie.</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot pending"></div>
                <div class="timeline-text"><strong>Raport końcowy</strong> — Przygotowanie i przekazanie szczegółowego raportu z rekomendacjami.</div>
            </div>
        </div>

        <div class="cta-wrap">
            <a href="{{ url('/strefa-klienta') }}" class="cta-btn">Przejdź do Strefy Klienta</a>
            <div class="cta-note">Śledź postępy audytu i komunikuj się z naszym zespołem online.</div>
        </div>

        <div class="divider"></div>

        <p style="color:#4c6373; font-size:12px; line-height:1.6; text-align:center;">
            Ta wiadomość została wysłana automatycznie przez system ENESA.<br>
            Masz pytania? Napisz do nas: <a href="mailto:kontakt@enesa.pl" style="color:#1ba84a;">kontakt@enesa.pl</a>
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
