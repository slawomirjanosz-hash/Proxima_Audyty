<x-layouts.app>
    <style>
        .dashboard-header { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:4px; }
        .company-tiles { display:grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap:14px; margin-top:14px; }
        .company-tile {
            background:#fff;
            border:1px solid #d5e0ea;
            border-radius:16px;
            padding:20px;
            box-shadow:0 4px 16px rgba(14,55,85,.05);
            display:flex;
            flex-direction:column;
            gap:8px;
            transition:box-shadow .15s;
        }
        .company-tile:hover { box-shadow:0 8px 24px rgba(14,55,85,.1); }
        .company-tile.has-inquiry {
            border-color:#f59e0b;
            box-shadow:0 4px 20px rgba(245,158,11,.15);
        }
        .company-tile.has-unread-chat {
            border-color: #7dd3fc;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        }
        .company-tile.chat-incoming {
            animation: tile-chat-flash 1.2s ease-in-out 3;
            border-color: #0e89d8;
        }
        @keyframes tile-chat-flash {
            0%,100% { box-shadow: 0 4px 16px rgba(14,55,85,.05); border-color:#7dd3fc; }
            50%      { box-shadow: 0 0 0 4px rgba(14,137,216,.35), 0 4px 24px rgba(14,137,216,.3); border-color:#0e89d8; }
        }
        .co-row.chat-incoming-row td {
            animation: row-chat-flash 1.2s ease-in-out 3;
        }
        @keyframes row-chat-flash {
            0%,100% { background: transparent; }
            50%      { background: #dbeafe; }
        }
        .company-tile.has-offer-accepted {
            border-color:#16a34a;
            box-shadow:0 4px 20px rgba(22,163,74,.18);
        }
        .tile-header { display:flex; justify-content:space-between; align-items:flex-start; gap:8px; }
        .tile-name { font-size:15px; font-weight:800; color:#0f2330; line-height:1.3; }
        .tile-badge-action {
            flex-shrink:0;
            background:#fef3c7;
            border:1px solid #f59e0b;
            color:#92400e;
            font-size:11px;
            font-weight:800;
            padding:3px 8px;
            border-radius:6px;
            white-space:nowrap;
        }
        .tile-meta { font-size:12px; color:#4c6373; display:flex; flex-direction:column; gap:3px; }
        .tile-meta span { display:flex; align-items:center; gap:5px; }
        .tile-inquiry-alert {
            margin-top:4px;
            padding:8px 10px;
            background:#fef3c7;
            border:1px solid #fbbf24;
            border-radius:8px;
            color:#78350f;
            font-size:12px;
            font-weight:700;
        }
        .orphan-card {
            background:#fff7ed;
            border:1px solid #fed7aa;
            border-radius:14px;
            padding:14px 18px;
            margin-top:14px;
            display:flex;
            align-items:center;
            gap:12px;
            color:#7c2d12;
            font-size:13px;
            font-weight:600;
        }
        .pending-section {
            background:#fff;
            border:2px solid #fbbf24;
            border-radius:14px;
            padding:18px 20px;
            margin-top:14px;
        }
        .pending-header {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:14px;
            flex-wrap:wrap;
            gap:8px;
        }
        .pending-header h2 { margin:0; font-size:16px; color:#78350f; }
        .pending-header p  { margin:4px 0 0; font-size:12px; color:#92400e; }
        .pending-badge {
            background:#fef3c7;
            border:1px solid #f59e0b;
            color:#92400e;
            font-size:13px;
            font-weight:800;
            padding:4px 12px;
            border-radius:8px;
        }
        .btn-accept {
            padding:5px 12px;
            border:none;
            border-radius:7px;
            background:#16a34a;
            color:#fff;
            font-weight:700;
            font-size:12px;
            cursor:pointer;
        }
        .btn-accept:hover { background:#15803d; }
        .btn-reject {
            padding:5px 12px;
            border:none;
            border-radius:7px;
            background:#dc2626;
            color:#fff;
            font-weight:700;
            font-size:12px;
            cursor:pointer;
            margin-left:4px;
        }
        .btn-reject:hover { background:#b91c1c; }
        .dash-section {
            margin-top:14px;
            border:1px solid #d5e0ea;
            border-radius:14px;
            background:#fff;
            overflow:hidden;
        }
        .dash-section-header {
            display:flex; justify-content:space-between; align-items:center;
            padding:12px 18px; cursor:pointer; user-select:none;
            gap:8px;
        }
        .dash-section-header:hover { background:#f7fbff; }
        .dash-section-header h2 { margin:0; font-size:15px; font-weight:800; color:#0f2330; }
        .dash-section-body { display:none; padding:0 18px 18px; }
        .dash-section.open .dash-section-body { display:block; }
        .dash-chevron { font-size:12px; color:#6b8aa3; transition:transform .2s; }
        .dash-section.open .dash-chevron { transform:rotate(180deg); }
        /* ── Company controls ─────────────────────────────────────── */
        .co-controls { display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; padding:10px 0 4px; }
        .co-search-wrap { flex:1; min-width:180px; max-width:380px; position:relative; }
        .co-search-wrap::before { content:'🔍'; position:absolute; left:10px; top:50%; transform:translateY(-50%); font-size:12px; pointer-events:none; }
        .co-search-wrap input { width:100%; padding:8px 12px 8px 32px; border:1px solid #c8d8e6; border-radius:9px; font-size:13px; background:#f8fbfd; box-sizing:border-box; outline:none; }
        .co-search-wrap input:focus { border-color:#0e89d8; background:#fff; }
        .co-view-btns { display:flex; gap:4px; }
        .co-view-btn { padding:7px 14px; border:1px solid #c8d8e6; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; background:#f8fbfd; color:#1d4f73; transition:.15s; }
        .co-view-btn.active { background:linear-gradient(130deg,#1ba84a,#0e89d8); color:#fff; border-color:transparent; }
        /* ── Company table ────────────────────────────────────────── */
        .co-tbl { width:100%; border-collapse:collapse; font-size:13px; }
        .co-tbl th { padding:9px 12px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:#4c6373; background:#f3f8fd; border-bottom:2px solid #d5e0ea; white-space:nowrap; cursor:pointer; user-select:none; }
        .co-tbl th.sortable:hover { background:#e8f1fb; color:#0e89d8; }
        .co-tbl th.sort-asc::after { content:' ↑'; color:#0e89d8; }
        .co-tbl th.sort-desc::after { content:' ↓'; color:#0e89d8; }
        .co-tbl td { padding:9px 12px; border-bottom:1px solid #eaf1f7; vertical-align:middle; }
        .co-tbl tbody tr:hover { background:#f0f7ff; }
        .co-row-link { color:#0e89d8; font-weight:700; text-decoration:none; font-size:12px; }
        .co-row-link:hover { text-decoration:underline; }
        .co-status-badge { display:inline-block; font-size:11px; font-weight:700; padding:3px 8px; border-radius:6px; white-space:nowrap; }
        .st-accepted { background:#d1fae5; color:#065f46; }
        .st-inquiry  { background:#fef3c7; color:#92400e; }
        .st-chat     { background:#e0f2fe; color:#0369a1; }
        .co-empty { text-align:center; padding:24px; color:#9ab4c5; font-size:13px; }
        /* ── Per-company assign panel ─────────────────────────────── */
        .co-tile-item { display:flex; flex-direction:column; }
        .co-tile-item .company-tile { flex:1; }
        .co-tile-assign-btn { border:none; border-top:1px solid #e8f1f8; background:transparent; padding:8px 14px; cursor:pointer; font-size:12px; font-weight:700; color:#0e89d8; display:flex; justify-content:space-between; align-items:center; width:100%; text-align:left; transition:background .15s; }
        .co-tile-assign-btn:hover { background:#f0f8ff; }
        /* ── AI token stats ───────────────────────────────────────── */
        .ai-summary-bar {
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap:10px;
            margin-top:14px;
            padding:14px 16px;
            background:linear-gradient(135deg,#f0f4ff 0%,#e8f5e9 100%);
            border:1px solid #c7d8f5;
            border-radius:14px;
        }
        .ai-stat { text-align:center; }
        .ai-stat-val { font-size:18px; font-weight:800; color:#1a3a5c; line-height:1.2; }
        .ai-stat-lbl { font-size:11px; color:#5a7390; margin-top:2px; }
        .ai-token-badge {
            display:inline-flex;
            align-items:center;
            gap:3px;
            background:#eef2ff;
            border:1px solid #c7d4f8;
            color:#3a4dbf;
            font-size:10px;
            font-weight:700;
            padding:2px 6px;
            border-radius:5px;
            white-space:nowrap;
        }
        .ai-token-badge.zero { background:#f5f5f5; border-color:#e0e0e0; color:#bbb; }
    </style>

    <section class="panel">
        <div class="dashboard-header">
            <div>
                <h1 style="margin:0;">Dashboard</h1>
                <p class="muted" style="margin:4px 0 0; font-size:13px;">Przegląd firm klientów i nowych zapytań wymagających działania.</p>
            </div>
            <div style="background:#eaf5ff; border:1px solid #cae3f6; border-radius:10px; padding:8px 14px; font-size:13px; font-weight:700; color:#145086;">
                {{ $companies->count() }} {{ $companies->count() === 1 ? 'firma' : ($companies->count() < 5 ? 'firmy' : 'firm') }}
            </div>
        </div>

        @if ($aiSummary['total'] > 0)
        <div class="ai-summary-bar">
            <div class="ai-stat">
                <div class="ai-stat-val">{{ number_format($aiSummary['total']) }}</div>
                <div class="ai-stat-lbl">🤖 Tokeny AI łącznie</div>
            </div>
            <div class="ai-stat">
                <div class="ai-stat-val">{{ number_format($aiSummary['input']) }}</div>
                <div class="ai-stat-lbl">⬆ Tokeny wejściowe</div>
            </div>
            <div class="ai-stat">
                <div class="ai-stat-val">{{ number_format($aiSummary['output']) }}</div>
                <div class="ai-stat-lbl">⬇ Tokeny wyjściowe</div>
            </div>
            <div class="ai-stat">
                <div class="ai-stat-val">${{ number_format($aiSummary['cost_usd'], 4) }}</div>
                <div class="ai-stat-lbl">💵 Koszt USD (szacunek)</div>
            </div>
            <div class="ai-stat">
                <div class="ai-stat-val">{{ number_format($aiSummary['cost_pln'], 2) }} zł</div>
                <div class="ai-stat-lbl">💰 Koszt PLN (~3,85 USD/PLN)</div>
            </div>
            <div class="ai-stat" style="align-self:center;">
                <div style="font-size:10px; color:#8a9bbf; line-height:1.5;">
                    Model: Claude Haiku 4.5<br>
                    $0.80/1M in · $4.00/1M out
                </div>
            </div>
        </div>
        @endif

        @if ($orphanInquiries > 0)
            <div class="orphan-card">
                <span style="font-size:22px;">⚠️</span>
                <div>
                    <div>{{ $orphanInquiries }} {{ $orphanInquiries === 1 ? 'zapytanie' : ($orphanInquiries < 5 ? 'zapytania' : 'zapytań') }} bez przypisanej firmy wymaga uwagi.</div>
                    <a href="{{ route('strefa-klienta') }}" style="font-size:12px; color:#92400e; text-decoration:underline;">Zobacz wszystkie zapytania →</a>
                </div>
            </div>
        @endif

        @if ($pendingRegistrations->isNotEmpty())
            <div class="dash-section" id="dash-sec-registrations">
                <div class="dash-section-header" onclick="dashToggle('dash-sec-registrations')">
                    <h2>⏳ Oczekujące rejestracje firm</h2>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="pending-badge">{{ $pendingRegistrations->count() }} {{ $pendingRegistrations->count() === 1 ? 'wniosek' : ($pendingRegistrations->count() < 5 ? 'wnioski' : 'wniosków') }}</span>
                        <span class="dash-chevron">▼</span>
                    </div>
                </div>
                <div class="dash-section-body">
                <p style="margin:0 0 10px; font-size:12px; color:#92400e;">Firmy, które złożyły wniosek rejestracyjny — zaakceptuj lub odrzuć każdy wniosek.</p>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Firma</th>
                            <th>NIP</th>
                            <th>Miasto</th>
                            <th>Telefon</th>
                            <th>E-mail</th>
                            <th>Data zgłoszenia</th>
                            <th style="width:160px;">Akcja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingRegistrations as $reg)
                            <tr>
                                <td style="font-weight:700;">{{ $reg->name }}</td>
                                <td style="font-family:monospace; font-size:13px;">{{ $reg->nip }}</td>
                                <td>{{ $reg->city ?? '—' }}</td>
                                <td>{{ $reg->phone }}</td>
                                <td>{{ $reg->email }}</td>
                                <td style="font-size:12px; color:#5a7390;">{{ $reg->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('register.accept', $reg->id) }}" style="display:inline">
                                        @csrf
                                        <button type="submit" class="btn-accept" onclick="return confirm('Dodać firmę {{ addslashes($reg->name) }} do systemu?')">✅ Akceptuj</button>
                                    </form>
                                    <form method="POST" action="{{ route('register.destroy', $reg->id) }}" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-reject" onclick="return confirm('Odrzucić wniosek firmy {{ addslashes($reg->name) }}?')">🗑 Usuń</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>{{-- /dash-section-body --}}
            </div>{{-- /dash-section --}}
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
                                            <label style="font-size:11px; font-weight:700; color:#374151; display:block; margin-bottom:4px;">Agent AI *</label>
                                            <select name="agent_type" required style="width:100%; border:1px solid #d1fae5; border-radius:8px; padding:7px 10px; font-size:12px; background:#fff; box-sizing:border-box;">
                                                <optgroup label="Audyty energetyczne" data-category="energy">
                                                    <option value="general"                 {{ $pa->agent_type === 'general'                 ? 'selected' : '' }}>Ogólnie</option>
                                                    <option value="compressor_room"         {{ $pa->agent_type === 'compressor_room'         ? 'selected' : '' }}>Sprężarkownia</option>
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
                                                    <option value="bc_compressor_room"         {{ $pa->agent_type === 'bc_compressor_room'         ? 'selected' : '' }}>Sprężarkownia (BC)</option>
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
                                    <label style="font-size:11px; font-weight:700; color:#374151; display:block; margin-bottom:4px;">Agent AI *</label>
                                    <select name="agent_type" id="manual-assign-agent-type" required style="width:100%; border:1px solid #c8d8e6; border-radius:8px; padding:7px 10px; font-size:12px; box-sizing:border-box;">
                                        <option value="">— wybierz agenta —</option>
                                        <optgroup label="Audyty energetyczne" data-category="energy">
                                            <option value="general">Ogólnie</option>
                                            <option value="compressor_room">Sprężarkownia</option>
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
                                            <option value="bc_compressor_room">Sprężarkownia (BC)</option>
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

        @if ($companies->isEmpty())
            <div style="padding:32px; text-align:center; color:#9ab4c5; border:1px dashed #d5e0ea; border-radius:12px; margin-top:14px; font-size:14px;">
                Brak firm w systemie. Dodaj firmy w Ustawieniach.
            </div>
        @else
            @php $totalUnread = $unreadChatByCompany->sum(); @endphp
            <div class="dash-section open" id="dash-sec-tiles">
                <div class="dash-section-header" onclick="dashToggle('dash-sec-tiles')">
                    <h2>🏢 Firmy klientów</h2>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span id="co-count-badge" style="background:#e0f2fe; border:1px solid #bae6fd; color:#0369a1; font-size:11px; font-weight:700; padding:3px 8px; border-radius:6px;">
                            {{ $companies->count() }} {{ $companies->count() === 1 ? 'firma' : ($companies->count() < 5 ? 'firmy' : 'firm') }}
                        </span>
                        <span id="dash-chat-unread-total"
                              style="background:#fef3c7; border:1px solid #fbbf24; color:#92400e; font-size:11px; font-weight:700; padding:3px 8px; border-radius:6px; {{ $totalUnread > 0 ? '' : 'display:none;' }}">💬 {{ $totalUnread }} nieprzeczytanych</span>
                        <span class="dash-chevron">▼</span>
                    </div>
                </div>
                <div class="dash-section-body">

                    {{-- Controls: wyszukiwarka + przełącznik widoku --}}
                    <div class="co-controls">
                        <div class="co-search-wrap">
                            <input type="search" id="co-search" placeholder="Szukaj firmy, miasta, opiekuna…" oninput="filterCompanies(this.value)" autocomplete="off">
                        </div>
                        <div class="co-view-btns">
                            <button id="co-btn-tiles" class="co-view-btn active" onclick="setCoView('tiles')">⊞ Kafelki</button>
                            <button id="co-btn-table" class="co-view-btn" onclick="setCoView('table')">☰ Tabela</button>
                        </div>
                    </div>

                    {{-- WIDOK: KAFELKI --}}
                    <div id="co-tiles-view">
                        <div class="company-tiles">
                            @foreach ($companies as $company)
                                @php
                                    $inquiryCount  = (int) ($newInquiriesByCompany[$company->id] ?? 0);
                                    $acceptedCount = (int) ($acceptedOffersByCompany[$company->id] ?? 0);
                                    $unreadChat    = (int) ($unreadChatByCompany[$company->id] ?? 0);
                                    $auditCount    = $company->energyAudits->count();
                                    if ($acceptedCount > 0)    $tileClass = 'has-offer-accepted';
                                    elseif ($inquiryCount > 0) $tileClass = 'has-inquiry';
                                    elseif ($unreadChat > 0)   $tileClass = 'has-unread-chat';
                                    else                       $tileClass = '';
                                    $coSearch = strtolower($company->name . ' ' . ($company->city ?? '') . ' ' . ($company->auditor?->name ?? '') . ' ' . ($company->client?->name ?? ''));
                                    $compTokens = $tokensByCompany[$company->id] ?? null;
                                @endphp
                                <div class="co-tile-item"
                                     data-search="{{ $coSearch }}"
                                     data-company-id="{{ $company->id }}"
                                     data-unread="{{ $unreadChat }}">
                                    <a href="{{ route('firma.show', $company) }}"
                                       class="company-tile {{ $tileClass }}"
                                       style="text-decoration:none; color:inherit;">
                                        <div class="tile-header">
                                            <span class="tile-name">{{ $company->name }}</span>
                                            @if ($acceptedCount > 0)
                                                <span class="tile-badge-action" style="background:#d1fae5; border-color:#16a34a; color:#065f46;">✅ Przydziel audyt</span>
                                            @elseif ($inquiryCount > 0)
                                                <span class="tile-badge-action">⚡ Wymaga działania</span>
                                            @endif
                                        </div>
                                        <div class="tile-meta">
                                            @if ($company->city)
                                                <span>📍 {{ $company->city }}</span>
                                            @endif
                                            @if ($company->auditor)
                                                <span>👤 {{ $company->auditor->name }}</span>
                                            @endif
                                            @if ($company->client)
                                                <span>🔐 Klient: {{ $company->client->name }}</span>
                                            @endif
                                            @if ($unreadChat > 0)
                                                <span style="color:#0369a1; font-weight:700;" data-unread-label>💬 {{ $unreadChat }} {{ $unreadChat === 1 ? 'nowa wiadomość' : ($unreadChat < 5 ? 'nowe wiadomości' : 'nowych wiadomości') }}</span>
                                            @else
                                                <span style="color:#0369a1; font-weight:700; display:none;" data-unread-label></span>
                                            @endif
                                            @if ($auditCount > 0)
                                                <span>📋 {{ $auditCount }} {{ $auditCount === 1 ? 'audyt' : ($auditCount < 5 ? 'audyty' : 'audytów') }}</span>
                                            @endif
                                            @if ($compTokens && $compTokens['total'] > 0)
                                                <span>
                                                    <span class="ai-token-badge">🤖 {{ number_format($compTokens['total']) }} tok · {{ number_format($compTokens['cost_pln'], 2) }} zł</span>
                                                </span>
                                            @endif
                                        </div>
                                        @if ($acceptedCount > 0)
                                            <div class="tile-inquiry-alert" style="background:#d1fae5; border-color:#16a34a; color:#065f46;">
                                                ✅ Klient zaakceptował ofertę — przydziel audyt!
                                            </div>
                                        @elseif ($inquiryCount > 0)
                                            <div class="tile-inquiry-alert">
                                                📬 {{ $inquiryCount }} nowe {{ $inquiryCount === 1 ? 'zapytanie' : ($inquiryCount < 5 ? 'zapytania' : 'zapytań') }} oczekuje na decyzję
                                            </div>
                                        @elseif ($unreadChat > 0)
                                            <div class="tile-inquiry-alert" style="background:#e0f2fe; border-color:#7dd3fc; color:#0369a1;">
                                                💬 Klient wysłał {{ $unreadChat }} {{ $unreadChat === 1 ? 'wiadomość' : ($unreadChat < 5 ? 'wiadomości' : 'wiadomości') }} — kliknij by odpowiedzieć
                                            </div>
                                        @endif
                                    </a>
                                </div>{{-- /co-tile-item --}}
                            @endforeach
                        </div>
                        <div id="co-tiles-empty" class="co-empty" style="display:none;">Brak wyników dla wpisanej frazy.</div>
                    </div>

                    {{-- WIDOK: TABELA --}}
                    <div id="co-table-view" style="display:none; overflow-x:auto;">
                        <table class="co-tbl" id="co-table">
                            <thead>
                                <tr>
                                    <th class="sortable" data-col="0" onclick="sortCoTable(0)">Status</th>
                                    <th class="sortable" data-col="1" onclick="sortCoTable(1)">Firma</th>
                                    <th class="sortable" data-col="2" onclick="sortCoTable(2)">Miasto</th>
                                    <th class="sortable" data-col="3" onclick="sortCoTable(3)">Opiekun</th>
                                    <th class="sortable" data-col="4" onclick="sortCoTable(4)">Klient</th>
                                    <th class="sortable" data-col="5" onclick="sortCoTable(5)" style="text-align:center;">Audyty</th>
                                    <th class="sortable" data-col="6" onclick="sortCoTable(6)" style="text-align:right;">Tokeny AI</th>
                                    <th>Akcja</th>
                                </tr>
                            </thead>
                            <tbody id="co-table-body">
                                @foreach ($companies as $company)
                                    @php
                                        $inquiryCount  = (int) ($newInquiriesByCompany[$company->id] ?? 0);
                                        $acceptedCount = (int) ($acceptedOffersByCompany[$company->id] ?? 0);
                                        $unreadChat    = (int) ($unreadChatByCompany[$company->id] ?? 0);
                                        $auditCount    = $company->energyAudits->count();
                                        $statusPrio    = $acceptedCount > 0 ? 3 : ($inquiryCount > 0 ? 2 : ($unreadChat > 0 ? 1 : 0));
                                        $coSearch = strtolower($company->name . ' ' . ($company->city ?? '') . ' ' . ($company->auditor?->name ?? '') . ' ' . ($company->client?->name ?? ''));
                                        $compTokens = $tokensByCompany[$company->id] ?? null;
                                    @endphp
                                    <tr class="co-row" data-search="{{ $coSearch }}" data-company-id="{{ $company->id }}" data-unread="{{ $unreadChat }}">
                                        <td data-val="{{ $statusPrio }}">
                                            @if ($acceptedCount > 0)
                                                <span class="co-status-badge st-accepted">✅ Przydziel audyt</span>
                                            @elseif ($inquiryCount > 0)
                                                <span class="co-status-badge st-inquiry">⚡ Zapytanie</span>
                                            @elseif ($unreadChat > 0)
                                                <span class="co-status-badge st-chat">💬 Czat</span>
                                            @else
                                                <span style="color:#c5d4df; font-size:12px;">—</span>
                                            @endif
                                        </td>
                                        <td data-val="{{ $company->name }}" style="font-weight:700; color:#0f2330;">{{ $company->name }}</td>
                                        <td data-val="{{ $company->city ?? '' }}">{{ $company->city ?? '—' }}</td>
                                        <td data-val="{{ $company->auditor?->name ?? '' }}">{{ $company->auditor?->name ?? '—' }}</td>
                                        <td data-val="{{ $company->client?->name ?? '' }}">{{ $company->client?->name ?? '—' }}</td>
                                        <td data-val="{{ $auditCount }}" style="text-align:center; color:{{ $auditCount > 0 ? '#0f2330' : '#c5d4df' }};">{{ $auditCount ?: '—' }}</td>
                                        <td data-val="{{ $compTokens ? $compTokens['total'] : 0 }}" style="text-align:right;">
                                            @if ($compTokens && $compTokens['total'] > 0)
                                                <span class="ai-token-badge">{{ number_format($compTokens['total']) }}</span><br>
                                                <span style="font-size:10px; color:#6b8294;">{{ number_format($compTokens['cost_pln'], 2) }} zł</span>
                                            @else
                                                <span class="ai-token-badge zero">—</span>
                                            @endif
                                        </td>
                                        <td><a href="{{ route('firma.show', $company) }}" class="co-row-link">Otwórz →</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="co-table-empty" class="co-empty" style="display:none;">Brak wyników dla wpisanej frazy.</div>
                    </div>

                </div>{{-- /dash-section-body --}}
            </div>{{-- /dash-section --}}
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
    var tableEl = document.getElementById('co-table-view');
    var btnTiles = document.getElementById('co-btn-tiles');
    var btnTable = document.getElementById('co-btn-table');
    if (!tilesEl || !tableEl) return;
    tilesEl.style.display = (v === 'tiles') ? '' : 'none';
    tableEl.style.display = (v === 'table') ? '' : 'none';
    btnTiles.classList.toggle('active', v === 'tiles');
    btnTable.classList.toggle('active', v === 'table');
    // re-run filter so empty state updates correctly
    var q = document.getElementById('co-search');
    if (q) filterCompanies(q.value);
}

// ── Live search ───────────────────────────────────────────────
function filterCompanies(q) {
    q = (q || '').toLowerCase().trim();
    // Tiles
    var tiles = document.querySelectorAll('.co-tile-item');
    var tileCount = 0;
    tiles.forEach(function(el) {
        var show = !q || el.dataset.search.includes(q);
        el.style.display = show ? '' : 'none';
        if (show) tileCount++;
    });
    var tilesEmpty = document.getElementById('co-tiles-empty');
    if (tilesEmpty) tilesEmpty.style.display = tileCount === 0 ? '' : 'none';
    // Table rows
    var rows = document.querySelectorAll('#co-table-body .co-row');
    var rowCount = 0;
    rows.forEach(function(el) {
        var show = !q || el.dataset.search.includes(q);
        el.style.display = show ? '' : 'none';
        if (show) rowCount++;
    });
    var tableEmpty = document.getElementById('co-table-empty');
    if (tableEmpty) tableEmpty.style.display = rowCount === 0 ? '' : 'none';
}

// ── Table sort ────────────────────────────────────────────────
var coSortCol = -1, coSortDir = 1;

function sortCoTable(col) {
    var tbody = document.getElementById('co-table-body');
    if (!tbody) return;
    var rows = Array.from(tbody.querySelectorAll('.co-row'));
    if (coSortCol === col) {
        coSortDir *= -1;
    } else {
        coSortCol = col;
        coSortDir = 1;
    }
    rows.sort(function(a, b) {
        var av = (a.querySelectorAll('td')[col].dataset.val || '').trim();
        var bv = (b.querySelectorAll('td')[col].dataset.val || '').trim();
        var an = parseFloat(av), bn = parseFloat(bv);
        if (!isNaN(an) && !isNaN(bn)) return (an - bn) * coSortDir;
        return av.localeCompare(bv, 'pl') * coSortDir;
    });
    rows.forEach(function(r) { tbody.appendChild(r); });
    document.querySelectorAll('#co-table th.sortable').forEach(function(th) {
        th.classList.remove('sort-asc', 'sort-desc');
        if (parseInt(th.dataset.col) === coSortCol) {
            th.classList.add(coSortDir === 1 ? 'sort-asc' : 'sort-desc');
        }
    });
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

                    // Flash table row
                    const row = document.querySelector('.co-row[data-company-id="' + id + '"]');
                    if (row) {
                        row.classList.add('chat-incoming-row');
                        row.dataset.unread = String(newCount);
                        row.addEventListener('animationend', function () {
                            row.classList.remove('chat-incoming-row');
                        }, { once: true });
                    }
                }

                _knownCounts[id] = newCount;
            });

            // Update total badge
            const total = parseInt(data.total || 0, 10);
            const badge = document.getElementById('dash-chat-unread-total');
            if (badge) {
                if (total > 0) {
                    badge.textContent = '💬 ' + total + ' nieprzeczytanych';
                    badge.style.display = '';
                } else {
                    badge.style.display = 'none';
                }
            }

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

