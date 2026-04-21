<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Oferta {{ $offer->offer_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11pt; color: #0f2330; margin: 0; padding: 24px; }
        h1 { font-size: 20pt; margin: 0 0 4px; color: #0f2330; }
        .subtitle { color: #4c6373; font-size: 10pt; margin-bottom: 24px; }
        .header { border-bottom: 2px solid #0e89d8; padding-bottom: 12px; margin-bottom: 20px; }
        .header-inner { display: flex; justify-content: space-between; align-items: flex-start; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .info-box { border: 1px solid #d5e0ea; border-radius: 8px; padding: 12px; }
        .info-box h3 { margin: 0 0 8px; font-size: 10pt; text-transform: uppercase; letter-spacing: .5px; color: #4c6373; }
        .info-box p { margin: 3px 0; font-size: 10pt; }
        .section-title { font-size: 13pt; font-weight: bold; margin: 20px 0 8px; padding-bottom: 4px; border-bottom: 1px solid #d5e0ea; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9.5pt; }
        th { background: #f3f8f7; padding: 6px 5px; text-align: left; font-size: 8.5pt; text-transform: uppercase; letter-spacing: .3px; color: #4c6373; border-bottom: 1px solid #d5e0ea; }
        td { padding: 5px; border-bottom: 1px solid #e4edf3; vertical-align: top; }
        .text-right { text-align: right; }
        .sum-row td { font-weight: bold; background: #f3f8f7; }
        .total-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 14px; margin-top: 20px; }
        .total-box h2 { margin: 0 0 6px; font-size: 15pt; }
        .payment-box { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px; padding: 14px; margin-top: 16px; }
        .schedule-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 14px; margin-top: 16px; }
        .description-box { border: 1px solid #d5e0ea; border-radius: 8px; padding: 14px; margin-top: 16px; }
        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #d5e0ea; font-size: 9pt; color: #4c6373; text-align: center; }
    </style>
</head>
<body>

<div class="header">
    <div class="header-inner">
        <div>
            <h1>OFERTA</h1>
            <div class="subtitle">
                @if($offer->offer_number) Nr {{ $offer->offer_number }} &nbsp;|&nbsp; @endif
                @if($offer->offer_date) Data: {{ $offer->offer_date->format('d.m.Y') }} @endif
            </div>
        </div>
        <div style="text-align:right;font-size:10pt;color:#4c6373;">
            ENESA — Energy Audit Systems<br>
            {{ date('d.m.Y') }}
        </div>
    </div>
</div>

<div class="info-grid">
    <div class="info-box">
        <h3>Dane oferty</h3>
        <p><strong>Tytuł:</strong> {{ $offer->offer_title }}</p>
        @if($offer->offer_number)
        <p><strong>Numer:</strong> {{ $offer->offer_number }}</p>
        @endif
        @if($offer->offer_date)
        <p><strong>Data:</strong> {{ $offer->offer_date->format('d.m.Y') }}</p>
        @endif
        @if($offer->crmDeal)
        <p><strong>Szansa CRM:</strong> {{ $offer->crmDeal->name }}</p>
        @endif
    </div>
    <div class="info-box">
        <h3>Dane klienta</h3>
        @if($offer->customer_name)<p><strong>{{ $offer->customer_name }}</strong></p>@endif
        @if($offer->customer_nip)<p>NIP: {{ $offer->customer_nip }}</p>@endif
        @if($offer->customer_address)<p>{{ $offer->customer_address }}</p>@endif
        @if($offer->customer_postal_code || $offer->customer_city)<p>{{ trim($offer->customer_postal_code . ' ' . $offer->customer_city) }}</p>@endif
        @if($offer->customer_phone)<p>Tel: {{ $offer->customer_phone }}</p>@endif
        @if($offer->customer_email)<p>E-mail: {{ $offer->customer_email }}</p>@endif
    </div>
</div>

@php
// Helper: convert stored value (float or formatted string) to float
function offerNumeric($v): float {
    if (is_numeric($v)) return (float) $v;
    $s = preg_replace('/[^\d,.\-]/', '', (string) $v);
    $s = str_replace(',', '.', $s);
    return (float) $s;
}
@endphp
{{-- SEKCJE GŁÓWNE --}}
@php
    $sections = [
        ['id' => 'services',  'label' => 'Usługi',        'items' => $offer->services   ?? []],
        ['id' => 'works',     'label' => 'Prace własne',  'items' => $offer->works      ?? []],
        ['id' => 'materials', 'label' => 'Materiały',     'items' => $offer->materials  ?? []],
    ];
    $showUnitPrices = $offer->show_unit_prices ?? true;
@endphp

@foreach($sections as $section)
@if(!empty($section['items']))
<div class="section-title">{{ $section['label'] }}</div>
<table>
    <thead>
        <tr>
            <th style="width:25px;">Nr</th>
            <th>Nazwa</th>
            <th>Opis/Typ</th>
            <th style="width:50px;">Ilość</th>
            @if($showUnitPrices)<th class="text-right" style="width:90px;">Cena jedn.</th>@endif
            <th class="text-right" style="width:90px;">Wartość</th>
        </tr>
    </thead>
    <tbody>
        @foreach($section['items'] as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item['name'] ?? '' }}</td>
            <td>{{ $item['type'] ?? '' }}</td>
            <td>{{ $item['quantity'] ?? '' }}</td>
            @if($showUnitPrices)<td class="text-right">{{ isset($item['price']) ? number_format(offerNumeric($item['price']), 2, ',', ' ').' zł' : '' }}</td>@endif
            <td class="text-right">{{ isset($item['value']) ? number_format(offerNumeric($item['value']), 2, ',', ' ').' zł' : '' }}</td>
        </tr>
        @endforeach
        @php($sectionSum = array_sum(array_map(fn($it) => offerNumeric($it['value'] ?? 0), $section['items'])))
        <tr class="sum-row">
            <td colspan="{{ $showUnitPrices ? 5 : 4 }}" class="text-right">Suma {{ $section['label'] }}:</td>
            <td class="text-right">{{ number_format($sectionSum, 2, ',', ' ') }} zł</td>
        </tr>
    </tbody>
</table>
@endif
@endforeach

{{-- SEKCJE NIESTANDARDOWE --}}
@foreach($offer->custom_sections ?? [] as $cs)
@if(!empty($cs['items']))
<div class="section-title">{{ $cs['name'] ?? 'Sekcja' }}</div>
<table>
    <thead>
        <tr>
            <th style="width:25px;">Nr</th>
            <th>Nazwa</th>
            <th>Opis/Typ</th>
            <th style="width:50px;">Ilość</th>
            @if($showUnitPrices)<th class="text-right" style="width:90px;">Cena jedn.</th>@endif
            <th class="text-right" style="width:90px;">Wartość</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cs['items'] as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item['name'] ?? '' }}</td>
            <td>{{ $item['type'] ?? '' }}</td>
            <td>{{ $item['quantity'] ?? '' }}</td>
            @if($showUnitPrices)<td class="text-right">{{ isset($item['price']) ? number_format(offerNumeric($item['price']), 2, ',', ' ').' zł' : '' }}</td>@endif
            <td class="text-right">{{ isset($item['value']) ? number_format(offerNumeric($item['value']), 2, ',', ' ').' zł' : '' }}</td>
        </tr>
        @endforeach
        @php($csSum = array_sum(array_map(fn($it) => offerNumeric($it['value'] ?? 0), $cs['items'])))
        <tr class="sum-row">
            <td colspan="{{ $showUnitPrices ? 5 : 4 }}" class="text-right">Suma {{ $cs['name'] ?? 'Sekcja' }}:</td>
            <td class="text-right">{{ number_format($csSum, 2, ',', ' ') }} zł</td>
        </tr>
    </tbody>
</table>
@endif
@endforeach

{{-- ŁĄCZNA CENA --}}
<div class="total-box">
    <h2 style="color:#0f2330;">Łączna wartość oferty:</h2>
    <p style="font-size:22pt;font-weight:800;margin:0;color:#1ba84a;">
        {{ number_format($offer->total_price, 2, ',', ' ') }} zł
    </p>
    @if($offer->profit_amount > 0)
    <p style="font-size:10pt;color:#4c6373;margin:4px 0 0;">
        Zawiera marżę: {{ number_format($offer->profit_amount, 2, ',', ' ') }} zł
        ({{ number_format($offer->profit_percent, 1, ',', ' ') }}%)
    </p>
    @endif
</div>

{{-- HARMONOGRAM --}}
@if($offer->schedule_enabled && !empty($offer->schedule))
<div class="schedule-box">
    <h3 style="margin:0 0 10px;font-size:12pt;">Harmonogram realizacji</h3>
    <table>
        <thead>
            <tr>
                <th style="width:30px;">Nr</th>
                <th>Etap / Kamień milowy</th>
                <th>Opis</th>
            </tr>
        </thead>
        <tbody>
            @foreach($offer->schedule as $i => $step)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $step['milestone'] ?? '' }}</td>
                <td>{{ $step['description'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- WARUNKI PŁATNOŚCI --}}
@if(!empty($offer->payment_terms))
<div class="payment-box">
    <h3 style="margin:0 0 10px;font-size:12pt;">Warunki płatności</h3>
    <table>
        <thead>
            <tr>
                <th style="width:30px;">Nr</th>
                <th>Opis raty</th>
                <th style="width:70px;">% wartości</th>
                <th style="width:120px;">Termin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($offer->payment_terms as $i => $pt)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $pt['description'] ?? '' }}</td>
                <td class="text-right">{{ isset($pt['percent']) ? $pt['percent'].'%' : '' }}</td>
                <td>{{ $pt['deadline'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- OPIS --}}
@if($offer->offer_description)
<div class="description-box">
    <h3 style="margin:0 0 8px;font-size:12pt;">Opis oferty</h3>
    {!! $offer->offer_description !!}
</div>
@endif

<div class="footer">
    Oferta przygotowana przez ENESA — Energy Audit Systems &nbsp;|&nbsp; {{ date('d.m.Y') }}
</div>

</body>
</html>
