<x-layouts.app>

    <style>
        .client-hero {
            background: linear-gradient(135deg, #0f2330 0%, #0e4a6e 50%, #0b6e3d 100%);
            border-radius: 16px;
            padding: 36px 40px;
            color: #fff;
            display: grid;
            gap: 10px;
            box-shadow: 0 18px 50px rgba(14,55,85,.18);
            position: relative;
            overflow: hidden;
        }
        .client-hero::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 280px; height: 280px;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
        }
        .client-hero::after {
            content: '';
            position: absolute;
            bottom: -40px; left: 30%;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(27,168,74,.1);
        }
        .client-tag {
            width: fit-content;
            background: rgba(27,168,74,.22);
            border: 1px solid rgba(27,168,74,.4);
            color: #6ee7a4;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 6px;
        }
        .client-hero h1 {
            margin: 0;
            font-size: clamp(24px, 3vw, 38px);
            font-weight: 800;
            line-height: 1.15;
        }
        .client-hero h1 span { color: #6ee7a4; }
        .client-hero p {
            margin: 0;
            font-size: 14px;
            color: rgba(255,255,255,.65);
            max-width: 560px;
        }
        .client-meta {
            display: flex;
            gap: 24px;
            margin-top: 8px;
            flex-wrap: wrap;
        }
        .client-meta-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .client-meta-item .label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,.45);
            font-weight: 700;
        }
        .client-meta-item .value {
            font-size: 14px;
            font-weight: 600;
            color: rgba(255,255,255,.9);
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-top: 14px;
        }
        .stat-card {
            background: #fff;
            border: 1px solid #d5e0ea;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 4px 16px rgba(14,55,85,.05);
        }
        .stat-card .stat-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #4c6373;
            margin-bottom: 8px;
        }
        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: #0e89d8;
            line-height: 1;
        }
        .stat-card .stat-sub {
            font-size: 12px;
            color: #4c6373;
            margin-top: 4px;
        }
        .section-title {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .9px;
            color: #4c6373;
            padding-bottom: 8px;
            border-bottom: 2px solid #e8f0f7;
            margin-bottom: 0;
        }
        .company-list { display:grid; gap:10px; margin-top:14px; }
        .company-item { border:1px solid #d7e5f0; border-radius:12px; overflow:hidden; background:#fbfdff; }
        .company-header { width:100%; border:none; background:#f6fbff; padding:12px; display:flex; justify-content:space-between; align-items:center; cursor:pointer; text-align:left; }
        .company-header:hover { background:#eef6ff; }
        .company-main { display:flex; flex-direction:column; gap:3px; }
        .company-title { font-weight:800; color:#10344c; }
        .company-meta { font-size:12px; color:#4c6373; }
        .company-chevron { color:#6b8aa3; font-size:16px; transition:transform .2s; }
        .company-item.open .company-chevron { transform:rotate(180deg); }
        .company-body { display:none; padding:12px; border-top:1px solid #e0ecf5; }
        .company-item.open .company-body { display:block; }
        .company-audits-list { margin:0; padding-left:18px; display:grid; gap:6px; }
        .company-audits-list li { color:#2f4e65; font-size:13px; }
        @media (max-width: 800px) {
            .stat-grid { grid-template-columns: 1fr 1fr; }
            .client-hero { padding: 24px 22px; }
        }
    </style>

    {{-- Welcome hero --}}
    <div class="client-hero">
        <span class="client-tag">{{ __('ui.client.tag') }}</span>
        <h1>{{ __('ui.client.welcome') }}, <span>{{ auth()->user()->name }}</span></h1>
        <p>
            {{ __('ui.client.description') }}
        </p>
        <div class="client-meta">
            <div class="client-meta-item">
                <span class="label">{{ __('ui.client.meta.account') }}</span>
                <span class="value">{{ auth()->user()->email }}</span>
            </div>
            <div class="client-meta-item">
                <span class="label">{{ __('ui.client.meta.access_level') }}</span>
                <span class="value">{{ auth()->user()->role->label() }}</span>
            </div>
            <div class="client-meta-item">
                <span class="label">{{ __('ui.client.meta.session_date') }}</span>
                <span class="value">{{ now()->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    @if($previewMode)
        <section class="panel" style="margin-top:14px; background:#f2f8ff; border-color:#cfe0ff; color:#154f93;">
            <strong>{{ __('ui.client.preview_title') }}</strong> {{ __('ui.client.preview_text') }}
        </section>
    @endif

    {{-- Stats --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-label">{{ __('ui.client.stats.companies') }}</div>
            <div class="stat-value">{{ $companies->count() }}</div>
            <div class="stat-sub">{{ __('ui.client.stats.companies_sub') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">{{ __('ui.client.stats.audits') }}</div>
            <div class="stat-value">{{ $audits->count() }}</div>
            <div class="stat-sub">{{ __('ui.client.stats.audits_sub') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Firmy z audytami</div>
            <div class="stat-value">{{ ($auditsByCompany ?? collect())->count() }}</div>
            <div class="stat-sub">kliknij firmę, aby zobaczyć przypisane audyty</div>
        </div>
    </div>

    {{-- Companies table --}}
    <section class="panel">
        <p class="section-title">{{ __('ui.client.tables.companies.title') }}</p>
        <div class="company-list">
            @forelse($companies as $company)
                @php($companyAudits = ($auditsByCompany[(string) $company->id] ?? collect()))
                <div class="company-item" id="company-item-{{ $company->id }}">
                    <button type="button" class="company-header" onclick="toggleCompanyItem('{{ $company->id }}')">
                        <div class="company-main">
                            <span class="company-title">{{ $loop->iteration }}. {{ $company->name }}</span>
                            <span class="company-meta">{{ __('ui.client.tables.companies.columns.city') }}: {{ $company->city ?? '—' }} • {{ __('ui.client.tables.companies.columns.assigned_auditor') }}: {{ $company->auditor?->name ?? '—' }}</span>
                        </div>
                        <span class="company-chevron">&#9660;</span>
                    </button>
                    <div class="company-body">
                        <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#4c6373; margin-bottom:8px;">Przypisane audyty</div>
                        @if($companyAudits->isNotEmpty())
                            <ul class="company-audits-list">
                                @foreach($companyAudits as $audit)
                                    <li>
                                        <strong>{{ $audit->title }}</strong>
                                        — status: {{ $audit->status }}
                                        @if($audit->auditor)
                                            — audytor: {{ $audit->auditor->name }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div style="color:#9ab4c5; font-size:13px;">Brak przypisanych audytów.</div>
                        @endif
                    </div>
                </div>
            @empty
                <div style="color:#9ab4c5; text-align:center; padding:24px;">{{ __('ui.client.tables.companies.empty') }}</div>
            @endforelse
        </div>
    </section>

    <script>
        function toggleCompanyItem(companyId) {
            const item = document.getElementById('company-item-' + companyId);
            if (!item) {
                return;
            }

            item.classList.toggle('open');
        }
    </script>

</x-layouts.app>
