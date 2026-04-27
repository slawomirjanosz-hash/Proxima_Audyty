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
                        @if($totalUnread > 0)
                            <span style="background:#fef3c7; border:1px solid #fbbf24; color:#92400e; font-size:11px; font-weight:700; padding:3px 8px; border-radius:6px;">💬 {{ $totalUnread }} nieprzeczytanych</span>
                        @endif
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
                                @endphp
                                <a href="{{ route('firma.show', $company) }}"
                                   class="company-tile {{ $tileClass }} co-tile-item"
                                   style="text-decoration:none; color:inherit;"
                                   data-search="{{ $coSearch }}">
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
                                            <span style="color:#0369a1; font-weight:700;">💬 {{ $unreadChat }} {{ $unreadChat === 1 ? 'nowa wiadomość' : ($unreadChat < 5 ? 'nowe wiadomości' : 'nowych wiadomości') }}</span>
                                        @endif
                                        @if ($auditCount > 0)
                                            <span>📋 {{ $auditCount }} {{ $auditCount === 1 ? 'audyt' : ($auditCount < 5 ? 'audyty' : 'audytów') }}</span>
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
                                    @endphp
                                    <tr class="co-row" data-search="{{ $coSearch }}">
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
</script>
</x-layouts.app>

