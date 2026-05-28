<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Oferta — {{ $offer_title }}</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
    color: #1a1a1a;
    background: #fff;
    padding: 32px 40px;
    max-width: 800px;
    margin: 0 auto;
  }
  .hdr {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 3px solid #1A4D3A;
    padding-bottom: 24px;
    margin-bottom: 36px;
  }
  .logo-wrap img { height: 75px; width: auto; display: block; }
  .enesa-addr { text-align: right; font-size: 12px; color: #555; line-height: 1.9; }
  .enesa-addr strong { font-size: 13px; color: #1A4D3A; display: block; margin-bottom: 2px; }
  .title-block {
    text-align: center;
    padding: 32px 0;
    border-bottom: 1px solid #e4edf3;
    margin-bottom: 32px;
  }
  .badge {
    display: inline-block;
    background: #1A4D3A;
    color: #fff;
    padding: 6px 22px;
    border-radius: 30px;
    font-size: 12px;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 16px;
  }
  .offer-title { font-size: 26px; font-weight: 800; color: #1a1a1a; margin-bottom: 10px; }
  .offer-meta { font-size: 13px; color: #888; }
  .offer-meta span { margin: 0 12px; }
  .client-block {
    background: #f7faf9;
    border: 1px solid #c3ddd4;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 32px;
  }
  .client-block .label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #1A4D3A;
    font-weight: 700;
    margin-bottom: 10px;
  }
  .client-block .c-name { font-size: 18px; font-weight: 700; margin-bottom: 6px; }
  .client-block .c-detail { font-size: 13px; color: #555; line-height: 1.9; }
  .sec-title {
    font-size: 15px;
    font-weight: 700;
    color: #1A4D3A;
    border-left: 4px solid #1A4D3A;
    padding-left: 12px;
    margin: 32px 0 14px;
  }
  .description { font-size: 14px; line-height: 1.85; color: #333; margin-bottom: 32px; }
  .description p { margin-bottom: 10px; }
  .items-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 16px; }
  .items-table th {
    background: #1A4D3A;
    color: #fff;
    padding: 9px 12px;
    text-align: left;
    font-weight: 600;
  }
  .items-table th:first-child { border-radius: 8px 0 0 0; }
  .items-table th:last-child  { border-radius: 0 8px 0 0; text-align: right; }
  .items-table td { padding: 9px 12px; border-bottom: 1px solid #eef0f2; color: #1a1a1a; }
  .items-table td:last-child { text-align: right; }
  .totals-box { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 32px; }
  .totals-box td { padding: 7px 12px; }
  .totals-box td:first-child { color: #555; width: 70%; }
  .totals-box td:last-child  { font-weight: 600; text-align: right; }
  .totals-box tr.total-brutto td {
    border-top: 2px solid #c3ddd4;
    padding-top: 12px;
    font-weight: 700;
    font-size: 15px;
    color: #1A4D3A;
  }
  .travel-box {
    background: #eef8f3;
    border: 1px solid #c3ddd4;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 32px;
  }
  .travel-box table { width: 100%; font-size: 13px; border-collapse: collapse; }
  .travel-box td { padding: 7px 4px; vertical-align: top; }
  .travel-box td:first-child { color: #555; width: 65%; }
  .travel-box td:last-child  { font-weight: 600; }
  .travel-total td {
    border-top: 2px solid #c3ddd4;
    padding-top: 12px !important;
    font-weight: 700 !important;
    font-size: 15px !important;
    color: #1A4D3A !important;
  }
  .info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 32px;
    font-size: 13px;
  }
  .info-item .info-label {
    color: #888;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 3px;
  }
  .info-item .info-value { color: #1a1a1a; font-weight: 600; }
  .footer {
    border-top: 2px solid #e4edf3;
    margin-top: 40px;
    padding-top: 24px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    font-size: 12px;
    color: #888;
  }
  .sign-box { text-align: center; }
  .sign-line { border-top: 1px solid #bbb; width: 180px; margin: 48px auto 8px; }
  .footer-center { text-align: center; line-height: 1.8; }
</style>
</head>
<body>

{{-- NAGŁÓWEK --}}
<div class="hdr">
  <div class="logo-wrap">
    @if(file_exists(public_path('img/logo.png')))
      <img src="{{ public_path('img/logo.png') }}" alt="{{ $enesa_name }}">
    @else
      <strong style="font-size:20px;color:#1A4D3A;">{{ $enesa_name }}</strong>
    @endif
  </div>
  <div class="enesa-addr">
    <strong>{{ $enesa_name }}</strong>
    {{ $enesa_street }}<br>
    {{ $enesa_postal }} {{ $enesa_city }}<br>
    NIP: {{ $enesa_nip }}<br>
    {{ $enesa_email }} | {{ $enesa_phone }}
  </div>
</div>

{{-- TYTUŁ --}}
<div class="title-block">
  <div class="badge">Oferta handlowa</div>
  <div class="offer-title">{{ $offer_title }}</div>
  <div class="offer-meta">
    <span>Nr oferty: <strong>{{ $offer_number }}</strong></span>
    <span>Data: <strong>{{ $offer_date }}</strong></span>
    @if($auditor_hours)
    <span>Godzin audytu: <strong>{{ $auditor_hours }} h</strong></span>
    @endif
  </div>
</div>

{{-- ZAMAWIAJĄCY --}}
<div class="client-block">
  <div class="label">Zamawiający</div>
  <div class="c-name">{{ $customer_name }}</div>
  <div class="c-detail">
    @if($customer_nip)NIP: {{ $customer_nip }}<br>@endif
    {{ $customer_address }}, {{ $customer_postal_code }} {{ $customer_city }}<br>
    @if($customer_phone)Tel: {{ $customer_phone }} &nbsp;|&nbsp; @endif
    @if($customer_email)E-mail: {{ $customer_email }}@endif
  </div>
</div>

{{-- PRZEDMIOT OFERTY --}}
@if($description)
<div class="sec-title">Przedmiot oferty</div>
<div class="description">
  {!! nl2br(e($description)) !!}
</div>
@endif

{{-- ZAKRES I WYCENA --}}
@if(!empty($items))
<div class="sec-title">Zakres i wycena</div>
<table class="items-table">
  <thead>
    <tr>
      <th>Lp.</th>
      <th>Pozycja</th>
      <th>Jedn.</th>
      <th>Ilość</th>
      <th>Cena jedn. netto</th>
      <th>Wartość netto</th>
    </tr>
  </thead>
  <tbody>
    @foreach($items as $index => $item)
    <tr>
      <td>{{ $index + 1 }}</td>
      <td>{{ $item['name'] }}</td>
      <td>{{ $item['unit'] ?? 'szt.' }}</td>
      <td>{{ $item['qty'] ?? 1 }}</td>
      <td>{{ number_format($item['price_unit'], 2, ',', ' ') }} zł</td>
      <td>{{ number_format($item['price_total'], 2, ',', ' ') }} zł</td>
    </tr>
    @endforeach
  </tbody>
</table>

{{-- PODSUMOWANIE KWOT --}}
<table class="totals-box">
  <tr>
    <td>Wartość netto</td>
    <td>{{ number_format($total_price_net, 2, ',', ' ') }} zł</td>
  </tr>
  <tr>
    <td>VAT ({{ $vat_rate }}%)</td>
    <td>{{ number_format($total_price_vat, 2, ',', ' ') }} zł</td>
  </tr>
  <tr class="total-brutto">
    <td>Wartość brutto</td>
    <td>{{ number_format($total_price, 2, ',', ' ') }} zł</td>
  </tr>
</table>
@endif

{{-- WARUNKI --}}
<div class="sec-title">Warunki</div>
<div class="info-grid">
  @if($payment_terms)
  <div class="info-item">
    <div class="info-label">Warunki płatności</div>
    <div class="info-value">{{ $payment_terms }}</div>
  </div>
  @endif
  @if($offer_validity)
  <div class="info-item">
    <div class="info-label">Termin ważności oferty</div>
    <div class="info-value">{{ $offer_validity }}</div>
  </div>
  @endif
  @if($delivery_deadline)
  <div class="info-item">
    <div class="info-label">Termin realizacji</div>
    <div class="info-value">{{ $delivery_deadline }}</div>
  </div>
  @endif
</div>

{{-- STOPKA --}}
<div class="footer">
  <div class="sign-box">
    <div class="sign-line"></div>
    <div>Sporządził</div>
  </div>
  <div class="footer-center">
    {{ $enesa_name }} | {{ $enesa_street }}, {{ $enesa_postal }} {{ $enesa_city }}<br>
    {{ $enesa_email }} | www.enesa.pl
  </div>
  <div class="sign-box">
    <div class="sign-line"></div>
    <div>Data i podpis Zamawiającego</div>
  </div>
</div>

</body>
</html>
