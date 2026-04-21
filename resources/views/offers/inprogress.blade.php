<x-layouts.app>
<div class="panel">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h2 style="margin:0;font-size:20px;">Oferty w toku</h2>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('offers.create') }}" style="padding:8px 16px;background:#1ba84a;color:#fff;border-radius:9px;text-decoration:none;font-weight:600;font-size:14px;">+ Nowa oferta</a>
            <a href="{{ route('offers.index') }}" style="padding:8px 16px;background:#e2e8f0;color:#0f2330;border-radius:9px;text-decoration:none;font-weight:600;font-size:14px;">← Wróć</a>
        </div>
    </div>

    @if($offers->isEmpty())
        <p style="color:#4c6373;text-align:center;padding:32px 0;">Brak ofert w toku.</p>
    @else
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Nr oferty</th>
                        <th>Nazwa</th>
                        <th>Data</th>
                        <th>Klient</th>
                        <th>Szansa CRM</th>
                        <th style="text-align:right;">Cena końcowa</th>
                        <th style="text-align:center;">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offers as $offer)
                    <tr>
                        <td style="font-weight:600;">{{ $offer->offer_number ?: '—' }}</td>
                        <td>{{ $offer->offer_title }}</td>
                        <td>{{ $offer->offer_date ? $offer->offer_date->format('d.m.Y') : '—' }}</td>
                        <td>{{ $offer->customer_name ?: '—' }}</td>
                        <td>
                            @if($offer->crmDeal)
                                <a href="{{ route('crm.index') }}" style="color:#0e89d8;">{{ $offer->crmDeal->name }}</a>
                            @else
                                <span style="color:#9cb0c0;">—</span>
                            @endif
                        </td>
                        <td style="text-align:right;font-weight:600;">
                            {{ number_format($offer->total_price, 2, ',', ' ') }} zł
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;">
                                <a href="{{ route('offers.generateWord', $offer) }}" style="padding:5px 10px;background:#7c3aed;color:#fff;border-radius:7px;text-decoration:none;font-size:12px;font-weight:600;" title="Word">W</a>
                                <a href="{{ route('offers.generatePdf', $offer) }}" style="padding:5px 10px;background:#dc2626;color:#fff;border-radius:7px;text-decoration:none;font-size:12px;font-weight:600;" title="PDF">PDF</a>
                                <a href="{{ route('offers.edit', $offer) }}" style="padding:5px 10px;background:#0e89d8;color:#fff;border-radius:7px;text-decoration:none;font-size:12px;font-weight:600;">Edytuj</a>
                                <form method="POST" action="{{ route('offers.copy', $offer) }}" style="display:inline;" onsubmit="return confirm('Skopiować ofertę?')">
                                    @csrf
                                    <button type="submit" style="padding:5px 10px;background:#1ba84a;color:#fff;border-radius:7px;border:0;cursor:pointer;font-size:12px;font-weight:600;">Kopiuj</button>
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
