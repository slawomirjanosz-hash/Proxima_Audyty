<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5pt;
            color: #111111;
            background: #ffffff;
        }

        /* ── HEADER ── */
        .page-header {
            width: 100%;
            border-bottom: 3px solid #111111;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        table.header-tbl { width: 100%; border-collapse: collapse; }
        table.header-tbl td { vertical-align: middle; padding: 0; }
        .hdr-left { width: 65%; }
        .hdr-right { width: 35%; text-align: right; }
        .hdr-right img { height: 60px; }

        .company-name {
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #111111;
            margin-bottom: 2px;
        }
        .company-tagline {
            font-size: 7.5pt;
            color: #555555;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ── DOCUMENT TITLE BAND ── */
        .title-band {
            background: #111111;
            color: #ffffff;
            padding: 11px 16px;
            margin-bottom: 18px;
        }
        .title-band-main {
            font-size: 13pt;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .title-band-sub {
            font-size: 8pt;
            color: #cccccc;
            margin-top: 3px;
            letter-spacing: 0.5px;
        }

        /* ── META BOX ── */
        table.meta-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #dddddd;
        }
        table.meta-box td {
            padding: 7px 12px;
            font-size: 8.5pt;
            border-bottom: 1px solid #eeeeee;
            vertical-align: top;
        }
        table.meta-box td.meta-label {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #444444;
            width: 30%;
            background: #f5f5f5;
            border-right: 1px solid #dddddd;
        }
        table.meta-box td.meta-val { color: #111111; }
        table.meta-box tr:last-child td { border-bottom: none; }

        /* ── CLIENT BLOCK ── */
        .client-header {
            font-size: 7.5pt;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #ffffff;
            background: #333333;
            padding: 5px 12px;
            margin-bottom: 0;
        }
        table.client-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
            border: 1px solid #dddddd;
            border-top: none;
        }
        table.client-data td {
            padding: 6px 12px;
            font-size: 8.5pt;
            border-bottom: 1px solid #eeeeee;
            vertical-align: top;
        }
        table.client-data td.cl-label {
            font-weight: 700;
            color: #444444;
            width: 30%;
            background: #fafafa;
            border-right: 1px solid #dddddd;
        }
        table.client-data tr:last-child td { border-bottom: none; }

        /* ── SECTION HEADER ── */
        .section-block { margin-bottom: 2px; }
        table.section-hdr { width: 100%; border-collapse: collapse; margin-top: 18px; }
        table.section-hdr td.acc { width: 4px; background: #111111; }
        table.section-hdr td.label {
            background: #222222;
            color: #ffffff;
            padding: 6px 12px;
            font-weight: 700;
            font-size: 9.5pt;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        /* ── DATA TABLE ── */
        table.data {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cccccc;
            border-top: none;
        }
        table.data td {
            padding: 6px 12px;
            vertical-align: top;
            border-bottom: 1px solid #e8e8e8;
            font-size: 8.5pt;
        }
        table.data td.key {
            color: #333333;
            font-weight: 700;
            width: 40%;
            background: #fafafa;
            border-right: 1px solid #e0e0e0;
        }
        table.data td.val { color: #111111; }
        table.data tr.even td { background: #f5f5f5; }
        table.data tr.even td.key { background: #efefef; }
        table.data tr:last-child td { border-bottom: none; }

        /* ── REMARKS ── */
        .remarks {
            border: 1px solid #aaaaaa;
            margin-top: 20px;
        }
        .remarks-title {
            background: #333333;
            color: #ffffff;
            font-weight: 700;
            font-size: 8pt;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 5px 12px;
        }
        .remarks-body {
            padding: 10px 14px;
            font-size: 8.5pt;
            color: #111111;
            line-height: 1.65;
        }

        /* ── SIGNATURE ── */
        .signature-block {
            margin-top: 36px;
            border-top: 1px solid #111111;
            padding-top: 6px;
        }
        table.sig-tbl { width: 100%; border-collapse: collapse; }
        table.sig-tbl td { width: 50%; font-size: 8pt; padding: 0 8px; }
        table.sig-tbl td.sig-right { text-align: right; }
        .sig-line {
            border-top: 1px solid #111111;
            margin-top: 32px;
            margin-bottom: 4px;
        }
        .sig-label { font-size: 7.5pt; color: #555555; text-align: center; letter-spacing: 0.5px; }

        /* ── FOOTER ── */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #333333;
            padding: 5px 0 0 0;
        }
        table.footer-tbl { width: 100%; border-collapse: collapse; }
        table.footer-tbl td {
            font-size: 7.5pt;
            color: #555555;
            padding: 0;
        }
        table.footer-tbl td.f-right { text-align: right; }
        table.footer-tbl td.f-center { text-align: center; color: #888888; }
    </style>
</head>
<body>

    {{-- STAŁA STOPKA --}}
    <div class="page-footer">
        <table class="footer-tbl">
            <tr>
                <td>ENESA Sp. z o.o. — Protokół Audytowy</td>
                <td class="f-center">Dokument wygenerowany przez system audytowy — do weryfikacji przez audytora</td>
                <td class="f-right">{{ now()->format('d.m.Y') }}</td>
            </tr>
        </table>
    </div>

    {{-- NAGŁÓWEK --}}
    <div class="page-header">
        <table class="header-tbl">
            <tr>
                <td class="hdr-left">
                    <div class="company-name">ENESA Sp. z o.o.</div>
                    <div class="company-tagline">Energy Audit &amp; Solutions</div>
                </td>
                <td class="hdr-right">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('Logo2.png'))) }}" alt="ENESA" />
                </td>
            </tr>
        </table>
    </div>

    {{-- PASEK TYTUŁU --}}
    <div class="title-band">
        <div class="title-band-main">Protokół Audytowy</div>
        <div class="title-band-sub">
            @php
                $typeLabels = [
                    'energy_audit' => 'Audyt Energetyczny Budynku / Obiektu',
                    'iso50001'     => 'System Zarządzania Energią — ISO 50001',
                    'offer'        => 'Wycena i Oferta',
                    'general'      => 'Konsultacja Ogólna',
                ];
                echo $typeLabels[$conversation->context_type ?? 'general'] ?? 'Audyt';
            @endphp
            &nbsp;&mdash;&nbsp;{{ $conversation->title }}
        </div>
    </div>

    {{-- META DOKUMENTU --}}
    <table class="meta-box">
        <tr>
            <td class="meta-label">Nr dokumentu</td>
            <td class="meta-val">PROT/{{ str_pad($conversation->id, 5, '0', STR_PAD_LEFT) }}/{{ now()->format('Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Data wygenerowania</td>
            <td class="meta-val">{{ now()->format('d.m.Y') }}</td>
        </tr>
        @if($conversation->protocol_generated_at)
        <tr>
            <td class="meta-label">Data protokołu</td>
            <td class="meta-val">{{ $conversation->protocol_generated_at->format('d.m.Y H:i') }}</td>
        </tr>
        @endif
        <tr>
            <td class="meta-label">Status</td>
            <td class="meta-val">Wygenerowany automatycznie — do weryfikacji przez audytora</td>
        </tr>
    </table>

    {{-- DANE KLIENTA --}}
    @if($company)
    <div class="client-header">Dane Zleceniodawcy</div>
    <table class="client-data">
        <tr>
            <td class="cl-label">Nazwa firmy</td>
            <td>{{ $company->name }}</td>
        </tr>
        @if($company->nip)
        <tr>
            <td class="cl-label">NIP</td>
            <td>{{ $company->nip }}</td>
        </tr>
        @endif
        @if($company->street || $company->city)
        <tr>
            <td class="cl-label">Adres</td>
            <td>
                @if($company->street){{ $company->street }}, @endif
                @if($company->postal_code){{ $company->postal_code }} @endif
                {{ $company->city }}
            </td>
        </tr>
        @endif
        @if($company->phone)
        <tr>
            <td class="cl-label">Telefon</td>
            <td>{{ $company->phone }}</td>
        </tr>
        @endif
        @if($company->email)
        <tr>
            <td class="cl-label">E-mail</td>
            <td>{{ $company->email }}</td>
        </tr>
        @endif
    </table>
    @elseif($conversation->user)
    <div class="client-header">Dane Zleceniodawcy</div>
    <table class="client-data">
        <tr>
            <td class="cl-label">Imię i nazwisko</td>
            <td>{{ $conversation->user->name }}</td>
        </tr>
        @if($conversation->user->email)
        <tr>
            <td class="cl-label">E-mail</td>
            <td>{{ $conversation->user->email }}</td>
        </tr>
        @endif
    </table>
    @endif

    {{-- DANE Z PROTOKOŁU --}}
    @if(!empty($protocol['sekcje']))
        @foreach($protocol['sekcje'] as $sekcja)
            <table class="section-hdr">
                <tr>
                    <td class="acc"></td>
                    <td class="label">{{ $sekcja['nazwa'] ?? 'Sekcja' }}</td>
                </tr>
            </table>
            <table class="data">
                @foreach($sekcja['pola'] ?? [] as $i => $pole)
                    <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
                        <td class="key">{{ $pole['klucz'] ?? '' }}</td>
                        <td class="val">{{ $pole['wartosc'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </table>
        @endforeach
    @else
        <p style="color:#888888; margin-top:20px; font-size:8.5pt;">Brak danych w protokole.</p>
    @endif

    {{-- UWAGI --}}
    @if(!empty($protocol['uwagi']))
        <div class="remarks">
            <div class="remarks-title">Uwagi i zalecenia</div>
            <div class="remarks-body">{{ $protocol['uwagi'] }}</div>
        </div>
    @endif

    {{-- ANALIZA I REKOMENDACJE --}}
    @php $analiza = $protocol['analiza'] ?? null; @endphp
    @if($analiza)
        <div style="margin-top:24px; border-top:2px solid #111111; padding-top:18px;">
            <table class="section-hdr">
                <tr>
                    <td class="acc"></td>
                    <td class="label">Analiza i rekomendacje energetyczne</td>
                </tr>
            </table>

            @if(!empty($analiza['podsumowanie']))
                <div style="background:#f0f9ff; border:1px solid #bae6fd; padding:10px 12px; margin-top:10px; margin-bottom:14px;">
                    <div style="font-size:7.5pt; font-weight:700; color:#0369a1; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Stan energetyczny obiektu</div>
                    <div style="font-size:8.5pt; color:#0c4a6e; line-height:1.6;">{{ $analiza['podsumowanie'] }}</div>
                </div>
            @endif

            @if(!empty($analiza['rekomendacje']))
                @foreach($analiza['rekomendacje'] as $rec)
                    @php
                        $p = strtolower($rec['priorytet'] ?? 'sredni');
                        $borderColor = $p === 'wysoki' ? '#dc2626' : ($p === 'sredni' ? '#d97706' : '#059669');
                        $bgColor     = $p === 'wysoki' ? '#fee2e2' : ($p === 'sredni' ? '#fef3c7' : '#d1fae5');
                        $txtColor    = $p === 'wysoki' ? '#991b1b' : ($p === 'sredni' ? '#92400e' : '#065f46');
                        $priorLabel  = $p === 'wysoki' ? 'Wysoki priorytet' : ($p === 'sredni' ? 'Sredni priorytet' : 'Niski priorytet');
                    @endphp
                    <div style="border:1px solid #cccccc; border-left:4px solid {{ $borderColor }}; padding:8px 12px; margin-bottom:8px;">
                        <div style="font-weight:700; font-size:8.5pt; color:#111111; margin-bottom:3px;">
                            {{ $rec['nr'] ?? '' }}. {{ $rec['obszar'] ?? '' }}
                            <span style="font-size:7.5pt; padding:1px 7px; background:{{ $bgColor }}; color:{{ $txtColor }}; margin-left:6px;">{{ $priorLabel }}</span>
                        </div>
                        <div style="font-size:8.5pt; font-weight:600; color:#1a2d3d; margin-bottom:2px;">{{ $rec['dzialanie'] ?? '' }}</div>
                        @if(!empty($rec['uzasadnienie']))
                            <div style="font-size:8pt; color:#4c6373; margin-bottom:2px;">{{ $rec['uzasadnienie'] }}</div>
                        @endif
                        @if(!empty($rec['szacowane_oszczednosci']))
                            <div style="font-size:8pt; font-weight:700; color:#166534;">Oszczednosci: {{ $rec['szacowane_oszczednosci'] }}</div>
                        @endif
                    </div>
                @endforeach
            @endif

            @if(!empty($analiza['kolejnosc_dzialan']))
                <div style="background:#fafafa; border:1px solid #e0e0e0; padding:10px 12px; margin-top:12px;">
                    <div style="font-size:7.5pt; font-weight:700; color:#555555; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Optymalna kolejnosc dzialan</div>
                    <div style="font-size:8.5pt; color:#333333; line-height:1.6;">{{ $analiza['kolejnosc_dzialan'] }}</div>
                </div>
            @endif
        </div>
    @endif

    {{-- PODPISY --}}
    <div class="signature-block">
        <table class="sig-tbl">
            <tr>
                <td>
                    <div class="sig-line"></div>
                    <div class="sig-label">Podpis Zleceniodawcy</div>
                </td>
                <td class="sig-right">
                    <div class="sig-line"></div>
                    <div class="sig-label">Podpis i pieczec Audytora / ENESA Sp. z o.o.</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
            </table>
            <table class="data">
                @foreach($sekcja['pola'] ?? [] as $i => $pole)
                    <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
                        <td class="key">{{ $pole['klucz'] ?? '' }}</td>
                        <td class="val">{{ $pole['wartosc'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </table>
        @endforeach
    @else
        <p style="color:#8a9fb3;margin-top:20px;">Brak danych w protokole.</p>
    @endif

    @if(!empty($protocol['uwagi']))
        <div class="remarks">
            <div class="remarks-title">Uwagi i zalecenia</div>
            <div class="remarks-body">{{ $protocol['uwagi'] }}</div>
        </div>
    @endif

    <div class="footer">
        <table class="footer-tbl">
            <tr>
                <td>Enesa sp. z o. o. &mdash; dokument wygenerowany automatycznie przez system AI</td>
                <td class="right">{{ now()->format('d.m.Y') }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
