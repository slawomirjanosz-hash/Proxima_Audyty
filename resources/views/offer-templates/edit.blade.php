<x-layouts.app>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.17/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.17/theme/dracula.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.17/addon/dialog/dialog.min.css">
<style>
.ot-panel { background:#fff; border:1px solid var(--paper-deep); border-radius:14px; padding:20px; margin-bottom:16px; }
.ot-label { display:block; font-size:12px; font-weight:700; color:var(--ink-mute); margin-bottom:4px; text-transform:uppercase; letter-spacing:.4px; }
.ot-input { padding:9px 12px; border-radius:9px; border:1px solid #c9d7e3; font-size:14px; width:100%; box-sizing:border-box; background:#fff; transition: border-color .15s; }
.ot-input:focus { border-color:#1A4D3A; outline:none; box-shadow:0 0 0 3px rgba(26,77,58,.08); }
.ot-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.ot-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }
.ot-btn { padding:9px 18px; border-radius:9px; border:0; cursor:pointer; font-size:14px; font-weight:600; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
.ot-btn-green { background:var(--green-primary); color:#fff; }
.ot-btn-gray  { background:#718096; color:#fff; }
.ot-btn-blue  { background:#3b82f6; color:#fff; }
.ot-btn-red   { background:#dc2626; color:#fff; }
.editor-wrap { display:grid; grid-template-columns:1fr 1fr; gap:0; border:1px solid #c9d7e3; border-radius:12px; overflow:hidden; height:600px; }
.editor-col { display:flex; flex-direction:column; }
.editor-col-header { background:#1a1a2e; color:#e2e8f0; padding:10px 16px; font-size:13px; font-weight:600; display:flex; align-items:center; justify-content:space-between; }
.editor-col .CodeMirror { font-family:'Fira Code','Courier New',monospace; font-size:12.5px; line-height:1.6; height:556px; }
.editor-col .CodeMirror-gutters { border-right:1px solid #3d3d5c; }
#html-preview { flex:1; width:100%; height:100%; border:none; background:#fff; }
.placeholder-tags { display:flex; flex-wrap:wrap; gap:6px; padding:10px 14px; background:#f8fafc; border-radius:8px 8px 0 0; border:1px solid #c9d7e3; border-bottom:none; }
.placeholder-tags span { font-size:11px; font-family:monospace; background:#e0f2fe; color:#0369a1; padding:3px 8px; border-radius:5px; cursor:pointer; user-select:none; }
.placeholder-tags span:hover { background:#bae6fd; }
.di-tbl { width:100%; border-collapse:collapse; font-size:13px; }
.di-tbl th { padding:8px 6px; font-size:11px; text-transform:uppercase; letter-spacing:.4px; color:var(--ink-mute); background:#f3f8f7; text-align:left; border-bottom:1px solid #e4edf3; }
.di-tbl td { padding:4px 4px; border-bottom:1px solid #f0f4f8; }
.di-input { padding:5px 7px; border-radius:6px; border:1px solid #c9d7e3; font-size:13px; width:100%; box-sizing:border-box; }
</style>

<div class="panel">
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <div>
        <h2 style="margin:0 0 4px;font-size:22px;">Edytuj szablon: <em>{{ $template->name }}</em></h2>
        <p style="margin:0;font-size:13px;color:var(--ink-mute);">Kod: <code>{{ $template->type_code }}</code> &nbsp;|&nbsp; {{ $template->offers_count ?? $template->offers()->count() }} ofert korzysta z tego szablonu</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('offer-templates.preview', $template) }}" target="_blank" class="ot-btn" style="background:#eff6ff;color:#1d4ed8;">👁 Podgląd</a>
        <a href="{{ route('offer-templates.index', ['category' => $template->audit_category]) }}" class="ot-btn ot-btn-gray">← Wróć</a>
    </div>
</div>

@if(session('status'))
    <div style="background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:14px;color:#166534;font-weight:600;">{{ session('status') }}</div>
@endif
@if($errors->any())
    <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:14px;color:#991b1b;">
        <ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('offer-templates.update', $template) }}" id="tpl-form">
@csrf @method('PUT')
<input type="hidden" name="audit_category" value="{{ old('audit_category', $template->audit_category) }}">

{{-- PODSTAWOWE DANE --}}
<div class="ot-panel">
    <div class="ot-grid-2" style="margin-bottom:14px;">
        <div>
            <label class="ot-label">Nazwa szablonu *</label>
            <input type="text" name="name" value="{{ old('name', $template->name) }}" class="ot-input" required>
        </div>
        <div>
            <label class="ot-label">Kod typu *</label>
            <input type="text" name="type_code" value="{{ old('type_code', $template->type_code) }}" class="ot-input" required>
        </div>
    </div>
    <div style="margin-bottom:14px;">
        <label class="ot-label">Opis</label>
        <textarea name="description" class="ot-input" rows="2">{{ old('description', $template->description) }}</textarea>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }} style="width:16px;height:16px;">
        <label for="is_active" style="font-size:14px;cursor:pointer;">Szablon aktywny</label>
    </div>
</div>

{{-- STAWKI --}}
<div class="ot-panel">
    <h3 style="margin:0 0 14px;font-size:16px;color:#1A4D3A;">Domyślne stawki i godziny audytu</h3>
    <div class="ot-grid-3">
        <div>
            <label class="ot-label">Stawka za km (zł/km)</label>
            <input type="number" name="default_km_rate" step="0.01" min="0" value="{{ old('default_km_rate', $template->default_km_rate) }}" class="ot-input">
        </div>
        <div>
            <label class="ot-label">Stawka za godz. jazdy (zł/h)</label>
            <input type="number" name="default_hour_rate" step="0.01" min="0" value="{{ old('default_hour_rate', $template->default_hour_rate) }}" class="ot-input">
        </div>
        <div>
            <label class="ot-label">Domyślna liczba godzin audytu</label>
            <input type="number" name="default_auditor_hours" step="0.5" min="0" value="{{ old('default_auditor_hours', $template->default_auditor_hours) }}" class="ot-input">
        </div>
    </div>
</div>

{{-- DOMYŚLNE WARTOŚCI PÓL --}}
<div class="ot-panel">
    @php $df = $template->default_fields ?? []; @endphp
    <h3 style="margin:0 0 14px;font-size:16px;color:#1A4D3A;">Domyślne wartości pól oferty</h3>
    <p style="margin:0 0 14px;font-size:12px;color:var(--ink-mute);">Wartości domyślne używane gdy pole nie jest uzupełnione przy tworzeniu oferty.</p>

    @php
        $cA  = 'background:#f3f8f7;padding:2px 6px;border-radius:4px;font-size:11px;color:#1A4D3A;';
        $cM  = 'background:#f3f8f7;padding:2px 6px;border-radius:4px;font-size:11px;color:#9ca3af;';
        $rH  = 'border-bottom:1px solid #e8f0ed;';
        $rAlt = 'border-bottom:1px solid #e8f0ed;background:#f9fafb;';
        $tA  = 'padding:8px 12px;color:#9ca3af;font-style:italic;font-size:12px;';
        $tN  = 'padding:8px 12px;color:#374151;';
        $tV  = 'padding:8px 12px;color:#9ca3af;font-size:12px;font-style:italic;';
        $hdr = 'padding:5px 12px;font-size:11px;font-weight:700;color:#1A4D3A;letter-spacing:.5px;text-transform:uppercase;';
    @endphp
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="background:#f3f8f7;">
                <th style="padding:8px 12px;text-align:left;font-weight:600;color:#1A4D3A;border-bottom:2px solid #c9d7e3;width:24%;">Pole</th>
                <th style="padding:8px 12px;text-align:left;font-weight:600;color:#1A4D3A;border-bottom:2px solid #c9d7e3;width:22%;">Zmienna</th>
                <th style="padding:8px 12px;text-align:left;font-weight:600;color:#1A4D3A;border-bottom:2px solid #c9d7e3;">Wartość domyślna</th>
            </tr>
        </thead>
        <tbody>

            {{-- ══ OFERTA ══ --}}
            <tr style="background:#e8f3ef;"><td colspan="3" style="{{ $hdr }}">Oferta</td></tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Tytuł oferty</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{offer_title}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_offer_title" value="{{ old('df_offer_title', $df['offer_title'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="np. Oferta na audyt energetyczny"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Przedmiot oferty</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{offer_subject}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_offer_subject" value="{{ old('df_offer_subject', $df['offer_subject'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="np. Przeprowadzenie audytu energetycznego"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Opis / wstęp oferty</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{description}}</code></td>
                <td style="padding:6px 12px;"><textarea name="df_offer_description" class="ot-input" rows="2" style="margin:0;" placeholder="Domyślny opis lub wstęp...">{{ old('df_offer_description', $df['offer_description'] ?? '') }}</textarea></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Numer oferty</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{offer_number}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_offer_number" value="{{ old('df_offer_number', $df['offer_number'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="np. OF-2026/001"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Data oferty</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{offer_date}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_offer_date" value="{{ old('df_offer_date', $df['offer_date'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="np. 2026-01-01"></td>
            </tr>

            {{-- ══ KLIENT ══ --}}
            <tr style="background:#e8f3ef;"><td colspan="3" style="{{ $hdr }}">Klient</td></tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Rodzaj klienta</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_type}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_customer_type" value="{{ old('df_customer_type', $df['customer_type'] ?? 'Firma') }}" class="ot-input" style="margin:0;" placeholder="np. Firma / Osoba fizyczna"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Nazwa klienta</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_name}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_customer_name" value="{{ old('df_customer_name', $df['customer_name'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="Domyślna nazwa klienta..."></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">NIP klienta</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_nip}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_customer_nip" value="{{ old('df_customer_nip', $df['customer_nip'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="np. 123-456-78-90"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Adres klienta</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_address}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_customer_address" value="{{ old('df_customer_address', $df['customer_address'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="ul. Przykładowa 1"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Kod pocztowy</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_postal_code}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_customer_postal_code" value="{{ old('df_customer_postal_code', $df['customer_postal_code'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="np. 44-100"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Miasto klienta</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_city}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_customer_city" value="{{ old('df_customer_city', $df['customer_city'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="np. Gliwice"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Telefon klienta</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_phone}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_customer_phone" value="{{ old('df_customer_phone', $df['customer_phone'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="+48 ..."></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">E-mail klienta</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_email}}</code></td>
                <td style="padding:6px 12px;"><input type="email" name="df_customer_email" value="{{ old('df_customer_email', $df['customer_email'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="kontakt@firma.pl"></td>
            </tr>

            {{-- ══ POZYCJE ══ --}}
            <tr style="background:#e8f3ef;"><td colspan="3" style="{{ $hdr }}">Pozycje cenowe</td></tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Stawka VAT %</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{vat_rate}}</code><div style="font-size:10px;color:var(--ink-mute);margin-top:2px;">→ liczy @{{total_price_vat}}, @{{total_price}}</div></td>
                <td style="padding:6px 12px;"><input type="number" name="df_vat_rate" value="{{ old('df_vat_rate', $df['vat_rate'] ?? '23') }}" class="ot-input" style="margin:0;max-width:120px;" min="0" max="100" step="1" placeholder="23"></td>
            </tr>
            <tr style="{{ $rAlt }}"><td style="{{ $tA }}">Tabela pozycji</td><td style="padding:8px 12px;"><code style="{{ $cM }}">@{{items_table}}</code></td><td style="{{ $tV }}">generowana z pozycji oferty (sekcja poniżej)</td></tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Cena netto</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{total_price_net}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_total_price_net" value="{{ old('df_total_price_net', $df['total_price_net'] ?? '') }}" class="ot-input" style="margin:0;max-width:160px;" min="0" step="0.01" placeholder="np. 10000"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Kwota VAT</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{total_price_vat}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_total_price_vat" value="{{ old('df_total_price_vat', $df['total_price_vat'] ?? '') }}" class="ot-input" style="margin:0;max-width:160px;" min="0" step="0.01" placeholder="np. 2300"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Cena brutto</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{total_price}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_total_price" value="{{ old('df_total_price', $df['total_price'] ?? '') }}" class="ot-input" style="margin:0;max-width:160px;" min="0" step="0.01" placeholder="np. 12300"></td>
            </tr>

            {{-- ══ WARUNKI ══ --}}
            <tr style="background:#e8f3ef;"><td colspan="3" style="{{ $hdr }}">Warunki</td></tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Warunki płatności</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{payment_terms}}</code></td>
                <td style="padding:6px 12px;"><textarea name="df_payment_terms_text" class="ot-input" rows="2" style="margin:0;">{{ old('df_payment_terms_text', $df['payment_terms_text'] ?? 'Płatność na podstawie faktury VAT, 14 dni od wystawienia.') }}</textarea></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Termin ważności oferty</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{offer_validity}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_offer_validity" value="{{ old('df_offer_validity', $df['offer_validity'] ?? '30 dni') }}" class="ot-input" style="margin:0;" placeholder="np. 30 dni"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Termin realizacji</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{delivery_deadline}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_delivery_deadline" value="{{ old('df_delivery_deadline', $df['delivery_deadline'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="np. 30 dni roboczych"></td>
            </tr>

            {{-- ══ DOJAZD ══ --}}
            <tr style="background:#e8f3ef;"><td colspan="3" style="{{ $hdr }}">Dojazd</td></tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Domyślny dystans (km)</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{distance_km}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_distance_km" value="{{ old('df_distance_km', $df['distance_km'] ?? '') }}" class="ot-input" style="margin:0;max-width:140px;" min="0" step="1" placeholder="np. 80"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Stawka km (zł/km)</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{km_rate}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_km_rate" value="{{ old('df_km_rate', $df['km_rate'] ?? '') }}" class="ot-input" style="margin:0;max-width:140px;" min="0" step="0.01" placeholder="np. 1.50"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Domyślny czas jazdy (godz.)</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{travel_hours}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_travel_hours" value="{{ old('df_travel_hours', $df['travel_hours'] ?? '') }}" class="ot-input" style="margin:0;max-width:140px;" min="0" step="0.5" placeholder="np. 1.5"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Stawka godz. jazdy (zł/h)</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{hour_rate}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_hour_rate" value="{{ old('df_hour_rate', $df['hour_rate'] ?? '') }}" class="ot-input" style="margin:0;max-width:140px;" min="0" step="0.01" placeholder="np. 80"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Koszt dojazdu</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{travel_cost}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_travel_cost" value="{{ old('df_travel_cost', $df['travel_cost'] ?? '') }}" class="ot-input" style="margin:0;max-width:160px;" min="0" step="0.01" placeholder="np. 600"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Liczba godz. audytu</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{auditor_hours}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_auditor_hours" value="{{ old('df_auditor_hours', $df['auditor_hours'] ?? '') }}" class="ot-input" style="margin:0;max-width:140px;" min="0" step="0.5" placeholder="np. 8"></td>
            </tr>

            {{-- ══ ENESA ══ --}}
            <tr style="background:#e8f3ef;"><td colspan="3" style="{{ $hdr }}">ENESA — Moja firma</td></tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Nazwa firmy</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{enesa_name}}</code><div style="font-size:10px;color:var(--ink-mute);margin-top:2px;">Fallback gdy brak ustawień „Moja firma"</div></td>
                <td style="padding:6px 12px;"><input type="text" name="df_enesa_name" value="{{ old('df_enesa_name', $df['enesa_name'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="np. Enesa Sp. z o.o."></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">NIP firmy</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{enesa_nip}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_enesa_nip" value="{{ old('df_enesa_nip', $df['enesa_nip'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="np. 123-456-78-90"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Ulica / adres</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{enesa_street}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_enesa_street" value="{{ old('df_enesa_street', $df['enesa_street'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="ul. ..."></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Miasto</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{enesa_city}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_enesa_city" value="{{ old('df_enesa_city', $df['enesa_city'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="np. Gliwice"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Kod pocztowy</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{enesa_postal}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_enesa_postal" value="{{ old('df_enesa_postal', $df['enesa_postal'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="np. 44-100"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">E-mail firmy</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{enesa_email}}</code></td>
                <td style="padding:6px 12px;"><input type="email" name="df_enesa_email" value="{{ old('df_enesa_email', $df['enesa_email'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="biuro@enesa.pl"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Telefon firmy</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{enesa_phone}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_enesa_phone" value="{{ old('df_enesa_phone', $df['enesa_phone'] ?? '') }}" class="ot-input" style="margin:0;" placeholder="+48 ..."></td>
            </tr>

        </tbody>
    </table>
</div>

{{-- DOMYŚLNE POZYCJE --}}
<div class="ot-panel">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <h3 style="margin:0;font-size:16px;color:#1A4D3A;">Domyślne pozycje cenowe</h3>
        <button type="button" onclick="addDiRow()" class="ot-btn ot-btn-blue" style="font-size:13px;padding:6px 14px;">+ Dodaj pozycję</button>
    </div>
    <table class="di-tbl">
        <thead>
            <tr>
                <th>Nazwa pozycji</th><th>Opis/Typ</th>
                <th style="width:70px;">Ilość</th><th style="width:110px;">Cena jedn. (zł)</th>
                <th style="width:40px;"></th>
            </tr>
        </thead>
        <tbody id="di-table"></tbody>
    </table>
</div>

{{-- EDYTOR HTML --}}
<div class="ot-panel">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <div>
            <h3 style="margin:0 0 4px;font-size:16px;color:#1A4D3A;">Szablon HTML oferty</h3>
            <p style="margin:0;font-size:12px;color:var(--ink-mute);">Znaczniki <code style="background:#f3f8f7;padding:1px 5px;border-radius:4px;">{{"{{"}}pole{!! '}}' !!}</code> zostaną zastąpione danymi oferty.</p>
        </div>
        <button type="button" onclick="refreshPreview()" class="ot-btn ot-btn-blue" style="font-size:13px;padding:7px 14px;">↻ Odśwież podgląd</button>
    </div>

    @php
    $phGroups = [
        'Oferta'  => ['offer_title','offer_number','offer_date','offer_subject','description'],
        'Klient'  => ['customer_name','customer_type','customer_nip','customer_address','customer_postal_code','customer_city','customer_phone','customer_email'],
        'Pozycje' => ['items_table','total_price_net','vat_rate','total_price_vat','total_price'],
        'Warunki' => ['payment_terms','offer_validity','delivery_deadline'],
        'Dojazd'  => ['distance_km','km_rate','travel_hours','hour_rate','travel_cost','auditor_hours'],
        'ENESA'   => ['enesa_name','enesa_nip','enesa_street','enesa_city','enesa_postal','enesa_email','enesa_phone'],
    ];
    @endphp
    <div class="placeholder-tags" style="margin-bottom:10px;flex-direction:column;gap:5px;align-items:stretch;">
        @foreach($phGroups as $grpLabel => $phs)
        <div style="display:flex;flex-wrap:wrap;gap:5px;align-items:center;">
            <span style="font-size:10px;font-weight:700;color:var(--ink-mute);min-width:52px;text-transform:uppercase;letter-spacing:.3px;flex-shrink:0;">{{ $grpLabel }}:</span>
            @foreach($phs as $ph)
            <span onclick="insertPlaceholder('{{"{{"}}{{ $ph }}{!! '}}' !!}')">{{"{{"}}{{ $ph }}{!! '}}' !!}</span>
            @endforeach
        </div>
        @endforeach
    </div>

    <div class="editor-wrap">
        <div class="editor-col">
            <div class="editor-col-header">
                <span>📝 Kod HTML</span>
                <button type="button" onclick="clearEditor()" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:12px;">✕ Wyczyść</button>
            </div>
            <textarea id="html-textarea" name="html_content" spellcheck="false">{{ old('html_content', $template->html_content) }}</textarea>
        </div>
        <div class="editor-col" style="border-left:2px solid #1A4D3A;">
            <div class="editor-col-header" style="background:#1A4D3A;">
                <span>👁 Podgląd na żywo</span>
                <button type="button" onclick="openFullPreview()" style="background:none;border:none;color:#a7f3d0;cursor:pointer;font-size:12px;">⤢ Pełny ekran</button>
            </div>
            <iframe id="html-preview" sandbox="allow-same-origin allow-scripts"></iframe>
        </div>
    </div>
</div>

{{-- PRZYCISKI --}}
<div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;">
    <a href="{{ route('offer-templates.index', ['category' => $template->audit_category]) }}" class="ot-btn ot-btn-gray">Anuluj</a>
    <button type="submit" class="ot-btn ot-btn-green">💾 Zapisz zmiany</button>
</div>

</form>

{{-- NIEBEZPIECZNA STREFA — poza głównym <form>, żeby nie gniazdować formularzy --}}
@if(!$template->offers()->exists())
<div class="ot-panel" style="border-color:#fca5a5;margin-top:8px;">
    <h3 style="margin:0 0 10px;font-size:15px;color:#dc2626;">Strefa usunięcia</h3>
    <form method="POST" action="{{ route('offer-templates.destroy', $template) }}" onsubmit="return confirm('Na pewno usunąć szablon {{ addslashes($template->name) }}?')">
        @csrf @method('DELETE')
        <button type="submit" class="ot-btn ot-btn-red" style="font-size:13px;">🗑 Usuń szablon</button>
    </form>
</div>
@endif
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.17/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.17/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.17/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.17/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.17/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.17/addon/edit/closetag.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.17/addon/edit/matchbrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.17/addon/dialog/dialog.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.17/addon/search/searchcursor.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.17/addon/search/search.min.js"></script>
<script>
const editor = CodeMirror.fromTextArea(document.getElementById('html-textarea'), {
    mode: 'htmlmixed',
    theme: 'dracula',
    lineNumbers: true,
    lineWrapping: true,
    autoCloseTags: true,
    matchBrackets: true,
    indentWithTabs: false,
    indentUnit: 2,
    tabSize: 2,
    extraKeys: {
        'Ctrl-Z': 'undo',
        'Ctrl-Y': 'redo',
        'Shift-Ctrl-Z': 'redo',
        'Cmd-Z': 'undo',
        'Cmd-Y': 'redo',
        'Ctrl-F': 'findPersistent',
        'Cmd-F': 'findPersistent',
        'Ctrl-H': 'replace',
        'Cmd-H': 'replace',
    }
});
editor.setSize('100%', 556);

document.getElementById('tpl-form').addEventListener('submit', function() {
    editor.save();
});

function insertPlaceholder(ph) {
    editor.replaceSelection(ph);
    editor.focus();
    refreshPreview();
}

// ph() renders an unfilled variable as a highlighted badge
function ph(name) {
    return '<span style="display:inline;background:#fef9c3;color:#78350f;padding:1px 6px;border-radius:3px;font-family:monospace;font-size:.88em;border:1px dashed #f59e0b;">' + '{{' + name + '}}' + '</span>';
}
function getV(selector) {
    const el = document.querySelector(selector);
    return el && el.value.trim() ? el.value.trim() : null;
}

function escHtmlPh(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function buildItemsTableHtml() {
    const rows = document.querySelectorAll('#di-table tr');
    let items = [];
    rows.forEach(tr => {
        const name  = tr.querySelector('[name^="di_name"]')?.value.trim()  || '';
        const type  = tr.querySelector('[name^="di_type"]')?.value.trim()  || '';
        const qty   = parseFloat(tr.querySelector('[name^="di_qty"]')?.value)   || 1;
        const price = parseFloat(tr.querySelector('[name^="di_price"]')?.value) || 0;
        if (name) items.push({name, type, qty, price, val: qty * price});
    });
    const fmtC = n => n.toLocaleString('pl-PL', {minimumFractionDigits:2, maximumFractionDigits:2});
    const getN = sel => parseFloat(document.querySelector(sel)?.value) || 0;
    const kmRate   = getN('[name="df_km_rate"]')   || 1.5;
    const hourRate = getN('[name="df_hour_rate"]')  || 80;
    const distKm   = getN('[name="df_distance_km"]')|| 0;
    const travelH  = getN('[name="df_travel_hours"]')|| 0;
    const travelCost = getN('[name="df_travel_cost"]') || (travelH * hourRate + distKm * kmRate);
    if (travelCost > 0) {
        items.push({name: 'Koszty dojazdu i delegacji', type: 'usł.', qty: 1, price: travelCost, val: travelCost});
    }
    if (!items.length) return '<p style="color:#888;font-style:italic;padding:12px 0;">Brak pozycji — dodaj pozycje w sekcji "Domyślne pozycje cenowe" powyżej.</p>';
    let total = 0, html = '<table style="width:100%;border-collapse:collapse;font-size:13px;">'
        + '<thead><tr>'
        + '<th style="background:#1A4D3A;color:#fff;padding:10px;text-align:center;width:40px;">Nr</th>'
        + '<th style="background:#1A4D3A;color:#fff;padding:10px;text-align:left;">Nazwa pozycji</th>'
        + '<th style="background:#1A4D3A;color:#fff;padding:10px;text-align:left;">Opis / Typ</th>'
        + '<th style="background:#1A4D3A;color:#fff;padding:10px;text-align:center;width:60px;">Ilość</th>'
        + '<th style="background:#1A4D3A;color:#fff;padding:10px;text-align:right;width:110px;">Cena jedn.</th>'
        + '<th style="background:#1A4D3A;color:#fff;padding:10px;text-align:right;width:110px;">Wartość</th>'
        + '</tr></thead><tbody>';
    items.forEach((item, i) => {
        total += item.val;
        html += '<tr>'
            + `<td style="padding:8px 10px;border-bottom:1px solid #e4edf3;text-align:center;">${i+1}</td>`
            + `<td style="padding:8px 10px;border-bottom:1px solid #e4edf3;font-weight:600;">${escHtmlPh(item.name)}</td>`
            + `<td style="padding:8px 10px;border-bottom:1px solid #e4edf3;color:#555;">${escHtmlPh(item.type)}</td>`
            + `<td style="padding:8px 10px;border-bottom:1px solid #e4edf3;text-align:center;">${item.qty}</td>`
            + `<td style="padding:8px 10px;border-bottom:1px solid #e4edf3;text-align:right;">${fmtC(item.price)} zł</td>`
            + `<td style="padding:8px 10px;border-bottom:1px solid #e4edf3;text-align:right;font-weight:700;">${fmtC(item.val)} zł</td>`
            + '</tr>';
    });
    html += '</tbody><tfoot><tr>'
        + '<td colspan="5" style="padding:10px;text-align:right;font-weight:700;background:#1A4D3A;color:#fff;font-size:14px;">Razem (netto)</td>'
        + `<td style="padding:10px;text-align:right;font-weight:800;font-size:15px;background:#1A4D3A;color:#fff;">${fmtC(total)} zł</td>`
        + '</tr></tfoot></table>';
    return html;
}

function getDiTableTotal() {
    let total = 0;
    document.querySelectorAll('#di-table tr').forEach(tr => {
        const qty   = parseFloat(tr.querySelector('[name^="di_qty"]')?.value)   || 1;
        const price = parseFloat(tr.querySelector('[name^="di_price"]')?.value) || 0;
        const name  = tr.querySelector('[name^="di_name"]')?.value.trim() || '';
        if (name) total += qty * price;
    });
    const getN = sel => parseFloat(document.querySelector(sel)?.value) || 0;
    const kmRate   = getN('[name="df_km_rate"]')    || 1.5;
    const hourRate = getN('[name="df_hour_rate"]')   || 80;
    const distKm   = getN('[name="df_distance_km"]') || 0;
    const travelH  = getN('[name="df_travel_hours"]')|| 0;
    const travelCost = getN('[name="df_travel_cost"]') || (travelH * hourRate + distKm * kmRate);
    return total + travelCost;
}

let previewTimer = null;
function refreshPreview() {
    const iframe = document.getElementById('html-preview');
    const getN   = sel => parseFloat(getV(sel)) || 0;

    const kmRate    = getN('[name="df_km_rate"]') || 1.5;
    const hourRate  = getN('[name="df_hour_rate"]') || 80;
    const distKm    = getN('[name="df_distance_km"]') || 120;
    const travelH   = getN('[name="df_travel_hours"]') || 1.5;
    const calcTravel = (distKm * kmRate * 2) + (travelH * hourRate * 2);
    const travelCostVal = getN('[name="df_travel_cost"]') || calcTravel;

    const vatRate   = parseFloat(getV('[name="df_vat_rate"]') || '23') || 23;
    const netInput  = getN('[name="df_total_price_net"]');
    const demoNet   = netInput || getDiTableTotal() || 10000;
    const calcVat   = Math.round(demoNet * vatRate) / 100;
    const vatAmt    = getN('[name="df_total_price_vat"]') || calcVat;
    const calcGross = demoNet + vatAmt;
    const gross     = getN('[name="df_total_price"]') || calcGross;
    const fmt       = n => n.toLocaleString('pl-PL', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    const vals = {
        'offer_title':          getV('[name="df_offer_title"]'),
        'offer_number':         getV('[name="df_offer_number"]') || 'OF-2026/0001',
        'offer_date':           getV('[name="df_offer_date"]') || new Date().toLocaleDateString('pl-PL'),
        'offer_subject':        getV('[name="df_offer_subject"]'),
        'description':          getV('[name="df_offer_description"]'),
        'customer_name':        getV('[name="df_customer_name"]'),
        'customer_type':        getV('[name="df_customer_type"]'),
        'customer_nip':         getV('[name="df_customer_nip"]'),
        'customer_address':     getV('[name="df_customer_address"]'),
        'customer_postal_code': getV('[name="df_customer_postal_code"]'),
        'customer_city':        getV('[name="df_customer_city"]'),
        'customer_phone':       getV('[name="df_customer_phone"]'),
        'customer_email':       getV('[name="df_customer_email"]'),
        'items_table':          buildItemsTableHtml(),
        'distance_km':          distKm.toLocaleString('pl-PL', {minimumFractionDigits: 1}),
        'km_rate':              fmt(kmRate),
        'travel_hours':         travelH.toLocaleString('pl-PL', {minimumFractionDigits: 1}),
        'hour_rate':            fmt(hourRate),
        'travel_cost':          fmt(travelCostVal),
        'total_price_net':      fmt(demoNet),
        'vat_rate':             vatRate + '%',
        'total_price_vat':      fmt(vatAmt),
        'total_price':          fmt(gross),
        'auditor_hours':        getV('[name="df_auditor_hours"]') || '8',
        'offer_validity':       getV('[name="df_offer_validity"]'),
        'delivery_deadline':    getV('[name="df_delivery_deadline"]'),
        'payment_terms':        getV('[name="df_payment_terms_text"]'),
        'enesa_name':           getV('[name="df_enesa_name"]'),
        'enesa_nip':            getV('[name="df_enesa_nip"]'),
        'enesa_street':         getV('[name="df_enesa_street"]'),
        'enesa_city':           getV('[name="df_enesa_city"]'),
        'enesa_postal':         getV('[name="df_enesa_postal"]'),
        'enesa_email':          getV('[name="df_enesa_email"]'),
        'enesa_phone':          getV('[name="df_enesa_phone"]'),
    };

    let html = editor.getValue();
    Object.entries(vals).forEach(([key, val]) => {
        const re = new RegExp('\\{\\{' + key + '\\}\\}', 'g');
        html = html.replace(re, (val !== null && val !== '') ? val : ph(key));
    });
    html = html.replace(/\{\{([a-zA-Z0-9_]+)\}\}/g, (_, n) => ph(n));

    iframe.srcdoc = html;
}

editor.on('change', function() {
    clearTimeout(previewTimer);
    previewTimer = setTimeout(refreshPreview, 600);
});
document.querySelectorAll('[name^="df_"]').forEach(function(el) {
    el.addEventListener('input', function() {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(refreshPreview, 400);
    });
});
// Refresh preview when default items change
document.getElementById('di-table').addEventListener('input', function() {
    clearTimeout(previewTimer);
    previewTimer = setTimeout(refreshPreview, 400);
});
refreshPreview();

function clearEditor() {
    if (!confirm('Wyczyścić kod HTML?')) return;
    editor.setValue('');
    refreshPreview();
}

function formatHtml() {
    const totalLines = editor.lineCount();
    for (let i = 0; i < totalLines; i++) {
        editor.indentLine(i, 'smart');
    }
}

function openFullPreview() {
    const w = window.open('', '_blank');
    w.document.write(document.getElementById('html-preview').srcdoc || '<p>Brak podglądu.</p>');
    w.document.close();
}

// ─── Default items ───
let diCount = 0;
function addDiRow(name, type, qty, price) {
    const tbody = document.getElementById('di-table');
    const idx   = diCount++;
    const tr    = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" name="di_name[${idx}]" value="${esc(name||'')}" class="di-input" placeholder="Nazwa pozycji"></td>
        <td><input type="text" name="di_type[${idx}]" value="${esc(type||'')}" class="di-input" placeholder="Opis / typ"></td>
        <td><input type="number" name="di_qty[${idx}]" value="${qty||1}" min="0" step="0.01" class="di-input" style="text-align:right;"></td>
        <td><input type="number" name="di_price[${idx}]" value="${price||''}" min="0" step="0.01" class="di-input" style="text-align:right;" placeholder="0,00"></td>
        <td style="text-align:center;"><button type="button" onclick="this.closest('tr').remove();refreshPreview();" style="background:#fee2e2;color:#991b1b;border:0;border-radius:6px;padding:5px 9px;cursor:pointer;">✕</button></td>
    `;
    tbody.appendChild(tr);
    refreshPreview();
}
function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// Pre-fill existing default_items
@if($template->default_items)
    @foreach($template->default_items as $item)
        addDiRow(@json($item['name'] ?? ''), @json($item['type'] ?? ''), @json($item['quantity'] ?? 1), @json($item['price'] ?? ''));
    @endforeach
@endif
@if(old('di_name'))
    // validation fail — clear and refill from old
    document.getElementById('di-table').innerHTML = '';
    diCount = 0;
    @foreach(old('di_name', []) as $i => $name)
        addDiRow(@json($name), @json(old('di_type.'.$i,'')), @json(old('di_qty.'.$i,1)), @json(old('di_price.'.$i,'')));
    @endforeach
@endif
</script>
</x-layouts.app>
