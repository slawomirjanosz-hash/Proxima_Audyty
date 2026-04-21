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
            <div class="dash-section" id="dash-sec-tiles">
                <div class="dash-section-header" onclick="dashToggle('dash-sec-tiles')">
                    <h2>🏢 Firmy klientów</h2>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="background:#e0f2fe; border:1px solid #bae6fd; color:#0369a1; font-size:11px; font-weight:700; padding:3px 8px; border-radius:6px;">
                            {{ $companies->count() }} {{ $companies->count() === 1 ? 'firma' : ($companies->count() < 5 ? 'firmy' : 'firm') }}
                        </span>
                        @if($totalUnread > 0)
                            <span style="background:#fef3c7; border:1px solid #fbbf24; color:#92400e; font-size:11px; font-weight:700; padding:3px 8px; border-radius:6px;">💬 {{ $totalUnread }} nieprzeczytanych</span>
                        @endif
                        <span class="dash-chevron">▼</span>
                    </div>
                </div>
                <div class="dash-section-body">
            <div class="company-tiles" id="company-tiles-grid">
                @foreach ($companies as $company)
                    @php
                        $inquiryCount  = (int) ($newInquiriesByCompany[$company->id] ?? 0);
                        $acceptedCount = (int) ($acceptedOffersByCompany[$company->id] ?? 0);
                        $unreadChat    = (int) ($unreadChatByCompany[$company->id] ?? 0);
                        if ($acceptedCount > 0)      $tileClass = 'has-offer-accepted';
                        elseif ($inquiryCount > 0)   $tileClass = 'has-inquiry';
                        elseif ($unreadChat > 0)     $tileClass = 'has-unread-chat';
                        else                         $tileClass = '';
                    @endphp
                    <a href="{{ route('firma.show', $company) }}" class="company-tile {{ $tileClass }}" style="text-decoration:none; color:inherit;">
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
                            @php($auditCount = $company->energyAudits()->count())
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
</script>
</x-layouts.app>

