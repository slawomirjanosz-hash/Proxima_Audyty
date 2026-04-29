<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5pt;
            color: #0f2330;
            background: #ffffff;
        }

        /* â”€â”€ HEADER â”€â”€ */
        .page-header {
            width: 100%;
            border-bottom: 3px solid #0e89d8;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        table.header-tbl { width: 100%; border-collapse: collapse; }
        table.header-tbl td { vertical-align: middle; padding: 0; }
        .hdr-left { width: 65%; }
        .hdr-right { width: 35%; text-align: right; }
        .hdr-right img { height: 40px; }

        .company-name {
            font-size: 14pt;
            font-weight: 700;
            color: #0e89d8;
            margin-bottom: 2px;
        }
        .company-tagline {
            font-size: 7.5pt;
            color: #4c6373;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* â”€â”€ DOCUMENT TITLE â”€â”€ */
        .report-title {
            font-size: 15pt;
            font-weight: 800;
            color: #0f2330;
            margin: 0 0 4px;
        }
        .report-subtitle {
            font-size: 9pt;
            color: #4c6373;
            margin-bottom: 14px;
        }

        /* â”€â”€ META GRID â”€â”€ */
        table.meta-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            margin: 12px 0 18px;
        }
        table.meta-grid td { width: 33%; vertical-align: top; }
        .meta-card {
            background: #f3f8fc;
            border: 1px solid #d5e0ea;
            padding: 7px 10px;
        }
        .meta-lbl {
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b8aa3;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .meta-val {
            font-size: 8.5pt;
            font-weight: 600;
            color: #0f2330;
        }

        /* â”€â”€ CLIENT BLOCK â”€â”€ */
        .client-header {
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #ffffff;
            background: #0e89d8;
            padding: 5px 12px;
            margin-bottom: 0;
        }
        table.client-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            border: 1px solid #d5e0ea;
            border-top: none;
        }
        table.client-data td {
            padding: 6px 12px;
            font-size: 8.5pt;
            border-bottom: 1px solid #e5eef6;
            vertical-align: top;
        }
        table.client-data td.cl-label {
            font-weight: 700;
            color: #2c4e67;
            width: 30%;
            background: #eef5fb;
            border-right: 1px solid #d5e0ea;
        }
        table.client-data tr:last-child td { border-bottom: none; }

        /* â”€â”€ SECTION TITLE â”€â”€ */
        .section-title {
            font-size: 11pt;
            font-weight: 800;
            color: #10344c;
            margin: 18px 0 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #d5e0ea;
        }
        .section-num {
            background: #0e89d8;
            color: #ffffff;
            font-size: 8pt;
            font-weight: 800;
            padding: 2px 6px;
            margin-right: 6px;
        }

        /* â”€â”€ DATA TABLE â”€â”€ */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            border: 1px solid #d5e0ea;
        }
        table.data th {
            background: #eef5fb;
            color: #2c4e67;
            font-weight: 700;
            padding: 5px 10px;
            text-align: left;
            border: 1px solid #d5e0ea;
            font-size: 8pt;
        }
        table.data td {
            padding: 5px 10px;
            vertical-align: top;
            border-bottom: 1px solid #dde8f2;
            font-size: 8.5pt;
        }
        table.data td.key {
            color: #2c4e67;
            font-weight: 700;
            width: 42%;
            background: #f8fbff;
            border-right: 1px solid #dde8f2;
        }
        table.data td.val { color: #0f2330; }
        table.data tr.even td { background: #f3f8fd; }
        table.data tr.even td.key { background: #eef4fa; }
        table.data tr:last-child td { border-bottom: none; }

        /* â”€â”€ REMARKS â”€â”€ */
        .remarks {
            background: #fffbf0;
            border: 1px solid #e8d07a;
            margin-top: 16px;
            padding: 12px 14px;
        }
        .remarks-title {
            font-size: 8.5pt;
            font-weight: 800;
            color: #7d5e00;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .remarks-body {
            font-size: 8.5pt;
            color: #6b4e00;
            line-height: 1.65;
        }

        /* â”€â”€ SIGNATURE â”€â”€ */
        .signature-block {
            margin-top: 36px;
            border-top: 1px solid #d5e0ea;
            padding-top: 6px;
        }
        table.sig-tbl { width: 100%; border-collapse: collapse; }
        table.sig-tbl td { width: 50%; font-size: 8pt; padding: 0 8px; }
        table.sig-tbl td.sig-right { text-align: right; }
        .sig-line {
            border-top: 1px solid #0f2330;
            margin-top: 32px;
            margin-bottom: 4px;
        }
        .sig-label { font-size: 7.5pt; color: #6b8aa3; text-align: center; letter-spacing: 0.5px; }

        /* â”€â”€ FOOTER â”€â”€ */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #d5e0ea;
            padding: 5px 0 0 0;
        }
        table.footer-tbl { width: 100%; border-collapse: collapse; }
        table.footer-tbl td {
            font-size: 7.5pt;
            color: #8a9bac;
            padding: 0;
        }
        table.footer-tbl td.f-right { text-align: right; }
        table.footer-tbl td.f-center { text-align: center; color: #9ab4c5; }
    </style>
</head>
<body>

    {{-- STAĹA STOPKA --}}
    <div class="page-footer">
        <table class="footer-tbl">
            <tr>
                <td>ENESA Sp. z o.o. â€” ProtokĂłĹ‚ Audytowy</td>
                <td class="f-center">Dokument wygenerowany przez system audytowy â€” do weryfikacji przez audytora</td>
                <td class="f-right">{{ now()->format('d.m.Y') }}</td>
            </tr>
        </table>
    </div>

    {{-- NAGĹĂ“WEK --}}
    <div class="page-header">
        <table class="header-tbl">
            <tr>
                <td class="hdr-left">
                    <div class="company-name">ENESA</div>
                    <div class="company-tagline">Energy Audit &amp; Solutions</div>
                </td>
                <td class="hdr-right">
                    @php
                        $logoPath = public_path('Logo.png');
                        if (!file_exists($logoPath)) $logoPath = public_path('Logo2.png');
                    @endphp
                    @if(file_exists($logoPath))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="ENESA" />
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- TYTUĹ --}}
    <div class="report-title">ProtokĂłĹ‚ Audytowy</div>
    <div class="report-subtitle">
        @php
            $typeLabels = [
                'energy_audit'            => 'Audyt Energetyczny Budynku / Obiektu',
                'iso50001'                => 'System ZarzÄ…dzania EnergiÄ… â€” ISO 50001',
                'offer'                   => 'Wycena i Oferta',
                'compressor_room'         => 'SprÄ™ĹĽarkownia',
                'boiler_room'             => 'KotĹ‚ownia',
                'drying_room'             => 'Suszarnia',
                'buildings'               => 'Budynki',
                'technological_processes' => 'Procesy technologiczne',
                'bc_general'              => 'BC OgĂłlnie',
                'bc_compressor_room'      => 'BC SprÄ™ĹĽarkownia',
                'bc_boiler_room'          => 'BC KotĹ‚ownia',
                'bc_drying_room'          => 'BC Suszarnia',
                'bc_buildings'            => 'BC Budynki',
                'bc_technological_processes' => 'BC Procesy technologiczne',
                'general'                 => 'Konsultacja OgĂłlna',
            ];
            echo $typeLabels[$conversation->context_type ?? 'general'] ?? 'Audyt';
        @endphp
        &nbsp;&mdash;&nbsp;{{ $conversation->title }}
    </div>

    {{-- META --}}
    <table class="meta-grid">
        <tr>
            <td><div class="meta-card"><div class="meta-lbl">Nr dokumentu</div><div class="meta-val">PROT/{{ str_pad($conversation->id, 5, '0', STR_PAD_LEFT) }}/{{ now()->format('Y') }}</div></div></td>
            <td><div class="meta-card"><div class="meta-lbl">Data wygenerowania</div><div class="meta-val">{{ now()->format('d.m.Y') }}</div></div></td>
            <td><div class="meta-card"><div class="meta-lbl">Status</div><div class="meta-val">Do weryfikacji przez audytora</div></div></td>
        </tr>
    </table>

    {{-- DANE KLIENTA --}}
    @if($company)
    <div class="client-header">Dane Zleceniodawcy</div>
    <table class="client-data">
        <tr><td class="cl-label">Nazwa firmy</td><td>{{ $company->name }}</td></tr>
        @if($company->nip)
        <tr><td class="cl-label">NIP</td><td>{{ $company->nip }}</td></tr>
        @endif
        @if($company->street || $company->city)
        <tr>
            <td class="cl-label">Adres</td>
            <td>{{ implode(' ', array_filter([$company->street ?? null, $company->postal_code ?? null, $company->city ?? null])) }}</td>
        </tr>
        @endif
        @if($company->phone)
        <tr><td class="cl-label">Telefon</td><td>{{ $company->phone }}</td></tr>
        @endif
        @if($company->email)
        <tr><td class="cl-label">E-mail</td><td>{{ $company->email }}</td></tr>
        @endif
    </table>
    @endif
    @if(!$company && $conversation->user)
    <div class="client-header">Dane Zleceniodawcy</div>
    <table class="client-data">
        <tr><td class="cl-label">Imię i nazwisko</td><td>{{ $conversation->user->name }}</td></tr>
        @if($conversation->user->email)
        <tr><td class="cl-label">E-mail</td><td>{{ $conversation->user->email }}</td></tr>
        @endif
    </table>
    @endif

    {{-- DANE Z PROTOKOĹU --}}
    @if(!empty($protocol['sekcje']))
        @foreach($protocol['sekcje'] as $idx => $sekcja)
            <div class="section-title">
                <span class="section-num">{{ $idx + 1 }}</span>{{ $sekcja['nazwa'] ?? 'Sekcja' }}
            </div>
            <table class="data">
                <thead>
                    <tr><th style="width:42%">Pole</th><th>WartoĹ›Ä‡</th></tr>
                </thead>
                <tbody>
                    @foreach($sekcja['pola'] ?? [] as $i => $pole)
                        <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
                            <td class="key">{{ $pole['klucz'] ?? '' }}</td>
                            <td class="val">{{ $pole['wartosc'] ?? 'â€”' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @else
        <p style="color:#8a9fb3; margin-top:20px; font-size:8.5pt;">Brak danych w protokole.</p>
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
        <div style="margin-top:24px; border-top:2px solid #0e89d8; padding-top:18px;">
            <div class="section-title" style="margin-top:0;">
                <span class="section-num">R</span>Analiza i rekomendacje energetyczne
            </div>

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
                    <div style="border:1px solid #e0e0e0; border-left:4px solid {{ $borderColor }}; padding:8px 12px; margin-bottom:8px;">
                        <div style="font-weight:700; font-size:8.5pt; color:#0f2330; margin-bottom:3px;">
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
                <div style="background:#f3f8fc; border:1px solid #d5e0ea; padding:10px 12px; margin-top:12px;">
                    <div style="font-size:7.5pt; font-weight:700; color:#2c4e67; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Optymalna kolejnosc dzialan</div>
                    <div style="font-size:8.5pt; color:#0f2330; line-height:1.6;">{{ $analiza['kolejnosc_dzialan'] }}</div>
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
