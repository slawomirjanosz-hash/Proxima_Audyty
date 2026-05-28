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
.ot-grid-4 { display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:14px; }
.ot-form-row { margin-bottom:14px; }
.ot-btn { padding:9px 18px; border-radius:9px; border:0; cursor:pointer; font-size:14px; font-weight:600; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
.ot-btn-green { background:var(--green-primary); color:#fff; }
.ot-btn-gray  { background:#718096; color:#fff; }
.ot-btn-blue  { background:#3b82f6; color:#fff; }
.ot-btn-red   { background:#dc2626; color:#fff; }

/* HTML Editor layout */
.editor-wrap { display:grid; grid-template-columns:1fr 1fr; gap:0; border:1px solid #c9d7e3; border-radius:12px; overflow:hidden; height:600px; }
.editor-col { display:flex; flex-direction:column; }
.editor-col-header { background:#1a1a2e; color:#e2e8f0; padding:10px 16px; font-size:13px; font-weight:600; display:flex; align-items:center; justify-content:space-between; }
.editor-col-header .tabs button { background:none; border:none; color:#94a3b8; cursor:pointer; font-size:13px; padding:4px 10px; border-radius:6px; margin-left:4px; }
.editor-col-header .tabs button.active { background:#1A4D3A; color:#fff; }
.editor-col .CodeMirror { font-family:'Fira Code','Courier New',monospace; font-size:12.5px; line-height:1.6; height:556px; }
.editor-col .CodeMirror-gutters { border-right:1px solid #3d3d5c; }
#html-preview { flex:1; width:100%; height:100%; border:none; background:#fff; }
.placeholder-tags { display:flex; flex-wrap:wrap; gap:6px; padding:10px 14px; background:#f8fafc; border-top:1px solid #e4edf3; }
.placeholder-tags span { font-size:11px; font-family:monospace; background:#e0f2fe; color:#0369a1; padding:3px 8px; border-radius:5px; cursor:pointer; user-select:none; transition: background .15s; }
.placeholder-tags span:hover { background:#bae6fd; }

/* Items defaults table */
.di-tbl { width:100%; border-collapse:collapse; font-size:13px; }
.di-tbl th { padding:8px 6px; font-size:11px; text-transform:uppercase; letter-spacing:.4px; color:var(--ink-mute); background:#f3f8f7; text-align:left; border-bottom:1px solid #e4edf3; }
.di-tbl td { padding:4px 4px; border-bottom:1px solid #f0f4f8; }
.di-input { padding:5px 7px; border-radius:6px; border:1px solid #c9d7e3; font-size:13px; width:100%; box-sizing:border-box; }
</style>

<div class="panel">
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <div>
        <h2 style="margin:0 0 4px;font-size:22px;">Nowy szablon oferty</h2>
        <p style="margin:0;font-size:13px;color:var(--ink-mute);">Zdefiniuj strukturę HTML, domyślne stawki i godziny audytorów.</p>
    </div>
    <a href="{{ route('offer-templates.index') }}" class="ot-btn ot-btn-gray">← Wróć</a>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:14px;color:#991b1b;">
    <ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('offer-templates.store') }}" id="tpl-form">
@csrf
<input type="hidden" name="audit_category" value="{{ $category }}">

{{-- PODSTAWOWE DANE --}}
<div class="ot-panel">
    <div class="ot-grid-2" style="margin-bottom:14px;">
        <div class="ot-form-row" style="margin:0;">
            <label class="ot-label">Nazwa szablonu *</label>
            <input type="text" name="name" value="{{ old('name') }}" class="ot-input" required placeholder="np. Audyt energetyczny, Audyt kompresorów">
        </div>
        <div class="ot-form-row" style="margin:0;">
            <label class="ot-label">Kod typu * <span style="font-weight:400;text-transform:none;">(małe litery, cyfry, _)</span></label>
            <input type="text" name="type_code" id="type_code" value="{{ old('type_code') }}" class="ot-input" required placeholder="np. energy_audit">
        </div>
    </div>
    <div class="ot-form-row">
        <label class="ot-label">Opis szablonu</label>
        <textarea name="description" class="ot-input" rows="2" placeholder="Krótki opis — dla czego używamy tego szablonu">{{ old('description') }}</textarea>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} style="width:16px;height:16px;">
        <label for="is_active" style="font-size:14px;cursor:pointer;">Szablon aktywny (dostępny przy tworzeniu ofert)</label>
    </div>
</div>

{{-- STAWKI I GODZINY --}}
<div class="ot-panel">
    <h3 style="margin:0 0 14px;font-size:16px;color:#1A4D3A;">Domyślne stawki i godziny audytu</h3>
    <div class="ot-grid-3">
        <div class="ot-form-row" style="margin:0;">
            <label class="ot-label">Stawka za km (zł/km)</label>
            <input type="number" name="default_km_rate" step="0.01" min="0" value="{{ old('default_km_rate', '1.50') }}" class="ot-input">
        </div>
        <div class="ot-form-row" style="margin:0;">
            <label class="ot-label">Stawka za godz. jazdy (zł/h)</label>
            <input type="number" name="default_hour_rate" step="0.01" min="0" value="{{ old('default_hour_rate', '80.00') }}" class="ot-input">
        </div>
        <div class="ot-form-row" style="margin:0;">
            <label class="ot-label">Domyślna liczba godzin audytu</label>
            <input type="number" name="default_auditor_hours" step="0.5" min="0" value="{{ old('default_auditor_hours', '8.0') }}" class="ot-input">
        </div>
    </div>
</div>

{{-- DOMYŚLNE WARTOŚCI PÓL --}}
<div class="ot-panel">
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
                <td style="padding:6px 12px;"><input type="text" name="df_offer_title" value="{{ old('df_offer_title') }}" class="ot-input" style="margin:0;" placeholder="np. Oferta na audyt energetyczny"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Przedmiot oferty</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{offer_subject}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_offer_subject" value="{{ old('df_offer_subject') }}" class="ot-input" style="margin:0;" placeholder="np. Przeprowadzenie audytu energetycznego"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Opis / wstęp oferty</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{description}}</code></td>
                <td style="padding:6px 12px;"><textarea name="df_offer_description" class="ot-input" rows="2" style="margin:0;" placeholder="Domyślny opis lub wstęp...">{{ old('df_offer_description') }}</textarea></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Numer oferty</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{offer_number}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_offer_number" value="{{ old('df_offer_number') }}" class="ot-input" style="margin:0;" placeholder="np. OF-2026/001"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Data oferty</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{offer_date}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_offer_date" value="{{ old('df_offer_date') }}" class="ot-input" style="margin:0;" placeholder="np. 2026-01-01"></td>
            </tr>

            {{-- ══ KLIENT ══ --}}
            <tr style="background:#e8f3ef;"><td colspan="3" style="{{ $hdr }}">Klient</td></tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Rodzaj klienta</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_type}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_customer_type" value="{{ old('df_customer_type', 'Firma') }}" class="ot-input" style="margin:0;" placeholder="np. Firma / Osoba fizyczna"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Nazwa klienta</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_name}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_customer_name" value="{{ old('df_customer_name') }}" class="ot-input" style="margin:0;" placeholder="Domyślna nazwa klienta..."></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">NIP klienta</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_nip}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_customer_nip" value="{{ old('df_customer_nip') }}" class="ot-input" style="margin:0;" placeholder="np. 123-456-78-90"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Adres klienta</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_address}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_customer_address" value="{{ old('df_customer_address') }}" class="ot-input" style="margin:0;" placeholder="ul. Przykładowa 1"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Kod pocztowy</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_postal_code}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_customer_postal_code" value="{{ old('df_customer_postal_code') }}" class="ot-input" style="margin:0;" placeholder="np. 44-100"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Miasto klienta</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_city}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_customer_city" value="{{ old('df_customer_city') }}" class="ot-input" style="margin:0;" placeholder="np. Gliwice"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Telefon klienta</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_phone}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_customer_phone" value="{{ old('df_customer_phone') }}" class="ot-input" style="margin:0;" placeholder="+48 ..."></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">E-mail klienta</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{customer_email}}</code></td>
                <td style="padding:6px 12px;"><input type="email" name="df_customer_email" value="{{ old('df_customer_email') }}" class="ot-input" style="margin:0;" placeholder="kontakt@firma.pl"></td>
            </tr>

            {{-- ══ POZYCJE ══ --}}
            <tr style="background:#e8f3ef;"><td colspan="3" style="{{ $hdr }}">Pozycje cenowe</td></tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Stawka VAT %</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{vat_rate}}</code><div style="font-size:10px;color:var(--ink-mute);margin-top:2px;">→ liczy @{{total_price_vat}}, @{{total_price}}</div></td>
                <td style="padding:6px 12px;"><input type="number" name="df_vat_rate" value="{{ old('df_vat_rate', '23') }}" class="ot-input" style="margin:0;max-width:120px;" min="0" max="100" step="1" placeholder="23"></td>
            </tr>
            <tr style="{{ $rAlt }}"><td style="{{ $tA }}">Tabela pozycji</td><td style="padding:8px 12px;"><code style="{{ $cM }}">@{{items_table}}</code></td><td style="{{ $tV }}">generowana z pozycji oferty (sekcja poniżej)</td></tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Cena netto</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{total_price_net}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_total_price_net" value="{{ old('df_total_price_net') }}" class="ot-input" style="margin:0;max-width:160px;" min="0" step="0.01" placeholder="np. 10000"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Kwota VAT</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{total_price_vat}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_total_price_vat" value="{{ old('df_total_price_vat') }}" class="ot-input" style="margin:0;max-width:160px;" min="0" step="0.01" placeholder="np. 2300"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Cena brutto</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{total_price}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_total_price" value="{{ old('df_total_price') }}" class="ot-input" style="margin:0;max-width:160px;" min="0" step="0.01" placeholder="np. 12300"></td>
            </tr>

            {{-- ══ WARUNKI ══ --}}
            <tr style="background:#e8f3ef;"><td colspan="3" style="{{ $hdr }}">Warunki</td></tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Warunki płatności</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{payment_terms}}</code></td>
                <td style="padding:6px 12px;"><textarea name="df_payment_terms_text" class="ot-input" rows="2" style="margin:0;">{{ old('df_payment_terms_text', 'Płatność na podstawie faktury VAT, 14 dni od wystawienia.') }}</textarea></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Termin ważności oferty</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{offer_validity}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_offer_validity" value="{{ old('df_offer_validity', '30 dni') }}" class="ot-input" style="margin:0;" placeholder="np. 30 dni"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Termin realizacji</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{delivery_deadline}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_delivery_deadline" value="{{ old('df_delivery_deadline') }}" class="ot-input" style="margin:0;" placeholder="np. 30 dni roboczych"></td>
            </tr>

            {{-- ══ DOJAZD ══ --}}
            <tr style="background:#e8f3ef;"><td colspan="3" style="{{ $hdr }}">Dojazd</td></tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Domyślny dystans (km)</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{distance_km}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_distance_km" value="{{ old('df_distance_km') }}" class="ot-input" style="margin:0;max-width:140px;" min="0" step="1" placeholder="np. 80"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Stawka km (zł/km)</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{km_rate}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_km_rate" value="{{ old('df_km_rate') }}" class="ot-input" style="margin:0;max-width:140px;" min="0" step="0.01" placeholder="np. 1.50"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Domyślny czas jazdy (godz.)</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{travel_hours}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_travel_hours" value="{{ old('df_travel_hours') }}" class="ot-input" style="margin:0;max-width:140px;" min="0" step="0.5" placeholder="np. 1.5"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Stawka godz. jazdy (zł/h)</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{hour_rate}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_hour_rate" value="{{ old('df_hour_rate') }}" class="ot-input" style="margin:0;max-width:140px;" min="0" step="0.01" placeholder="np. 80"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Koszt dojazdu</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{travel_cost}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_travel_cost" value="{{ old('df_travel_cost') }}" class="ot-input" style="margin:0;max-width:160px;" min="0" step="0.01" placeholder="np. 600"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Liczba godz. audytu</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{auditor_hours}}</code></td>
                <td style="padding:6px 12px;"><input type="number" name="df_auditor_hours" value="{{ old('df_auditor_hours') }}" class="ot-input" style="margin:0;max-width:140px;" min="0" step="0.5" placeholder="np. 8"></td>
            </tr>

            {{-- ══ ENESA ══ --}}
            <tr style="background:#e8f3ef;"><td colspan="3" style="{{ $hdr }}">ENESA — Moja firma</td></tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Nazwa firmy</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{enesa_name}}</code><div style="font-size:10px;color:var(--ink-mute);margin-top:2px;">Fallback gdy brak ustawień „Moja firma"</div></td>
                <td style="padding:6px 12px;"><input type="text" name="df_enesa_name" value="{{ old('df_enesa_name') }}" class="ot-input" style="margin:0;" placeholder="np. Enesa Sp. z o.o."></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">NIP firmy</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{enesa_nip}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_enesa_nip" value="{{ old('df_enesa_nip') }}" class="ot-input" style="margin:0;" placeholder="np. 123-456-78-90"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Ulica / adres</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{enesa_street}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_enesa_street" value="{{ old('df_enesa_street') }}" class="ot-input" style="margin:0;" placeholder="ul. ..."></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">Miasto</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{enesa_city}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_enesa_city" value="{{ old('df_enesa_city') }}" class="ot-input" style="margin:0;" placeholder="np. Gliwice"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Kod pocztowy</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{enesa_postal}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_enesa_postal" value="{{ old('df_enesa_postal') }}" class="ot-input" style="margin:0;" placeholder="np. 44-100"></td>
            </tr>
            <tr style="{{ $rAlt }}">
                <td style="{{ $tN }}">E-mail firmy</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{enesa_email}}</code></td>
                <td style="padding:6px 12px;"><input type="email" name="df_enesa_email" value="{{ old('df_enesa_email') }}" class="ot-input" style="margin:0;" placeholder="biuro@enesa.pl"></td>
            </tr>
            <tr style="{{ $rH }}">
                <td style="{{ $tN }}">Telefon firmy</td>
                <td style="padding:8px 12px;"><code style="{{ $cA }}">@{{enesa_phone}}</code></td>
                <td style="padding:6px 12px;"><input type="text" name="df_enesa_phone" value="{{ old('df_enesa_phone') }}" class="ot-input" style="margin:0;" placeholder="+48 ..."></td>
            </tr>

        </tbody>
    </table>
</div>

{{-- DOMYŚLNE POZYCJE CENOWE --}}
<div class="ot-panel">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <h3 style="margin:0;font-size:16px;color:#1A4D3A;">Domyślne pozycje cenowe</h3>
        <button type="button" onclick="addDiRow()" class="ot-btn ot-btn-blue" style="font-size:13px;padding:6px 14px;">+ Dodaj pozycję</button>
    </div>
    <table class="di-tbl">
        <thead>
            <tr>
                <th>Nazwa pozycji</th>
                <th>Opis/Typ</th>
                <th style="width:70px;">Ilość</th>
                <th style="width:110px;">Cena jedn. (zł)</th>
                <th style="width:40px;"></th>
            </tr>
        </thead>
        <tbody id="di-table"></tbody>
    </table>
    <p style="font-size:12px;color:var(--ink-mute);margin-top:10px;">Pozycje te będą automatycznie zaciągane przy tworzeniu oferty z tego szablonu.</p>
</div>

{{-- EDYTOR HTML --}}
<div class="ot-panel">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <div>
            <h3 style="margin:0 0 4px;font-size:16px;color:#1A4D3A;">Szablon HTML oferty</h3>
            <p style="margin:0;font-size:12px;color:var(--ink-mute);">Użyj znaczników <code style="background:#f3f8f7;padding:1px 5px;border-radius:4px;">{{"{{"}}pole{!! '}}' !!}</code> — zostaną zastąpione danymi oferty przy generowaniu.</p>
        </div>
        <div style="display:flex;gap:8px;">
            <button type="button" onclick="loadDefaultHtml()" class="ot-btn" style="background:#f3f8f7;color:#1A4D3A;font-size:13px;padding:7px 14px;">↩ Wczytaj domyślny</button>
            <button type="button" onclick="refreshPreview()" class="ot-btn ot-btn-blue" style="font-size:13px;padding:7px 14px;">↻ Odśwież podgląd</button>
        </div>
    </div>

    <!-- Placeholders quick-insert -->
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
    <div class="placeholder-tags" style="margin-bottom:10px;border-radius:8px 8px 0 0;border:1px solid #c9d7e3;border-bottom:none;flex-direction:column;gap:5px;align-items:stretch;">
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
        <!-- LEFT: code editor -->
        <div class="editor-col">
            <div class="editor-col-header">
                <span>📝 Kod HTML</span>
                <div class="tabs">
                    <button type="button" onclick="formatHtml()" title="Formatuj" style="font-size:12px;">⟳ Format</button>
                    <button type="button" onclick="clearEditor()" title="Wyczyść" style="font-size:12px;color:#f87171;">✕ Wyczyść</button>
                </div>
            </div>
            <textarea id="html-textarea" name="html_content" spellcheck="false" placeholder="Wklej lub napisz HTML szablonu...">{{ old('html_content', $defaultHtml) }}</textarea>
        </div>
        <!-- RIGHT: live preview -->
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
    <a href="{{ route('offer-templates.index') }}" class="ot-btn ot-btn-gray">Anuluj</a>
    <button type="submit" class="ot-btn ot-btn-green">💾 Zapisz szablon</button>
</div>

</form>
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
// ─── CodeMirror editor ───
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

// Sync content to hidden textarea before form submit
document.getElementById('tpl-form').addEventListener('submit', function() {
    editor.save();
});

function insertPlaceholder(ph) {
    editor.replaceSelection(ph);
    editor.focus();
    refreshPreview();
}

// ─── Live preview ───
// ph() renders an unfilled variable as a highlighted badge
function ph(name) {
    return '<span style="display:inline;background:#fef9c3;color:#78350f;padding:1px 6px;border-radius:3px;font-family:monospace;font-size:.88em;border:1px dashed #f59e0b;">' + '{{' + name + '}}' + '</span>';
}
// getV() reads a form input value, returns null if empty
function getV(selector) {
    const el = document.querySelector(selector);
    return el && el.value.trim() ? el.value.trim() : null;
}

const DEMO_ITEMS = '<table style="width:100%;border-collapse:collapse;font-size:13px;"><thead><tr><th style="background:#1A4D3A;color:#fff;padding:8px;">Nr</th><th style="background:#1A4D3A;color:#fff;padding:8px;text-align:left;">Pozycja</th><th style="background:#1A4D3A;color:#fff;padding:8px;text-align:right;">Wartość</th></tr></thead><tbody><tr><td style="padding:8px;text-align:center;">1</td><td style="padding:8px;">Audyt energetyczny — etap I</td><td style="padding:8px;text-align:right;">8 000,00 zł</td></tr><tr><td style="padding:8px;text-align:center;background:#f7faf9;">2</td><td style="padding:8px;background:#f7faf9;">Raport końcowy</td><td style="padding:8px;text-align:right;background:#f7faf9;">2 000,00 zł</td></tr></tbody><tfoot><tr><td colspan="2" style="padding:10px;text-align:right;font-weight:700;background:#1A4D3A;color:#fff;">Razem netto</td><td style="padding:10px;text-align:right;font-weight:700;background:#1A4D3A;color:#fff;">10 000,00 zł</td></tr></tfoot></table>';

let previewTimer = null;
function refreshPreview() {
    const iframe = document.getElementById('html-preview');
    const vatRate = parseFloat(getV('[name="df_vat_rate"]') || '23') || 23;
    const demoNet = 10000;
    const vatAmt  = Math.round(demoNet * vatRate) / 100;
    const gross   = demoNet + vatAmt;
    const fmt     = n => n.toLocaleString('pl-PL', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    // Values from form inputs; null = show placeholder badge
    const vals = {
        'offer_title':          getV('[name="df_offer_title"]'),
        'offer_number':         'OF-2026/0001',
        'offer_date':           new Date().toLocaleDateString('pl-PL'),
        'offer_subject':        getV('[name="df_offer_subject"]'),
        'description':          getV('[name="df_offer_description"]'),
        'customer_name':        null,
        'customer_type':        getV('[name="df_customer_type"]'),
        'customer_nip':         null,
        'customer_address':     null,
        'customer_postal_code': null,
        'customer_city':        null,
        'customer_phone':       null,
        'customer_email':       null,
        'items_table':          DEMO_ITEMS,
        'distance_km':          getV('[name="df_distance_km"]') || '120',
        'km_rate':              null,
        'travel_hours':         getV('[name="df_travel_hours"]') || '1,5',
        'hour_rate':            null,
        'travel_cost':          '600,00',
        'total_price_net':      fmt(demoNet),
        'vat_rate':             vatRate + '%',
        'total_price_vat':      fmt(vatAmt),
        'total_price':          fmt(gross),
        'auditor_hours':        null,
        'offer_validity':       getV('[name="df_offer_validity"]'),
        'delivery_deadline':    getV('[name="df_delivery_deadline"]'),
        'payment_terms':        getV('[name="df_payment_terms_text"]'),
        'enesa_name':           null,
        'enesa_nip':            null,
        'enesa_street':         null,
        'enesa_city':           null,
        'enesa_postal':         null,
        'enesa_email':          null,
        'enesa_phone':          null,
    };

    let html = editor.getValue();
    Object.entries(vals).forEach(([key, val]) => {
        const re = new RegExp('\\{\\{' + key + '\\}\\}', 'g');
        html = html.replace(re, (val !== null && val !== '') ? val : ph(key));
    });
    // Any remaining unknown {{...}} — show as placeholder
    html = html.replace(/\{\{([a-zA-Z0-9_]+)\}\}/g, (_, n) => ph(n));

    iframe.srcdoc = html;
}

editor.on('change', function() {
    clearTimeout(previewTimer);
    previewTimer = setTimeout(refreshPreview, 600);
});
// Auto-refresh when default field values change
document.querySelectorAll('[name^="df_"]').forEach(function(el) {
    el.addEventListener('input', function() {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(refreshPreview, 400);
    });
});

refreshPreview();

// ─── Default HTML ───
const DEFAULT_HTML = @json($defaultHtml);
function loadDefaultHtml() {
    if (editor.getValue().trim() && !confirm('Zastąpić obecny kod domyślnym szablonem?')) return;
    editor.setValue(DEFAULT_HTML);
    refreshPreview();
}

function clearEditor() {
    if (!confirm('Wyczyścić kod HTML?')) return;
    editor.setValue('');
    refreshPreview();
}

function openFullPreview() {
    const w = window.open('', '_blank');
    w.document.write(document.getElementById('html-preview').srcdoc || '<p>Brak podglądu.</p>');
    w.document.close();
}

function formatHtml() {
    // Re-indent current document
    const totalLines = editor.lineCount();
    for (let i = 0; i < totalLines; i++) {
        editor.indentLine(i, 'smart');
    }
}

// ─── Auto type_code from name ───
document.querySelector('[name="name"]').addEventListener('input', function() {
    const tcInput = document.getElementById('type_code');
    if (tcInput.dataset.manual) return;
    tcInput.value = this.value.toLowerCase()
        .replace(/\s+/g, '_')
        .replace(/[^a-z0-9_]/g, '')
        .substring(0, 60);
});
document.getElementById('type_code').addEventListener('input', function() {
    this.dataset.manual = '1';
});

// ─── Default items table ───
let diCount = 0;
function addDiRow(name, type, qty, price) {
    const tbody = document.getElementById('di-table');
    const idx   = diCount++;
    const tr    = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" name="di_name[${idx}]" value="${escHtml(name||'')}" class="di-input" placeholder="Nazwa pozycji"></td>
        <td><input type="text" name="di_type[${idx}]" value="${escHtml(type||'')}" class="di-input" placeholder="Opis / typ"></td>
        <td><input type="number" name="di_qty[${idx}]" value="${qty||1}" min="0" step="0.01" class="di-input" style="text-align:right;"></td>
        <td><input type="number" name="di_price[${idx}]" value="${price||''}" min="0" step="0.01" class="di-input" style="text-align:right;" placeholder="0,00"></td>
        <td style="text-align:center;"><button type="button" onclick="this.closest('tr').remove()" style="background:#fee2e2;color:#991b1b;border:0;border-radius:6px;padding:5px 9px;cursor:pointer;font-size:13px;">✕</button></td>
    `;
    tbody.appendChild(tr);
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Pre-fill old default_items if validation failed
@if(old('di_name'))
    @foreach(old('di_name', []) as $i => $name)
        addDiRow(
            @json($name),
            @json(old('di_type.'.$i, '')),
            @json(old('di_qty.'.$i, 1)),
            @json(old('di_price.'.$i, ''))
        );
    @endforeach
@endif
</script>
</x-layouts.app>
