<x-layouts.app>
<div class="panel">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h2 style="margin:0;font-size:20px;">Oferty w toku / Wysłane</h2>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('offers.create') }}" style="padding:8px 16px;background:var(--green-primary);color:#fff;border-radius:9px;text-decoration:none;font-weight:600;font-size:14px;">+ Nowa oferta</a>
            <a href="{{ route('offers.index') }}" style="padding:8px 16px;background:#e2e8f0;color:var(--ink);border-radius:9px;text-decoration:none;font-weight:600;font-size:14px;">← Wróć</a>
        </div>
    </div>

    @if($offers->isEmpty())
        <p style="color:var(--ink-mute);text-align:center;padding:32px 0;">Brak ofert w toku.</p>
    @else
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Nr oferty</th>
                        <th>Nazwa</th>
                        <th>Data</th>
                        <th>Klient / Firma</th>
                        <th>Status</th>
                        <th style="text-align:right;">Cena końcowa</th>
                        <th style="text-align:center;">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offers as $offer)
                    @php
                        $statusLabel = match($offer->status) {
                            'inprogress' => ['label' => 'W toku',      'bg' => '#fef3c7', 'color' => '#92400e'],
                            'sent'       => ['label' => 'Wysłana',     'bg' => '#dbeafe', 'color' => '#1e40af'],
                            'accepted'   => ['label' => 'Zaakceptowana','bg' => '#d1fae5', 'color' => '#065f46'],
                            default      => ['label' => $offer->status, 'bg' => '#f3f4f6', 'color' => '#374151'],
                        };
                    @endphp
                    <tr>
                        <td style="font-weight:600;">{{ $offer->offer_number ?: '—' }}</td>
                        <td>{{ $offer->offer_title }}</td>
                        <td>{{ $offer->offer_date ? $offer->offer_date->format('d.m.Y') : '—' }}</td>
                        <td>
                            {{ $offer->company?->name ?: ($offer->customer_name ?: '—') }}
                        </td>
                        <td>
                            <span style="display:inline-block;padding:2px 10px;border-radius:6px;font-size:12px;font-weight:700;background:{{ $statusLabel['bg'] }};color:{{ $statusLabel['color'] }};">{{ $statusLabel['label'] }}</span>
                        </td>
                        <td style="text-align:right;font-weight:600;">
                            {{ number_format($offer->total_price, 2, ',', ' ') }} zł
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;">
                                <a href="{{ route('offers.generateWord', $offer) }}" style="padding:5px 10px;background:#7c3aed;color:#fff;border-radius:7px;text-decoration:none;font-size:12px;font-weight:600;" title="Word">W</a>
                                <a href="{{ route('offers.generatePdf', $offer) }}" style="padding:5px 10px;background:#dc2626;color:#fff;border-radius:7px;text-decoration:none;font-size:12px;font-weight:600;" title="PDF">PDF</a>
                                <a href="{{ route('offers.edit', $offer) }}" style="padding:5px 10px;background:var(--green-primary);color:#fff;border-radius:7px;text-decoration:none;font-size:12px;font-weight:600;">Edytuj</a>
                                <form method="POST" action="{{ route('offers.copy', $offer) }}" style="display:inline;" onsubmit="return confirm('Skopiować ofertę?')">
                                    @csrf
                                    <button type="submit" style="padding:5px 10px;background:var(--green-primary);color:#fff;border-radius:7px;border:0;cursor:pointer;font-size:12px;font-weight:600;">Kopiuj</button>
                                </form>
                                <form method="POST" action="{{ route('offers.archive', $offer) }}" style="display:inline;" onsubmit="return confirm('Przenieść do archiwum?')">
                                    @csrf
                                    <button type="submit" style="padding:5px 10px;background:#718096;color:#fff;border-radius:7px;border:0;cursor:pointer;font-size:12px;font-weight:600;">Archiwizuj</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
</x-layouts.app>
