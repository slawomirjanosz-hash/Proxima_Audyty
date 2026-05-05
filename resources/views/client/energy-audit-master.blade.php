<x-layouts.app>
<style>
/* =============================================
   Audyt Energetyczny całego zakładu — Master Form
   Szata naszego systemu + funkcjonalność ENESA Master
   ============================================= */

/* == Sticky progress bar == */
.master-topbar {
    position: sticky;
    top: 0;
    z-index: 50;
    background: #fff;
    border-bottom: 1px solid #d5e0ea;
    padding: 10px 0;
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}
.master-progress-bar {
    flex: 1;
    height: 6px;
    background: #e0ecf5;
    border-radius: 99px;
    overflow: hidden;
    min-width: 120px;
}
.master-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #1ba84a, #0e89d8);
    border-radius: 99px;
    transition: width 0.4s ease;
}
.master-save-status {
    font-size: 12px;
    color: #6b8aa3;
    white-space: nowrap;
    min-width: 180px;
    text-align: right;
}
.master-save-status.saved { color: #16a34a; }
.master-save-status.saving { color: #d97706; }
.master-save-status.error { color: #dc2626; }

/* == Navigation tabs (sections) == */
.master-nav {
    display: flex;
    gap: 2px;
    overflow-x: auto;
    padding: 0 0 8px;
    flex-wrap: nowrap;
    scrollbar-width: thin;
}
.master-nav-btn {
    flex-shrink: 0;
    padding: 7px 14px;
    background: #f0f7ff;
    border: 1px solid #d5e0ea;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    color: #1e3a5f;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 6px;
}
.master-nav-btn:hover { background: #dbeafe; border-color: #93c5fd; }
.master-nav-btn.active { background: #0e89d8; color: #fff; border-color: #0e89d8; }
.master-nav-btn .nav-count { font-size: 10px; opacity: .75; font-family: monospace; }

/* == Section panels == */
.master-section {
    display: none;
}
.master-section.active {
    display: block;
}
.master-section-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 20px 0 16px;
    border-bottom: 2px solid #0e89d8;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.master-section-eyebrow {
    font-size: 10px;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #d97706;
    font-weight: 700;
    margin-bottom: 4px;
}
.master-section-title {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    color: #0f2330;
    line-height: 1.25;
}
.master-section-desc {
    font-size: 13px;
    color: #4c6373;
    margin-top: 4px;
    max-width: 640px;
}
.master-section-badge {
    background: #e0f2fe;
    color: #0369a1;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 8px;
    white-space: nowrap;
    flex-shrink: 0;
    font-family: monospace;
}

/* == Group == */
.master-group {
    background: #f8fbff;
    border: 1px solid #d5e0ea;
    border-radius: 12px;
    padding: 16px 18px;
    margin-bottom: 16px;
}
.master-group-title {
    font-size: 14px;
    font-weight: 800;
    color: #0f2330;
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.master-group-title::before { content: '▶'; font-size: 9px; color: #0e89d8; }
.master-group-desc { font-size: 12px; color: #6b8aa3; font-style: italic; margin-bottom: 12px; }
.master-group-info {
    background: #f0f9ff;
    border-left: 4px solid #38bdf8;
    border-radius: 0 8px 8px 0;
    padding: 10px 14px;
    margin-bottom: 14px;
    font-size: 12.5px;
    color: #0c4a6e;
}
.master-group-info ul { margin: 8px 0 0 20px; }
.master-group-info li { margin-bottom: 3px; }
.master-group-info strong { color: #0f2330; }

/* == Field row == */
.master-field {
    display: grid;
    grid-template-columns: 220px 1fr 90px;
    gap: 12px;
    align-items: start;
    padding: 10px 0;
    border-bottom: 1px dashed #e0ecf5;
}
.master-field:last-child { border-bottom: none; }
@media (max-width: 860px) {
    .master-field { grid-template-columns: 1fr; gap: 4px; }
}
.mf-label { display: flex; flex-direction: column; gap: 3px; }
.mf-q { font-size: 13px; font-weight: 600; color: #1a2e3d; line-height: 1.3; }
.mf-id { font-size: 10px; color: #8aa3b5; font-family: monospace; }
.mf-wrap { display: flex; flex-direction: column; gap: 3px; }
.mf-hint { font-size: 11px; color: #8aa3b5; font-style: italic; }
.mf-unit { font-size: 11px; color: #6b8aa3; font-family: monospace; padding-top: 10px; text-align: right; }
.mf-input, .mf-select, .mf-textarea {
    font-size: 13px;
    padding: 8px 11px;
    border: 1px solid #c8daea;
    border-radius: 8px;
    background: #fff;
    color: #0f2330;
    width: 100%;
    font-family: inherit;
    transition: all .15s;
}
.mf-input:focus, .mf-select:focus, .mf-textarea:focus {
    outline: none;
    border-color: #0e89d8;
    box-shadow: 0 0 0 3px rgba(14,137,216,.12);
}
.mf-input.mf-filled, .mf-select.mf-filled, .mf-textarea.mf-filled {
    background: #f0f9ff;
    border-color: #38bdf8;
}
.mf-textarea { min-height: 80px; resize: vertical; line-height: 1.5; }

/* == Tags == */
.mtag {
    display: inline-block;
    padding: 2px 7px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .4px;
    border-radius: 4px;
    font-family: monospace;
}
.mtag-kon { background: #0f2330; color: #fff; }
.mtag-em  { background: #d97706; color: #fff; }
.mtag-ur  { background: #64748b; color: #fff; }

/* == Transposed table == */
.master-table-wrap { overflow-x: auto; border-radius: 10px; border: 1px solid #d5e0ea; margin-top: 14px; }
.master-table { border-collapse: collapse; min-width: 100%; font-size: 12.5px; }
.master-table th, .master-table td {
    padding: 7px 10px;
    border: 1px solid #e0ecf5;
    text-align: left;
    vertical-align: middle;
}
.master-table thead th {
    background: #0f2330;
    color: #fff;
    font-size: 11.5px;
    text-align: center;
    font-weight: 700;
    position: sticky;
    top: 0;
    z-index: 2;
}
.master-table .th-q {
    text-align: left;
    min-width: 230px;
    position: sticky;
    left: 0;
    z-index: 3;
    background: #0f2330;
}
.master-table .td-q {
    background: #f8fbff;
    position: sticky;
    left: 0;
    z-index: 1;
    border-right: 2px solid #d5e0ea;
}
.master-table .td-q .q-l { font-size: 12.5px; font-weight: 600; color: #1a2e3d; }
.master-table .td-q .q-id { font-size: 9.5px; color: #8aa3b5; font-family: monospace; }
.master-table .td-q .q-h { font-size: 11px; color: #8aa3b5; font-style: italic; margin-top: 2px; }
.master-table td.td-num { background: #f8fbff; text-align: center; color: #6b8aa3; font-family: monospace; font-size: 11px; }
.master-table .cell-inp {
    width: 100%; padding: 6px 8px; font-size: 12px; font-family: inherit;
    border: 1px solid #d5e0ea; border-radius: 5px; text-align: center;
    transition: all .15s;
    background: #fff;
}
.master-table .cell-inp:focus { outline: none; border-color: #0e89d8; background: #f0f9ff; }
.master-table .cell-inp.mf-filled { background: #f0f9ff; border-color: #38bdf8; }
.master-table .sec-header td { background: #e0ecf5; font-weight: 700; color: #0c3c5f; font-size: 11.5px; text-transform: uppercase; letter-spacing: .4px; }

/* == Consumption table == */
.zuzycia-foot td { background: #e0ecf5; font-weight: 700; font-family: monospace; font-size: 11.5px; }
.sigma-cell { background: #fef9c3 !important; color: #854d0e; font-weight: 700; }

/* == Matrix status == */
.mac-ok { color: #16a34a; font-weight: 700; font-family: monospace; }
.mac-err { color: #dc2626; font-weight: 700; font-family: monospace; }

/* == Row-add button == */
.row-add-btn {
    margin-top: 10px;
    padding: 8px 16px;
    background: #fff;
    color: #0e89d8;
    border: 1px dashed #38bdf8;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s;
}
.row-add-btn:hover { background: #f0f9ff; border-style: solid; }

/* == Chat / agent helper banner == */
.master-helper-banner {
    display: flex;
    align-items: center;
    gap: 12px;
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border: 1px solid #bae6fd;
    border-radius: 14px;
    padding: 14px 18px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.master-helper-icon { font-size: 28px; flex-shrink: 0; }
.master-helper-text { flex: 1; min-width: 200px; }
.master-helper-text strong { font-size: 14px; font-weight: 800; color: #0c4a6e; display: block; margin-bottom: 2px; }
.master-helper-text span { font-size: 13px; color: #0369a1; }
.master-helper-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.master-helper-btn { padding: 8px 14px; border-radius: 8px; border: none; cursor: pointer; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.master-helper-btn-chat  { background: #0f2330; color: #fff; }
.master-helper-btn-agent { background: linear-gradient(130deg,#1ba84a,#0e89d8); color: #fff; }

/* == Bottom nav == */
.master-bottom-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 24px;
    padding-top: 16px;
    border-top: 1px solid #d5e0ea;
    gap: 12px;
    flex-wrap: wrap;
}
.btn-master-prev, .btn-master-next {
    padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; border: none; transition: all .15s;
}
.btn-master-prev { background: #e0ecf5; color: #1d4f73; }
.btn-master-prev:disabled { opacity: .4; cursor: not-allowed; }
.btn-master-next { background: linear-gradient(130deg, #1ba84a, #0e89d8); color: #fff; }
.btn-master-next:disabled { opacity: .4; cursor: not-allowed; }
</style>

<section class="panel">

    {{-- Page header --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:16px; flex-wrap:wrap;">
        <div>
            <h1 style="margin:0 0 4px; font-size:22px; font-weight:800; color:#0f2330;">⚡ Audyt Energetyczny całego zakładu</h1>
            <p style="margin:0; font-size:13px; color:#4c6373;">
                Ankieta Master — dane zebrane tutaj będą automatycznie używane we wszystkich kolejnych audytach energetycznych.
                @if($company)
                    &nbsp;·&nbsp; <strong>{{ $company->name }}</strong>
                @endif
            </p>
        </div>
        <a href="{{ route('strefa-klienta') }}" style="text-decoration:none; padding:8px 14px; background:#e0ecf5; color:#1d4f73; font-weight:700; border-radius:10px; font-size:13px;">← Strefa klienta</a>
    </div>

    @if(session('status'))
    <div style="background:#f0f9ff; border:1px solid #38bdf8; border-radius:12px; padding:12px 16px; margin-bottom:14px; font-size:13px; color:#0c4a6e; display:flex; align-items:center; gap:10px;">
        <span>ℹ️</span> {{ session('status') }}
        @if($masterData && $masterData->completion_percent >= 30)
        &nbsp;·&nbsp;
        <a href="{{ route('strefa-klienta') }}" style="color:#0e89d8; font-weight:700; text-decoration:underline;">Wróć do strefy klienta i kontynuuj audyt →</a>
        @endif
    </div>
    @endif

    {{-- Helper banner --}}
    <div class="master-helper-banner">
        <div class="master-helper-icon">💡</div>
        <div class="master-helper-text">
            <strong>Nie wiesz jak wypełnić którąś sekcję?</strong>
            <span>Możesz napisać do nas na czacie lub przejść do pracy z agentem AI, który przeprowadzi Cię przez ankietę krok po kroku.</span>
        </div>
        <div class="master-helper-actions">
            <a href="{{ route('strefa-klienta') }}#chat" class="master-helper-btn master-helper-btn-chat">💬 Czat z doradcą</a>
            <a href="{{ route('strefa-klienta') }}#zapytanie" class="master-helper-btn master-helper-btn-agent">🤖 Praca z agentem AI</a>
        </div>
    </div>

    {{-- Sticky topbar: progress --}}
    <div class="master-topbar">
        <div style="font-size:12px; font-weight:700; color:#1d4f73; white-space:nowrap;">Uzupełnienie:</div>
        <div class="master-progress-bar">
            <div class="master-progress-fill" id="master-prog-fill" style="width:0%"></div>
        </div>
        <div id="master-prog-text" style="font-size:12px; font-weight:700; color:#1d4f73; white-space:nowrap; font-family:monospace;">0%</div>
        <div class="master-save-status" id="master-save-status">Zmiany zapisują się automatycznie</div>
    </div>

    {{-- Section navigation tabs --}}
    <div class="master-nav" id="master-nav" style="margin-top:12px;">
        <button class="master-nav-btn active" data-section="e0">E0 <span class="nav-count" id="nc-e0">0/20</span></button>
        <button class="master-nav-btn" data-section="e1">E1 Zakres <span class="nav-count" id="nc-e1">0/9</span></button>
        <button class="master-nav-btn" data-section="e2">E2 Zakład <span class="nav-count" id="nc-e2">0/12</span></button>
        <button class="master-nav-btn" data-section="e3">E3 Procesy <span class="nav-count" id="nc-e3">0/10</span></button>
        <button class="master-nav-btn" data-section="e4">E4 Wydziały <span class="nav-count" id="nc-e4">0/—</span></button>
        <button class="master-nav-btn" data-section="e5">E5 Hale <span class="nav-count" id="nc-e5">0/—</span></button>
        <button class="master-nav-btn" data-section="e6">E6 Macierz <span class="nav-count" id="nc-e6">0/—</span></button>
        <button class="master-nav-btn" data-section="e7">E7 Nośniki <span class="nav-count" id="nc-e7">0/35</span></button>
        <button class="master-nav-btn" data-section="e8">E8 Zużycia <span class="nav-count" id="nc-e8">0/—</span></button>
        <button class="master-nav-btn" data-section="e9">E9 Zmienne <span class="nav-count" id="nc-e9">0/10</span></button>
        <button class="master-nav-btn" data-section="e10">E10 EnMS <span class="nav-count" id="nc-e10">0/8</span></button>
    </div>

    {{-- ================================================================ --}}
    {{-- ETAP 0 · AUDYT — metadane                                        --}}
    {{-- ================================================================ --}}
    <div class="master-section active" id="sec-e0">
        <div class="master-section-head">
            <div>
                <div class="master-section-eyebrow">ETAP 0</div>
                <h2 class="master-section-title">Audyt — metadane</h2>
                <p class="master-section-desc">Dane formalne firmy, zleceniodawca, audytor, zespół, parametry audytu · 20 pól · ok. 5-10 min · zgodność z PN-EN 16247-1 § 5.4</p>
            </div>
            <div class="master-section-badge" id="sb-e0">0 / 20</div>
        </div>

        <div class="master-group">
            <div class="master-group-title">Identyfikacja klienta</div>
            <div class="master-group-desc">Dane formalne firmy klienta — z KRS / wpisu CEIDG / faktury</div>
            @php $fields_e0_id = [
                ['AUD-V1-NAZWA', 'Pełna nazwa firmy klienta', 'text', 'np. Volkswagen Poznań Sp. z o.o.', 'Z KRS/CEIDG. Pełna forma prawna.', '—', 'em'],
                ['AUD-V2-NIP', 'NIP', 'text', '10 cyfr', '10 cyfr, np. 7000000613', '—', 'em'],
                ['AUD-V3-REGON', 'REGON', 'text', '9 lub 14 cyfr', '9 lub 14 cyfr', '—', 'em'],
                ['AUD-V4-ADRES', 'Adres siedziby', 'text', 'ul. Warszawska 100, 61-058 Poznań', 'Adres rejestracyjny — może różnić się od lokalizacji audytowanej', '—', 'em'],
                ['AUD-V5-PKD', 'Główny kod PKD/NACE', 'text', 'np. 29.10.E (Produkcja samochodów)', 'Z CEIDG/GUS. Kod 5-cyfrowy z PKD 2007.', '—', 'em'],
            ]; @endphp
            @foreach($fields_e0_id as [$fid, $label, $type, $ph, $hint, $unit, $who])
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">{{ $label }}</div><div class="mf-id">{{ $fid }}</div></div>
                <div class="mf-wrap">
                    <input type="{{ $type }}" class="mf-input {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}" value="{{ $formData[$fid] ?? '' }}">
                    <div class="mf-hint">{{ $hint }}</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-{{ $who }}">{{ strtoupper($who) }}</span><br>{{ $unit }}</div>
            </div>
            @endforeach
            {{-- Wielkość przedsiębiorstwa --}}
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">Wielkość przedsiębiorstwa</div><div class="mf-id">AUD-V6-WIELKOSC</div></div>
                <div class="mf-wrap">
                    <select class="mf-select {{ isset($formData['AUD-V6-WIELKOSC']) && $formData['AUD-V6-WIELKOSC'] !== '' ? 'mf-filled' : '' }}" data-id="AUD-V6-WIELKOSC">
                        <option value="">— wybierz —</option>
                        @foreach(['mikro (do 10 osób)','małe (10-50)','średnie (50-250)','DUŻE (>250 osób LUB >50 mln EUR)','nie wiem'] as $opt)
                            <option @selected(($formData['AUD-V6-WIELKOSC'] ?? '') === $opt)>{{ $opt }}</option>
                        @endforeach
                    </select>
                    <div class="mf-hint">Tylko duże mają obowiązek audytu wg Ustawy o EE 2016</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-em">EM</span></div>
            </div>
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">Roczne zużycie energii</div><div class="mf-id">AUD-V7-ZUZYCIE</div></div>
                <div class="mf-wrap">
                    <input type="number" class="mf-input {{ isset($formData['AUD-V7-ZUZYCIE']) && $formData['AUD-V7-ZUZYCIE'] !== '' ? 'mf-filled' : '' }}" data-id="AUD-V7-ZUZYCIE" placeholder="np. 45.7" step="0.1" value="{{ $formData['AUD-V7-ZUZYCIE'] ?? '' }}">
                    <div class="mf-hint">Σ wszystkich nośników. Próg: &gt;10 TJ = audyt obowiązkowy (EED 2023)</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-em">EM</span><br>TJ/rok</div>
            </div>
        </div>

        <div class="master-group">
            <div class="master-group-title">Parametry audytu</div>
            <div class="master-group-desc">Cel, norma referencyjna, daty, kontrakt</div>
            @php $fields_param = [
                ['AUD-V14-CEL', 'Cel audytu', 'select', ['Compliance Ustawa o EE 2016','Compliance Dyrektywa EED 2023','Wsparcie ISO 50001','Dotacja: Biały certyfikat','Dotacja: FENG/FE','Dobrowolny','Inne'], 'Norma + powód audytu — wpływa na zakres raportu', 'kon'],
                ['AUD-V15-NORMA', 'Norma referencyjna', 'text', 'np. PN-EN 16247-1 + ISO 50001:2018', 'PN-EN 16247-1 (ogólna) + ISO 50001:2018 (jeśli wsparcie EnMS)', 'kon'],
                ['AUD-V18-OKRES', 'Okres bilansowy audytu', 'text', 'np. 1.01.2024 - 31.12.2024', 'Typowo poprzedni pełny rok kalendarzowy lub ostatnie 12 mies.', 'kon'],
            ]; @endphp
            @foreach($fields_param as [$fid, $label, $type, $phOrOpts, $hint, $who])
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">{{ $label }}</div><div class="mf-id">{{ $fid }}</div></div>
                <div class="mf-wrap">
                    @if($type === 'select')
                        <select class="mf-select {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}">
                            <option value="">— wybierz —</option>
                            @foreach($phOrOpts as $opt)
                                <option @selected(($formData[$fid] ?? '') === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" class="mf-input {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $phOrOpts }}" value="{{ $formData[$fid] ?? '' }}">
                    @endif
                    <div class="mf-hint">{{ $hint }}</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-{{ $who }}">{{ strtoupper($who) }}</span></div>
            </div>
            @endforeach
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">Data rozpoczęcia audytu</div><div class="mf-id">AUD-V16-DATA-START</div></div>
                <div class="mf-wrap">
                    <input type="date" class="mf-input {{ isset($formData['AUD-V16-DATA-START']) && $formData['AUD-V16-DATA-START'] !== '' ? 'mf-filled' : '' }}" data-id="AUD-V16-DATA-START" value="{{ $formData['AUD-V16-DATA-START'] ?? '' }}">
                    <div class="mf-hint">Data sesji otwierającej</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-kon">KON</span></div>
            </div>
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">Data planowanego zakończenia</div><div class="mf-id">AUD-V17-DATA-END</div></div>
                <div class="mf-wrap">
                    <input type="date" class="mf-input {{ isset($formData['AUD-V17-DATA-END']) && $formData['AUD-V17-DATA-END'] !== '' ? 'mf-filled' : '' }}" data-id="AUD-V17-DATA-END" value="{{ $formData['AUD-V17-DATA-END'] ?? '' }}">
                    <div class="mf-hint">Termin oddania raportu</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-kon">KON</span></div>
            </div>
        </div>

        <div class="master-group">
            <div class="master-group-title">Zespół audytowy klienta — 5 osób</div>
            <div class="master-group-desc">Lista uczestników po stronie klienta. Główny respondent (TAK) = zwykle Energy Manager.</div>
            <div class="master-table-wrap">
                <table class="master-table" id="team-table">
                    <thead><tr>
                        <th class="th-q">ATRYBUT</th>
                        <th style="min-width:130px">Osoba 1</th>
                        <th style="min-width:130px">Osoba 2</th>
                        <th style="min-width:130px">Osoba 3</th>
                        <th style="min-width:130px">Osoba 4</th>
                        <th style="min-width:130px">Osoba 5</th>
                    </tr></thead>
                    <tbody>
                        @foreach([
                            ['AUD-V13-IMIE','Imię i nazwisko','text',''],
                            ['AUD-V13-STAN','Stanowisko','text',''],
                            ['AUD-V13-DZIAL','Dział / komórka','text',''],
                            ['AUD-V13-MAIL','Email','email',''],
                            ['AUD-V13-TEL','Telefon','tel',''],
                        ] as [$prefix,$label,$itype,$ph])
                        <tr>
                            <td class="td-q"><div class="q-l">{{ $label }}</div><div class="q-id">{{ $prefix }}</div></td>
                            @for($u=1;$u<=5;$u++)
                            <td><input type="{{ $itype }}" class="cell-inp {{ isset($formData[$prefix.'-U'.$u]) && $formData[$prefix.'-U'.$u] !== '' ? 'mf-filled' : '' }}" data-id="{{ $prefix }}-U{{ $u }}" value="{{ $formData[$prefix.'-U'.$u] ?? '' }}" placeholder="{{ $ph }}"></td>
                            @endfor
                        </tr>
                        @endforeach
                        <tr>
                            <td class="td-q"><div class="q-l">Rola w audycie</div><div class="q-id">AUD-V13-ROLA</div></td>
                            @for($u=1;$u<=5;$u++)
                            <td><select class="cell-inp {{ isset($formData['AUD-V13-ROLA-U'.$u]) && $formData['AUD-V13-ROLA-U'.$u] !== '' ? 'mf-filled' : '' }}" data-id="AUD-V13-ROLA-U{{ $u }}">
                                <option value="">—</option>
                                @foreach(['UR','EM','KON','SPEC','KIER','INNE'] as $r)
                                <option @selected(($formData['AUD-V13-ROLA-U'.$u] ?? '') === $r)>{{ $r }}</option>
                                @endforeach
                            </select></td>
                            @endfor
                        </tr>
                        <tr>
                            <td class="td-q"><div class="q-l">Główny respondent</div><div class="q-id">AUD-V13-MAIN</div></td>
                            @for($u=1;$u<=5;$u++)
                            <td><select class="cell-inp {{ isset($formData['AUD-V13-MAIN-U'.$u]) && $formData['AUD-V13-MAIN-U'.$u] !== '' ? 'mf-filled' : '' }}" data-id="AUD-V13-MAIN-U{{ $u }}">
                                <option value="">—</option>
                                @foreach(['TAK','NIE'] as $r)
                                <option @selected(($formData['AUD-V13-MAIN-U'.$u] ?? '') === $r)>{{ $r }}</option>
                                @endforeach
                            </select></td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @include('client.energy-audit-master-nav', ['current' => 'e0', 'prev' => null, 'next' => 'e1'])
    </div>

    {{-- ================================================================ --}}
    {{-- ETAP 1 · ZAKRES i granice                                        --}}
    {{-- ================================================================ --}}
    <div class="master-section" id="sec-e1">
        <div class="master-section-head">
            <div>
                <div class="master-section-eyebrow">ETAP 1</div>
                <h2 class="master-section-title">Zakres i granice bilansowe</h2>
                <p class="master-section-desc">Lokalizacja, lista budynków, wyłączenia, granice fizyczne audytu · 9 pól · ok. 10-15 min · zgodność z PN-EN 16247-1 § 5.4</p>
            </div>
            <div class="master-section-badge" id="sb-e1">0 / 9</div>
        </div>

        <div class="master-group">
            <div class="master-group-title">Lokalizacja audytowana</div>
            @php $fields_e1 = [
                ['ZAK-V1-LOK-NAZWA','Nazwa lokalizacji audytowanej','text','np. Zakład Spawalniczo-Lakierniczy w Tychach','Może być inna niż siedziba (E0). Nazwa identyfikująca obiekt audytowany.','—','em'],
                ['ZAK-V2-LOK-ADRES','Adres lokalizacji audytowanej','text','ul. Główna 12, 43-100 Tychy','Fizyczna lokalizacja zakładu','—','em'],
                ['ZAK-V3-GPS','Współrzędne GPS','text','np. 50.124°N 18.978°E','Opcjonalnie — pomocne dla profili klimatycznych (HDD/CDD)','—','em'],
                ['ZAK-V4-OBIEKTY-N','Liczba budynków/obiektów','number','np. 3','Liczba osobnych budynków na działce','szt','kon'],
                ['ZAK-V5-OBIEKTY-LIST','Lista budynków/obiektów','text','np. Hala G1, Hala G2, Magazyn M1, Biurowiec BU','Lista oddzielona przecinkami','—','em'],
                ['ZAK-V6-POW-CALK','Łączna powierzchnia obiektów','number','np. 25000','Σ powierzchni audytowanych budynków','m²','em'],
            ]; @endphp
            @foreach($fields_e1 as [$fid,$label,$type,$ph,$hint,$unit,$who])
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">{{ $label }}</div><div class="mf-id">{{ $fid }}</div></div>
                <div class="mf-wrap">
                    <input type="{{ $type }}" class="mf-input {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}" value="{{ $formData[$fid] ?? '' }}">
                    <div class="mf-hint">{{ $hint }}</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-{{ $who }}">{{ strtoupper($who) }}</span><br>{{ $unit }}</div>
            </div>
            @endforeach
        </div>
        <div class="master-group">
            <div class="master-group-title">Granice bilansowe</div>
            <div class="master-group-info">
                <strong>Wymóg Ustawy o EE 2016:</strong> audyt MUSI obejmować ≥90% zużycia energii. Reszta wymaga wyjaśnienia.
            </div>
            @foreach([
                ['ZAK-V7-WYLACZENIA','Wyłączenia z audytu','textarea','np. najemcy w hali G2 (5% powierzchni), flota transportowa','Co NIE wchodzi w audyt — i dlaczego','—','kon'],
                ['ZAK-V8-UDZIAL-AUDYT','% udziału audytowanego zakresu w całkowitym zużyciu','number','np. 95','Zgodnie z Ustawą o EE: ≥90%.','%','kon'],
                ['ZAK-V9-MAPA-LINK','Link do mapy / planu zakładu','text','URL lub referencja','Link do dokumentu (Google Drive, SharePoint)','—','em'],
            ] as [$fid,$label,$type,$ph,$hint,$unit,$who])
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">{{ $label }}</div><div class="mf-id">{{ $fid }}</div></div>
                <div class="mf-wrap">
                    @if($type === 'textarea')
                        <textarea class="mf-textarea {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}">{{ $formData[$fid] ?? '' }}</textarea>
                    @else
                        <input type="{{ $type }}" class="mf-input {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}" value="{{ $formData[$fid] ?? '' }}">
                    @endif
                    <div class="mf-hint">{{ $hint }}</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-{{ $who }}">{{ strtoupper($who) }}</span><br>{{ $unit }}</div>
            </div>
            @endforeach
        </div>
        @include('client.energy-audit-master-nav', ['current' => 'e1', 'prev' => 'e0', 'next' => 'e2'])
    </div>

    {{-- ================================================================ --}}
    {{-- ETAP 2 · ZAKŁAD — charakterystyka                                --}}
    {{-- ================================================================ --}}
    <div class="master-section" id="sec-e2">
        <div class="master-section-head">
            <div>
                <div class="master-section-eyebrow">ETAP 2</div>
                <h2 class="master-section-title">Zakład — charakterystyka</h2>
                <p class="master-section-desc">Branża, klimat, BAC, dane statyczne · 12 pól · ok. 15-20 min · ISO 50001 § 6.3 (Static Factors)</p>
            </div>
            <div class="master-section-badge" id="sb-e2">0 / 12</div>
        </div>

        <div class="master-group">
            <div class="master-group-title">Klasyfikacja branżowa</div>
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">Branża wiodąca</div><div class="mf-id">ZAK-V1-BRANZA</div></div>
                <div class="mf-wrap">
                    <select class="mf-select {{ isset($formData['ZAK-V1-BRANZA']) && $formData['ZAK-V1-BRANZA'] !== '' ? 'mf-filled' : '' }}" data-id="ZAK-V1-BRANZA">
                        <option value="">— wybierz —</option>
                        @foreach(['Automotive','Spożywcza','Chemiczna','Metalurgiczna','Drzewno-meblarska','Tekstylna','Tworzywa sztuczne','Elektrotechniczna','Farmaceutyczna','Inna'] as $opt)
                            <option @selected(($formData['ZAK-V1-BRANZA'] ?? '') === $opt)>{{ $opt }}</option>
                        @endforeach
                    </select>
                    <div class="mf-hint">Wpływa na typowe SEU i benchmarki EnPI</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-em">EM</span></div>
            </div>
            @foreach([
                ['ZAK-V2-PODBRANZA','Podbranża / specjalizacja','text','np. produkcja podzespołów / lakiernia kontraktowa','Konkretyzacja branży','—','em'],
                ['ZAK-V3-PRODUKTY-MAIN','Główne produkty / kody PKWiU','textarea','np. nadwozia samochodowe (PKWiU 29.10), drzwi i klapy','2-3 najważniejsze produkty po przychodzie','—','em'],
            ] as [$fid,$label,$type,$ph,$hint,$unit,$who])
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">{{ $label }}</div><div class="mf-id">{{ $fid }}</div></div>
                <div class="mf-wrap">
                    @if($type === 'textarea')
                        <textarea class="mf-textarea {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}">{{ $formData[$fid] ?? '' }}</textarea>
                    @else
                        <input type="text" class="mf-input {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}" value="{{ $formData[$fid] ?? '' }}">
                    @endif
                    <div class="mf-hint">{{ $hint }}</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-{{ $who }}">{{ strtoupper($who) }}</span><br>{{ $unit }}</div>
            </div>
            @endforeach
        </div>

        <div class="master-group">
            <div class="master-group-title">Warunki klimatyczne i lokalizacyjne</div>
            <div class="master-group-info">
                <strong>HDD/CDD</strong> = stopniodni grzewcze/chłodzące. Kluczowe zmienne dla EnPI baseline (E9). Konsultant uzupełnia z danych meteorologicznych.
            </div>
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">Strefa klimatyczna PL</div><div class="mf-id">ZAK-V4-KLIMAT</div></div>
                <div class="mf-wrap">
                    <select class="mf-select {{ isset($formData['ZAK-V4-KLIMAT']) && $formData['ZAK-V4-KLIMAT'] !== '' ? 'mf-filled' : '' }}" data-id="ZAK-V4-KLIMAT">
                        <option value="">— wybierz —</option>
                        @foreach(['I (najcieplejsza, np. Wrocław, Zielona Góra)','II (np. Poznań, Łódź, Warszawa)','III (np. Tychy, Katowice, Lublin)','IV (np. Olsztyn, Białystok)','V (najzimniejsza, np. Suwałki)'] as $opt)
                            <option @selected(($formData['ZAK-V4-KLIMAT'] ?? '') === $opt)>{{ $opt }}</option>
                        @endforeach
                    </select>
                    <div class="mf-hint">Wg PN-EN 12831</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-kon">KON</span></div>
            </div>
            @foreach([
                ['ZAK-V5-HDD','Stopniodni grzewcze (HDD)','number','np. 3500','Baseline 18°C, roczne, z danych meteorologicznych','K·dni/rok','kon'],
                ['ZAK-V6-CDD','Stopniodni chłodzenia (CDD)','number','np. 250','Baseline 18°C','K·dni/rok','kon'],
                ['ZAK-V7-ALTITUDE','Wysokość n.p.m.','number','np. 250','Wpływ na ciśnienie atmosferyczne — istotne dla sprężarek','m','kon'],
            ] as [$fid,$label,$type,$ph,$hint,$unit,$who])
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">{{ $label }}</div><div class="mf-id">{{ $fid }}</div></div>
                <div class="mf-wrap">
                    <input type="{{ $type }}" class="mf-input {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}" value="{{ $formData[$fid] ?? '' }}">
                    <div class="mf-hint">{{ $hint }}</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-{{ $who }}">{{ strtoupper($who) }}</span><br>{{ $unit }}</div>
            </div>
            @endforeach
        </div>

        <div class="master-group">
            <div class="master-group-title">Charakterystyka techniczna budynków</div>
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">Klasa BAC budynków</div><div class="mf-id">ZAK-V8-BAC</div></div>
                <div class="mf-wrap">
                    <select class="mf-select {{ isset($formData['ZAK-V8-BAC']) && $formData['ZAK-V8-BAC'] !== '' ? 'mf-filled' : '' }}" data-id="ZAK-V8-BAC">
                        <option value="">— wybierz —</option>
                        @foreach(['A (top automatyka, BMS pełen)','B (dobra automatyka)','C (standard, podstawowa)','D (brak BMS, sterowanie ręczne)','brak (niesklasyfikowany)','mieszane (per budynek)','nie wiem'] as $opt)
                            <option @selected(($formData['ZAK-V8-BAC'] ?? '') === $opt)>{{ $opt }}</option>
                        @endforeach
                    </select>
                    <div class="mf-hint">Wg PN-EN ISO 52120-1. Klasa D → największy potencjał oszczędności przez automatykę.</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-kon">KON</span></div>
            </div>
            @foreach([
                ['ZAK-V10-WIEK','Średni wiek budynków','number','np. 35','Pomocnicze. Wpływ na izolację, infiltrację, BAC.','lata','em'],
                ['ZAK-V11-POW-PROD','Powierzchnia produkcyjna','number','np. 18000','Łączna powierzchnia produkcyjna (bez biur)','m²','em'],
                ['ZAK-V12-KUBATURA','Łączna kubatura','number','np. 144000','Σ kubatur audytowanych budynków (dla bilansu cieplnego)','m³','em'],
            ] as [$fid,$label,$type,$ph,$hint,$unit,$who])
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">{{ $label }}</div><div class="mf-id">{{ $fid }}</div></div>
                <div class="mf-wrap">
                    <input type="{{ $type }}" class="mf-input {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}" value="{{ $formData[$fid] ?? '' }}">
                    <div class="mf-hint">{{ $hint }}</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-{{ $who }}">{{ strtoupper($who) }}</span><br>{{ $unit }}</div>
            </div>
            @endforeach
        </div>
        @include('client.energy-audit-master-nav', ['current' => 'e2', 'prev' => 'e1', 'next' => 'e3'])
    </div>

    {{-- ================================================================ --}}
    {{-- ETAP 3 · PROCESY produkcyjne                                     --}}
    {{-- ================================================================ --}}
    <div class="master-section" id="sec-e3">
        <div class="master-section-head">
            <div>
                <div class="master-section-eyebrow">ETAP 3</div>
                <h2 class="master-section-title">Procesy produkcyjne</h2>
                <p class="master-section-desc">Narracja procesu, asortyment, profile produkcji · 10 pól · ok. 30 min · PN-EN 16247-3 (Procesy)</p>
            </div>
            <div class="master-section-badge" id="sb-e3">0 / 10</div>
        </div>

        <div class="master-group">
            <div class="master-group-title">Opis procesu produkcyjnego</div>
            @foreach([
                ['PRO-V1-NARRACJA','Opis procesu produkcyjnego','textarea','np. 1. Press shop (tłoczenie blachy). 2. Spawalnia (zgrzewanie nadwozia). 3. Lakiernia...','Tekst 200-500 słów. Sekwencja operacji od materiału wejściowego do produktu finalnego.','—','em'],
                ['PRO-V2-PROCES-DIAGRAM','Link do diagramu procesu','text','URL lub referencja do dokumentów','Schemat blokowy / diagram przepływu (jeśli istnieje)','—','em'],
            ] as [$fid,$label,$type,$ph,$hint,$unit,$who])
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">{{ $label }}</div><div class="mf-id">{{ $fid }}</div></div>
                <div class="mf-wrap">
                    @if($type==='textarea')
                        <textarea class="mf-textarea {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}" style="min-height:100px">{{ $formData[$fid] ?? '' }}</textarea>
                    @else
                        <input type="text" class="mf-input {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}" value="{{ $formData[$fid] ?? '' }}">
                    @endif
                    <div class="mf-hint">{{ $hint }}</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-{{ $who }}">{{ strtoupper($who) }}</span><br>{{ $unit }}</div>
            </div>
            @endforeach
        </div>
        <div class="master-group">
            <div class="master-group-title">Asortyment i wielkość produkcji</div>
            <div class="master-group-info">
                <strong>Wielkość produkcji</strong> (PRO-V4) jest <strong>kluczowa</strong> — to mianownik dla większości EnPI (np. kWh/szt nadwozia, kWh/tonę produktu).
            </div>
            @foreach([
                ['PRO-V3-ASORTYMENT','Liczba SKU / wariantów produktu','number','np. 12','Pomocnicze. Wpływ na zmienność produkcji i EnPI.','szt','em'],
                ['PRO-V4-PRODUKCJA-ROK','Wielkość produkcji rocznej','number','np. 50000','KLUCZOWE — to mianownik dla EnPI. np. 50000 szt nadwozi/rok','jedn/rok','em'],
                ['PRO-V6-WARTOSC-ROK','Wartość produkcji rocznej (przychód)','number','opcjonalnie','Opcjonalnie. Wpływ na EnPI w kosztach.','PLN/rok','em'],
            ] as [$fid,$label,$type,$ph,$hint,$unit,$who])
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">{{ $label }}</div><div class="mf-id">{{ $fid }}</div></div>
                <div class="mf-wrap">
                    <input type="{{ $type }}" class="mf-input {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}" value="{{ $formData[$fid] ?? '' }}">
                    <div class="mf-hint">{{ $hint }}</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-{{ $who }}">{{ strtoupper($who) }}</span><br>{{ $unit }}</div>
            </div>
            @endforeach
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">Jednostka produkcji</div><div class="mf-id">PRO-V5-PRODUKCJA-JM</div></div>
                <div class="mf-wrap">
                    <select class="mf-select {{ isset($formData['PRO-V5-PRODUKCJA-JM']) && $formData['PRO-V5-PRODUKCJA-JM'] !== '' ? 'mf-filled' : '' }}" data-id="PRO-V5-PRODUKCJA-JM">
                        <option value="">— wybierz —</option>
                        @foreach(['sztuki','tony','m³','m²','litry','kg','inne'] as $opt)
                            <option @selected(($formData['PRO-V5-PRODUKCJA-JM'] ?? '') === $opt)>{{ $opt }}</option>
                        @endforeach
                    </select>
                    <div class="mf-hint">Jednostka miary dla pola PRO-V4</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-em">EM</span></div>
            </div>
        </div>
        <div class="master-group">
            <div class="master-group-title">Profile produkcji</div>
            @foreach([
                ['PRO-V7-PROFIL-MIES','Profil produkcji miesięczny','textarea','np. bez sezonowości / sezon V-IX 70% / minimum styczeń-luty','Sezonowość. Tabela 12 mies. lub opis tekstowy.','—','em'],
                ['PRO-V9-DNI-ROK','Liczba dni pracy w roku','number','np. 250','np. 250 dni (5×52-urlopy) / 365 dni (24/7) / 200 dni (sezonowy)','dni/rok','em'],
                ['PRO-V10-PRZESTOJE','Planowane przestoje','textarea','np. lipiec — przerwa urlopowa 2 tygodnie, święta','Wpływ na profil zużycia w E8.','—','em'],
            ] as [$fid,$label,$type,$ph,$hint,$unit,$who])
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">{{ $label }}</div><div class="mf-id">{{ $fid }}</div></div>
                <div class="mf-wrap">
                    @if($type==='textarea')
                        <textarea class="mf-textarea {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}">{{ $formData[$fid] ?? '' }}</textarea>
                    @else
                        <input type="{{ $type }}" class="mf-input {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}" value="{{ $formData[$fid] ?? '' }}">
                    @endif
                    <div class="mf-hint">{{ $hint }}</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-{{ $who }}">{{ strtoupper($who) }}</span><br>{{ $unit }}</div>
            </div>
            @endforeach
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">Tryb pracy zakładu</div><div class="mf-id">PRO-V8-TRYB-PRACY</div></div>
                <div class="mf-wrap">
                    <select class="mf-select {{ isset($formData['PRO-V8-TRYB-PRACY']) && $formData['PRO-V8-TRYB-PRACY'] !== '' ? 'mf-filled' : '' }}" data-id="PRO-V8-TRYB-PRACY">
                        <option value="">— wybierz —</option>
                        @foreach(['1 zmiana','2 zmiany','3 zmiany','24-7','sezonowy','mieszany (per wydział)'] as $opt)
                            <option @selected(($formData['PRO-V8-TRYB-PRACY'] ?? '') === $opt)>{{ $opt }}</option>
                        @endforeach
                    </select>
                    <div class="mf-hint">Per wydział tryb pracy zostanie doprecyzowany w E4</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-em">EM</span></div>
            </div>
        </div>
        @include('client.energy-audit-master-nav', ['current' => 'e3', 'prev' => 'e2', 'next' => 'e4'])
    </div>

    {{-- ================================================================ --}}
    {{-- ETAP 4 · WYDZIAŁY                                                --}}
    {{-- ================================================================ --}}
    <div class="master-section" id="sec-e4">
        <div class="master-section-head">
            <div>
                <div class="master-section-eyebrow">ETAP 4</div>
                <h2 class="master-section-title">Wydziały — SEU candidates</h2>
                <p class="master-section-desc">Lista wydziałów produkcyjnych z klasyfikacją SEU · 10 pól × N wydziałów · ISO 50001 § 6.3 (Significant Energy Uses)</p>
            </div>
            <div class="master-section-badge" id="sb-e4">0 / —</div>
        </div>
        <div class="master-group-info" style="margin-bottom:14px;">
            <strong>Jak to działa:</strong> Każda <strong>kolumna</strong> = jeden wydział. Każdy <strong>wiersz</strong> = jedno pytanie. Typowy zakład ma 5-7 wydziałów.
        </div>
        <div class="master-table-wrap">
            <table class="master-table" id="wydzialy-table">
                <thead><tr>
                    <th class="th-q">Pytanie</th>
                    <th style="min-width:130px">Wydział 1</th>
                    <th style="min-width:130px">Wydział 2</th>
                    <th style="min-width:130px">Wydział 3</th>
                    <th style="min-width:130px">Wydział 4</th>
                    <th style="min-width:130px">Wydział 5</th>
                </tr></thead>
                <tbody>
                    <tr class="sec-header"><td colspan="6">▼ Identyfikacja wydziału</td></tr>
                    <tr>
                        <td class="td-q"><div class="q-l">Nazwa wydziału</div><div class="q-id">WYD-V2-NAZWA</div><div class="q-h">np. Spawalnia A, Press shop, Utilities</div></td>
                        @for($w=1;$w<=5;$w++)
                        <td><input type="text" class="cell-inp wydz-name-input {{ isset($formData['WYD-V2-NAZWA-W'.$w]) && $formData['WYD-V2-NAZWA-W'.$w] !== '' ? 'mf-filled' : '' }}" data-id="WYD-V2-NAZWA-W{{ $w }}" data-wydz-idx="{{ $w }}" placeholder="{{ ['Press shop','Spawalnia','Lakiernia','Montaż','Utilities'][$w-1] }}" value="{{ $formData['WYD-V2-NAZWA-W'.$w] ?? '' }}"></td>
                        @endfor
                    </tr>
                    <tr>
                        <td class="td-q"><div class="q-l">Kierownik / kontakt</div><div class="q-id">WYD-V3-KIEROWNIK</div></td>
                        @for($w=1;$w<=5;$w++)
                        <td><input type="text" class="cell-inp {{ isset($formData['WYD-V3-KIEROWNIK-W'.$w]) && $formData['WYD-V3-KIEROWNIK-W'.$w] !== '' ? 'mf-filled' : '' }}" data-id="WYD-V3-KIEROWNIK-W{{ $w }}" value="{{ $formData['WYD-V3-KIEROWNIK-W'.$w] ?? '' }}"></td>
                        @endfor
                    </tr>
                    <tr class="sec-header"><td colspan="6">▼ Charakterystyka wydziału</td></tr>
                    <tr>
                        <td class="td-q"><div class="q-l">Liczba pracowników [osób]</div><div class="q-id">WYD-V4-ZATRUDNIENIE</div></td>
                        @for($w=1;$w<=5;$w++)
                        <td><input type="number" class="cell-inp {{ isset($formData['WYD-V4-ZATRUDNIENIE-W'.$w]) && $formData['WYD-V4-ZATRUDNIENIE-W'.$w] !== '' ? 'mf-filled' : '' }}" data-id="WYD-V4-ZATRUDNIENIE-W{{ $w }}" value="{{ $formData['WYD-V4-ZATRUDNIENIE-W'.$w] ?? '' }}"></td>
                        @endfor
                    </tr>
                    <tr>
                        <td class="td-q"><div class="q-l">Powierzchnia wydziału [m²]</div><div class="q-id">WYD-V5-POW</div></td>
                        @for($w=1;$w<=5;$w++)
                        <td><input type="number" class="cell-inp {{ isset($formData['WYD-V5-POW-W'.$w]) && $formData['WYD-V5-POW-W'.$w] !== '' ? 'mf-filled' : '' }}" data-id="WYD-V5-POW-W{{ $w }}" value="{{ $formData['WYD-V5-POW-W'.$w] ?? '' }}"></td>
                        @endfor
                    </tr>
                    <tr class="sec-header"><td colspan="6">▼ Klasyfikacja SEU (ISO 50001)</td></tr>
                    <tr>
                        <td class="td-q"><div class="q-l">Klasa SEU</div><div class="q-id">WYD-V7-SEU</div><div class="q-h">PRIMARY (&gt;20%) / SECONDARY (5-20%) / SMALL (&lt;5%)</div></td>
                        @for($w=1;$w<=5;$w++)
                        <td><select class="cell-inp {{ isset($formData['WYD-V7-SEU-W'.$w]) && $formData['WYD-V7-SEU-W'.$w] !== '' ? 'mf-filled' : '' }}" data-id="WYD-V7-SEU-W{{ $w }}">
                            <option value="">—</option>
                            @foreach(['PRIMARY (>20%)','SECONDARY (5-20%)','SMALL (<5%)','do oceny po E8'] as $opt)
                                <option @selected(($formData['WYD-V7-SEU-W'.$w] ?? '') === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select></td>
                        @endfor
                    </tr>
                    <tr>
                        <td class="td-q"><div class="q-l">Komentarz / specyfika</div><div class="q-id">WYD-V10-KOMENTARZ</div></td>
                        @for($w=1;$w<=5;$w++)
                        <td><input type="text" class="cell-inp {{ isset($formData['WYD-V10-KOMENTARZ-W'.$w]) && $formData['WYD-V10-KOMENTARZ-W'.$w] !== '' ? 'mf-filled' : '' }}" data-id="WYD-V10-KOMENTARZ-W{{ $w }}" value="{{ $formData['WYD-V10-KOMENTARZ-W'.$w] ?? '' }}"></td>
                        @endfor
                    </tr>
                </tbody>
            </table>
        </div>
        <button class="row-add-btn" type="button" id="add-wydz-btn">+ Dodaj kolejny wydział</button>
        @include('client.energy-audit-master-nav', ['current' => 'e4', 'prev' => 'e3', 'next' => 'e5'])
    </div>

    {{-- ================================================================ --}}
    {{-- ETAP 5 · HALE                                                    --}}
    {{-- ================================================================ --}}
    <div class="master-section" id="sec-e5">
        <div class="master-section-head">
            <div>
                <div class="master-section-eyebrow">ETAP 5</div>
                <h2 class="master-section-title">Hale — lokalizacje fizyczne</h2>
                <p class="master-section-desc">Lista hal z parametrami fizycznymi · 10 pól × N hal · PN-EN 16247-2 (Budynki)</p>
            </div>
            <div class="master-section-badge" id="sb-e5">0 / —</div>
        </div>
        <div class="master-group-info" style="margin-bottom:14px;">
            <strong>Hala vs Wydział:</strong> Hala = lokalizacja fizyczna. Wydział = funkcja (proces). Jedna hala może obsługiwać wiele wydziałów. Mapowanie w E6.
        </div>
        <div class="master-table-wrap">
            <table class="master-table" id="hale-table">
                <thead><tr>
                    <th class="th-q">Pytanie</th>
                    <th style="min-width:130px">Hala 1</th>
                    <th style="min-width:130px">Hala 2</th>
                    <th style="min-width:130px">Hala 3</th>
                    <th style="min-width:130px">Hala 4</th>
                    <th style="min-width:130px">Hala 5</th>
                </tr></thead>
                <tbody>
                    <tr class="sec-header"><td colspan="6">▼ Identyfikacja hali</td></tr>
                    <tr>
                        <td class="td-q"><div class="q-l">Nazwa robocza hali</div><div class="q-id">HAL-V2-NAZWA</div></td>
                        @for($h=1;$h<=5;$h++)
                        <td><input type="text" class="cell-inp hal-name-input {{ isset($formData['HAL-V2-NAZWA-H'.$h]) && $formData['HAL-V2-NAZWA-H'.$h] !== '' ? 'mf-filled' : '' }}" data-id="HAL-V2-NAZWA-H{{ $h }}" placeholder="{{ ['G1','G2','Magazyn M1','Biurowiec','Hala pomocnicza'][$h-1] }}" value="{{ $formData['HAL-V2-NAZWA-H'.$h] ?? '' }}"></td>
                        @endfor
                    </tr>
                    <tr>
                        <td class="td-q"><div class="q-l">Typ hali</div><div class="q-id">HAL-V3-TYP</div></td>
                        @for($h=1;$h<=5;$h++)
                        <td><select class="cell-inp {{ isset($formData['HAL-V3-TYP-H'.$h]) && $formData['HAL-V3-TYP-H'.$h] !== '' ? 'mf-filled' : '' }}" data-id="HAL-V3-TYP-H{{ $h }}">
                            <option value="">—</option>
                            @foreach(['Produkcja','Magazyn','Biuro','Mieszany','Inny'] as $opt)
                                <option @selected(($formData['HAL-V3-TYP-H'.$h] ?? '') === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select></td>
                        @endfor
                    </tr>
                    <tr class="sec-header"><td colspan="6">▼ Parametry fizyczne</td></tr>
                    @foreach([
                        ['HAL-V4-POW','Powierzchnia hali [m²]','number'],
                        ['HAL-V5-KUB','Kubatura hali [m³]','number'],
                        ['HAL-V6-WYS','Wysokość hali (średnia) [m]','number'],
                        ['HAL-V7-BRAMY','Liczba bram zewnętrznych','number'],
                    ] as [$pref,$label,$type])
                    <tr>
                        <td class="td-q"><div class="q-l">{{ $label }}</div><div class="q-id">{{ $pref }}</div></td>
                        @for($h=1;$h<=5;$h++)
                        <td><input type="{{ $type }}" class="cell-inp {{ isset($formData[$pref.'-H'.$h]) && $formData[$pref.'-H'.$h] !== '' ? 'mf-filled' : '' }}" data-id="{{ $pref }}-H{{ $h }}" value="{{ $formData[$pref.'-H'.$h] ?? '' }}"></td>
                        @endfor
                    </tr>
                    @endforeach
                    <tr class="sec-header"><td colspan="6">▼ Charakterystyka techniczna</td></tr>
                    <tr>
                        <td class="td-q"><div class="q-l">Jakość izolacji</div><div class="q-id">HAL-V8-IZOLACJA</div></td>
                        @for($h=1;$h<=5;$h++)
                        <td><select class="cell-inp {{ isset($formData['HAL-V8-IZOLACJA-H'.$h]) && $formData['HAL-V8-IZOLACJA-H'.$h] !== '' ? 'mf-filled' : '' }}" data-id="HAL-V8-IZOLACJA-H{{ $h }}">
                            <option value="">—</option>
                            @foreach(['dobra','średnia','słaba','brak','nie wiem'] as $opt)
                                <option @selected(($formData['HAL-V8-IZOLACJA-H'.$h] ?? '') === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select></td>
                        @endfor
                    </tr>
                    <tr>
                        <td class="td-q"><div class="q-l">Rok budowy / modernizacji</div><div class="q-id">HAL-V9-WIEK</div></td>
                        @for($h=1;$h<=5;$h++)
                        <td><input type="text" class="cell-inp {{ isset($formData['HAL-V9-WIEK-H'.$h]) && $formData['HAL-V9-WIEK-H'.$h] !== '' ? 'mf-filled' : '' }}" data-id="HAL-V9-WIEK-H{{ $h }}" value="{{ $formData['HAL-V9-WIEK-H'.$h] ?? '' }}"></td>
                        @endfor
                    </tr>
                </tbody>
            </table>
        </div>
        <button class="row-add-btn" type="button" id="add-hal-btn">+ Dodaj kolejną halę</button>
        @include('client.energy-audit-master-nav', ['current' => 'e5', 'prev' => 'e4', 'next' => 'e6'])
    </div>

    {{-- ================================================================ --}}
    {{-- ETAP 6 · MACIERZ Hala × Wydział                                 --}}
    {{-- ================================================================ --}}
    <div class="master-section" id="sec-e6">
        <div class="master-section-head">
            <div>
                <div class="master-section-eyebrow">ETAP 6</div>
                <h2 class="master-section-title">Macierz Hala × Wydział — alokacja %</h2>
                <p class="master-section-desc">Każda hala alokowana w 100% między wydziały · WIERSZE = hale, KOLUMNY = wydziały · NOWE — wymóg EnPI per SEU</p>
            </div>
            <div class="master-section-badge" id="sb-e6">0 / —</div>
        </div>
        <div class="master-group-info" style="margin-bottom:14px;">
            <strong>Jak działa macierz alokacji:</strong> Każda hala (z E5) musi być alokowana w sumie <strong>100%</strong> między wydziały (z E4).
            Przykład: <em>Hala G1 = 60% Spawalnia + 30% Magazyn + 10% Komunikacja</em>.
            Status wiersza: <span style="color:#16a34a;font-weight:700">OK ✓</span> gdy 100%, <span style="color:#dc2626;font-weight:700">⚠</span> przy odchyłce.
        </div>
        <div class="master-table-wrap" id="macierz-wrap">
            <table class="master-table" id="macierz-table">
                <thead><tr id="macierz-header-row">
                    <th class="th-q">↓ Hale / Wydziały →</th>
                </tr></thead>
                <tbody id="macierz-body"></tbody>
                <tfoot id="macierz-foot"></tfoot>
            </table>
        </div>
        <p style="font-size:11px;color:#8aa3b5;font-style:italic;margin-top:8px;">★ Macierz odbudowuje się automatycznie po wpisaniu nazw hal (E5) i wydziałów (E4).</p>
        @include('client.energy-audit-master-nav', ['current' => 'e6', 'prev' => 'e5', 'next' => 'e7'])
    </div>

    {{-- ================================================================ --}}
    {{-- ETAP 7 · NOŚNIKI energii                                         --}}
    {{-- ================================================================ --}}
    <div class="master-section" id="sec-e7">
        <div class="master-section-head">
            <div>
                <div class="master-section-eyebrow">ETAP 7</div>
                <h2 class="master-section-title">Nośniki energii — taryfy i ceny</h2>
                <p class="master-section-desc">8 nośników × ~7 atrybutów + PV/kogeneracja · 35 pól · ok. 30-45 min · PN-EN 16247-1 § 5.5 + ISO 50001 § 6.3</p>
            </div>
            <div class="master-section-badge" id="sb-e7">0 / 35</div>
        </div>
        <div class="master-group-info" style="margin-bottom:14px;">
            <strong>Źródła danych:</strong> Faktury klienta (12 mies.) — ceny, taryfy, opłaty stałe. Pomijamy nośniki nieużywane.
        </div>

        @php $nosniki = [
            ['EE','Energia elektryczna','PLN/MWh'],
            ['GAZ','Gaz ziemny','PLN/m³'],
            ['CIEPLO','Ciepło sieciowe','PLN/GJ'],
            ['OLEJ','Olej opałowy','PLN/l'],
            ['LPG','Gaz LPG','PLN/kg'],
        ]; @endphp
        @foreach($nosniki as [$nos,$label,$unit])
        <div class="master-group">
            <div class="master-group-title">Nośnik — {{ $label }}</div>
            @foreach([
                ["NOS-{$nos}-DOSTAWCA","Dostawca",'text','—','Z faktury','—','em'],
                ["NOS-{$nos}-CENA-NETTO","Cena netto",'number','—',"Z faktury",$unit,'kon'],
            ] as [$fid,$fl,$ft,$fph,$fhint,$funit,$fwho])
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">{{ $fl }}</div><div class="mf-id">{{ $fid }}</div></div>
                <div class="mf-wrap">
                    <input type="{{ $ft }}" class="mf-input {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $fph }}" value="{{ $formData[$fid] ?? '' }}">
                    <div class="mf-hint">{{ $fhint }}</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-{{ $fwho }}">{{ strtoupper($fwho) }}</span><br>{{ $funit }}</div>
            </div>
            @endforeach
        </div>
        @endforeach

        <div class="master-group">
            <div class="master-group-title">Własna produkcja energii (PV / kogeneracja)</div>
            @foreach([
                ['NOS-PV-MOC','Moc instalacji PV','number','0 jeśli brak','kWp','em'],
                ['NOS-PV-PROD-ROK','Produkcja PV rocznie','number','0 jeśli brak','MWh/rok','em'],
                ['NOS-KOGEN-MOC','Moc kogeneracji','number','0 jeśli brak','kW','em'],
            ] as [$fid,$label,$type,$ph,$unit,$who])
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">{{ $label }}</div><div class="mf-id">{{ $fid }}</div></div>
                <div class="mf-wrap">
                    <input type="{{ $type }}" class="mf-input {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}" value="{{ $formData[$fid] ?? '' }}">
                </div>
                <div class="mf-unit"><span class="mtag mtag-{{ $who }}">{{ strtoupper($who) }}</span><br>{{ $unit }}</div>
            </div>
            @endforeach
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">Odzysk ciepła odpadowego</div><div class="mf-id">NOS-ODZYSK</div></div>
                <div class="mf-wrap">
                    <select class="mf-select {{ isset($formData['NOS-ODZYSK']) && $formData['NOS-ODZYSK'] !== '' ? 'mf-filled' : '' }}" data-id="NOS-ODZYSK">
                        <option value="">— wybierz —</option>
                        @foreach(['TAK','NIE','planowany','częściowo','nie wiem'] as $opt)
                            <option @selected(($formData['NOS-ODZYSK'] ?? '') === $opt)>{{ $opt }}</option>
                        @endforeach
                    </select>
                    <div class="mf-hint">Czy klient odzyskuje ciepło ze spalin / sprężarek / chłodzenia?</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-em">EM</span></div>
            </div>
        </div>
        @include('client.energy-audit-master-nav', ['current' => 'e7', 'prev' => 'e6', 'next' => 'e8'])
    </div>

    {{-- ================================================================ --}}
    {{-- ETAP 8 · ZUŻYCIA — tabela 36 mies × 9 nośników                  --}}
    {{-- ================================================================ --}}
    <div class="master-section" id="sec-e8">
        <div class="master-section-head">
            <div>
                <div class="master-section-eyebrow">ETAP 8</div>
                <h2 class="master-section-title">Zużycia roczne z faktur</h2>
                <p class="master-section-desc">36 miesięcy × 9 nośników z auto-konwersją na MWh · ISO 50001 § 6.3 (Historical Energy Data)</p>
            </div>
            <div class="master-section-badge" id="sb-e8">0 / —</div>
        </div>
        <div class="master-group-info" style="margin-bottom:14px;">
            Wpisz wartości z faktur za 12-36 miesięcy. Suma MWh kalkuluje się automatycznie.
            Konwersje: EE×1 · Gaz×0.0097 · Ciepło×0.278 · Olej×0.0105 · LPG×0.0128
        </div>
        <div class="master-table-wrap" style="max-height:520px; overflow-y:auto;">
            <table class="master-table" id="zuzycia-table">
                <thead><tr>
                    <th class="th-q" style="min-width:90px">Miesiąc</th>
                    <th style="min-width:65px">Rok</th>
                    <th style="min-width:80px">Energia el.<br>[MWh]</th>
                    <th style="min-width:75px">Gaz<br>[m³]</th>
                    <th style="min-width:75px">Ciepło<br>[GJ]</th>
                    <th style="min-width:70px">Olej<br>[l]</th>
                    <th style="min-width:65px">LPG<br>[kg]</th>
                    <th style="min-width:65px">Para<br>[t]</th>
                    <th style="min-width:70px">PV<br>[MWh]</th>
                    <th style="min-width:80px; background:#d97706">Σ [MWh]</th>
                </tr></thead>
                <tbody id="zuzycia-body"></tbody>
                <tfoot id="zuzycia-foot"></tfoot>
            </table>
        </div>
        @include('client.energy-audit-master-nav', ['current' => 'e8', 'prev' => 'e7', 'next' => 'e9'])
    </div>

    {{-- ================================================================ --}}
    {{-- ETAP 9 · ZMIENNE ISTOTNE                                         --}}
    {{-- ================================================================ --}}
    <div class="master-section" id="sec-e9">
        <div class="master-section-head">
            <div>
                <div class="master-section-eyebrow">ETAP 9</div>
                <h2 class="master-section-title">Zmienne istotne</h2>
                <p class="master-section-desc">HDD/CDD, produkcja, zatrudnienie · 10 pól · ISO 50001 § 6.4 (Relevant Variables)</p>
            </div>
            <div class="master-section-badge" id="sb-e9">0 / 10</div>
        </div>
        <div class="master-group">
            <div class="master-group-title">Zmienne klimatyczne i produkcyjne</div>
            @foreach([
                ['ZMI-V1-HDD','Stopniodni grzewcze (HDD) — historia 36 mies.','textarea','2022: 3450, 2023: 3380, 2024: 3520 K·dni','Tabela miesięczna z stacji meteo','K·dni','kon'],
                ['ZMI-V2-CDD','Stopniodni chłodzenia (CDD) — historia 36 mies.','textarea','2022: 220, 2023: 280, 2024: 250 K·dni','—','K·dni','kon'],
                ['ZMI-V4-PRODUKCJA-MIES','Wielkość produkcji miesięczna (12-36 mies.)','textarea','Tabela miesięczna lub link do CSV','KLUCZOWE dla EnPI baseline','jedn./mies','em'],
                ['ZMI-V5-ASORTYMENT-MIX','Mix asortymentowy (% udział głównych SKU)','textarea','np. Produkt A 60%, B 30%, C 10%','Czy zmienność produktów wpływa na zużycie?','%','em'],
                ['ZMI-V6-WYDAJNOSC','Wydajność / efektywność procesu','text','np. OEE 78%, braki 2.3%','Wpływ na energię/jednostkę','%','em'],
                ['ZMI-V7-ZATRUDNIENIE','Zatrudnienie (etaty) — średnia miesięczna','number','np. 850','Wpływ na zyski wewnętrzne, oświetlenie','osób','em'],
                ['ZMI-V8-GODZINY-PRACY','Godziny pracy zakładu (/mies)','number','np. 480','Σ godzin pracy zmianowej × zmiany × dni roboczych','h/mies','em'],
                ['ZMI-V9-KORELACJA','Korelacja zużycia ze zmienną — R²','text','np. EE-Produkcja: R²=0.84','Współczynnik R². R² > 0.7 = mocna korelacja.','—','kon'],
                ['ZMI-V10-MODEL','Model EnPI baseline','textarea','np. Zużycie EE [MWh] = 0.12 × Produkcja + 0.045 × HDD + 250','Funkcja regresji. ISO 50001 § 6.4.','—','kon'],
            ] as [$fid,$label,$type,$ph,$hint,$unit,$who])
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">{{ $label }}</div><div class="mf-id">{{ $fid }}</div></div>
                <div class="mf-wrap">
                    @if($type==='textarea')
                        <textarea class="mf-textarea {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}">{{ $formData[$fid] ?? '' }}</textarea>
                    @else
                        <input type="{{ $type }}" class="mf-input {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}" value="{{ $formData[$fid] ?? '' }}">
                    @endif
                    <div class="mf-hint">{{ $hint }}</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-{{ $who }}">{{ strtoupper($who) }}</span><br>{{ $unit }}</div>
            </div>
            @endforeach
        </div>
        @include('client.energy-audit-master-nav', ['current' => 'e9', 'prev' => 'e8', 'next' => 'e10'])
    </div>

    {{-- ================================================================ --}}
    {{-- ETAP 10 · STATUS EnMS                                            --}}
    {{-- ================================================================ --}}
    <div class="master-section" id="sec-e10">
        <div class="master-section-head">
            <div>
                <div class="master-section-eyebrow">ETAP 10</div>
                <h2 class="master-section-title">Status systemu zarządzania energią (EnMS)</h2>
                <p class="master-section-desc">ISO 50001 — czy klient ma EnMS · 8 pól · ok. 10 min</p>
            </div>
            <div class="master-section-badge" id="sb-e10">0 / 8</div>
        </div>
        <div class="master-group">
            <div class="master-group-title">Status certyfikacji</div>
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">Czy klient ma certyfikat ISO 50001?</div><div class="mf-id">ENMS-V1-CERTYFIKAT</div></div>
                <div class="mf-wrap">
                    <select class="mf-select {{ isset($formData['ENMS-V1-CERTYFIKAT']) && $formData['ENMS-V1-CERTYFIKAT'] !== '' ? 'mf-filled' : '' }}" data-id="ENMS-V1-CERTYFIKAT">
                        <option value="">— wybierz —</option>
                        @foreach(['TAK','NIE','w trakcie wdrażania','planowane','nie wiem'] as $opt)
                            <option @selected(($formData['ENMS-V1-CERTYFIKAT'] ?? '') === $opt)>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mf-unit"><span class="mtag mtag-em">EM</span></div>
            </div>
            @foreach([
                ['ENMS-V2-DATA-CERT','Data certyfikacji ISO 50001','date','','Tylko jeśli TAK','data','em'],
                ['ENMS-V3-CERT-AUDYTOR','Jednostka certyfikująca','text','np. TÜV, BSI, DEKRA','—','—','em'],
                ['ENMS-V4-ENERGY-MANAGER','Imię / e-mail Energy Managera','text','Jan Kowalski, j.kowalski@firma.pl','Osoba odpowiedzialna za zarządzanie energią w firmie','—','em'],
                ['ENMS-V5-POLITYKA','Polityka energetyczna — status','text','np. zatwierdzona 2023, v2.1','—','—','em'],
                ['ENMS-V6-SEU-LISTA','Identyfikacja SEU — status','text','np. zidentyfikowane 5 SEU: Lakiernia, Sprężarnia...','—','—','em'],
                ['ENMS-V7-CELE','Cele energetyczne i cele operacyjne','textarea','np. redukcja zużycia EE o 5% w 2025 vs baseline 2023','—','—','em'],
                ['ENMS-V8-PRZEGLAD','Przegląd energetyczny — ostatni','date','','Data ostatniego przeglądu energetycznego','data','em'],
            ] as [$fid,$label,$type,$ph,$hint,$unit,$who])
            <div class="master-field">
                <div class="mf-label"><div class="mf-q">{{ $label }}</div><div class="mf-id">{{ $fid }}</div></div>
                <div class="mf-wrap">
                    @if($type==='textarea')
                        <textarea class="mf-textarea {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}">{{ $formData[$fid] ?? '' }}</textarea>
                    @else
                        <input type="{{ $type }}" class="mf-input {{ isset($formData[$fid]) && $formData[$fid] !== '' ? 'mf-filled' : '' }}" data-id="{{ $fid }}" placeholder="{{ $ph }}" value="{{ $formData[$fid] ?? '' }}">
                    @endif
                    <div class="mf-hint">{{ $hint }}</div>
                </div>
                <div class="mf-unit"><span class="mtag mtag-{{ $who }}">{{ strtoupper($who) }}</span><br>{{ $unit }}</div>
            </div>
            @endforeach
        </div>
        {{-- Final save banner --}}
        <div style="margin-top:24px; background:linear-gradient(135deg,#f0fdf4,#e0f2fe); border:1px solid #86efac; border-radius:14px; padding:18px 22px; display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
            <div style="font-size:28px;">🎉</div>
            <div style="flex:1;">
                <div style="font-weight:800; font-size:15px; color:#0f2330; margin-bottom:3px;">Ankieta Master gotowa!</div>
                <div style="font-size:13px; color:#16a34a;">Dane są automatycznie zapisywane i będą zaciągane do wszystkich kolejnych audytów energetycznych (sprężarkownia, kotłownia, etc.).</div>
            </div>
            <a href="{{ route('strefa-klienta') }}" style="padding:10px 20px; background:#0f2330; color:#fff; font-weight:700; border-radius:10px; text-decoration:none; font-size:13px;">← Wróć do Strefy klienta</a>
        </div>
        @include('client.energy-audit-master-nav', ['current' => 'e10', 'prev' => 'e9', 'next' => null])
    </div>

</section>

<script>
(function() {
'use strict';

const SAVE_URL  = '{{ route("client.energy-audit-master.save") }}';
const CSRF      = '{{ csrf_token() }}';
const FORM_DATA = @json($formData);

// == State ==
let saveTimer   = null;
let nWydz       = 5;
let nHal        = 5;

// == Sections ==
const SECTIONS = ['e0','e1','e2','e3','e4','e5','e6','e7','e8','e9','e10'];
const SECTION_TOTALS = { e0:20, e1:9, e2:12, e3:10, e4:0, e5:0, e6:0, e7:35, e8:0, e9:10, e10:8 };

// == Tabs ==
document.querySelectorAll('.master-nav-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const sec = btn.dataset.section;
        document.querySelectorAll('.master-nav-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.master-section').forEach(s => s.classList.remove('active'));
        const secEl = document.getElementById('sec-' + sec);
        if (secEl) {
            secEl.classList.add('active');
            if (sec === 'e6') rebuildMacierz();
            if (sec === 'e8') buildZuzyciaTable();
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

// == Navigation from bottom buttons ==
document.addEventListener('click', e => {
    const btn = e.target.closest('[data-goto-section]');
    if (!btn) return;
    const target = btn.dataset.gotoSection;
    const navBtn = document.querySelector('.master-nav-btn[data-section="' + target + '"]');
    if (navBtn) navBtn.click();
});

// == Field input tracking ==
function bindField(el) {
    el.removeEventListener('input', onFieldChange);
    el.removeEventListener('change', onFieldChange);
    el.addEventListener('input', onFieldChange);
    el.addEventListener('change', onFieldChange);
}

function onFieldChange(e) {
    const el = e.target;
    const val = el.value.trim();
    if (val !== '') el.classList.add('mf-filled');
    else el.classList.remove('mf-filled');
    FORM_DATA[el.dataset.id] = val;
    scheduleAutoSave();
    updateAllProgress();
    // Trigger macierz rebuild if wydz/hal name changed
    if (el.classList.contains('wydz-name-input') || el.classList.contains('hal-name-input')) {
        rebuildMacierz();
    }
    // Zuzycia row — recalculate sigma
    if (el.closest('#zuzycia-body')) recalcZuzyciaRow(el.closest('tr'));
}

document.querySelectorAll('[data-id]').forEach(bindField);

// == Progress ==
function updateAllProgress() {
    let totalFilled = 0, totalAll = 0;
    SECTIONS.forEach(sec => {
        const container = document.getElementById('sec-' + sec);
        if (!container) return;
        const fields = container.querySelectorAll('[data-id]');
        let filled = 0;
        fields.forEach(f => { if ((f.value || '').trim() !== '') filled++; });
        const total = fields.length;
        totalFilled += filled;
        totalAll += total;
        const badge = document.getElementById('sb-' + sec);
        if (badge) badge.textContent = filled + ' / ' + total;
        const nc = document.getElementById('nc-' + sec);
        if (nc) nc.textContent = filled + '/' + total;
    });
    const pct = totalAll > 0 ? Math.round(totalFilled / totalAll * 100) : 0;
    const fill = document.getElementById('master-prog-fill');
    const text = document.getElementById('master-prog-text');
    if (fill) fill.style.width = pct + '%';
    if (text) text.textContent = pct + '%';
}
updateAllProgress();

// == Auto-save ==
function scheduleAutoSave() {
    clearTimeout(saveTimer);
    const statusEl = document.getElementById('master-save-status');
    if (statusEl) { statusEl.textContent = 'Zapisywanie...'; statusEl.className = 'master-save-status saving'; }
    saveTimer = setTimeout(doSave, 800);
}

function doSave() {
    const filled = Object.entries(FORM_DATA).filter(([,v]) => v !== null && v !== '').length;
    const total  = Object.keys(FORM_DATA).length || 1;
    const pct    = Math.min(100, Math.round(filled / Math.max(total, 1) * 100));

    fetch(SAVE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ fields: FORM_DATA, completion_percent: pct }),
    })
    .then(r => r.json())
    .then(data => {
        const statusEl = document.getElementById('master-save-status');
        if (statusEl) {
            statusEl.textContent = '✓ Zapisano o ' + (data.last_saved_at || '--');
            statusEl.className = 'master-save-status saved';
        }
    })
    .catch(() => {
        const statusEl = document.getElementById('master-save-status');
        if (statusEl) { statusEl.textContent = '⚠ Błąd zapisu — sprawdź połączenie'; statusEl.className = 'master-save-status error'; }
    });
}

// == MACIERZ Hala × Wydział ==
function getNWydz() {
    const inputs = document.querySelectorAll('.wydz-name-input');
    return inputs.length;
}
function getNHal() {
    const inputs = document.querySelectorAll('.hal-name-input');
    return inputs.length;
}

function rebuildMacierz() {
    const headerRow = document.getElementById('macierz-header-row');
    const body      = document.getElementById('macierz-body');
    const foot      = document.getElementById('macierz-foot');
    if (!headerRow || !body || !foot) return;

    nWydz = getNWydz();
    nHal  = getNHal();

    const wydzNames = [];
    for (let w = 1; w <= nWydz; w++) {
        const el = document.querySelector('[data-id="WYD-V2-NAZWA-W' + w + '"]');
        wydzNames.push(el && el.value ? el.value : 'Wydz. ' + w);
    }
    const halNames = [];
    for (let h = 1; h <= nHal; h++) {
        const el = document.querySelector('[data-id="HAL-V2-NAZWA-H' + h + '"]');
        halNames.push(el && el.value ? el.value : 'Hala ' + h);
    }

    // Save existing values
    const existing = {};
    body.querySelectorAll('input[data-id]').forEach(inp => { existing[inp.dataset.id] = inp.value; });

    // Rebuild header
    while (headerRow.children.length > 1) headerRow.removeChild(headerRow.lastChild);
    for (let w = 1; w <= nWydz; w++) {
        const th = document.createElement('th');
        th.style.minWidth = '90px';
        th.textContent = wydzNames[w-1];
        headerRow.appendChild(th);
    }
    const thS = document.createElement('th'); thS.style.minWidth='65px'; thS.style.background='#d97706'; thS.style.color='#fff'; thS.textContent='Σ'; headerRow.appendChild(thS);
    const thSt = document.createElement('th'); thSt.style.minWidth='110px'; thSt.style.background='#d97706'; thSt.style.color='#fff'; thSt.textContent='Status'; headerRow.appendChild(thSt);

    // Rebuild body
    body.innerHTML = '';
    for (let h = 1; h <= nHal; h++) {
        const tr = document.createElement('tr');
        const tdH = document.createElement('td');
        tdH.className = 'td-q';
        tdH.innerHTML = '<div class="q-l">' + halNames[h-1] + '</div><div class="q-id">HAL-' + h + '</div>';
        tr.appendChild(tdH);
        for (let w = 1; w <= nWydz; w++) {
            const td = document.createElement('td');
            const inp = document.createElement('input');
            inp.type = 'number'; inp.className = 'cell-inp mac-inp'; inp.min='0'; inp.max='100'; inp.step='0.1'; inp.placeholder='%';
            inp.dataset.id = 'MAC-H' + h + '-W' + w;
            inp.dataset.row = h; inp.dataset.col = w;
            if (existing[inp.dataset.id]) { inp.value = existing[inp.dataset.id]; inp.classList.add('mf-filled'); }
            else if (FORM_DATA[inp.dataset.id]) { inp.value = FORM_DATA[inp.dataset.id]; inp.classList.add('mf-filled'); }
            inp.addEventListener('input', onMacInput);
            td.appendChild(inp);
            tr.appendChild(td);
        }
        const tdSum = document.createElement('td'); tdSum.style.fontWeight='700'; tdSum.style.fontFamily='monospace'; tdSum.dataset.sumFor=h; tdSum.textContent='0'; tr.appendChild(tdSum);
        const tdSt = document.createElement('td'); tdSt.style.fontSize='11px'; tdSt.dataset.statusFor=h; tdSt.textContent='(brak)'; tr.appendChild(tdSt);
        body.appendChild(tr);
    }

    // Rebuild foot
    foot.innerHTML = '';
    const trF = document.createElement('tr');
    const tdL = document.createElement('td'); tdL.className='td-q'; tdL.style.fontWeight='700'; tdL.textContent='Σ kolumn'; trF.appendChild(tdL);
    for (let w = 1; w <= nWydz; w++) {
        const td = document.createElement('td'); td.style.fontWeight='700'; td.style.fontFamily='monospace'; td.dataset.colSumFor=w; td.textContent='0'; trF.appendChild(td);
    }
    trF.appendChild(document.createElement('td'));
    trF.appendChild(document.createElement('td'));
    foot.appendChild(trF);

    updateMacSums();
    updateAllProgress();
}

function onMacInput(e) {
    const inp = e.target;
    const val = inp.value.trim();
    if (val !== '') inp.classList.add('mf-filled'); else inp.classList.remove('mf-filled');
    FORM_DATA[inp.dataset.id] = val;
    scheduleAutoSave();
    updateMacSums();
}

function updateMacSums() {
    const nW = getNWydz(), nH = getNHal();
    for (let h = 1; h <= nH; h++) {
        let sum = 0;
        for (let w = 1; w <= nW; w++) {
            const inp = document.querySelector('input[data-id="MAC-H' + h + '-W' + w + '"]');
            if (inp && inp.value) sum += parseFloat(inp.value) || 0;
        }
        const tdSum = document.querySelector('td[data-sum-for="' + h + '"]');
        if (tdSum) { tdSum.textContent = sum.toFixed(1); tdSum.style.color = Math.abs(sum-100)<0.1 ? '#16a34a' : (sum>0 ? '#dc2626' : '#8aa3b5'); }
        const tdSt = document.querySelector('td[data-status-for="' + h + '"]');
        if (tdSt) {
            if (sum === 0) { tdSt.textContent = '(brak)'; tdSt.className = ''; }
            else if (Math.abs(sum-100) < 0.1) { tdSt.textContent = 'OK ✓'; tdSt.className = 'mac-ok'; }
            else { tdSt.textContent = sum < 100 ? '⚠ Brakuje ' + (100-sum).toFixed(1) + '%' : '⚠ Nadmiar ' + (sum-100).toFixed(1) + '%'; tdSt.className = 'mac-err'; }
        }
    }
}

// == ADD WYDZIAŁ ==
document.getElementById('add-wydz-btn')?.addEventListener('click', () => {
    const table = document.getElementById('wydzialy-table');
    if (!table) return;
    const hdrRow = table.querySelector('thead tr');
    const newIdx = hdrRow.querySelectorAll('th:not(.th-q)').length + 1;
    const th = document.createElement('th'); th.style.minWidth='130px'; th.textContent='Wydział ' + newIdx;
    hdrRow.appendChild(th);
    table.querySelectorAll('tbody tr').forEach(tr => {
        if (tr.classList.contains('sec-header')) { const td=tr.querySelector('td[colspan]'); if(td) td.setAttribute('colspan', String(newIdx+1)); return; }
        const tds = tr.querySelectorAll('td:not(.td-q)');
        if (!tds.length) return;
        const last = tds[tds.length-1];
        const newTd = last.cloneNode(true);
        const oldSuffix = '-W' + (newIdx-1);
        const newSuffix = '-W' + newIdx;
        newTd.querySelectorAll('[data-id]').forEach(el => {
            const old = el.getAttribute('data-id');
            if (old && old.endsWith(oldSuffix)) el.setAttribute('data-id', old.slice(0, -oldSuffix.length) + newSuffix);
            el.value = '';
            el.classList.remove('mf-filled');
            bindField(el);
            if (el.classList.contains('wydz-name-input')) el.classList.add('wydz-name-input');
        });
        tr.appendChild(newTd);
    });
    nWydz = newIdx;
    updateAllProgress();
});

// == ADD HALA ==
document.getElementById('add-hal-btn')?.addEventListener('click', () => {
    const table = document.getElementById('hale-table');
    if (!table) return;
    const hdrRow = table.querySelector('thead tr');
    const newIdx = hdrRow.querySelectorAll('th:not(.th-q)').length + 1;
    const th = document.createElement('th'); th.style.minWidth='130px'; th.textContent='Hala ' + newIdx;
    hdrRow.appendChild(th);
    table.querySelectorAll('tbody tr').forEach(tr => {
        if (tr.classList.contains('sec-header')) { const td=tr.querySelector('td[colspan]'); if(td) td.setAttribute('colspan', String(newIdx+1)); return; }
        const tds = tr.querySelectorAll('td:not(.td-q)');
        if (!tds.length) return;
        const last = tds[tds.length-1];
        const newTd = last.cloneNode(true);
        const oldSuffix = '-H' + (newIdx-1);
        const newSuffix = '-H' + newIdx;
        newTd.querySelectorAll('[data-id]').forEach(el => {
            const old = el.getAttribute('data-id');
            if (old && old.endsWith(oldSuffix)) el.setAttribute('data-id', old.slice(0, -oldSuffix.length) + newSuffix);
            el.value = '';
            el.classList.remove('mf-filled');
            bindField(el);
            if (el.classList.contains('hal-name-input')) el.classList.add('hal-name-input');
        });
        tr.appendChild(newTd);
    });
    nHal = newIdx;
    rebuildMacierz();
    updateAllProgress();
});

// == ZUZYCIA TABLE E8 ==
const MIESIACE = ['styczeń','luty','marzec','kwiecień','maj','czerwiec','lipiec','sierpień','wrzesień','październik','listopad','grudzień'];
const NOS_FACTORS = { EE: 1, GAZ: 0.0097, CIEPLO: 0.2778, OLEJ: 0.0105, LPG: 0.0128, PARA: 0.7, PV: 1 };
const NOS_KEYS    = ['EE','GAZ','CIEPLO','OLEJ','LPG','PARA','PV'];
const N_MIES      = 24;

function buildZuzyciaTable() {
    const body = document.getElementById('zuzycia-body');
    const foot = document.getElementById('zuzycia-foot');
    if (!body || !foot || body.children.length > 0) return; // already built

    for (let m = 1; m <= N_MIES; m++) {
        const tr = document.createElement('tr');
        const mIdx = (m-1) % 12;
        const baseYear = new Date().getFullYear() - 2;
        const year = baseYear + Math.floor((m-1)/12);

        // Miesiąc
        const tdM = document.createElement('td'); tdM.className='td-q'; tdM.style.fontWeight='600'; tdM.textContent=MIESIACE[mIdx]; tr.appendChild(tdM);
        // Rok
        const tdR = document.createElement('td');
        const inpR = document.createElement('input'); inpR.type='number'; inpR.className='cell-inp'; inpR.dataset.id='ZUZ-ROK-'+m; inpR.value=FORM_DATA['ZUZ-ROK-'+m]||year; inpR.style.width='65px';
        if (FORM_DATA['ZUZ-ROK-'+m]) inpR.classList.add('mf-filled');
        inpR.addEventListener('input', e => { FORM_DATA[e.target.dataset.id]=e.target.value; scheduleAutoSave(); });
        tdR.appendChild(inpR); tr.appendChild(tdR);

        // Nośniki
        NOS_KEYS.forEach(nos => {
            const td = document.createElement('td');
            const inp = document.createElement('input'); inp.type='number'; inp.className='cell-inp'; inp.dataset.id='ZUZ-'+nos+'-'+m; inp.step='0.01'; inp.placeholder='0';
            if (FORM_DATA[inp.dataset.id]) { inp.value=FORM_DATA[inp.dataset.id]; inp.classList.add('mf-filled'); }
            inp.addEventListener('input', e => {
                const v = e.target.value; FORM_DATA[e.target.dataset.id]=v;
                if (v) e.target.classList.add('mf-filled'); else e.target.classList.remove('mf-filled');
                scheduleAutoSave(); recalcZuzyciaRow(e.target.closest('tr')); updateAllProgress();
            });
            td.appendChild(inp); tr.appendChild(td);
        });

        // Sigma
        const tdSig = document.createElement('td'); tdSig.className='sigma-cell'; tdSig.dataset.sigmaRow=m; tdSig.style.fontFamily='monospace'; tdSig.textContent='0'; tr.appendChild(tdSig);

        body.appendChild(tr);
        recalcZuzyciaRow(tr);
    }
    updateAllProgress();
}

function recalcZuzyciaRow(tr) {
    if (!tr) return;
    let sigma = 0;
    NOS_KEYS.forEach((nos, i) => {
        const inp = tr.querySelectorAll('td:not(.td-q)')[i+1]; // +1 for year col
        if (inp) {
            const field = inp.querySelector('input');
            const val = parseFloat(field?.value || 0) || 0;
            sigma += val * (NOS_FACTORS[nos] || 1);
        }
    });
    const sigTd = tr.querySelector('[data-sigma-row]');
    if (sigTd) sigTd.textContent = sigma.toFixed(2);
}

// Build E8 if starting on that tab
if (document.getElementById('sec-e8')?.classList.contains('active')) buildZuzyciaTable();

// Initial macierz build if on e6
if (document.getElementById('sec-e6')?.classList.contains('active')) rebuildMacierz();

})();
</script>
</x-layouts.app>
