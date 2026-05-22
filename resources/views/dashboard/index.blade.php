<x-layouts.app>
    <style>
        .dashboard-header { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:4px; }
        .company-tiles { display:grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap:14px; margin-top:14px; }
        .company-tile {
            background:var(--paper-soft);
            border:1px solid var(--paper-deep);
            border-radius:8px;
            padding:20px;
            box-shadow:0 2px 8px rgba(26,77,58,.06);
            display:flex;
            flex-direction:column;
            gap:8px;
            transition:box-shadow .15s, background .15s;
        }
        .company-tile:hover { box-shadow:0 6px 18px rgba(26,77,58,.10); background:var(--green-bg); }
        .company-tile.has-inquiry { border-color:var(--gold); box-shadow:0 4px 20px rgba(168,127,42,.15); }
        .company-tile.has-unread-chat { border-color:var(--green-primary); background:var(--green-bg); }
        .company-tile.chat-incoming { animation:tile-chat-flash 1.2s ease-in-out 3; border-color:var(--green-primary); }
        @keyframes tile-chat-flash {
            0%,100% { box-shadow:0 2px 8px rgba(26,77,58,.06); border-color:var(--green-light); }
            50%      { box-shadow:0 0 0 4px rgba(46,125,92,.25); border-color:var(--green-primary); }
        }
        .co-row.chat-incoming-row td { animation:row-chat-flash 1.2s ease-in-out 3; }
        @keyframes row-chat-flash { 0%,100%{background:transparent;} 50%{background:var(--green-bg);} }
        .company-tile.has-offer-accepted { border-color:var(--green-primary); box-shadow:0 4px 20px rgba(46,125,92,.18); }
        .tile-header { display:flex; justify-content:space-between; align-items:flex-start; gap:8px; }
        .tile-name { font-size:15px; font-weight:700; color:var(--green-deep); line-height:1.3; font-family:var(--serif); }
        .tile-badge-action {
            flex-shrink:0; background:#fef3c7; border:1px solid var(--gold); color:#92400e;
            font-size:11px; font-weight:700; padding:3px 8px; border-radius:4px; white-space:nowrap; font-family:var(--mono);
        }
        .tile-meta { font-size:12px; color:var(--ink-mute); display:flex; flex-direction:column; gap:3px; }
        .tile-meta span { display:flex; align-items:center; gap:5px; }
        .tile-inquiry-alert {
            margin-top:4px; padding:8px 10px; background:#fef3c7; border:1px solid #fbbf24;
            border-radius:5px; color:#78350f; font-size:12px; font-weight:700;
        }
        .orphan-card {
            background:#fff7ed; border:1px solid #fed7aa; border-radius:6px; padding:14px 18px;
            margin-top:14px; display:flex; align-items:center; gap:12px; color:#7c2d12; font-size:13px; font-weight:600;
        }
        .pending-section {
            background:var(--paper-soft); border:2px solid var(--gold); border-radius:8px; padding:18px 20px; margin-top:14px;
        }
        .pending-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:8px; }
        .pending-header h2 { margin:0; font-size:16px; color:var(--gold); font-family:var(--serif); font-weight:600; }
        .pending-header p  { margin:4px 0 0; font-size:12px; color:var(--ink-mute); }
        .pending-badge {
            background:#fef3c7; border:1px solid var(--gold); color:#92400e;
            font-size:13px; font-weight:700; padding:4px 12px; border-radius:4px;
        }
        .btn-accept { padding:5px 12px; border:none; border-radius:5px; background:var(--green-primary); color:var(--paper); font-weight:700; font-size:12px; cursor:pointer; font-family:var(--sans); }
        .btn-accept:hover { background:var(--green-deep); }
        .btn-reject { padding:5px 12px; border:none; border-radius:5px; background:#dc2626; color:#fff; font-weight:700; font-size:12px; cursor:pointer; margin-left:4px; font-family:var(--sans); }
        .btn-reject:hover { background:#b91c1c; }
        .dash-section { margin-top:14px; border:1px solid var(--paper-deep); border-radius:8px; background:var(--paper-soft); overflow:hidden; }
        .dash-section-header { display:flex; justify-content:space-between; align-items:center; padding:12px 18px; cursor:pointer; user-select:none; gap:8px; background:var(--green-deep); color:var(--paper); }
        .dash-section-header:hover { background:var(--green-primary); }
        .dash-section.open .dash-section-header { background:var(--green-primary); }
        .dash-section-header h2 { margin:0; font-size:15px; font-weight:600; color:var(--paper); font-family:var(--sans); }
        .dash-section-body { display:none; padding:0 18px 18px; }
        .dash-section.open .dash-section-body { display:block; }
        .dash-chevron { font-size:12px; color:var(--green-light); transition:transform .2s; }
        .dash-section.open .dash-chevron { transform:rotate(180deg); }
        .co-controls { display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; padding:10px 0 4px; }
        .co-search-wrap { flex:1; min-width:180px; max-width:380px; position:relative; }
        .co-search-wrap input { width:100%; padding:8px 12px; border:1px solid var(--paper-deep); border-radius:5px; font-size:13px; background:white; box-sizing:border-box; outline:none; font-family:var(--sans); }
        .co-search-wrap input:focus { border-color:var(--green-primary); box-shadow:0 0 0 2px rgba(46,125,92,.12); }
        .co-view-btns { display:flex; gap:4px; }
        .co-view-btn { padding:7px 14px; border:1px solid var(--paper-deep); border-radius:5px; font-size:13px; font-weight:600; cursor:pointer; background:var(--paper-soft); color:var(--green-deep); transition:.15s; font-family:var(--sans); }
        .co-view-btn.active { background:var(--green-primary); color:var(--paper); border-color:transparent; }
        .co-tbl { width:100%; border-collapse:collapse; font-size:13px; }
        .co-tbl th { padding:9px 12px; font-size:11px; text-transform:uppercase; letter-spacing:.6px; color:var(--ink-mute); background:var(--paper-deep); border-bottom:2px solid var(--paper-deep); white-space:nowrap; cursor:pointer; user-select:none; font-family:var(--mono); }
        .co-tbl th.sortable:hover { background:var(--green-bg); color:var(--gold); }
        .co-tbl th.sort-asc::after { content:' ↑'; color:var(--gold); }
        .co-tbl th.sort-desc::after { content:' ↓'; color:var(--gold); }
        .co-tbl td { padding:9px 12px; border-bottom:1px solid var(--paper-deep); vertical-align:middle; }
        .co-tbl tbody tr:hover { background:var(--green-bg); }
        .co-row-link { color:var(--green-primary); font-weight:700; text-decoration:none; font-size:12px; }
        .co-row-link:hover { text-decoration:underline; color:var(--green-deep); }
        .co-status-badge { display:inline-block; font-size:11px; font-weight:700; padding:3px 8px; border-radius:4px; white-space:nowrap; font-family:var(--mono); }
        .st-accepted { background:var(--green-bg); color:var(--green-deep); }
        .st-inquiry  { background:#fef3c7; color:#92400e; }
        .st-chat     { background:#e0f2fe; color:#0369a1; }
        .co-empty { text-align:center; padding:24px; color:var(--ink-mute); font-size:13px; }
        .co-tile-item { display:flex; flex-direction:column; }
        .co-tile-item .company-tile { flex:1; }
        #co-tiles-view.list-mode .company-tiles { display:block; margin-top:0; }
        #co-tiles-view.list-mode .co-tile-item { margin:0; }
        #co-tiles-view.list-mode .company-tile {
            display:grid;
            grid-template-columns:minmax(220px, 2fr) minmax(280px, 3fr);
            align-items:center;
            gap:12px;
            padding:8px 12px;
            border-radius:0;
            border-left:none;
            border-right:none;
            box-shadow:none;
            background:#fff;
        }
        #co-tiles-view.list-mode .company-tile:hover { box-shadow:none; background:var(--green-bg); }
        #co-tiles-view.list-mode .tile-name { font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        #co-tiles-view.list-mode .tile-meta { flex-direction:row; gap:10px; align-items:center; white-space:nowrap; overflow:hidden; }
        #co-tiles-view.list-mode .tile-meta span { max-width:100%; overflow:hidden; text-overflow:ellipsis; }
        #dash-sec-clients-funnel .dash-section-header { padding:8px 12px; }
        #dash-sec-clients-funnel .dash-section-body { padding-top:10px; }
        .co-tile-assign-btn { border:none; border-top:1px solid var(--paper-deep); background:transparent; padding:8px 14px; cursor:pointer; font-size:12px; font-weight:700; color:var(--green-primary); display:flex; justify-content:space-between; align-items:center; width:100%; text-align:left; transition:background .15s; font-family:var(--sans); }
        .co-tile-assign-btn:hover { background:var(--green-bg); }
        .ai-summary-compact {
            margin-top:10px;
            padding:7px 10px;
            border:1px solid var(--paper-deep);
            border-radius:6px;
            background:var(--paper-soft);
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(130px, 1fr));
            gap:6px;
            font-family:var(--mono);
        }
        .ai-summary-compact .ai-item {
            font-size:10px;
            color:var(--ink-mute);
            line-height:1.35;
        }
        .ai-summary-compact .ai-item strong {
            color:var(--green-deep);
            font-size:11px;
        }
        .ai-token-badge {
            display:inline-flex; align-items:center; gap:3px; background:var(--green-bg);
            border:1px solid var(--green-light); color:var(--green-deep);
            font-size:10px; font-weight:700; padding:2px 6px; border-radius:3px; white-space:nowrap; font-family:var(--mono);
        }
        .ai-token-badge.zero { background:var(--paper-deep); border-color:var(--paper-deep); color:var(--ink-mute); }
    </style>

    <section class="panel">
        <div class="dashboard-header">
            <div>
                <h1 style="margin:0; font-family:var(--serif); color:var(--green-deep);">Dashboard</h1>
                <p class="muted" style="margin:4px 0 0; font-size:13px;">Przegląd firm klientów i nowych zapytań wymagających działania.</p>
            </div>
            <div style="background:var(--green-bg); border:1px solid var(--green-light); border-radius:5px; padding:8px 14px; font-size:13px; font-weight:700; color:var(--green-deep); font-family:var(--mono);">
                {{ $companies->count() }} {{ $companies->count() === 1 ? 'firma' : ($companies->count() < 5 ? 'firmy' : 'firm') }}
            </div>
        </div>



        @if ($orphanInquiries > 0)
            <div class="orphan-card">
                <span style="font-size:22px;">⚠️</span>
                <div>
                    <div>{{ $orphanInquiries }} {{ $orphanInquiries === 1 ? 'zapytanie' : ($orphanInquiries < 5 ? 'zapytania' : 'zapytań') }} bez przypisanej firmy wymaga uwagi.</div>
                    <a href="{{ route('strefa-klienta') }}" style="font-size:12px; color:#92400e; text-decoration:underline;">Zobacz wszystkie zapytania →</a>
                </div>
            </div>
        @endif



        {{-- Audit assignment is per-company (inside each company tile) --}}
        {{-- REMOVED global assign section --}}
        <div style="display:none" id="dash-sec-assign-audit">
            <div>
                <h2>⚡ Przydziel nowy audyt
                    @if($pendingAudits->isNotEmpty())
                        <span style="background:#dcfce7; color:#15803d; border:1px solid #86efac; border-radius:20px; padding:2px 10px; font-size:12px; font-weight:700; margin-left:8px;">{{ $pendingAudits->count() }} oczekujących</span>
                    @endif
                </h2>
                <span class="dash-chevron">▼</span>
            </div>
            <div class="dash-section-body">

                {{-- Auto-pending audits from accepted offers --}}
                @if($pendingAudits->isNotEmpty())
                <div style="margin-bottom:18px;">
                    <p style="font-size:13px; color:#4c6373; margin:0 0 12px;">Poniższe audyty zostały automatycznie utworzone po zaakceptowaniu ofert przez klientów. Zatwierdź nazwę i przydziel audytora.</p>
                    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:14px;">
                        @foreach($pendingAudits as $pa)
                        <div style="background:#f0fff4; border:2px solid #86efac; border-radius:14px; padding:16px 18px;">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
                                <div>
                                    <a href="{{ route('firma.show', $pa->company) }}" style="font-size:14px; font-weight:800; color:#0f2330; text-decoration:none;">{{ $pa->company->name ?? '—' }}</a>
                                    @if($pa->auditType)
                                        <span style="display:inline-block; margin-left:6px; background:#dcfce7; color:#15803d; border-radius:20px; padding:2px 10px; font-size:11px; font-weight:700;">{{ $pa->auditType->name }}</span>
                                    @endif
                                </div>
                                <span style="font-size:11px; color:#16a34a; font-weight:700; white-space:nowrap;">🕐 Oczekuje</span>
                            </div>
                            <form method="POST" action="{{ route('firma.approveAudit', [$pa->company, $pa]) }}">
                                @csrf
                                @method('PATCH')
                                <div style="display:grid; gap:8px;">
                                    <div>
                                        <label style="font-size:11px; font-weight:700; color:#374151; display:block; margin-bottom:4px;">Nazwa audytu *</label>
                                        <input type="text" name="title" value="{{ $pa->title }}" required
                                            style="width:100%; border:1px solid #d1fae5; border-radius:8px; padding:7px 10px; font-size:13px; background:#fff; box-sizing:border-box;">
                                    </div>
                                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                                        <div>
                                            <label style="font-size:11px; font-weight:700; color:#374151; display:block; margin-bottom:4px;">Agent Enesa *</label>
                                            <select name="agent_type" required style="width:100%; border:1px solid #d1fae5; border-radius:8px; padding:7px 10px; font-size:12px; background:#fff; box-sizing:border-box;">
                                                <optgroup label="Audyty energetyczne" data-category="energy">
                                                    <option value="general"                 {{ $pa->agent_type === 'general'                 ? 'selected' : '' }}>Audyt Energetyczny zakładu (Master)</option>
                                                    <option value="compressor_room"         {{ $pa->agent_type === 'compressor_room'         ? 'selected' : '' }}>Kompresory</option>
                                                    <option value="boiler_room"             {{ $pa->agent_type === 'boiler_room'             ? 'selected' : '' }}>Kotłownia</option>
                                                    <option value="drying_room"             {{ $pa->agent_type === 'drying_room'             ? 'selected' : '' }}>Suszarnia</option>
                                                    <option value="buildings"               {{ $pa->agent_type === 'buildings'               ? 'selected' : '' }}>Budynki</option>
                                                    <option value="technological_processes" {{ $pa->agent_type === 'technological_processes' ? 'selected' : '' }}>Procesy technologiczne</option>
                                                </optgroup>
                                                <optgroup label="ISO 50001" data-category="iso">
                                                    <option value="iso50001"                {{ $pa->agent_type === 'iso50001'                ? 'selected' : '' }}>ISO 50001</option>
                                                </optgroup>
                                                <optgroup label="Białe certyfikaty" data-category="white_cert">
                                                    <option value="bc_general"                 {{ $pa->agent_type === 'bc_general'                 ? 'selected' : '' }}>Ogólnie (BC)</option>
                                                    <option value="bc_compressor_room"         {{ $pa->agent_type === 'bc_compressor_room'         ? 'selected' : '' }}>Kompresory (BC)</option>
                                                    <option value="bc_boiler_room"             {{ $pa->agent_type === 'bc_boiler_room'             ? 'selected' : '' }}>Kotłownia (BC)</option>
                                                    <option value="bc_drying_room"             {{ $pa->agent_type === 'bc_drying_room'             ? 'selected' : '' }}>Suszarnia (BC)</option>
                                                    <option value="bc_buildings"               {{ $pa->agent_type === 'bc_buildings'               ? 'selected' : '' }}>Budynki (BC)</option>
                                                    <option value="bc_technological_processes" {{ $pa->agent_type === 'bc_technological_processes' ? 'selected' : '' }}>Procesy technologiczne (BC)</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="font-size:11px; font-weight:700; color:#374151; display:block; margin-bottom:4px;">Audytor</label>
                                            <select name="auditor_id" style="width:100%; border:1px solid #d1fae5; border-radius:8px; padding:7px 10px; font-size:12px; background:#fff; box-sizing:border-box;">
                                                <option value="">Brak przydziału</option>
                                                @foreach($auditors as $auditor)
                                                    <option value="{{ $auditor->id }}">{{ $auditor->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div style="display:flex; gap:8px; margin-top:2px;">
                                        <button type="submit" style="flex:1; padding:8px 14px; background:linear-gradient(130deg,#22c55e,#16a34a); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer;">✓ Zatwierdź audyt</button>
                                        <button type="button" onclick="rejectPendingAudit({{ $pa->id }}, '{{ addslashes($pa->title) }}')" style="padding:8px 12px; background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;">Odrzuć</button>
                                        <form id="reject-form-{{ $pa->id }}" method="POST" action="{{ route('firma.destroyAudit', [$pa->company, $pa]) }}" style="display:none;">@csrf @method('DELETE')</form>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                <hr style="border:none; border-top:1px solid #e8f1f8; margin:0 0 18px;">
                @else
                <p style="font-size:13px; color:#8a9bac; margin:0 0 16px;">Brak audytów oczekujących na zatwierdzenie. Możesz dodać audyt ręcznie poniżej.</p>
                @endif

                {{-- Manual new audit assignment --}}
                <div id="manual-assign-wrap" style="border:1px solid #d5e0ea; border-radius:12px; overflow:hidden;">
                    <button type="button" id="manual-assign-toggle-btn" onclick="toggleManualAssign()"
                        style="width:100%; border:none; background:#f8fbfd; padding:12px 16px; display:flex; justify-content:space-between; align-items:center; cursor:pointer; text-align:left; transition:background .15s;">
                        <span style="font-size:14px; font-weight:700; color:#1d3a50;">➕ Dodaj nowy audyt ręcznie</span>
                        <span id="manual-assign-icon" style="font-size:22px; font-weight:300; color:#0e89d8; line-height:1;">+</span>
                    </button>
                    <div id="manual-assign-panel" style="display:none; padding:16px 18px; border-top:1px solid #e8f1f8;">
                        <div style="margin-bottom:12px;">
                            <label style="font-size:12px; font-weight:700; color:#374151; display:block; margin-bottom:6px;">Firma *</label>
                            <select id="manual-assign-company" onchange="updateManualAssignAction(this)"
                                style="width:100%; max-width:340px; border:1px solid #d5e0ea; border-radius:8px; padding:8px 12px; font-size:13px;">
                                <option value="">— wybierz firmę —</option>
                                @foreach($companies as $mc)
                                    <option value="{{ $mc->id }}" data-url="{{ route('firma.storeAudit', $mc) }}">{{ $mc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <p style="margin:0 0 12px; font-size:13px; color:#4c6373;">Wybierz rodzaj audytu — kliknij kartę, uzupełnij nazwę i kliknij Przydziel.</p>
                        @if($auditTypes->isEmpty())
                            <p style="color:#8a9bac; font-size:13px; font-style:italic; margin:0 0 12px;">Brak zdefiniowanych rodzajów audytów.</p>
                        @else
                            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(160px, 1fr)); gap:10px; margin-bottom:14px;" id="manual-audit-type-cards">
                                @foreach($auditTypes as $type)
                                <button type="button"
                                    class="manual-audit-type-card"
                                    data-id="{{ $type->id }}"
                                    data-name="{{ $type->name }}"
                                    data-category="{{ $type->category }}"
                                    data-agent-type="{{ $type->agent_type ?? '' }}"
                                    onclick="selectManualAuditTypeCard(this)"
                                    style="border:2px solid #d5e0ea; border-radius:12px; padding:12px 14px; background:#f8fbfd; cursor:pointer; text-align:left; transition:.15s;">
                                    <div style="font-size:13px; font-weight:800; color:#0f2330; margin-bottom:4px;">{{ $type->name }}</div>
                                    <div style="font-size:11px; color:#6b8aa3;">{{ $type->sections->count() }} {{ $type->sections->count() === 1 ? 'sekcja' : ($type->sections->count() < 5 ? 'sekcje' : 'sekcji') }}</div>
                                </button>
                                @endforeach
                            </div>
                        @endif
                        <form method="POST" action="" id="manual-assign-form" style="display:none;">
                            @csrf
                            <input type="hidden" name="audit_type_id" id="manual-audit-type-id">
                            <div style="padding:12px 14px; border:2px solid #0e89d8; border-radius:12px; background:#f0f8ff; display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
                                <div style="font-size:13px; font-weight:700; color:#0f2330; flex:1 0 100%; margin-bottom:4px;">
                                    Wybrany typ: <span id="manual-audit-type-name" style="color:#0e89d8;"></span>
                                </div>
                                <div style="flex:2; min-width:200px;">
                                    <label style="font-size:11px; font-weight:700; color:#374151; display:block; margin-bottom:4px;">Nazwa audytu *</label>
                                    <input type="text" name="title" id="manual-assign-title" required placeholder="np. Audyt energetyczny 2026"
                                        style="width:100%; border:1px solid #c8d8e6; border-radius:8px; padding:7px 10px; font-size:13px; box-sizing:border-box;">
                                </div>
                                <div style="flex:1; min-width:180px;">
                                    <label style="font-size:11px; font-weight:700; color:#374151; display:block; margin-bottom:4px;">Agent Enesa *</label>
                                    <select name="agent_type" id="manual-assign-agent-type" required style="width:100%; border:1px solid #c8d8e6; border-radius:8px; padding:7px 10px; font-size:12px; box-sizing:border-box;">
                                        <option value="">— wybierz agenta —</option>
                                        <optgroup label="Audyty energetyczne" data-category="energy">
                                            <option value="general">Ogólnie</option>
                                            <option value="compressor_room">Kompresory</option>
                                            <option value="boiler_room">Kotłownia</option>
                                            <option value="drying_room">Suszarnia</option>
                                            <option value="buildings">Budynki</option>
                                            <option value="technological_processes">Procesy technologiczne</option>
                                        </optgroup>
                                        <optgroup label="ISO 50001" data-category="iso">
                                            <option value="iso50001">ISO 50001</option>
                                        </optgroup>
                                        <optgroup label="Białe certyfikaty" data-category="white_cert">
                                            <option value="bc_general">Ogólnie (BC)</option>
                                            <option value="bc_compressor_room">Kompresory (BC)</option>
                                            <option value="bc_boiler_room">Kotłownia (BC)</option>
                                            <option value="bc_drying_room">Suszarnia (BC)</option>
                                            <option value="bc_buildings">Budynki (BC)</option>
                                            <option value="bc_technological_processes">Procesy technologiczne (BC)</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div style="flex:1; min-width:160px;">
                                    <label style="font-size:11px; font-weight:700; color:#374151; display:block; margin-bottom:4px;">Audytor</label>
                                    <select name="auditor_id" style="width:100%; border:1px solid #c8d8e6; border-radius:8px; padding:7px 10px; font-size:12px; box-sizing:border-box;">
                                        <option value="">Brak</option>
                                        @foreach($auditors as $auditor)
                                            <option value="{{ $auditor->id }}">{{ $auditor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="display:flex; gap:6px; align-self:flex-end;">
                                    <button type="submit" style="padding:8px 16px; background:linear-gradient(130deg,#0e89d8,#0772b5); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer;">Przydziel audyt</button>
                                    <button type="button" onclick="clearManualAuditTypeSelection()" style="padding:8px 12px; background:#f8fbfd; border:1px solid #d5e0ea; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; color:#4c6373;">Zmień typ</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>{{-- /dash-sec-assign-audit --}}

        @if ($companies->isEmpty() && $pendingRegistrations->isEmpty())
            <div style="padding:32px; text-align:center; color:#9ab4c5; border:1px dashed #d5e0ea; border-radius:12px; margin-top:14px; font-size:14px;">
                Brak klientów do wyświetlenia.
            </div>
        @else
            <div class="dash-section open" id="dash-sec-clients-funnel">
                <div class="dash-section-header" onclick="dashToggle('dash-sec-clients-funnel')">
                    <h2>🏢 Podział klientów</h2>
                    <div style="display:flex; align-items:center; gap:8px;" onclick="event.stopPropagation();">
                        <div class="co-search-wrap" style="width:260px; max-width:260px;">
                            <input type="search" id="co-search" placeholder="Szukaj firmy lub miasta..." oninput="filterCompanies(this.value)" autocomplete="off">
                        </div>
                        <div class="co-view-btns">
                            <button id="co-btn-tiles" type="button" class="co-view-btn active" onclick="setCoView('tiles'); event.stopPropagation();">⊞ Kafelki</button>
                            <button id="co-btn-list" type="button" class="co-view-btn" onclick="setCoView('list'); event.stopPropagation();">☰ Lista</button>
                        </div>
                        <span style="background:#e0f2fe; border:1px solid #bae6fd; color:#0369a1; font-size:11px; font-weight:700; padding:3px 8px; border-radius:6px;">
                            {{ $companies->count() + $pendingRegistrations->count() }} łącznie
                        </span>
                        <span class="dash-chevron">▼</span>
                    </div>
                </div>
                <div class="dash-section-body">
                    <div id="co-tiles-view" style="display:flex; flex-direction:column; gap:12px;">
                        <div>
                            <div style="background:var(--green-primary); color:var(--paper); border-radius:8px; padding:7px 11px; font-size:13px; font-weight:800; margin-bottom:8px;">Klienci z audytami w toku ({{ $salesFunnel['audits_in_progress']->count() }})</div>
                            <div class="company-tiles">
                                @forelse ($salesFunnel['audits_in_progress'] as $company)
                                    @php $coSearch = strtolower($company->name . ' ' . ($company->city ?? '')); @endphp
                                    <div class="co-tile-item" data-search="{{ $coSearch }}" data-company-id="{{ $company->id }}" data-unread="0">
                                        <a href="{{ route('firma.show', $company) }}" class="company-tile" style="text-decoration:none; color:inherit;">
                                            <div class="tile-header"><span class="tile-name">{{ $company->name }}</span></div>
                                            <div class="tile-meta">
                                                @if ($company->city)<span>📍 {{ $company->city }}</span>@endif
                                                <span>📋 {{ $company->energyAudits->count() }} {{ $company->energyAudits->count() === 1 ? 'audyt' : ($company->energyAudits->count() < 5 ? 'audyty' : 'audytów') }}</span>
                                            </div>
                                        </a>
                                    </div>
                                @empty
                                    <div class="co-empty" style="display:block;">Brak klientów w tej sekcji.</div>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <div style="background:var(--gold); color:var(--paper); border-radius:8px; padding:7px 11px; font-size:13px; font-weight:800; margin-bottom:8px;">Klienci z ofertą ({{ $salesFunnel['offer_sent']->count() }})</div>
                            <div class="company-tiles">
                                @forelse ($salesFunnel['offer_sent'] as $company)
                                    @php $coSearch = strtolower($company->name . ' ' . ($company->city ?? '')); @endphp
                                    <div class="co-tile-item" data-search="{{ $coSearch }}" data-company-id="{{ $company->id }}" data-unread="0">
                                        <a href="{{ route('firma.show', $company) }}" class="company-tile" style="text-decoration:none; color:inherit;">
                                            <div class="tile-header"><span class="tile-name">{{ $company->name }}</span></div>
                                            <div class="tile-meta">@if ($company->city)<span>📍 {{ $company->city }}</span>@endif</div>
                                        </a>
                                    </div>
                                @empty
                                    <div class="co-empty" style="display:block;">Brak klientów w tej sekcji.</div>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <div style="background:var(--green-deep); color:var(--paper); border-radius:8px; padding:7px 11px; font-size:13px; font-weight:800; margin-bottom:8px;">Klienci nowe leady ({{ $salesFunnel['leads']->count() }})</div>
                            <div class="company-tiles">
                                @forelse ($salesFunnel['leads'] as $company)
                                    @php $coSearch = strtolower($company->name . ' ' . ($company->city ?? '')); @endphp
                                    <div class="co-tile-item" data-search="{{ $coSearch }}" data-company-id="{{ $company->id }}" data-unread="0">
                                        <a href="{{ route('firma.show', $company) }}" class="company-tile" style="text-decoration:none; color:inherit;">
                                            <div class="tile-header"><span class="tile-name">{{ $company->name }}</span></div>
                                            <div class="tile-meta">@if ($company->city)<span>📍 {{ $company->city }}</span>@endif</div>
                                        </a>
                                    </div>
                                @empty
                                    <div class="co-empty" style="display:block;">Brak klientów w tej sekcji.</div>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <div style="background:var(--ink-mute); color:var(--paper); border-radius:8px; padding:7px 11px; font-size:13px; font-weight:800; margin-bottom:8px;">Klienci oczekujący na dołączenie do systemu ({{ $pendingRegistrations->count() }})</div>
                            <div class="company-tiles">
                                @forelse ($pendingRegistrations as $reg)
                                    @php $coSearch = strtolower($reg->name . ' ' . ($reg->city ?? '')); @endphp
                                    <div class="co-tile-item" data-search="{{ $coSearch }}" data-unread="0">
                                        <div class="company-tile">
                                            <div class="tile-header"><span class="tile-name">{{ $reg->name }}</span></div>
                                            <div class="tile-meta">
                                                @if ($reg->city)<span>📍 {{ $reg->city }}</span>@endif
                                                <span>🕒 {{ $reg->created_at->format('d.m.Y H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="co-empty" style="display:block;">Brak oczekujących zgłoszeń.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div id="co-tiles-empty" class="co-empty" style="display:none; margin-top:10px;">Brak wyników dla wpisanej frazy.</div>
                </div>{{-- /dash-section-body --}}
            </div>{{-- /dash-section --}}
        @endif

        @if ($aiSummary['total'] > 0)
            <div class="ai-summary-compact">
                <div class="ai-item"><strong>{{ number_format($aiSummary['total']) }}</strong><br>tokeny AI</div>
                <div class="ai-item"><strong>{{ number_format($aiSummary['input']) }}</strong><br>wejściowe</div>
                <div class="ai-item"><strong>{{ number_format($aiSummary['output']) }}</strong><br>wyjściowe</div>
                <div class="ai-item"><strong>{{ number_format($aiSummary['cost_pln'], 2) }} zł</strong><br>koszt szacunkowy</div>
                <div class="ai-item"><strong>${{ number_format($aiSummary['cost_usd'], 4) }}</strong><br>USD</div>
            </div>
        @endif
    </section>

<script>
function dashToggle(id) {
    document.getElementById(id).classList.toggle('open');
}
// Reload on back-button (bfcache) so unread counts are fresh
window.addEventListener('pageshow', function(e) {
    if (e.persisted) location.reload();
});

// ── View toggle (persisted in localStorage) ──────────────────
var coCurrentView = localStorage.getItem('dashboard-co-view') || 'tiles';

function setCoView(v) {
    coCurrentView = v;
    localStorage.setItem('dashboard-co-view', v);
    var tilesEl = document.getElementById('co-tiles-view');
    var btnTiles = document.getElementById('co-btn-tiles');
    var btnList = document.getElementById('co-btn-list');
    if (!tilesEl) return;
    tilesEl.classList.toggle('list-mode', v === 'list');
    btnTiles.classList.toggle('active', v === 'tiles');
    if (btnList) btnList.classList.toggle('active', v === 'list');
    var q = document.getElementById('co-search');
    if (q) filterCompanies(q.value);
}

// ── Live search ───────────────────────────────────────────────
function filterCompanies(q) {
    q = (q || '').toLowerCase().trim();
    var tiles = document.querySelectorAll('.co-tile-item');
    var tileCount = 0;
    tiles.forEach(function(el) {
        var show = !q || el.dataset.search.includes(q);
        el.style.display = show ? '' : 'none';
        if (show) tileCount++;
    });
    var tilesEmpty = document.getElementById('co-tiles-empty');
    if (tilesEmpty) tilesEmpty.style.display = tileCount === 0 ? '' : 'none';
}

// ── Init ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    setCoView(coCurrentView);
});

// ── Real-time chat notifications ──────────────────────────────
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // Seed known counts from server-rendered data
    const _knownCounts = {};
    document.querySelectorAll('[data-company-id]').forEach(function (el) {
        const id = el.dataset.companyId;
        if (id && !_knownCounts[id]) {
            _knownCounts[id] = parseInt(el.dataset.unread || '0', 10);
        }
    });

    let _origTitle = document.title;
    let _titleInterval = null;

    function flashTitle(msg) {
        let on = true;
        clearInterval(_titleInterval);
        _titleInterval = setInterval(function () {
            document.title = on ? msg : _origTitle;
            on = !on;
        }, 900);
        setTimeout(function () {
            clearInterval(_titleInterval);
            document.title = _origTitle;
        }, 12000);
    }

    function pollDashboardChat() {
        fetch('{{ route('dashboard.chat.unread') }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            const byCompany = data.by_company || {};
            let anyNew = false;

            // Detect new messages per company
            const allIds = new Set([
                ...Object.keys(_knownCounts),
                ...Object.keys(byCompany),
            ]);
            allIds.forEach(function (id) {
                const newCount = parseInt(byCompany[id] || 0, 10);
                const oldCount = _knownCounts[id] || 0;

                if (newCount > oldCount) {
                    anyNew = true;

                    // Flash tile
                    const tileWrap = document.querySelector('.co-tile-item[data-company-id="' + id + '"]');
                    if (tileWrap) {
                        const tileCard = tileWrap.querySelector('.company-tile');
                        if (tileCard) {
                            tileCard.classList.add('has-unread-chat', 'chat-incoming');
                            tileCard.addEventListener('animationend', function () {
                                tileCard.classList.remove('chat-incoming');
                            }, { once: true });
                        }
                        tileWrap.dataset.unread = String(newCount);
                        // Update unread label inside tile
                        const unreadSpan = tileWrap.querySelector('[data-unread-label]');
                        if (unreadSpan) {
                            unreadSpan.textContent = '💬 ' + newCount + ' ' + (newCount === 1 ? 'nowa wiadomość' : (newCount < 5 ? 'nowe wiadomości' : 'nowych wiadomości'));
                            unreadSpan.style.display = '';
                        }
                    }

                }

                _knownCounts[id] = newCount;
            });

            if (anyNew) {
                flashTitle('💬 Nowa wiadomość!');
            }
        })
        .catch(function () {});
    }

    setInterval(pollDashboardChat, 8000);
})();

// ── Per-company assign panel ──────────────────────────────────
function rejectPendingAudit(id, title) {
    if (confirm('Odrzucić i usunąć audyt „' + title + '"? Tej operacji nie można cofnąć.')) {
        document.getElementById('reject-form-' + id).submit();
    }
}
</script>
</x-layouts.app>

