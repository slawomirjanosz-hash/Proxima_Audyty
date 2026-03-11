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
            <div class="stat-label">{{ __('ui.client.stats.active_offers') }}</div>
            <div class="stat-value">{{ $offers->count() }}</div>
            <div class="stat-sub">{{ __('ui.client.stats.active_offers_sub') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">{{ __('ui.client.stats.audits') }}</div>
            <div class="stat-value">{{ $audits->count() }}</div>
            <div class="stat-sub">{{ __('ui.client.stats.audits_sub') }}</div>
        </div>
    </div>

    {{-- Companies table --}}
    <section class="panel">
        <p class="section-title">{{ __('ui.client.tables.companies.title') }}</p>
        <table style="margin-top:14px;">
            <thead>
                <tr>
                    <th>{{ __('ui.client.tables.companies.columns.company_name') }}</th>
                    <th>{{ __('ui.client.tables.companies.columns.city') }}</th>
                    <th>{{ __('ui.client.tables.companies.columns.assigned_auditor') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                    <tr>
                        <td><strong>{{ $company->name }}</strong></td>
                        <td>{{ $company->city ?? '—' }}</td>
                        <td>{{ $company->auditor?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="color:#9ab4c5; text-align:center; padding:24px;">{{ __('ui.client.tables.companies.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    {{-- Offers --}}
    <section class="panel">
        <p class="section-title">{{ __('ui.client.tables.offers.title') }}</p>
        <table style="margin-top:14px;">
            <thead>
                <tr>
                    <th>{{ __('ui.client.tables.offers.columns.title') }}</th>
                    <th>{{ __('ui.client.tables.offers.columns.status') }}</th>
                    <th>{{ __('ui.client.tables.offers.columns.company') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($offers as $offer)
                    <tr>
                        <td><strong>{{ $offer->title }}</strong></td>
                        <td>
                            <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:6px;background:#eaf2ff;color:#154f93;">
                                {{ $offer->status }}
                            </span>
                        </td>
                        <td>{{ $offer->company?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="color:#9ab4c5; text-align:center; padding:24px;">{{ __('ui.client.tables.offers.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    {{-- Audits --}}
    <section class="panel">
        <p class="section-title">{{ __('ui.client.tables.audits.title') }}</p>
        <table style="margin-top:14px;">
            <thead>
                <tr>
                    <th>{{ __('ui.client.tables.audits.columns.title') }}</th>
                    <th>{{ __('ui.client.tables.audits.columns.status') }}</th>
                    <th>{{ __('ui.client.tables.audits.columns.company') }}</th>
                    <th>{{ __('ui.client.tables.audits.columns.auditor') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($audits as $audit)
                    <tr>
                        <td><strong>{{ $audit->title }}</strong></td>
                        <td>
                            <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:6px;background:#d9f6e3;color:#0c5f28;">
                                {{ $audit->status }}
                            </span>
                        </td>
                        <td>{{ $audit->company?->name ?? '—' }}</td>
                        <td>{{ $audit->auditor?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="color:#9ab4c5; text-align:center; padding:24px;">{{ __('ui.client.tables.audits.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

</x-layouts.app>
