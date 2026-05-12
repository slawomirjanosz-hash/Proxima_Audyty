@php
    // Encode images as base64 so dompdf works on Railway (no remote URLs allowed)
    $bgPath   = public_path('img/offer-cover-bg.jpg');
    $logoPath = public_path('logo.png');
    $bgB64    = file_exists($bgPath)   ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($bgPath))   : '';
    $logoB64  = file_exists($logoPath) ? 'data:image/png;base64,'  . base64_encode(file_get_contents($logoPath)) : '';

    $clientCity = $offer->customer_city
        ?? $offer->company?->city
        ?? '';
    $clientName = $offer->customer_name
        ?? $offer->company?->name
        ?? '—';
    $offerDate  = $offer->offer_date
        ? $offer->offer_date->format('d.m.Y')
        : now()->format('d.m.Y');
    $offerNum   = $offer->offer_number ?: '—';
@endphp
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Strona tytułowa – {{ $offerNum }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            background: white;
            width: 210mm;
            height: 297mm;
        }

        .page {
            width: 210mm;
            height: 297mm;
            background: white;
            position: relative;
            overflow: hidden;
        }

        /* ── RIGHT BACKGROUND IMAGE ── */
        .right-image {
            position: absolute;
            top: 0;
            right: 0;
            width: 58%;
            height: 100%;
        }
        /* dompdf clip-path via img trick: use a table-based approach */
        .right-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .right-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(
                180deg,
                rgba(0,76,130,0.65) 0%,
                rgba(0,30,60,0.88) 100%
            );
        }

        /* ── LEFT WHITE DIAGONAL MASK ── */
        /* dompdf does not support clip-path, so we overlay a white shape */
        .left-mask {
            position: absolute;
            top: 0;
            left: 0;
            width: 48%;
            height: 100%;
            background: white;
        }

        /* ── LOGO ── */
        .logo {
            position: absolute;
            top: 60px;
            left: 60px;
            z-index: 10;
        }
        .logo img {
            width: 180px;
        }

        /* ── CONTENT ── */
        .content {
            position: absolute;
            left: 60px;
            top: 240px;
            width: 44%;
            z-index: 10;
        }

        .subtitle {
            color: #1f2937;
            font-size: 11pt;
            letter-spacing: 1.5px;
            margin-bottom: 14px;
            font-weight: bold;
        }

        .title {
            font-size: 38pt;
            line-height: 1.0;
            font-weight: bold;
            color: #002b5b;
            margin-bottom: 18px;
        }

        .title-accent {
            color: #3ba935;
        }

        .line {
            width: 80px;
            height: 5px;
            background: #3ba935;
            border-radius: 10px;
            margin-bottom: 36px;
        }

        /* ── INFO BOX ── */
        .info-box {
            background: white;
            border-radius: 12px;
            padding: 22px 24px;
            border: 1px solid #edf1f3;
        }

        .info-row {
            margin-bottom: 20px;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-size: 7pt;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 4px;
            letter-spacing: 1px;
        }

        .info-value {
            font-size: 16pt;
            color: #111827;
            font-weight: bold;
        }

        .info-small {
            font-size: 12pt;
        }

        /* ── FOOTER ── */
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 80px;
            background: #002b5b;
            z-index: 10;
        }
        .footer-inner {
            display: table;
            width: 100%;
            height: 80px;
        }
        .footer-left {
            display: table-cell;
            vertical-align: middle;
            padding-left: 60px;
            font-size: 10pt;
            font-weight: bold;
            color: white;
        }
        .footer-right {
            display: table-cell;
            vertical-align: middle;
            padding-right: 60px;
            font-size: 11pt;
            color: #8de969;
            font-weight: bold;
            text-align: right;
        }

        /* ── DECORATIVE CIRCLES ── */
        .circle {
            position: absolute;
            width: 380px;
            height: 380px;
            border: 2px solid rgba(255,255,255,0.08);
            border-radius: 50%;
            right: -120px;
            bottom: 120px;
            z-index: 3;
        }
        .circle2 {
            position: absolute;
            width: 260px;
            height: 260px;
            border: 2px solid rgba(255,255,255,0.06);
            border-radius: 50%;
            right: -60px;
            bottom: 180px;
            z-index: 3;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Right background image --}}
    <div class="right-image">
        @if($bgB64)
            <img src="{{ $bgB64 }}" alt="">
        @else
            <div style="width:100%;height:100%;background:linear-gradient(180deg,#004c82,#001e3c);"></div>
        @endif
        <div class="right-overlay"></div>
    </div>

    {{-- White mask for left side (dompdf clip-path alternative) --}}
    <div class="left-mask"></div>

    {{-- Decorative circles --}}
    <div class="circle"></div>
    <div class="circle2"></div>

    {{-- Logo --}}
    <div class="logo">
        @if($logoB64)
            <img src="{{ $logoB64 }}" alt="ENESA">
        @else
            <span style="font-size:22pt;font-weight:bold;color:#002b5b;">ENESA</span>
        @endif
    </div>

    {{-- Main content --}}
    <div class="content">

        <div class="subtitle">OFERTA TECHNICZNO-HANDLOWA</div>

        <div class="title">
            AUDYT<br>
            <span class="title-accent">ENERGETYCZNY</span>
        </div>

        <div class="line"></div>

        <div class="info-box">

            <div class="info-row">
                <div class="info-label">Numer oferty</div>
                <div class="info-value">{{ $offerNum }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Klient</div>
                <div class="info-value info-small">{{ $clientName }}</div>
            </div>

            @if($clientCity)
            <div class="info-row">
                <div class="info-label">Lokalizacja</div>
                <div class="info-value info-small">{{ $clientCity }}</div>
            </div>
            @endif

            <div class="info-row">
                <div class="info-label">Data opracowania</div>
                <div class="info-value info-small">{{ $offerDate }}</div>
            </div>

            @if($offer->offer_title)
            <div class="info-row">
                <div class="info-label">Tytuł</div>
                <div class="info-value info-small">{{ $offer->offer_title }}</div>
            </div>
            @endif

        </div>

    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-inner">
            <div class="footer-left">ENESA Energy Audit &amp; Solutions</div>
            <div class="footer-right">www.enesa.pl</div>
        </div>
    </div>

</div>
</body>
</html>
