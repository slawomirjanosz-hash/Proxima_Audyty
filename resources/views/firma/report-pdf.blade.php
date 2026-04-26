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

    /* FIXED FOOTER */
    .page-footer {
        position: fixed;
        bottom: 0;
        left: 0; right: 0;
        height: 24px;
        border-top: 1px solid #cccccc;
        padding: 4px 0 0;
    }
    table.footer-tbl { width: 100%; border-collapse: collapse; font-size: 7pt; color: #888888; }
    .f-center { text-align: center; }
    .f-right { text-align: right; }

    /* HEADER */
    .page-header {
        border-bottom: 3px solid #111111;
        padding-bottom: 12px;
        margin-bottom: 20px;
    }
    table.header-tbl { width: 100%; border-collapse: collapse; }
    table.header-tbl td { vertical-align: middle; padding: 0; }
    .hdr-left { width: 65%; }
    .hdr-right { width: 35%; text-align: right; }
    .hdr-right img { height: 56px; }
    .company-name { font-size: 8pt; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #111111; margin-bottom: 2px; }
    .company-tagline { font-size: 7.5pt; color: #555555; letter-spacing: 1px; text-transform: uppercase; }

    /* TITLE BAND */
    .title-band { background: #111111; color: #ffffff; padding: 11px 16px; margin-bottom: 18px; }
    .title-band-main { font-size: 13pt; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; }
    .title-band-sub { font-size: 8pt; color: #cccccc; margin-top: 3px; letter-spacing: 0.5px; }

    /* META BOX */
    table.meta-box { width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #dddddd; }
    table.meta-box td { padding: 7px 12px; font-size: 8.5pt; border-bottom: 1px solid #eeeeee; vertical-align: top; }
    table.meta-box td.meta-label { font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #444444; width: 30%; background: #f5f5f5; border-right: 1px solid #dddddd; }
    table.meta-box td.meta-val { color: #111111; }
    table.meta-box tr:last-child td { border-bottom: none; }

    /* CLIENT */
    .client-header { font-size: 7.5pt; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #ffffff; background: #333333; padding: 5px 12px; }
    table.client-data { width: 100%; border-collapse: collapse; margin-bottom: 22px; border: 1px solid #dddddd; border-top: none; }
    table.client-data td { padding: 6px 12px; font-size: 8.5pt; border-bottom: 1px solid #eeeeee; vertical-align: top; }
    table.client-data td.cl-label { font-weight: 700; color: #444444; width: 30%; background: #fafafa; border-right: 1px solid #dddddd; }
    table.client-data tr:last-child td { border-bottom: none; }

    /* SECTION HEADER */
    table.section-hdr { width: 100%; border-collapse: collapse; margin-top: 18px; }
    table.section-hdr td.acc { width: 4px; background: #111111; }
    table.section-hdr td.label { background: #222222; color: #ffffff; padding: 6px 12px; font-weight: 700; font-size: 9.5pt; letter-spacing: 0.8px; text-transform: uppercase; }

    /* DATA TABLE */
    table.data { width: 100%; border-collapse: collapse; border: 1px solid #cccccc; border-top: none; }
    table.data td { padding: 6px 12px; vertical-align: top; border-bottom: 1px solid #e8e8e8; font-size: 8.5pt; }
    table.data td.key { color: #333333; font-weight: 700; width: 40%; background: #fafafa; border-right: 1px solid #e0e0e0; }
    table.data td.val { color: #111111; }
    table.data tr.even td { background: #f5f5f5; }
    table.data tr.even td.key { background: #efefef; }
    table.data tr:last-child td { border-bottom: none; }

    /* REMARKS */
    .remarks { border: 1px solid #aaaaaa; margin-top: 20px; }
    .remarks-title { background: #333333; color: #ffffff; font-weight: 700; font-size: 8pt; letter-spacing: 1.5px; text-transform: uppercase; padding: 5px 12px; }
    .remarks-body { padding: 10px 14px; font-size: 8.5pt; color: #111111; line-height: 1.65; }

    /* ANALYSIS */
    .analysis-header { background: #1a3a52; color: #ffffff; font-weight: 700; font-size: 8pt; letter-spacing: 1.5px; text-transform: uppercase; padding: 6px 12px; margin-top: 20px; }
    .summary-box { border: 1px solid #aaccee; background: #f0f8ff; padding: 10px 14px; margin-top: 0; margin-bottom: 12px; font-size: 8.5pt; color: #0c3a5a; line-height: 1.65; }
    .summary-label { font-size: 7pt; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #1a6aa0; margin-bottom: 4px; }

    table.rec-table { width: 100%; border-collapse: collapse; border: 1px solid #cccccc; margin-bottom: 8px; }
    table.rec-table td { padding: 6px 10px; font-size: 8pt; vertical-align: top; border-bottom: 1px solid #e8e8e8; }
    table.rec-table td.rec-label { font-weight: 700; width: 28%; background: #f5f5f5; border-right: 1px solid #e0e0e0; color: #444; font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.3px; }
    table.rec-table tr:last-child td { border-bottom: none; }
    .rec-title-row td { background: #f0f0f0; font-weight: 800; font-size: 9pt; color: #111; padding: 7px 10px; }
    .badge-high { background: #fee2e2; color: #991b1b; padding: 2px 8px; font-size: 7pt; font-weight: 700; }
    .badge-mid  { background: #fef3c7; color: #92400e; padding: 2px 8px; font-size: 7pt; font-weight: 700; }
    .badge-low  { background: #d1fae5; color: #065f46; padding: 2px 8px; font-size: 7pt; font-weight: 700; }
    .savings-box { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 4px 10px; font-size: 8pt; font-weight: 700; color: #166534; display: inline-block; margin-top: 4px; }

    .order-box { border: 1px solid #cccccc; background: #fafafa; padding: 10px 14px; margin-top: 10px; font-size: 8.5pt; color: #333; line-height: 1.65; }
    .order-label { font-size: 7pt; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #555; margin-bottom: 4px; }

    /* SIGNATURE */
    .signature-block { margin-top: 36px; border-top: 1px solid #111111; padding-top: 6px; }
    table.sig-tbl { width: 100%; border-collapse: collapse; }
    table.sig-tbl td { width: 50%; font-size: 8pt; padding: 0 8px; }
    table.sig-tbl td.sig-right { text-align: right; }
    .sig-line { border-top: 1px solid #111111; margin-top: 32px; margin-bottom: 4px; }
    .sig-label { font-size: 7.5pt; color: #555555; text-align: center; letter-spacing: 0.5px; }
</style>
</head>
<body>

    {{-- FIXED FOOTER --}}
    <div class="page-footer">
        <table class="footer-tbl">
            <tr>
                <td>ENESA Sp. z o.o. — Raport Audytowy</td>
                <td class="f-center">Dokument wygenerowany przez system audytowy — do weryfikacji przez audytora</td>
                <td class="f-right">{{ now()->format('d.m.Y') }}</td>
            </tr>
        </table>
    </div>

    {{-- HEADER --}}
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

    {{-- TITLE BAND --}}
    <div class="title-band">
        <div class="title-band-main">Raport Audytowy</div>
        <div class="title-band-sub">{{ $audit->title }}</div>
    </div>

    {{-- META --}}
    <table class="meta-box">
        <tr>
            <td class="meta-label">Nr dokumentu</td>
            <td class="meta-val">RAPORT/{{ str_pad($audit->id, 5, '0', STR_PAD_LEFT) }}/{{ now()->format('Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Data wygenerowania</td>
            <td class="meta-val">{{ now()->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Typ audytu</td>
            <td class="meta-val">{{ $audit->auditType?->name ?: $audit->audit_type ?: 'Audyt energetyczny' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Status</td>
            <td class="meta-val">{{ $audit->statusLabel() }}</td>
        </tr>
        <tr>
            <td class="meta-label">Audytor</td>
            <td class="meta-val">{{ $audit->auditor?->name ?? '—' }}</td>
        </tr>
    </table>

    {{-- KLIENT --}}
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
        <tr><td class="cl-label">Telefon</td><td>{{ $company->phone }}</td></tr>
        @endif
        @if($company->email)
        <tr><td class="cl-label">E-mail</td><td>{{ $company->email }}</td></tr>
        @endif
    </table>

    {{-- KWESTIONARIUSZ ISO 50001 --}}
    @if(isset($questionnaireQuestions) && $questionnaireQuestions && !empty($audit->questionnaire_answers))
        @php $answers = $audit->questionnaire_answers; @endphp
        <table class="section-hdr" style="margin-top:20px;">
            <tr>
                <td class="acc">&nbsp;</td>
                <td class="label">Kwestionariusz wstępny ISO 50001</td>
            </tr>
        </table>
        @foreach($questionnaireQuestions as $blockKey => $questions)
            @php $blockAnswers = $questions->filter(fn($q) => !empty($answers[$q->question_code])); @endphp
            @if($blockAnswers->isNotEmpty())
                <table class="data" style="margin-top:0;">
                    <tr>
                        <td colspan="2" style="background:#2a4a62; color:#fff; font-weight:700; font-size:8pt; letter-spacing:0.5px; padding:5px 12px;">
                            {{ \App\Models\Iso50001QuestionnaireQuestion::$blockLabels[$blockKey] ?? 'Blok ' . $blockKey }}
                        </td>
                    </tr>
                    @foreach($blockAnswers as $i => $question)
                    <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
                        <td class="key" style="width:45%;">
                            <span style="color:#1a6aa0; font-size:7.5pt;">{{ $question->question_code }}</span>
                            {{ $question->question_text }}
                        </td>
                        <td class="val">{{ $answers[$question->question_code] }}</td>
                    </tr>
                    @endforeach
                </table>
            @endif
        @endforeach
    @endif

    {{-- DANE AUDYTU --}}
    @php $proto = $conversation?->protocol_data ?? []; @endphp

    @if(!empty($proto['sekcje']))
        @foreach($proto['sekcje'] as $si => $section)
            <table class="section-hdr">
                <tr>
                    <td class="acc">&nbsp;</td>
                    <td class="label">{{ $si + 1 }}. {{ $section['nazwa'] ?? 'Sekcja' }}</td>
                </tr>
            </table>
            @if(!empty($section['pola']))
                <table class="data">
                    @foreach($section['pola'] as $pi => $pole)
                    <tr class="{{ $pi % 2 === 1 ? 'even' : '' }}">
                        <td class="key">{{ $pole['klucz'] ?? '' }}</td>
                        <td class="val">{{ $pole['wartosc'] ?? '—' }}</td>
                    </tr>
                    @endforeach
                </table>
            @endif
        @endforeach
    @endif

    @if(!empty($proto['uwagi']))
        <div class="remarks">
            <div class="remarks-title">Uwagi i zalecenia</div>
            <div class="remarks-body">{{ $proto['uwagi'] }}</div>
        </div>
    @endif

    {{-- REKOMENDACJE --}}
    @php $analiza = $proto['analiza'] ?? null; @endphp
    @if($analiza)
        <div class="analysis-header">Rekomendacje i analiza energetyczna</div>

        @if(!empty($analiza['podsumowanie']))
            <div class="summary-box">
                <div class="summary-label">Stan energetyczny obiektu</div>
                {{ $analiza['podsumowanie'] }}
            </div>
        @endif

        @if(!empty($analiza['rekomendacje']))
            @foreach($analiza['rekomendacje'] as $rec)
                @php $p = strtolower($rec['priorytet'] ?? 'sredni'); @endphp
                <table class="rec-table" style="margin-top:10px;">
                    <tr class="rec-title-row">
                        <td colspan="2">
                            {{ $rec['nr'] ?? '' }}. {{ $rec['obszar'] ?? '' }}
                            &nbsp;
                            <span class="{{ $p === 'wysoki' ? 'badge-high' : ($p === 'sredni' ? 'badge-mid' : 'badge-low') }}">
                                {{ $p === 'wysoki' ? 'Wysoki priorytet' : ($p === 'sredni' ? 'Średni priorytet' : 'Niski priorytet') }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="rec-label">Działanie</td>
                        <td>{{ $rec['dzialanie'] ?? '' }}</td>
                    </tr>
                    @if(!empty($rec['uzasadnienie']))
                    <tr>
                        <td class="rec-label">Uzasadnienie</td>
                        <td>{{ $rec['uzasadnienie'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($rec['szacowane_oszczednosci']))
                    <tr>
                        <td class="rec-label">Szacowane oszczędności</td>
                        <td><span class="savings-box">{{ $rec['szacowane_oszczednosci'] }}</span></td>
                    </tr>
                    @endif
                </table>
            @endforeach
        @endif

        @if(!empty($analiza['kolejnosc_dzialan']))
            <div class="order-box" style="margin-top:14px;">
                <div class="order-label">Optymalna kolejność działań</div>
                {{ $analiza['kolejnosc_dzialan'] }}
            </div>
        @endif
    @endif

    {{-- PODPISY --}}
    <div class="signature-block">
        <table class="sig-tbl">
            <tr>
                <td>
                    <div class="sig-line"></div>
                    <div class="sig-label">Audytor / Data</div>
                </td>
                <td class="sig-right">
                    <div class="sig-line"></div>
                    <div class="sig-label">Zleceniodawca / Data</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
