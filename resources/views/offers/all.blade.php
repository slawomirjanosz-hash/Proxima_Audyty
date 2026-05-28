<x-layouts.app>
<div class="panel">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <div>
            <h2 style="margin:0;font-size:20px;">Wszystkie oferty</h2>
            <p style="margin:4px 0 0;font-size:13px;color:var(--ink-mute);">Łącznie: {{ $offers->count() }} ofert</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('offers.create') }}" style="padding:8px 16px;background:var(--green-primary);color:#fff;border-radius:9px;text-decoration:none;font-weight:600;font-size:14px;">+ Nowa oferta</a>
            <a href="{{ route('audits.index') }}" style="padding:8px 16px;background:#e2e8f0;color:var(--ink);border-radius:9px;text-decoration:none;font-weight:600;font-size:14px;">← Rodzaje audytów</a>
        </div>
    </div>

    {{-- Status filter tabs --}}
    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px;" id="status-filters">
        <button onclick="filterOffers('all')" id="filter-all" class="ofr-filter active">Wszystkie ({{ $offers->count() }})</button>
        <button onclick="filterOffers('inprogress')" id="filter-inprogress" class="ofr-filter">W toku ({{ $offers->where('status','inprogress')->count() }})</button>
        <button onclick="filterOffers('sent')" id="filter-sent" class="ofr-filter">Wysłane ({{ $offers->where('status','sent')->count() }})</button>
        <button onclick="filterOffers('accepted')" id="filter-accepted" class="ofr-filter">Zaakceptowane ({{ $offers->where('status','accepted')->count() }})</button>
        <button onclick="filterOffers('archived')" id="filter-archived" class="ofr-filter">Archiwum ({{ $offers->where('status','archived')->count() }})</button>
        <button onclick="filterOffers('portfolio')" id="filter-portfolio" class="ofr-filter">Portfolio ({{ $offers->where('status','portfolio')->count() }})</button>
    </div>

    <style>
    .ofr-filter { padding:6px 14px; border-radius:8px; border:1px solid #d1e0ec; background:#f0f7fc; font-size:13px; font-weight:700; color:#2d5a78; cursor:pointer; transition:background .12s; }
    .ofr-filter.active { background:#1A4D3A; color:#fff; border-color:#1A4D3A; }
    .ofr-row { transition:opacity .15s; }
    </style>

    @if($offers->isEmpty())
        <p style="color:var(--ink-mute);text-align:center;padding:40px 0;">Brak ofert.</p>
    @else
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Nr oferty</th>
                        <th>Nazwa</th>
                        <th>Data</th>
                        <th>Klient / Firma</th>
                        <th>Szablon / Typ</th>
                        <th>Status</th>
                        <th style="text-align:right;">Cena końcowa</th>
                        <th style="text-align:center;">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offers as $offer)
                    @php
                        $statusMap = [
                            'inprogress' => ['label' => 'W toku',         'bg' => '#fef3c7', 'color' => '#92400e'],
                            'sent'       => ['label' => 'Wysłana',        'bg' => '#dbeafe', 'color' => '#1e40af'],
                            'accepted'   => ['label' => 'Zaakceptowana',  'bg' => '#d1fae5', 'color' => '#065f46'],
                            'archived'   => ['label' => 'Archiwum',       'bg' => '#f3f4f6', 'color' => '#374151'],
                            'portfolio'  => ['label' => 'Portfolio',      'bg' => '#ede9fe', 'color' => '#5b21b6'],
                        ];
                        $s = $statusMap[$offer->status] ?? ['label' => $offer->status, 'bg' => '#f3f4f6', 'color' => '#374151'];
                    @endphp
                    <tr class="ofr-row" data-status="{{ $offer->status }}">
                        <td style="font-weight:600;">{{ $offer->offer_number ?: '—' }}</td>
                        <td>{{ $offer->offer_title }}</td>
                        <td style="white-space:nowrap;">{{ $offer->offer_date ? $offer->offer_date->format('d.m.Y') : '—' }}</td>
                        <td>{{ $offer->company?->name ?: ($offer->customer_name ?: '—') }}</td>
                        <td style="font-size:12px;color:var(--ink-mute);">{{ $offer->offerTemplate?->name ?: '—' }}</td>
                        <td>
                            <span style="display:inline-block;padding:2px 10px;border-radius:6px;font-size:12px;font-weight:700;background:{{ $s['bg'] }};color:{{ $s['color'] }};">{{ $s['label'] }}</span>
                        </td>
                        <td style="text-align:right;font-weight:600;white-space:nowrap;">
                            {{ number_format($offer->total_price, 2, ',', ' ') }} zł
                        </td>
                        <td>
                            <div style="display:flex;gap:5px;justify-content:center;flex-wrap:wrap;">
                                <a href="{{ route('offers.edit', $offer) }}" style="padding:4px 10px;background:var(--green-primary);color:#fff;border-radius:7px;text-decoration:none;font-size:12px;font-weight:600;">Edytuj</a>
                                <a href="{{ route('offers.generatePdf', $offer) }}" style="padding:4px 10px;background:#dc2626;color:#fff;border-radius:7px;text-decoration:none;font-size:12px;font-weight:600;">PDF</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
function filterOffers(status) {
    document.querySelectorAll('.ofr-filter').forEach(b => b.classList.remove('active'));
    document.getElementById('filter-' + status)?.classList.add('active');
    document.querySelectorAll('.ofr-row').forEach(row => {
        row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
    });
}
// Auto-activate filter from URL param
const urlType = new URLSearchParams(location.search).get('status');
if (urlType) filterOffers(urlType);
</script>
</x-layouts.app>
