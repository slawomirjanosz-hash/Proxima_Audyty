<x-layouts.app>
    <section class="panel">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; margin-bottom:10px;">
            <h1 style="margin:0;">🔧 Diagnostyka CRM</h1>
            <a href="{{ route('crm.index') }}" class="login-btn" style="text-decoration:none;">Powrót do CRM</a>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px,1fr)); gap:8px; margin-bottom:12px;">
            <div style="padding:10px; border:1px solid #d7e5f0; border-radius:10px; background:#f8fbff;"><div class="muted" style="font-size:12px;">Firmy</div><div style="font-weight:800; font-size:22px;">{{ $stats['companies'] }}</div></div>
            <div style="padding:10px; border:1px solid #d7e5f0; border-radius:10px; background:#f8fbff;"><div class="muted" style="font-size:12px;">Szanse</div><div style="font-weight:800; font-size:22px;">{{ $stats['deals'] }}</div></div>
            <div style="padding:10px; border:1px solid #d7e5f0; border-radius:10px; background:#f8fbff;"><div class="muted" style="font-size:12px;">Zadania</div><div style="font-weight:800; font-size:22px;">{{ $stats['tasks'] }}</div></div>
            <div style="padding:10px; border:1px solid #d7e5f0; border-radius:10px; background:#f8fbff;"><div class="muted" style="font-size:12px;">Aktywności</div><div style="font-weight:800; font-size:22px;">{{ $stats['activities'] }}</div></div>
            <div style="padding:10px; border:1px solid #d7e5f0; border-radius:10px; background:#f8fbff;"><div class="muted" style="font-size:12px;">Typy klientów</div><div style="font-weight:800; font-size:22px;">{{ $stats['customer_types'] }}</div></div>
            <div style="padding:10px; border:1px solid #d7e5f0; border-radius:10px; background:#f8fbff;"><div class="muted" style="font-size:12px;">Etapy lejka</div><div style="font-weight:800; font-size:22px;">{{ $stats['stages'] }}</div></div>
        </div>

        <h3 style="margin:0 0 8px;">Ostatnie firmy CRM</h3>
        <table style="margin-bottom:12px;">
            <thead><tr><th>Nazwa</th><th>NIP</th><th>Miasto</th></tr></thead>
            <tbody>
                @forelse($latestCompanies as $company)
                    <tr><td>{{ $company->name }}</td><td>{{ $company->nip ?: '—' }}</td><td>{{ $company->city ?: '—' }}</td></tr>
                @empty
                    <tr><td colspan="3" class="muted">Brak danych</td></tr>
                @endforelse
            </tbody>
        </table>

        <h3 style="margin:0 0 8px;">Ostatnie szanse CRM</h3>
        <table>
            <thead><tr><th>Nazwa</th><th>Etap</th><th>Wartość</th></tr></thead>
            <tbody>
                @forelse($latestDeals as $deal)
                    <tr><td>{{ $deal->name }}</td><td>{{ $deal->stage }}</td><td>{{ number_format((float) $deal->value, 2, ',', ' ') }} {{ $deal->currency }}</td></tr>
                @empty
                    <tr><td colspan="3" class="muted">Brak danych</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</x-layouts.app>
