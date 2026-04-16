<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', serif; font-size: 11pt; color: #1a2d3d; }

        table.header-tbl { width: 100%; border-collapse: collapse; padding-bottom: 10px; }
        table.header-tbl td { padding: 0 0 10px 0; vertical-align: bottom; }
        .header-border { border-bottom: 3px solid #0e89d8; margin-bottom: 18px; }
        .logo-h1 { font-size: 18pt; color: #0e3755; font-weight: 700; }
        .logo-sub { font-size: 9pt; color: #6b8499; margin-top: 3px; }
        .meta-cell { text-align: right; font-size: 9pt; color: #6b8499; }

        .doc-title { font-size: 15pt; font-weight: 700; color: #0e3755; margin-bottom: 4px; }
        .doc-sub   { font-size: 10pt; color: #8a9fb3; margin-bottom: 20px; }

        table.section-hdr { width: 100%; border-collapse: collapse; margin-top: 16px; margin-bottom: 0; }
        table.section-hdr td.acc   { width: 5px; background: #0e89d8; }
        table.section-hdr td.label { background: #e2f0fb; color: #0e3755; padding: 7px 12px; font-weight: 700; font-size: 11pt; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
        table.data td { padding: 7px 12px; vertical-align: top; border-bottom: 1px solid #e0eaf2; }
        table.data td.key { color: #6b8499; font-weight: 600; width: 42%; }
        table.data td.val { color: #1a2d3d; }
        table.data tr.even td { background: #f5faff; }

        .remarks { background: #fffbf0; border: 1px solid #e8d07a; padding: 12px 14px; margin-top: 18px; }
        .remarks-title { font-weight: 700; font-size: 10pt; color: #7d5e00; margin-bottom: 6px; }
        .remarks-body { font-size: 10pt; color: #6b4e00; line-height: 1.6; }

        .footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #c5d8ea; }
        table.footer-tbl { width: 100%; border-collapse: collapse; }
        table.footer-tbl td { font-size: 8.5pt; color: #8a9fb3; padding: 0; }
        table.footer-tbl td.right { text-align: right; }
    </style>
</head>
<body>

    <table class="header-tbl">
        <tr>
            <td>
                <div class="logo-h1">Enesa sp. z o. o.</div>
                <div class="logo-sub">Audyty energetyczne i certyfikacja ISO 50001</div>
            </td>
            <td class="meta-cell">
                Wygenerowano: {{ now()->format('d.m.Y H:i') }}<br>
                Protokol nr: {{ $conversation->id }}
            </td>
        </tr>
    </table>
    <div class="header-border"></div>

    <div class="doc-title">Protokol z rozmowy z asystentem AI</div>
    <div class="doc-sub">
        {{ $conversation->title }} &middot;
        @php
            $typeLabels = [
                'energy_audit' => 'Audyt energetyczny',
                'iso50001'     => 'ISO 50001',
                'offer'        => 'Oferta',
                'general'      => 'Ogolny',
            ];
            echo $typeLabels[$conversation->context_type ?? 'general'] ?? 'Rozmowa';
        @endphp
        @if($conversation->protocol_generated_at)
            &middot; Wygenerowany: {{ $conversation->protocol_generated_at->format('d.m.Y H:i') }}
        @endif
    </div>

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
