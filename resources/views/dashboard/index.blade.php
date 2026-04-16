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

        @if ($companies->isEmpty())
            <div style="padding:32px; text-align:center; color:#9ab4c5; border:1px dashed #d5e0ea; border-radius:12px; margin-top:14px; font-size:14px;">
                Brak firm w systemie. Dodaj firmy w Ustawieniach.
            </div>
        @else
            <div class="company-tiles">
                @foreach ($companies as $company)
                    @php($inquiryCount = (int) ($newInquiriesByCompany[$company->id] ?? 0))
                    <div class="company-tile {{ $inquiryCount > 0 ? 'has-inquiry' : '' }}">
                        <div class="tile-header">
                            <span class="tile-name">{{ $company->name }}</span>
                            @if ($inquiryCount > 0)
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
                                <span>🏢 Klient: {{ $company->client->name }}</span>
                            @endif
                        </div>
                        @if ($inquiryCount > 0)
                            <div class="tile-inquiry-alert">
                                📬 {{ $inquiryCount }} nowe {{ $inquiryCount === 1 ? 'zapytanie' : ($inquiryCount < 5 ? 'zapytania' : 'zapytań') }} oczekuje na decyzję
                                <div style="margin-top:4px;">
                                    <a href="{{ route('strefa-klienta') }}" style="color:#78350f; text-decoration:underline; font-size:11px;">Przejdź do zapytań →</a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>

