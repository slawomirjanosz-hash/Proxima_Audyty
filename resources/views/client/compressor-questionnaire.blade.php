<x-layouts.app>

    <style>
        .cq-header {
            background: linear-gradient(135deg, #0f2c3e 0%, #0e89d8 60%, #1b7fc4 100%);
            color: #fff;
            border-radius: 16px;
            padding: 26px 28px;
            box-shadow: 0 12px 34px rgba(14,55,85,.22);
        }
        .cq-header h2 { margin: 0 0 6px; font-size: 26px; font-weight: 800; }
        .cq-header p  { margin: 0; color: rgba(255,255,255,.88); font-size: 14.5px; max-width: 800px; }
        .cq-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.18); border-radius: 999px;
            padding: 4px 12px; font-size: 12px; font-weight: 700;
            margin-bottom: 10px; letter-spacing: .5px; text-transform: uppercase;
        }

        .cq-section {
            margin-top: 18px;
            border: 1px solid #d2e3f1;
            border-radius: 14px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(18,72,110,.06);
        }
        .cq-section-header {
            width: 100%; text-align: left; border: none; cursor: pointer;
            background: #f0f7fc; padding: 13px 18px;
            display: flex; justify-content: space-between; align-items: center;
            font-weight: 800; font-size: 15.5px; color: #0e344e;
            border-bottom: 1px solid #d2e3f1; transition: background .15s;
        }
        .cq-section-header:hover { background: #e4f0fa; }
        .cq-section-header .badge-num {
            background: #0e89d8; color: #fff; border-radius: 999px;
            font-size: 11px; padding: 2px 9px; margin-right: 8px; font-weight: 700;
        }
        .cq-section-body { padding: 18px 20px; display: block; }
        .cq-section.collapsed .cq-section-body { display: none; }
        .chevron { font-size: 13px; color: #6b8aa3; transition: transform .2s; }
        .cq-section.collapsed .chevron { transform: rotate(-90deg); }

        /* Grid of form fields */
        .cq-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .cq-grid.wide { grid-template-columns: 1fr; }
        @media (max-width: 680px) { .cq-grid { grid-template-columns: 1fr; } }

        .cq-field { display: flex; flex-direction: column; gap: 4px; }
        .cq-label {
            font-size: 12.5px; font-weight: 700; color: #1c3a4e; margin-bottom: 1px;
        }
        .cq-hint { font-size: 11.5px; color: #7a9ab5; margin-bottom: 3px; }
        .cq-input, .cq-select, .cq-textarea {
            width: 100%; border: 1px solid #c8d9e8; border-radius: 9px;
            padding: 7px 10px; font-size: 13.5px; background: #fafcff;
            box-sizing: border-box; color: #1a2d3e;
        }
        .cq-input:focus, .cq-select:focus, .cq-textarea:focus {
            outline: none; border-color: #0e89d8; background: #fff;
            box-shadow: 0 0 0 3px rgba(14,137,216,.1);
        }
        .cq-textarea { min-height: 52px; resize: vertical; }
        .cq-select { appearance: auto; }

        /* Compressors table */
        .cq-table-wrap { overflow-x: auto; margin-top: 6px; }
        table.cq-tbl {
            width: 100%; border-collapse: collapse; font-size: 12.5px; min-width: 900px;
        }
        table.cq-tbl th {
            background: #eef5fb; color: #2c4e67; font-weight: 700;
            padding: 7px 8px; border: 1px solid #d5e0ea; font-size: 11.5px;
            white-space: nowrap; text-align: left;
        }
        table.cq-tbl td {
            padding: 5px 6px; border: 1px solid #dde8f2; vertical-align: middle;
        }
        table.cq-tbl tr:nth-child(even) td { background: #f8fbff; }
        table.cq-tbl input, table.cq-tbl select {
            width: 100%; border: 1px solid #c8d9e8; border-radius: 6px;
            padding: 5px 6px; font-size: 12px; background: #fff; box-sizing: border-box;
        }
        table.cq-tbl input:focus, table.cq-tbl select:focus {
            outline: none; border-color: #0e89d8;
            box-shadow: 0 0 0 2px rgba(14,137,216,.1);
        }
        .btn-add-row {
            margin-top: 8px; padding: 6px 14px; background: #e8f4ff;
            border: 1px solid #0e89d8; border-radius: 8px; cursor: pointer;
            font-size: 13px; font-weight: 600; color: #0e89d8;
        }
        .btn-add-row:hover { background: #d5ecfc; }
        .btn-del-row {
            background: none; border: none; cursor: pointer; color: #c03030;
            font-size: 16px; padding: 0 4px;
        }

        /* ─── Nameplate scan ─────────────────────────────────── */
        .btn-scan-nameplate {
            margin-top: 8px; margin-left: 8px; padding: 6px 14px;
            background: #fff7ed; border: 1px solid #f59e0b;
            border-radius: 8px; cursor: pointer;
            font-size: 13px; font-weight: 600; color: #92400e;
        }
        .btn-scan-nameplate:hover { background: #fef3c7; }
        .scan-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 2000;
            background: rgba(10,30,50,.7);
            align-items: center; justify-content: center;
        }
        .scan-overlay.visible { display: flex; }
        .scan-modal {
            background: #fff; border-radius: 18px;
            padding: 28px 28px 24px;
            width: min(480px, 94vw);
            box-shadow: 0 20px 60px rgba(10,30,50,.35);
            position: relative;
        }
        .scan-modal h3 { margin: 0 0 6px; font-size: 18px; font-weight: 800; color: #0e344e; }
        .scan-modal p  { margin: 0 0 18px; font-size: 13px; color: #4c6373; line-height: 1.5; }
        .scan-btns { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .scan-btn {
            flex: 1; min-width: 140px;
            padding: 14px 16px; border-radius: 12px;
            border: 2px solid #d5e0ea; background: #f7fbff; cursor: pointer;
            font-size: 14px; font-weight: 700; color: #0e344e;
            display: flex; flex-direction: column; align-items: center; gap: 6px;
            transition: border-color .14s, background .14s;
        }
        .scan-btn:hover { border-color: #0e89d8; background: #e0f2fe; }
        .scan-btn-icon { font-size: 30px; }
        .scan-btn-label { font-size: 12px; color: #4c6373; font-weight: 400; }
        .scan-close {
            position: absolute; top: 14px; right: 16px;
            background: none; border: none; cursor: pointer;
            font-size: 22px; color: #6b8aa3; padding: 4px;
        }
        .scan-close:hover { color: #c03030; }
        .scan-loading { display: none; text-align: center; padding: 20px; color: #0e89d8; font-weight: 600; }
        .scan-loading.visible { display: block; }
        .scan-err { display: none; margin-top: 10px; padding: 10px 14px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; color: #b91c1c; font-size: 13px; }
        .scan-err.visible { display: block; }
        .scan-result { display: none; margin-top: 10px; border: 1px solid #bae6fd; border-radius: 10px; padding: 12px 14px; background: #f0f9ff; font-size: 13px; }
        .scan-result.visible { display: block; }
        .scan-result-title { font-weight: 800; color: #0e344e; margin-bottom: 8px; font-size: 14px; }
        .scan-result table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        .scan-result td { padding: 3px 6px; border-bottom: 1px solid #d5e0ea; }
        .scan-result td:first-child { color: #4c6373; width: 44%; }
        .scan-result td:last-child { font-weight: 600; color: #0e344e; }
        .scan-apply-btn { margin-top: 12px; width: 100%; padding: 11px; background: #0e89d8; color: #fff; border: none; border-radius: 10px; cursor: pointer; font-size: 14px; font-weight: 700; }
        .scan-apply-btn:hover { background: #0a6faf; }

        /* Progress bar */
        .cq-progress-wrap {
            background: #fff; border: 1px solid #d2e3f1; border-radius: 12px;
            padding: 12px 16px; margin-top: 14px;
            display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
        }
        .cq-progress-bar {
            flex: 1; min-width: 120px; height: 8px; background: #dce9f4;
            border-radius: 999px; overflow: hidden;
        }
        .cq-progress-fill {
            height: 100%; background: linear-gradient(90deg, #0e89d8, #1ba84a);
            border-radius: 999px; transition: width .4s;
        }

        .cq-actions {
            display: flex; gap: 10px; flex-wrap: wrap;
            margin-top: 22px; padding-top: 18px; border-top: 1px solid #dce9f5;
        }

        /* Alert/info boxes */
        .cq-info {
            background: #f0f7fc; border: 1px solid #b3d4ef; border-radius: 10px;
            padding: 10px 14px; font-size: 13px; color: #1b4a6d; margin-bottom: 12px;
        }
        .cq-success {
            background: #f0faf0; border: 1px solid #b7dcb5;
            border-radius: 10px; padding: 10px 14px; font-size: 13px; color: #155724; margin-top: 12px;
        }
        .cq-error {
            background: #fff5f5; border: 1px solid #f5c2c7;
            border-radius: 10px; padding: 10px 14px; font-size: 13px; color: #842029; margin-top: 12px;
        }

        /* "Nie wiem" button */
        .dontknow-btn {
            font-size: 11.5px; padding: 3px 9px; border-radius: 7px;
            border: 1px solid #c8d9e8; background: #f5f8fc; color: #6b8aa3;
            cursor: pointer; margin-left: 6px; white-space: nowrap;
        }
        .dontknow-btn:hover { background: #e4eef8; border-color: #9bbcd6; }
        .dontknow-active { background: #fdf3e7; border-color: #e0a954; color: #7d5000; }

        /* Layout: sidenav + main — overflow fix */
        .cq-outer { max-width: 100%; overflow-x: hidden; }
        .cq-layout {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 28px;
            align-items: start;
            max-width: 100%;
        }
        @media (max-width: 800px) {
            .cq-layout { grid-template-columns: 1fr; }
            .cq-sidenav { display: none; }
        }

        /* Sidenav */
        .cq-sidenav {
            position: sticky;
            top: 80px;
            align-self: start;
            font-size: 13px;
        }
        .cq-sidenav-title {
            font-size: 10.5px;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #6b8aa3;
            margin-bottom: 12px;
            font-weight: 700;
        }
        .cq-sidenav-list { list-style: none; padding: 0; margin: 0; }
        .cq-sidenav-item {
            padding: 8px 8px 8px 11px;
            border-left: 2px solid #d2e3f1;
            color: #4c6b82;
            cursor: pointer;
            transition: all .18s;
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12.5px;
            margin-bottom: 1px;
            border-radius: 0 6px 6px 0;
        }
        .cq-sidenav-item:hover { border-left-color: #0e89d8; background: #f0f7fc; color: #0e344e; }
        .cq-sidenav-item.active { border-left-color: #1ba84a; background: #f0faf4; color: #0f3a20; font-weight: 700; }
        .cq-sidenav-item.active .cq-sidenav-num { color: #1ba84a; }
        .cq-sidenav-num { font-size: 10.5px; color: #6b8aa3; font-weight: 700; min-width: 24px; }
        .cq-sidenav-name { flex: 1; }
        .cq-sidenav-count { font-size: 10px; color: #9ab4c8; }
        .cq-main { min-width: 0; }

        /* Hero */
        .cq-hero {
            background: linear-gradient(135deg, #0f2c3e 0%, #0d4f70 55%, #0c6fa0 100%);
            color: #fff;
            border-radius: 14px;
            padding: 28px 32px;
            box-shadow: 0 10px 30px rgba(12,47,70,.22);
        }
        .hero-eyebrow {
            font-size: 11px; font-weight: 700; letter-spacing: .14em;
            text-transform: uppercase; color: rgba(255,255,255,.7); margin-bottom: 10px;
        }
        .hero-title {
            font-size: 25px; font-weight: 800; line-height: 1.25;
            margin: 0 0 12px; color: #fff;
        }
        .hero-title em { font-style: italic; color: #7dd3fc; }
        .hero-lead {
            font-size: 14px; color: rgba(255,255,255,.85);
            max-width: 760px; line-height: 1.65; margin: 0;
        }
    </style>

    {{-- OUTER WRAPPER --}}
    <div class="cq-outer">

    {{-- HERO (exact text from ENESA_Sprężarkownia.html) --}}
    <div class="cq-hero">
        <div class="hero-eyebrow">ISO 11011 · Audyt Sprężarkowni · Faza 1 z 3</div>
        <h1 class="hero-title">Wypełnij <em>co wiesz</em> — Audytor doprecyzuje resztę.</h1>
        <p class="hero-lead">
            Formularz ma 69 pól w 8 etapach + tabela sprężarek (28 pól na każdą sprężarkę).
            Wypełnij swoje. Pola, których nie znasz, zostaw puste lub kliknij „nie wiem" — trafią do osoby,
            która zna odpowiedź. Po wysłaniu Audytor ENESA przejrzy dane i dopyta tylko o niejasności i puste
            pola — szacowany czas konsultacji: 5–10 minut.
        </p>
    </div>

    {{-- Audit info bar --}}
    <div style="margin-top:12px; background:#fff; border:1px solid #d2e3f1; border-radius:12px; padding:12px 16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
        <div>
            <strong style="font-size:15px;">{{ $audit->title }}</strong>
            <span style="color:#6b8aa3; font-size:13px;"> — {{ $audit->company?->name ?? ($company?->name ?? '—') }}</span>
        </div>
        @if($audit->questionnaire_completed)
            <span style="background:#d9f5e8; border:1px solid #b3eacb; color:#1a6b3c; padding:4px 12px; border-radius:999px; font-size:12px; font-weight:700;">✓ Ankieta wypełniona</span>
        @endif
    </div>

    {{-- Notifications --}}
    @if(session('draft_saved'))
        <div class="cq-success" style="margin-top:10px;">✓ Kopia robocza zapisana.</div>
    @endif
    @if(session('status'))
        <div class="cq-success" style="margin-top:10px;">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="cq-error" style="margin-top:10px;">
            @foreach($errors->all() as $err) <div>{{ $err }}</div> @endforeach
        </div>
    @endif

    {{-- 2-column layout: sidenav + main content --}}
    <div class="cq-layout" style="margin-top:20px;">

        {{-- Left sidenav --}}
        <aside class="cq-sidenav">
            <div class="cq-sidenav-title">Etapy audytu</div>
            <ul class="cq-sidenav-list">
                <li class="cq-sidenav-item active" data-target="s0"><span class="cq-sidenav-num">0</span><span class="cq-sidenav-name">Respondent</span><span class="cq-sidenav-count">4</span></li>
                <li class="cq-sidenav-item" data-target="s1"><span class="cq-sidenav-num">1</span><span class="cq-sidenav-name">Kontekst</span><span class="cq-sidenav-count">5</span></li>
                <li class="cq-sidenav-item" data-target="s2"><span class="cq-sidenav-num">2</span><span class="cq-sidenav-name">Sprężarki</span><span class="cq-sidenav-count">5×28</span></li>
                <li class="cq-sidenav-item" data-target="s3"><span class="cq-sidenav-num">3</span><span class="cq-sidenav-name">Sprężarkownia</span><span class="cq-sidenav-count">7</span></li>
                <li class="cq-sidenav-item" data-target="s35"><span class="cq-sidenav-num">3.5</span><span class="cq-sidenav-name">Zasilanie</span><span class="cq-sidenav-count">10</span></li>
                <li class="cq-sidenav-item" data-target="s4"><span class="cq-sidenav-num">4</span><span class="cq-sidenav-name">Uzdatnianie</span><span class="cq-sidenav-count">9</span></li>
                <li class="cq-sidenav-item" data-target="s5"><span class="cq-sidenav-num">5</span><span class="cq-sidenav-name">Sieć</span><span class="cq-sidenav-count">12</span></li>
                <li class="cq-sidenav-item" data-target="s6"><span class="cq-sidenav-num">6</span><span class="cq-sidenav-name">Odbiorcy</span><span class="cq-sidenav-count">12</span></li>
                <li class="cq-sidenav-item" data-target="s7"><span class="cq-sidenav-num">7</span><span class="cq-sidenav-name">Eksploatacja</span><span class="cq-sidenav-count">10</span></li>
            </ul>
        </aside>

        {{-- Right: progress + form --}}
        <div class="cq-main">

            {{-- Progress --}}
            <div class="cq-progress-wrap">
                <span style="font-size:13px; color:#355c77; white-space:nowrap;">Wypełniono:</span>
                <div class="cq-progress-bar">
                    <div class="cq-progress-fill" id="prog-fill" style="width:0%"></div>
                </div>
                <span id="prog-text" style="font-size:13px; color:#355c77; white-space:nowrap;">0%</span>
            </div>

    <form method="POST" action="{{ route('client.audit.compressor.questionnaire.save', $audit) }}" id="cq-form">
        @csrf
        <input type="hidden" name="save_as_draft" id="draft-flag" value="0">

        {{-- ═══ ETAP 0 — Dane ogólne ═══ --}}
        <div class="cq-section" id="s0">
            <button type="button" class="cq-section-header" onclick="toggleSection('s0')">
                <span><span class="badge-num">0</span>Dane ogólne</span>
                <span class="chevron">▼</span>
            </button>
            <div class="cq-section-body">
                <div class="cq-info">Podaj podstawowe informacje o osobie wypełniającej ankietę i zakładzie.</div>
                <div class="cq-grid">
                    <div class="cq-field">
                        <label class="cq-label">Imię i nazwisko osoby wypełniającej</label>
                        <input type="text" name="answers[REQ-00-IMIE]" class="cq-input"
                               value="{{ old('answers.REQ-00-IMIE', $answers['REQ-00-IMIE'] ?? ($user->name ?? '')) }}"
                               placeholder="np. Jan Kowalski">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Stanowisko / funkcja</label>
                        <input type="text" name="answers[REQ-00-STAN]" class="cq-input"
                               value="{{ old('answers.REQ-00-STAN', $answers['REQ-00-STAN'] ?? ($user->position ?? '')) }}"
                               placeholder="np. Kierownik działu UR, Energetyk">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Dział / Wydział</label>
                        <input type="text" name="answers[REQ-00-DZIAL]" class="cq-input"
                               value="{{ old('answers.REQ-00-DZIAL', $answers['REQ-00-DZIAL'] ?? '') }}"
                               placeholder="np. Dział Utrzymania Ruchu">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Zakład / Lokalizacja</label>
                        <input type="text" name="answers[REQ-00-ZAKLAD]" class="cq-input"
                               value="{{ old('answers.REQ-00-ZAKLAD', $answers['REQ-00-ZAKLAD'] ?? ($company?->city ?? '')) }}"
                               placeholder="np. Zakład Produkcyjny, ul. Przemysłowa 5, Kraków">
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ ETAP 1 — Kontekst operacyjny ═══ --}}
        <div class="cq-section" id="s1">
            <button type="button" class="cq-section-header" onclick="toggleSection('s1')">
                <span><span class="badge-num">1</span>Kontekst operacyjny zakładu</span>
                <span class="chevron">▼</span>
            </button>
            <div class="cq-section-body">
                <div class="cq-grid">
                    <div class="cq-field">
                        <label class="cq-label">Branża zakładu</label>
                        <select name="answers[CTX-01-BR]" class="cq-select">
                            <option value="">— wybierz —</option>
                            @foreach(['Spożywcza','Motoryzacyjna','Metalowa / Hutnicza','Chemiczna / Petrochemiczna','Farmaceutyczna','Elektroniczna / Elektryczna','Papiernicza / Celulozowa','Tekstylna','Drzewna / Meblarska','Budowlana / Materiały budowlane','Energetyka / Utilities','Logistyka / Magazynowanie','Inna'] as $br)
                                <option value="{{ $br }}" {{ (old('answers.CTX-01-BR', $answers['CTX-01-BR'] ?? '') === $br) ? 'selected' : '' }}>{{ $br }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Liczba zmian produkcyjnych</label>
                        <select name="answers[CTX-02-ZM]" class="cq-select">
                            <option value="">— wybierz —</option>
                            @foreach(['1 zmiana (8h)','2 zmiany (16h)','3 zmiany (24h)','Ciągła praca (24/7)','Nieregularnie'] as $z)
                                <option value="{{ $z }}" {{ (old('answers.CTX-02-ZM', $answers['CTX-02-ZM'] ?? '') === $z) ? 'selected' : '' }}>{{ $z }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Liczba dni roboczych w roku</label>
                        <div class="cq-hint">np. 250 dni (5 dni × 50 tygodni)</div>
                        <input type="number" name="answers[CTX-03-DNI]" class="cq-input"
                               min="1" max="365"
                               value="{{ old('answers.CTX-03-DNI', $answers['CTX-03-DNI'] ?? '') }}"
                               placeholder="np. 250">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Procesy krytyczne wymagające stałego sprężonego powietrza</label>
                        <div class="cq-hint">np. linie pakowania, pneumatyczne siłowniki, narzędzia</div>
                        <textarea name="answers[CTX-04-KRYT]" class="cq-textarea"
                                  placeholder="Opisz krótko...">{{ old('answers.CTX-04-KRYT', $answers['CTX-04-KRYT'] ?? '') }}</textarea>
                    </div>
                    <div class="cq-field" style="grid-column: 1 / -1;">
                        <label class="cq-label">Plany inwestycyjne i modernizacyjne (kolejne 12–24 miesiące)</label>
                        <textarea name="answers[CTX-05-PLAN]" class="cq-textarea"
                                  placeholder="np. wymiana sprężarki, rozbudowa sieci, nowa linia produkcyjna...">{{ old('answers.CTX-05-PLAN', $answers['CTX-05-PLAN'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ ETAP 2 — Inwentaryzacja sprężarek ═══ --}}
        <div class="cq-section" id="s2">
            <button type="button" class="cq-section-header" onclick="toggleSection('s2')">
                <span><span class="badge-num">2</span>Inwentaryzacja sprężarek</span>
                <span class="chevron">▼</span>
            </button>
            <div class="cq-section-body">
                <div class="cq-info">
                    Wypełnij dane dla każdej sprężarki. Kliknij <strong>"+ Dodaj sprężarkę"</strong> żeby dodać kolejny wiersz.
                    Możesz wpisać tylko znane parametry — puste pola to żaden problem.
                </div>
                <div class="cq-table-wrap">
                    <table class="cq-tbl" id="compressors-table">
                        <thead>
                            <tr>
                                <th style="width:28px;">#</th>
                                <th>Nr inw.</th>
                                <th>Lokalizacja</th>
                                <th>Producent</th>
                                <th>Model</th>
                                <th>Typ</th>
                                <th>Moc [kW]</th>
                                <th>Wydajność [m³/min]</th>
                                <th>Pmax [bar]</th>
                                <th>Rok prod.</th>
                                <th>Klasa IE</th>
                                <th>Stan tech.</th>
                                <th>Ostatni serwis</th>
                                <th>Motogodz.</th>
                                <th>Godz/dobę</th>
                                <th>Obciążenie [%]</th>
                                <th>Tryb pracy</th>
                                <th>Sterowanie</th>
                                <th>Chłodzenie</th>
                                <th>Recyrk. ciepła</th>
                                <th style="width:32px;"></th>
                            </tr>
                        </thead>
                        <tbody id="compressors-body">
                            @php
                                $savedCompressors = $answers['_compressors'] ?? [];
                                if (empty($savedCompressors)) {
                                    $savedCompressors = [array_fill_keys(['nr_inw','lokalizacja','producent','model','typ','moc_kw','wydajnosc','pmax','rok','klasa_ie','stan','serwis','motogodz','godz_dobe','obciazenie','tryb','sterowanie','chlodzenie','recyrk'], '')];
                                }
                            @endphp
                            @foreach($savedCompressors as $ri => $row)
                                <tr>
                                    <td style="text-align:center; color:#6b8aa3; font-weight:700;">{{ $ri+1 }}</td>
                                    @foreach(['nr_inw','lokalizacja'] as $col)
                                        <td><input type="text" name="compressors[{{ $ri }}][{{ $col }}]" value="{{ $row[$col] ?? '' }}"></td>
                                    @endforeach
                                    <td>
                                        <select name="compressors[{{ $ri }}][producent]">
                                            <option value="">—</option>
                                            @foreach(['Atlas Copco','Kaeser','Boge','Ingersoll Rand','ABAC','CompAir','Gardner Denver','Mark','Mikropor','inny'] as $p)
                                                <option value="{{ $p }}" {{ ($row['producent'] ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" name="compressors[{{ $ri }}][model]" value="{{ $row['model'] ?? '' }}"></td>
                                    <td>
                                        <select name="compressors[{{ $ri }}][typ]">
                                            <option value="">—</option>
                                            @foreach(['Śrubowa','Tłokowa','Odśrodkowa','Spiralna (scroll)'] as $t)
                                                <option value="{{ $t }}" {{ ($row['typ'] ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    @foreach(['moc_kw','wydajnosc','pmax','rok'] as $col)
                                        <td><input type="text" name="compressors[{{ $ri }}][{{ $col }}]" value="{{ $row[$col] ?? '' }}"></td>
                                    @endforeach
                                    <td>
                                        <select name="compressors[{{ $ri }}][klasa_ie]">
                                            <option value="">—</option>
                                            @foreach(['IE1','IE2','IE3','IE4','IE5','Nie wiem'] as $ie)
                                                <option value="{{ $ie }}" {{ ($row['klasa_ie'] ?? '') === $ie ? 'selected' : '' }}>{{ $ie }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="compressors[{{ $ri }}][stan]">
                                            <option value="">—</option>
                                            @foreach(['Dobry','Przeciętny','Zły','Nieznany'] as $s)
                                                <option value="{{ $s }}" {{ ($row['stan'] ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    @foreach(['serwis','motogodz','godz_dobe'] as $col)
                                        <td><input type="text" name="compressors[{{ $ri }}][{{ $col }}]" value="{{ $row[$col] ?? '' }}"></td>
                                    @endforeach
                                    <td><input type="number" name="compressors[{{ $ri }}][obciazenie]" value="{{ $row['obciazenie'] ?? '' }}" min="0" max="100" placeholder="%"></td>
                                    <td>
                                        <select name="compressors[{{ $ri }}][tryb]">
                                            <option value="">—</option>
                                            @foreach(['Ciągły','Sekwencyjny','Rezerwowy','Szczytowy'] as $tr)
                                                <option value="{{ $tr }}" {{ ($row['tryb'] ?? '') === $tr ? 'selected' : '' }}>{{ $tr }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="compressors[{{ $ri }}][sterowanie]">
                                            <option value="">—</option>
                                            @foreach(['On/Off','VSD','VFD','Kaskadowe','Centralne'] as $st)
                                                <option value="{{ $st }}" {{ ($row['sterowanie'] ?? '') === $st ? 'selected' : '' }}>{{ $st }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="compressors[{{ $ri }}][chlodzenie]">
                                            <option value="">—</option>
                                            @foreach(['Powietrzne','Wodne'] as $ch)
                                                <option value="{{ $ch }}" {{ ($row['chlodzenie'] ?? '') === $ch ? 'selected' : '' }}>{{ $ch }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="compressors[{{ $ri }}][recyrk]">
                                            <option value="">—</option>
                                            @foreach(['Tak','Nie','Planowane'] as $r)
                                                <option value="{{ $r }}" {{ ($row['recyrk'] ?? '') === $r ? 'selected' : '' }}>{{ $r }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><button type="button" class="btn-del-row" onclick="delRow(this)" title="Usuń wiersz">✕</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn-add-row" onclick="addRow()">+ Dodaj sprężarkę</button>
                <button type="button" class="btn-scan-nameplate" onclick="openScanModal()">
                    📷 Skanuj tabliczkę znamionową
                </button>
            </div>
        </div>

        {{-- NAMEPLATE SCAN OVERLAY --}}
        <div class="scan-overlay" id="scan-overlay">
            <div class="scan-modal" role="dialog" aria-label="Odczyt tabliczki">
                <button class="scan-close" onclick="closeScanModal()" aria-label="Zamknij">&times;</button>
                <h3>📷 Odczyt tabliczki znamionowej</h3>
                <p>Zrób zdjęcie tabliczki sprężarki lub wybierz plik z galerii.<br>
                   Asystent AI odczyta parametry i wypełni wiersz automatycznie.</p>

                <div class="scan-btns">
                    <button type="button" class="scan-btn" onclick="document.getElementById('scan-camera-input').click()">
                        <span class="scan-btn-icon">📷</span>
                        <span>Aparat</span>
                        <span class="scan-btn-label">Zrób zdjęcie</span>
                    </button>
                    <button type="button" class="scan-btn" onclick="document.getElementById('scan-gallery-input').click()">
                        <span class="scan-btn-icon">🖼️</span>
                        <span>Galeria / plik</span>
                        <span class="scan-btn-label">Wybierz z dysku</span>
                    </button>
                </div>

                <input type="file" id="scan-camera-input" accept="image/*" capture="environment"
                       style="display:none" onchange="scanFile(this)">
                <input type="file" id="scan-gallery-input" accept="image/*"
                       style="display:none" onchange="scanFile(this)">

                <div class="scan-loading" id="scan-loading">
                    <div style="font-size:32px; margin-bottom:8px;">&#x1F50D;</div>
                    Asystent AI analizuje zdjęcie&hellip;
                </div>
                <div class="scan-err" id="scan-err"></div>

                <div class="scan-result" id="scan-result">
                    <div class="scan-result-title">✅ Odczytane dane:</div>
                    <table id="scan-result-table"></table>
                    <button type="button" class="scan-apply-btn" onclick="applyScanToRow()">
                        ⬇ Wstaw do nowego wiersza tabeli
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══ ETAP 3 — Parametry pracy ═══ --}}
        <div class="cq-section" id="s3">
            <button type="button" class="cq-section-header" onclick="toggleSection('s3')">
                <span><span class="badge-num">3</span>Parametry pracy instalacji</span>
                <span class="chevron">▼</span>
            </button>
            <div class="cq-section-body">
                <div class="cq-grid">
                    <div class="cq-field">
                        <label class="cq-label">Ciśnienie robocze w sieci [bar]</label>
                        <div class="cq-hint">Ciśnienie utrzymywane w instalacji</div>
                        <input type="text" name="answers[E3-PCIS]" class="cq-input"
                               value="{{ old('answers.E3-PCIS', $answers['E3-PCIS'] ?? '') }}"
                               placeholder="np. 7 bar">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Minimalne ciśnienie u odbiorców [bar]</label>
                        <div class="cq-hint">Minimalne wymagane przez maszyny / narzędzia</div>
                        <input type="text" name="answers[E3-PMIN]" class="cq-input"
                               value="{{ old('answers.E3-PMIN', $answers['E3-PMIN'] ?? '') }}"
                               placeholder="np. 5.5 bar">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Jednostkowy pobór mocy (SFC) [kW/(m³/min)]</label>
                        <div class="cq-hint">Specific Power — z dokumentacji lub pomiarów</div>
                        <input type="text" name="answers[E3-SFC]" class="cq-input"
                               value="{{ old('answers.E3-SFC', $answers['E3-SFC'] ?? '') }}"
                               placeholder="np. 6.5 kW/(m³/min)">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Roczne zużycie energii elektrycznej [kWh/rok]</label>
                        <div class="cq-hint">Dane z podlicznika lub faktury</div>
                        <input type="text" name="answers[E3-EE]" class="cq-input"
                               value="{{ old('answers.E3-EE', $answers['E3-EE'] ?? '') }}"
                               placeholder="np. 450 000 kWh/rok">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Koszt energii elektrycznej [zł/kWh]</label>
                        <input type="text" name="answers[E3-KOST]" class="cq-input"
                               value="{{ old('answers.E3-KOST', $answers['E3-KOST'] ?? '') }}"
                               placeholder="np. 0.72 zł/kWh">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Czy jest podlicznik energii dla sprężarkowni?</label>
                        <select name="answers[E3-LICZNIK]" class="cq-select">
                            <option value="">— wybierz —</option>
                            @foreach(['Tak — oddzielny licznik','Tak — podlicznik','Nie — szacunek z faktur','Nie wiem'] as $v)
                                <option value="{{ $v }}" {{ (old('answers.E3-LICZNIK', $answers['E3-LICZNIK'] ?? '') === $v) ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Rzeczywista wydajność [Nm³/h lub m³/min]</label>
                        <div class="cq-hint">Całkowite zapotrzebowanie na sprężone powietrze</div>
                        <input type="text" name="answers[E3-WYDAJ]" class="cq-input"
                               value="{{ old('answers.E3-WYDAJ', $answers['E3-WYDAJ'] ?? '') }}"
                               placeholder="np. 120 Nm³/h lub 2 m³/min">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Ciśnienie zasilania sprężarki [bar] — na wejściu</label>
                        <div class="cq-hint">Ciśnienie atmosferyczne = ok. 1 bar(a)</div>
                        <input type="text" name="answers[E3-PIN]" class="cq-input"
                               value="{{ old('answers.E3-PIN', $answers['E3-PIN'] ?? '') }}"
                               placeholder="np. 1 bar(a)">
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ ETAP 3.5 — Zasilanie energetyczne ═══ --}}
        <div class="cq-section collapsed" id="s35">
            <button type="button" class="cq-section-header" onclick="toggleSection('s35')">
                <span><span class="badge-num">3.5</span>Zasilanie energetyczne sprężarkowni</span>
                <span class="chevron" style="transform:rotate(-90deg);">▼</span>
            </button>
            <div class="cq-section-body">
                <div class="cq-grid">
                    <div class="cq-field">
                        <label class="cq-label">Napięcie zasilania [V]</label>
                        <select name="answers[EZ-NAP]" class="cq-select">
                            <option value="">— wybierz —</option>
                            @foreach(['400V / 3-fazowe','690V / 3-fazowe','230V / 1-fazowe','inne'] as $v)
                                <option value="{{ $v }}" {{ (old('answers.EZ-NAP', $answers['EZ-NAP'] ?? '') === $v) ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Łączna moc zainstalowana sprężarek [kW]</label>
                        <input type="text" name="answers[EZ-PMOC]" class="cq-input"
                               value="{{ old('answers.EZ-PMOC', $answers['EZ-PMOC'] ?? '') }}"
                               placeholder="np. 225 kW (3×75 kW)">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Moc zamówiona / pobierana [kW lub kVA]</label>
                        <input type="text" name="answers[EZ-PMOW]" class="cq-input"
                               value="{{ old('answers.EZ-PMOW', $answers['EZ-PMOW'] ?? '') }}"
                               placeholder="np. 250 kVA">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Współczynnik mocy cosφ</label>
                        <input type="text" name="answers[EZ-COS]" class="cq-input"
                               value="{{ old('answers.EZ-COS', $answers['EZ-COS'] ?? '') }}"
                               placeholder="np. 0.85">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Roczny koszt energii sprężarkowni [zł/rok]</label>
                        <input type="text" name="answers[EZ-KOSTZ]" class="cq-input"
                               value="{{ old('answers.EZ-KOSTZ', $answers['EZ-KOSTZ'] ?? '') }}"
                               placeholder="np. 320 000 zł/rok">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Taryfa energetyczna</label>
                        <select name="answers[EZ-TAR]" class="cq-select">
                            <option value="">— wybierz —</option>
                            @foreach(['C11','C12a','C12b','C21','C22a','B11','B21','B22','inne / nie wiem'] as $v)
                                <option value="{{ $v }}" {{ (old('answers.EZ-TAR', $answers['EZ-TAR'] ?? '') === $v) ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ ETAP 4 — Uzdatnianie ═══ --}}
        <div class="cq-section collapsed" id="s4">
            <button type="button" class="cq-section-header" onclick="toggleSection('s4')">
                <span><span class="badge-num">4</span>Uzdatnianie sprężonego powietrza</span>
                <span class="chevron" style="transform:rotate(-90deg);">▼</span>
            </button>
            <div class="cq-section-body">
                <div class="cq-grid">
                    <div class="cq-field">
                        <label class="cq-label">Typ osuszacza</label>
                        <select name="answers[UZ-OSUSZ]" class="cq-select">
                            <option value="">— wybierz —</option>
                            @foreach(['Chłodniczy','Adsorpcyjny (bezzgrzewowy)','Adsorpcyjny (z podgrzewem)','Membranowy','Brak osuszacza','Nie wiem'] as $v)
                                <option value="{{ $v }}" {{ (old('answers.UZ-OSUSZ', $answers['UZ-OSUSZ'] ?? '') === $v) ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Punkt rosy [°C]</label>
                        <div class="cq-hint">Na wyjściu z osuszacza, np. +3°C (chłodniczy) lub -40°C (adsorpcyjny)</div>
                        <input type="text" name="answers[UZ-PROSY]" class="cq-input"
                               value="{{ old('answers.UZ-PROSY', $answers['UZ-PROSY'] ?? '') }}"
                               placeholder="np. +3°C">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Klasa czystości powietrza (ISO 8573)</label>
                        <input type="text" name="answers[UZ-ISO]" class="cq-input"
                               value="{{ old('answers.UZ-ISO', $answers['UZ-ISO'] ?? '') }}"
                               placeholder="np. 1.4.1">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Zużycie energii przez osuszacze [kWh/rok]</label>
                        <input type="text" name="answers[UZ-EE]" class="cq-input"
                               value="{{ old('answers.UZ-EE', $answers['UZ-EE'] ?? '') }}"
                               placeholder="np. 35 000 kWh/rok">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Pojemność zbiornika buforowego [m³ lub litrów]</label>
                        <input type="text" name="answers[UZ-ZBIOR]" class="cq-input"
                               value="{{ old('answers.UZ-ZBIOR', $answers['UZ-ZBIOR'] ?? '') }}"
                               placeholder="np. 1000 L / 1 m³">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Ciśnienie w zbiorniku buforowym [bar]</label>
                        <input type="text" name="answers[UZ-PZBIOR]" class="cq-input"
                               value="{{ old('answers.UZ-PZBIOR', $answers['UZ-PZBIOR'] ?? '') }}"
                               placeholder="np. 9 bar">
                    </div>
                    <div class="cq-field" style="grid-column: 1 / -1;">
                        <label class="cq-label">Inne elementy uzdatniania (filtry, separatory, itp.)</label>
                        <textarea name="answers[UZ-INNE]" class="cq-textarea"
                                  placeholder="np. filtr cząstek stałych F0.1µm, separator oleju, filtr węglowy...">{{ old('answers.UZ-INNE', $answers['UZ-INNE'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ ETAP 5 — Sieć dystrybucji ═══ --}}
        <div class="cq-section collapsed" id="s5">
            <button type="button" class="cq-section-header" onclick="toggleSection('s5')">
                <span><span class="badge-num">5</span>Sieć dystrybucji</span>
                <span class="chevron" style="transform:rotate(-90deg);">▼</span>
            </button>
            <div class="cq-section-body">
                <div class="cq-grid">
                    <div class="cq-field">
                        <label class="cq-label">Całkowita długość sieci [m]</label>
                        <input type="text" name="answers[SD-DLG]" class="cq-input"
                               value="{{ old('answers.SD-DLG', $answers['SD-DLG'] ?? '') }}"
                               placeholder="np. 850 m">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Wiek sieci / rok budowy</label>
                        <input type="text" name="answers[SD-ROK]" class="cq-input"
                               value="{{ old('answers.SD-ROK', $answers['SD-ROK'] ?? '') }}"
                               placeholder="np. 2005">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Materiał rur</label>
                        <select name="answers[SD-MAT]" class="cq-select">
                            <option value="">— wybierz —</option>
                            @foreach(['Stal czarna','Stal nierdzewna','Aluminium','PE (tworzywo sztuczne)','Miedź','Galwanizowana','Inne'] as $v)
                                <option value="{{ $v }}" {{ (old('answers.SD-MAT', $answers['SD-MAT'] ?? '') === $v) ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Czy przeprowadzano test nieszczelności (leak test)?</label>
                        <select name="answers[SD-LEAK]" class="cq-select">
                            <option value="">— wybierz —</option>
                            @foreach(['Tak — ostatnio (< 1 rok)','Tak — ponad 1 rok temu','Nigdy','Nie wiem'] as $v)
                                <option value="{{ $v }}" {{ (old('answers.SD-LEAK', $answers['SD-LEAK'] ?? '') === $v) ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Szacowany poziom nieszczelności [% produkcji]</label>
                        <div class="cq-hint">Typowo: nowe sieci 5-10%, stare sieci 20-40%</div>
                        <input type="text" name="answers[SD-NIESZ]" class="cq-input"
                               value="{{ old('answers.SD-NIESZ', $answers['SD-NIESZ'] ?? '') }}"
                               placeholder="np. 15%">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Czy jest monitoring ciśnienia w sieci?</label>
                        <select name="answers[SD-MON]" class="cq-select">
                            <option value="">— wybierz —</option>
                            @foreach(['Tak — ciągły monitoring','Tak — sporadyczne pomiary','Nie','Planowany'] as $v)
                                <option value="{{ $v }}" {{ (old('answers.SD-MON', $answers['SD-MON'] ?? '') === $v) ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cq-field" style="grid-column: 1 / -1;">
                        <label class="cq-label">Znane problemy z siecią / uwagi</label>
                        <textarea name="answers[SD-UW]" class="cq-textarea"
                                  placeholder="np. korozja w hali A, niskie ciśnienie u prasy nr 3...">{{ old('answers.SD-UW', $answers['SD-UW'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ ETAP 6 — Odbiorcy ═══ --}}
        <div class="cq-section collapsed" id="s6">
            <button type="button" class="cq-section-header" onclick="toggleSection('s6')">
                <span><span class="badge-num">6</span>Odbiorcy sprężonego powietrza</span>
                <span class="chevron" style="transform:rotate(-90deg);">▼</span>
            </button>
            <div class="cq-section-body">
                <div class="cq-grid">
                    <div class="cq-field">
                        <label class="cq-label">Główni odbiorcy (maszyny / procesy)</label>
                        <div class="cq-hint">Wymień najważniejsze urządzenia zużywające sprężone powietrze</div>
                        <textarea name="answers[OD-GLOWNI]" class="cq-textarea"
                                  placeholder="np. siłowniki pneumatyczne linii 1 (~80 Nm³/h), narzędzia ręczne (~20 Nm³/h), opakowania (~15 Nm³/h)...">{{ old('answers.OD-GLOWNI', $answers['OD-GLOWNI'] ?? '') }}</textarea>
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Całkowite zapotrzebowanie [Nm³/h]</label>
                        <div class="cq-hint">Suma zapotrzebowania wszystkich odbiorców</div>
                        <input type="text" name="answers[OD-ZAP]" class="cq-input"
                               value="{{ old('answers.OD-ZAP', $answers['OD-ZAP'] ?? '') }}"
                               placeholder="np. 180 Nm³/h">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Profil obciążenia (zmienność w ciągu dnia)</label>
                        <select name="answers[OD-PROF]" class="cq-select">
                            <option value="">— wybierz —</option>
                            @foreach(['Równomierne (±10%)','Umiarkowanie zmienne (±30%)','Bardzo zmienne (skoki > 50%)','Sezonowe'] as $v)
                                <option value="{{ $v }}" {{ (old('answers.OD-PROF', $answers['OD-PROF'] ?? '') === $v) ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Czy są odbiorcy wymagający różnych klas czystości?</label>
                        <textarea name="answers[OD-KLASY]" class="cq-textarea"
                                  placeholder="np. linia farmaceutyczna wymaga klasy 1.2.1, reszta 1.4.2...">{{ old('answers.OD-KLASY', $answers['OD-KLASY'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ ETAP 7 — Eksploatacja i zarządzanie ═══ --}}
        <div class="cq-section collapsed" id="s7">
            <button type="button" class="cq-section-header" onclick="toggleSection('s7')">
                <span><span class="badge-num">7</span>Eksploatacja i zarządzanie</span>
                <span class="chevron" style="transform:rotate(-90deg);">▼</span>
            </button>
            <div class="cq-section-body">
                <div class="cq-grid">
                    <div class="cq-field">
                        <label class="cq-label">Czy jest system sterowania sekwencyjnego?</label>
                        <select name="answers[EX-SEK]" class="cq-select">
                            <option value="">— wybierz —</option>
                            @foreach(['Tak — automatyczne','Tak — ręczne','Nie','Planowane'] as $v)
                                <option value="{{ $v }}" {{ (old('answers.EX-SEK', $answers['EX-SEK'] ?? '') === $v) ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Czy jest BMS/SCADA dla sprężarkowni?</label>
                        <select name="answers[EX-BMS]" class="cq-select">
                            <option value="">— wybierz —</option>
                            @foreach(['Tak','Częściowy monitoring','Nie','Planowany'] as $v)
                                <option value="{{ $v }}" {{ (old('answers.EX-BMS', $answers['EX-BMS'] ?? '') === $v) ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Planowany budżet na modernizację [PLN]</label>
                        <input type="text" name="answers[EX-BUDZ]" class="cq-input"
                               value="{{ old('answers.EX-BUDZ', $answers['EX-BUDZ'] ?? '') }}"
                               placeholder="np. 200 000 zł">
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Oczekiwany czas zwrotu inwestycji</label>
                        <select name="answers[EX-ROI]" class="cq-select">
                            <option value="">— wybierz —</option>
                            @foreach(['Do 1 roku','1-2 lata','2-3 lata','3-5 lat','Powyżej 5 lat','Nie wiem'] as $v)
                                <option value="{{ $v }}" {{ (old('answers.EX-ROI', $answers['EX-ROI'] ?? '') === $v) ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cq-field">
                        <label class="cq-label">Główny cel audytu (co chcesz osiągnąć?)</label>
                        <select name="answers[EX-CEL]" class="cq-select">
                            <option value="">— wybierz —</option>
                            @foreach(['Obniżenie kosztów energii','Poprawa niezawodności','Spełnienie wymogów prawnych','Uzyskanie białych certyfikatów','Przygotowanie do ISO 50001','Ocena stanu technicznego','Inne'] as $v)
                                <option value="{{ $v }}" {{ (old('answers.EX-CEL', $answers['EX-CEL'] ?? '') === $v) ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cq-field" style="grid-column: 1 / -1;">
                        <label class="cq-label">Dodatkowe uwagi, problemy, obserwacje</label>
                        <textarea name="answers[EX-UW]" rows="3" class="cq-textarea"
                                  placeholder="Opisz wszelkie znane problemy, plany, wcześniejsze działania optymalizacyjne...">{{ old('answers.EX-UW', $answers['EX-UW'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="cq-actions">
            <button type="submit" class="btn-primary" style="padding: 10px 28px; font-size: 15px;">
                ✓ Zapisz i przejdź do rozmowy z AI
            </button>
            <button type="button" onclick="saveDraft()" class="btn-secondary" style="padding: 10px 20px;">
                💾 Zapisz jako szkic
            </button>
            <a href="{{ route('strefa-klienta') }}" class="btn-outline" style="padding: 10px 18px;">
                ← Wróć do strefy klienta
            </a>
        </div>

    </form>

        </div>{{-- /.cq-main --}}
    </div>{{-- /.cq-layout --}}
    </div>{{-- /.cq-outer --}}

    <script>
        function toggleSection(id) {
            const el = document.getElementById(id);
            el.classList.toggle('collapsed');
        }

        function saveDraft() {
            document.getElementById('draft-flag').value = '1';
            document.getElementById('cq-form').submit();
        }

        // Compressors table — add / delete rows
        let rowCount = {{ count($answers['_compressors'] ?? [['placeholder']]) }};

        function addRow() {
            const tbody = document.getElementById('compressors-body');
            const ri = rowCount++;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="text-align:center;color:#6b8aa3;font-weight:700;">${tbody.rows.length + 1}</td>
                <td><input type="text" name="compressors[${ri}][nr_inw]"></td>
                <td><input type="text" name="compressors[${ri}][lokalizacja]"></td>
                <td><select name="compressors[${ri}][producent]">
                    <option value="">—</option>
                    ${['Atlas Copco','Kaeser','Boge','Ingersoll Rand','ABAC','CompAir','Gardner Denver','Mark','Mikropor','inny'].map(p=>`<option value="${p}">${p}</option>`).join('')}
                </select></td>
                <td><input type="text" name="compressors[${ri}][model]"></td>
                <td><select name="compressors[${ri}][typ]">
                    <option value="">—</option>
                    ${['Śrubowa','Tłokowa','Odśrodkowa','Spiralna (scroll)'].map(t=>`<option value="${t}">${t}</option>`).join('')}
                </select></td>
                <td><input type="text" name="compressors[${ri}][moc_kw]"></td>
                <td><input type="text" name="compressors[${ri}][wydajnosc]"></td>
                <td><input type="text" name="compressors[${ri}][pmax]"></td>
                <td><input type="text" name="compressors[${ri}][rok]"></td>
                <td><select name="compressors[${ri}][klasa_ie]">
                    <option value="">—</option>
                    ${['IE1','IE2','IE3','IE4','IE5','Nie wiem'].map(ie=>`<option value="${ie}">${ie}</option>`).join('')}
                </select></td>
                <td><select name="compressors[${ri}][stan]">
                    <option value="">—</option>
                    ${['Dobry','Przeciętny','Zły','Nieznany'].map(s=>`<option value="${s}">${s}</option>`).join('')}
                </select></td>
                <td><input type="text" name="compressors[${ri}][serwis]"></td>
                <td><input type="text" name="compressors[${ri}][motogodz]"></td>
                <td><input type="text" name="compressors[${ri}][godz_dobe]"></td>
                <td><input type="number" name="compressors[${ri}][obciazenie]" min="0" max="100" placeholder="%"></td>
                <td><select name="compressors[${ri}][tryb]">
                    <option value="">—</option>
                    ${['Ciągły','Sekwencyjny','Rezerwowy','Szczytowy'].map(t=>`<option value="${t}">${t}</option>`).join('')}
                </select></td>
                <td><select name="compressors[${ri}][sterowanie]">
                    <option value="">—</option>
                    ${['On/Off','VSD','VFD','Kaskadowe','Centralne'].map(s=>`<option value="${s}">${s}</option>`).join('')}
                </select></td>
                <td><select name="compressors[${ri}][chlodzenie]">
                    <option value="">—</option>
                    ${['Powietrzne','Wodne'].map(c=>`<option value="${c}">${c}</option>`).join('')}
                </select></td>
                <td><select name="compressors[${ri}][recyrk]">
                    <option value="">—</option>
                    ${['Tak','Nie','Planowane'].map(r=>`<option value="${r}">${r}</option>`).join('')}
                </select></td>
                <td><button type="button" class="btn-del-row" onclick="delRow(this)" title="Usuń">✕</button></td>
            `;
            tbody.appendChild(tr);
            updateRowNumbers();
        }

        function delRow(btn) {
            const row = btn.closest('tr');
            row.remove();
            updateRowNumbers();
        }

        function updateRowNumbers() {
            const rows = document.querySelectorAll('#compressors-body tr');
            rows.forEach((r, i) => {
                const first = r.querySelector('td:first-child');
                if (first) first.textContent = i + 1;
            });
        }

        // ─── Nameplate scanner ───────────────────────────────────────────────
        let _scanData = null;

        function openScanModal() {
            _scanData = null;
            document.getElementById('scan-loading').classList.remove('visible');
            document.getElementById('scan-err').classList.remove('visible');
            document.getElementById('scan-result').classList.remove('visible');
            document.getElementById('scan-result-table').innerHTML = '';
            document.getElementById('scan-overlay').classList.add('visible');
        }
        function closeScanModal() {
            document.getElementById('scan-overlay').classList.remove('visible');
            document.getElementById('scan-camera-input').value = '';
            document.getElementById('scan-gallery-input').value = '';
        }
        document.getElementById('scan-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeScanModal();
        });

        async function scanFile(input) {
            const file = input.files[0];
            if (!file) return;
            input.value = '';

            document.getElementById('scan-loading').classList.add('visible');
            document.getElementById('scan-err').classList.remove('visible');
            document.getElementById('scan-result').classList.remove('visible');

            const fd = new FormData();
            fd.append('file', file);
            fd.append('_token', '{{ csrf_token() }}');

            try {
                const res  = await fetch('{{ route('client.audit.compressor.scan', $audit) }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: fd,
                });
                const json = await res.json();
                document.getElementById('scan-loading').classList.remove('visible');

                if (!json.success || !json.data) {
                    const errEl = document.getElementById('scan-err');
                    errEl.textContent = json.error || 'Nie udało się odczytać tabliczki.';
                    errEl.classList.add('visible');
                    return;
                }

                _scanData = json.data;
                const labels = {
                    producent: 'Producent', model: 'Model', typ: 'Typ sprężarki',
                    moc_kw: 'Moc [kW]', wydajnosc: 'Wydajność', pmax: 'Pmax [bar]',
                    rok: 'Rok produkcji', klasa_ie: 'Klasa IE', nr_seryjny: 'Nr seryjny',
                    napiecie: 'Napięcie', prad: 'Prąd', predkosc: 'Prędkość obrotowa',
                    opis: 'Opis odczytu',
                };
                const tbl = document.getElementById('scan-result-table');
                tbl.innerHTML = '';
                for (const [k, label] of Object.entries(labels)) {
                    const v = json.data[k];
                    if (v && String(v) !== 'null') {
                        const tr = document.createElement('tr');
                        tr.innerHTML = '<td>' + label + '</td><td>' + String(v).replace(/</g, '&lt;') + '</td>';
                        tbl.appendChild(tr);
                    }
                }
                document.getElementById('scan-result').classList.add('visible');

            } catch(err) {
                document.getElementById('scan-loading').classList.remove('visible');
                const errEl = document.getElementById('scan-err');
                errEl.textContent = 'Błąd połączenia. Spróbuj ponownie.';
                errEl.classList.add('visible');
            }
        }

        function applyScanToRow() {
            if (!_scanData) return;
            addRow();
            const tbody = document.getElementById('compressors-body');
            const row   = tbody.lastElementChild;
            if (!row) { closeScanModal(); return; }

            function fill(fieldName, value) {
                if (!value || String(value) === 'null') return;
                const el = row.querySelector('[name$="[' + fieldName + ']"]');
                if (!el) return;
                if (el.tagName === 'SELECT') {
                    const opts = [...el.options];
                    const lower = String(value).toLowerCase();
                    const exact = opts.find(o => o.value.toLowerCase() === lower);
                    if (exact) { el.value = exact.value; return; }
                    const partial = opts.find(o =>
                        o.value !== '' && lower.includes(o.value.toLowerCase().slice(0, 5))
                    );
                    if (partial) { el.value = partial.value; return; }
                    const inny = opts.find(o => o.value === 'inny');
                    if (inny) el.value = 'inny';
                } else {
                    el.value = value;
                }
            }

            fill('producent', _scanData.producent);
            fill('model',     _scanData.model);
            fill('typ',       _scanData.typ);
            fill('moc_kw',    _scanData.moc_kw);
            fill('wydajnosc', _scanData.wydajnosc);
            fill('pmax',      _scanData.pmax);
            fill('rok',       _scanData.rok);
            fill('klasa_ie',  _scanData.klasa_ie);

            closeScanModal();
            updateProgress();
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            row.style.transition = 'background .5s';
            row.style.background = '#fef9c3';
            setTimeout(() => { row.style.background = ''; }, 2200);
        }

        // Progress tracking
        function updateProgress() {
            const inputs = document.querySelectorAll('#cq-form input:not([type=hidden]), #cq-form select, #cq-form textarea');
            let total = 0, filled = 0;
            inputs.forEach(el => {
                if (el.name && !el.name.includes('save_as_draft')) {
                    total++;
                    if (el.value && el.value.trim() !== '') filled++;
                }
            });
            const pct = total > 0 ? Math.round(filled / total * 100) : 0;
            document.getElementById('prog-fill').style.width = pct + '%';
            document.getElementById('prog-text').textContent = pct + '%';
        }

        document.getElementById('cq-form').addEventListener('input', updateProgress);
        updateProgress();

        // Sidenav: click → scroll to section + expand it
        document.querySelectorAll('.cq-sidenav-item').forEach(item => {
            item.addEventListener('click', () => {
                const targetId = item.dataset.target;
                const section = document.getElementById(targetId);
                if (!section) return;
                // Expand section if collapsed
                if (section.classList.contains('collapsed')) {
                    section.classList.remove('collapsed');
                }
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        // Scroll-spy: highlight active sidenav item
        const spySections = ['s0','s1','s2','s3','s35','s4','s5','s6','s7'];
        function updateActiveNav() {
            let current = spySections[0];
            spySections.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    const rect = el.getBoundingClientRect();
                    if (rect.top <= 140) current = id;
                }
            });
            document.querySelectorAll('.cq-sidenav-item').forEach(item => {
                item.classList.toggle('active', item.dataset.target === current);
            });
        }
        window.addEventListener('scroll', updateActiveNav, { passive: true });
        updateActiveNav();
    </script>

    <x-client-chat-float :chatMessages="$chatMessages" :companyId="$audit->company_id" />

</x-layouts.app>
