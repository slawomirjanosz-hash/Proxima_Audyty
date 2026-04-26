<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Raport: {{ $audit->title }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 12px;
            color: #0f2330;
            background: #fff;
        }
        .page { max-width: 860px; margin: 0 auto; padding: 30px 36px 50px; }
        .report-header { border-bottom: 3px solid #0e89d8; padding-bottom: 16px; margin-bottom: 20px; }
        .report-logo { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .report-logo img { height: 40px; width: auto; }
        .report-logo-text h1 { margin: 0; font-size: 18px; font-weight: 800; color: #0e89d8; }
        .report-logo-text p { margin: 2px 0 0; font-size: 11px; color: #4c6373; text-transform: uppercase; letter-spacing: .5px; }
        .report-title { font-size: 20px; font-weight: 800; color: #0f2330; margin: 0 0 4px; }
        .report-subtitle { font-size: 13px; color: #4c6373; }
        .meta-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin: 16px 0; }
        .meta-card { background: #f3f8fc; border: 1px solid #d5e0ea; border-radius: 8px; padding: 8px 12px; }
        .meta-card .label { font-size: 10px; text-transform: uppercase; letter-spacing: .5px; color: #6b8aa3; font-weight: 700; margin-bottom: 2px; }
        .meta-card .value { font-size: 12px; font-weight: 600; color: #0f2330; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 5px; font-size: 11px; font-weight: 700; }
        .status-wysłany { background:#dbeafe; color:#1e40af; }
        .status-rozpoczęty { background:#d1fae5; color:#065f46; }
        .status-do_analizy { background:#fef3c7; color:#92400e; }
        .status-zwrócony_do_poprawy { background:#fee2e2; color:#991b1b; }
        .status-zaakceptowany { background:#d1fae5; color:#065f46; }
        .status-zakończony { background:#e5e7eb; color:#374151; }
        .status-zafakturowany { background:#ede9fe; color:#5b21b6; }
        .status-zapłacony { background:#d1fae5; color:#064e3b; }
        .section-title { font-size: 14px; font-weight: 800; color: #10344c; margin: 18px 0 8px; padding-bottom: 4px; border-bottom: 1px solid #d5e0ea; }
        .section-num { display: inline-block; width: 24px; height: 24px; background: #0e89d8; color: #fff; border-radius: 50%; text-align: center; line-height: 24px; font-size: 11px; font-weight: 800; margin-right: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 11px; }
        table th { background: #eef5fb; color: #2c4e67; font-weight: 700; padding: 6px 8px; text-align: left; border: 1px solid #d5e0ea; }
        table td { border: 1px solid #dde8f2; padding: 5px 8px; color: #0f2330; }
        table tr:nth-child(even) td { background: #f8fbff; }
        .tasks-list { list-style: none; padding: 0; margin: 6px 0; display: grid; gap: 3px; }
        .tasks-list li { padding: 3px 6px; background: #f3f8fc; border-radius: 5px; color: #355468; }
        .tasks-list li.done::before { content: "✓ "; color: #0c5f28; font-weight: 700; }
        .tasks-list li.todo::before { content: "○ "; color: #9ab4c5; }
        .no-data { color: #9ab4c5; font-style: italic; padding: 6px 0; }
        .footer { margin-top: 40px; padding-top: 12px; border-top: 1px solid #d5e0ea; display: flex; justify-content: space-between; font-size: 10px; color: #8a9bac; }
        @media print {
            @page { size: A4; margin: 0; }
            body { font-size: 11px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .page { padding: 14mm 16mm 18mm; max-width: 100%; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Print / Back buttons --}}
    <div class="no-print" style="margin-bottom:16px; display:flex; gap:10px;">
        <button onclick="window.print()" style="padding:8px 16px; background:linear-gradient(130deg,#1ba84a,#0e89d8); color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer;">🖨 Drukuj / Zapisz PDF</button>
        <a href="{{ route('firma.audit', [$company, $audit]) }}" style="padding:8px 16px; background:#dbe9f5; color:#1d4f73; border-radius:8px; font-weight:700; text-decoration:none;">← Wróć</a>
    </div>

    {{-- Header --}}
    <div class="report-header">
        <div class="report-logo">
            <img src="/Logo.png" alt="ENESA" onerror="this.style.display='none'">
            <div class="report-logo-text">
                <h1>ENESA</h1>
                <p>Energy Audit Systems</p>
            </div>
        </div>
        <div class="report-title">{{ $audit->title }}</div>
        <div class="report-subtitle">{{ $audit->auditType?->name ?: $audit->audit_type ?: 'Audyt energetyczny' }} · Raport</div>
    </div>

    {{-- Meta --}}
    <div class="meta-grid">
        <div class="meta-card">
            <div class="label">Firma</div>
            <div class="value">{{ $company->name }}</div>
        </div>
        <div class="meta-card">
            <div class="label">NIP</div>
            <div class="value">{{ $company->nip ?: '—' }}</div>
        </div>
        <div class="meta-card">
            <div class="label">Status audytu</div>
            <div class="value">
                @php $sc = str_replace(' ', '_', $audit->status); @endphp
                <span class="status-badge status-{{ $sc }}">{{ $audit->statusLabel() }}</span>
            </div>
        </div>
        <div class="meta-card">
            <div class="label">Audytor</div>
            <div class="value">{{ $audit->auditor?->name ?? '—' }}</div>
        </div>
        <div class="meta-card">
            <div class="label">Data utworzenia</div>
            <div class="value">{{ $audit->created_at->format('d.m.Y') }}</div>
        </div>
        <div class="meta-card">
            <div class="label">Ostatnia aktualizacja</div>
            <div class="value">{{ $audit->updated_at->format('d.m.Y H:i') }}</div>
        </div>
    </div>

    @if($company->city || $company->street)
        <div style="margin-bottom:16px; font-size:12px; color:#4c6373;">
            📍
            @if($company->street){{ $company->street }},@endif
            @if($company->postal_code){{ $company->postal_code }}@endif
            @if($company->city){{ $company->city }}@endif
        </div>
    @endif

    {{-- Questionnaire answers --}}
    @if($questionnaireQuestions && !empty($audit->questionnaire_answers))
        @php $answers = $audit->questionnaire_answers; @endphp
        <div class="section-title" style="margin-top:24px;">
            <span class="section-num">📋</span>Kwestionariusz wstępny ISO 50001
        </div>
        @foreach($questionnaireQuestions as $blockKey => $questions)
            @php
                $blockAnswers = $questions->filter(fn($q) => !empty($answers[$q->question_code]));
            @endphp
            @if($blockAnswers->isNotEmpty())
                <div style="font-size:11px; font-weight:800; color:#0e89d8; text-transform:uppercase; letter-spacing:.5px; margin:12px 0 4px;">
                    {{ \App\Models\Iso50001QuestionnaireQuestion::$blockLabels[$blockKey] ?? 'Blok ' . $blockKey }}
                </div>
                <table>
                    <thead>
                        <tr><th style="width:8%">Kod</th><th style="width:42%">Pytanie</th><th>Odpowiedź</th></tr>
                    </thead>
                    <tbody>
                        @foreach($blockAnswers as $question)
                            <tr>
                                <td style="font-weight:700; color:#0e89d8;">{{ $question->question_code }}</td>
                                <td style="color:#4c6373;">{{ $question->question_text }}</td>
                                <td style="font-weight:600;">{{ $answers[$question->question_code] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    @endif

    {{-- AI Protocol sections --}}
    @php
        $proto = $conversation?->protocol_data ?? [];
    @endphp

    @if(!empty($proto['sekcje']))
        @foreach($proto['sekcje'] as $sectionIndex => $section)
            <div class="section-title">
                <span class="section-num">{{ $sectionIndex + 1 }}</span>{{ $section['nazwa'] ?? 'Sekcja' }}
            </div>
            @if(!empty($section['pola']))
                <table>
                    <thead>
                        <tr><th>Pole</th><th>Wartość</th></tr>
                    </thead>
                    <tbody>
                        @foreach($section['pola'] as $i => $pole)
                            <tr>
                                <td style="font-weight:600; color:#2c4e67; width:48%;">{{ $pole['klucz'] ?? '' }}</td>
                                <td>{{ $pole['wartosc'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach

        @if(!empty($proto['uwagi']))
            <div class="section-title" style="margin-top:24px;">Uwagi i zalecenia</div>
            <div style="background:#fffbf0; border:1px solid #e8d07a; border-radius:8px; padding:12px 16px; font-size:12px; color:#6b4e00; line-height:1.7;">
                {{ $proto['uwagi'] }}
            </div>
        @endif

        {{-- Analiza i rekomendacje --}}
        @php $analiza = $proto['analiza'] ?? null; @endphp
        @if($analiza)
            <div style="margin-top:30px; border-top:2px solid #0e89d8; padding-top:22px; page-break-before:auto;">
                <div class="section-title" style="margin-top:0;">Rekomendacje i analiza energetyczna</div>

                @if(!empty($analiza['podsumowanie']))
                    <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:8px; padding:12px 14px; margin-bottom:16px;">
                        <div style="font-size:10px; font-weight:700; color:#0369a1; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Stan energetyczny obiektu</div>
                        <p style="margin:0; font-size:12px; color:#0c4a6e; line-height:1.7;">{{ $analiza['podsumowanie'] }}</p>
                    </div>
                @endif

                @if(!empty($analiza['rekomendacje']))
                    @foreach($analiza['rekomendacje'] as $rec)
                        @php $p = strtolower($rec['priorytet'] ?? 'sredni'); $border = $p === 'wysoki' ? '#dc2626' : ($p === 'sredni' ? '#d97706' : '#059669'); @endphp
                        <div style="border:1px solid #e0e0e0; border-left:4px solid {{ $border }}; border-radius:8px; padding:10px 14px; margin-bottom:10px;">
                            <div style="font-weight:800; font-size:12px; color:#0f2330; margin-bottom:4px;">
                                {{ $rec['nr'] ?? '' }}. {{ $rec['obszar'] ?? '' }}
                                <span style="padding:2px 8px; border-radius:4px; font-size:10px; margin-left:6px; background:{{ $p === 'wysoki' ? '#fee2e2' : ($p === 'sredni' ? '#fef3c7' : '#d1fae5') }}; color:{{ $p === 'wysoki' ? '#991b1b' : ($p === 'sredni' ? '#92400e' : '#065f46') }};">{{ $p === 'wysoki' ? 'Wysoki priorytet' : ($p === 'sredni' ? 'Średni priorytet' : 'Niski priorytet') }}</span>
                            </div>
                            <div style="font-size:12px; font-weight:600; color:#1a2d3d; margin-bottom:3px;">{{ $rec['dzialanie'] ?? '' }}</div>
                            @if(!empty($rec['uzasadnienie']))
                                <div style="font-size:11px; color:#4c6373; margin-bottom:3px;">{{ $rec['uzasadnienie'] }}</div>
                            @endif
                            @if(!empty($rec['szacowane_oszczednosci']))
                                <div style="font-size:11px; font-weight:700; color:#166534;">💰 {{ $rec['szacowane_oszczednosci'] }}</div>
                            @endif
                        </div>
                    @endforeach
                @endif

                @if(!empty($analiza['kolejnosc_dzialan']))
                    <div style="background:#f5f5f5; border:1px solid #e0e0e0; border-radius:8px; padding:12px 14px; margin-top:14px;">
                        <div style="font-size:10px; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Optymalna kolejność działań</div>
                        <p style="margin:0; font-size:12px; color:#333; line-height:1.7;">{{ $analiza['kolejnosc_dzialan'] }}</p>
                    </div>
                @endif
            </div>
        @endif

    @elseif($conversation && empty($proto))
        <div class="no-data" style="padding:20px 0;">
            Dane audytu zostały zebrane, ale raport nie jest jeszcze gotowy.
        </div>
    @else
        <div class="no-data" style="padding:20px 0;">Brak danych dla tego audytu.</div>
    @endif

    <div class="footer">
        <span>ENESA — Energy Audit Systems | Wygenerowano {{ now()->format('d.m.Y H:i') }}</span>
        <span>{{ $company->name }} · {{ $audit->title }}</span>
    </div>
</div>
</body>
</html>
