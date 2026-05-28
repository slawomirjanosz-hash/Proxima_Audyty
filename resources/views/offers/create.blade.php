<x-layouts.app>
<style>
.o-card { background:#fff; border:1px solid var(--paper-deep); border-radius:14px; padding:20px; margin-bottom:14px; }
.o-card-header { display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:#f3f8f7; border-radius:10px; cursor:pointer; user-select:none; }
.o-card-header h3 { margin:0; font-size:15px; font-weight:700; }
.o-card-body { padding:16px 0 4px; }
.o-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.o-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }
.o-grid-4 { display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:14px; }
.o-form-row { margin-bottom:12px; }
.o-label { display:block; font-size:12px; font-weight:700; color:var(--ink-mute); margin-bottom:4px; text-transform:uppercase; letter-spacing:.4px; }
.o-input { padding:8px 10px; border-radius:9px; border:1px solid #c9d7e3; font-size:14px; width:100%; box-sizing:border-box; }
.o-input:focus { border-color:#1A4D3A; outline:none; }
.o-select { padding:8px 10px; border-radius:9px; border:1px solid #c9d7e3; font-size:14px; width:100%; box-sizing:border-box; background:#fff; }
.o-btn { padding:9px 18px; border-radius:9px; border:0; cursor:pointer; font-size:14px; font-weight:600; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
.o-btn-green { background:var(--green-primary); color:#fff; }
.o-btn-gray { background:#718096; color:#fff; }
.o-btn-blue { background:#3b82f6; color:#fff; }
.o-btn-sm { padding:4px 8px; border-radius:6px; border:0; cursor:pointer; font-size:12px; font-weight:600; }
.o-tbl { width:100%; border-collapse:collapse; font-size:13px; }
.o-tbl th { padding:6px 4px; font-size:11px; text-transform:uppercase; letter-spacing:.4px; color:var(--ink-mute); background:#f3f8f7; text-align:left; }
.o-tbl td { padding:3px 4px; border-bottom:1px solid #e4edf3; }
.o-tbl-input { padding:4px 6px; border-radius:6px; border:1px solid #c9d7e3; font-size:13px; width:100%; box-sizing:border-box; }
.o-section-sumrow { display:flex; justify-content:flex-end; margin-top:8px; font-size:14px; }
.o-profit-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px; margin-bottom:14px; }
.o-schedule-box { background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:16px; margin-bottom:14px; }
.o-payment-box { background:#fff7ed; border:1px solid #fed7aa; border-radius:12px; padding:16px; margin-bottom:14px; }
.o-travel-box { background:#eef8f3; border:1px solid #c3e6d8; border-radius:12px; padding:18px; margin-bottom:14px; }
.tpl-card { border:2px solid #e4edf3; border-radius:12px; padding:14px 16px; cursor:pointer; transition:.15s; }
.tpl-card:hover { border-color:#1A4D3A; background:#f7faf9; }
.tpl-card.selected { border-color:#1A4D3A; background:#f0fdf4; }
.ql-toolbar { border-radius:9px 9px 0 0 !important; border-color:#c9d7e3 !important; }
.ql-container { border-radius:0 0 9px 9px !important; border-color:#c9d7e3 !important; font-size:14px !important; min-height:180px; }
</style>

<div class="panel">
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <h2 style="margin:0;font-size:20px;">Nowa oferta</h2>
    <a href="{{ route('offers.index') }}" class="o-btn o-btn-gray">← Wróć</a>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:14px;color:#991b1b;">
    <ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

@if($prefill)
<div style="background:#e0f2fe;border:1px solid #7dd3fc;border-radius:10px;padding:10px 16px;margin-bottom:14px;color:#0369a1;font-size:13px;font-weight:600;">
    📋 Tworzysz ofertę dla firmy: <strong>{{ $prefill->name }}</strong>
</div>
@endif

<form method="POST" action="{{ route('offers.store') }}" id="offer-form">
@csrf

{{-- ① RODZAJ OFERTY --}}
@if($offerTemplates->isNotEmpty())
<div class="o-card" style="border-color:#1A4D3A;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <div>
            <h3 style="margin:0 0 4px;font-size:16px;color:#1A4D3A;">Rodzaj oferty</h3>
            <p style="margin:0;font-size:12px;color:var(--ink-mute);">Wybierz szablon — zaciągną się domyślne stawki, godziny i pozycje. Oferta zostanie wygenerowana w HTML.</p>
        </div>
        <a href="{{ route('offer-templates.index') }}" style="font-size:12px;color:#1A4D3A;text-decoration:none;">+ Zarządzaj szablonami</a>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;">
        <div class="tpl-card selected" onclick="selectTemplate('')" id="tpl-none">
            <div style="font-size:14px;font-weight:600;margin-bottom:4px;">Bez szablonu</div>
            <div style="font-size:12px;color:var(--ink-mute);">Ręczna konfiguracja</div>
        </div>
        @foreach($offerTemplates as $tpl)
        <div class="tpl-card {{ old('offer_template_id') == $tpl->id ? 'selected' : '' }}"
             onclick="selectTemplate('{{ $tpl->id }}')"
             id="tpl-{{ $tpl->id }}"
             data-km="{{ $tpl->default_km_rate }}"
             data-hour="{{ $tpl->default_hour_rate }}"
             data-audit-h="{{ $tpl->default_auditor_hours }}"
             data-items="{{ json_encode($tpl->default_items ?? []) }}">
            <div style="font-size:14px;font-weight:600;margin-bottom:4px;">{{ $tpl->name }}</div>
            <div style="font-size:12px;color:var(--ink-mute);">{{ number_format($tpl->default_km_rate, 2, ',', ' ') }} zł/km &middot; {{ number_format($tpl->default_hour_rate, 2, ',', ' ') }} zł/h</div>
            <div style="font-size:11px;color:#1A4D3A;margin-top:4px;">{{ number_format($tpl->default_auditor_hours, 1, ',', ' ') }} h audytu</div>
        </div>
        @endforeach
    </div>
    <input type="hidden" name="offer_template_id" id="offer_template_id" value="{{ old('offer_template_id', '') }}">
</div>
@else
<input type="hidden" name="offer_template_id" value="">
@endif

{{-- ② PODSTAWOWE DANE --}}
<div class="o-card">
    <div class="o-grid-3">
        <div class="o-form-row">
            <label class="o-label">Numer oferty</label>
            <input type="text" name="offer_number" value="{{ old('offer_number', $nextNumber) }}" class="o-input">
        </div>
        <div class="o-form-row">
            <label class="o-label">Tytuł oferty *</label>
            <input type="text" name="offer_title" value="{{ old('offer_title') }}" class="o-input" required>
        </div>
        <div class="o-form-row">
            <label class="o-label">Data oferty</label>
            <input type="date" name="offer_date" value="{{ old('offer_date', date('Y-m-d')) }}" class="o-input">
        </div>
    </div>
    <div class="o-grid-2">
        <div class="o-form-row">
            <label class="o-label">Powiązana szansa CRM</label>
            <select name="crm_deal_id" class="o-select">
                <option value="">-- brak --</option>
                @foreach($crmDeals as $deal)
                    <option value="{{ $deal->id }}" {{ old('crm_deal_id') == $deal->id ? 'selected' : '' }}>{{ $deal->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="o-form-row">
            <label class="o-label">Status</label>
            <select name="status" class="o-select">
                <option value="portfolio" {{ old('status','portfolio') === 'portfolio' ? 'selected' : '' }}>Portfolio</option>
                <option value="inprogress" {{ old('status') === 'inprogress' ? 'selected' : '' }}>W toku</option>
            </select>
        </div>
    </div>
</div>

{{-- ③ DANE KLIENTA --}}
<div class="o-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <h3 style="margin:0;font-size:15px;">Dane klienta</h3>
        <div style="display:flex;gap:8px;align-items:center;">
            <label class="o-label" style="margin:0;">Z CRM:</label>
            <select id="crm-company-select" class="o-select" style="width:auto;min-width:200px;" onchange="fillCustomerFromCrm(this)">
                <option value="">-- wybierz --</option>
                @foreach($crmCompanies as $company)
                    <option value="{{ $company->id }}"
                        data-name="{{ $company->name }}" data-nip="{{ $company->nip ?? '' }}"
                        data-phone="{{ $company->phone ?? '' }}" data-email="{{ $company->email ?? '' }}"
                        data-address="{{ $company->address ?? '' }}" data-city="{{ $company->city ?? '' }}"
                        data-postal="{{ $company->postal_code ?? '' }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="o-grid-3">
        <div class="o-form-row">
            <label class="o-label">Nazwa klienta *</label>
            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $prefill?->name ?? '') }}" class="o-input" required>
        </div>
        <div class="o-form-row">
            <label class="o-label">NIP</label>
            <input type="text" name="customer_nip" id="customer_nip" value="{{ old('customer_nip', $prefill?->nip ?? '') }}" class="o-input" maxlength="20">
        </div>
        <div class="o-form-row">
            <label class="o-label">Telefon</label>
            <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone', $prefill?->phone ?? '') }}" class="o-input">
        </div>
        <div class="o-form-row">
            <label class="o-label">E-mail</label>
            <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email', $prefill?->email ?? '') }}" class="o-input">
        </div>
        <div class="o-form-row">
            <label class="o-label">Adres</label>
            <input type="text" name="customer_address" id="customer_address" value="{{ old('customer_address', $prefill?->street ?? '') }}" class="o-input">
        </div>
        <div class="o-form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div>
                <label class="o-label">Kod pocztowy</label>
                <input type="text" name="customer_postal_code" id="customer_postal_code" value="{{ old('customer_postal_code', $prefill?->postal_code ?? '') }}" class="o-input" maxlength="10">
            </div>
            <div>
                <label class="o-label">Miasto</label>
                <input type="text" name="customer_city" id="customer_city" value="{{ old('customer_city', $prefill?->city ?? '') }}" class="o-input">
            </div>
        </div>
    </div>
    @if($prefill)<input type="hidden" name="company_id" value="{{ $prefill->id }}">@endif
    @if($fromInquiry)<input type="hidden" name="inquiry_id" value="{{ $fromInquiry->id }}">@endif
</div>

{{-- ④ KOSZTY DOJAZDU --}}
<div class="o-travel-box">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <h3 style="margin:0;font-size:16px;color:#1A4D3A;">🚗 Koszty dojazdu</h3>
        <button type="button" onclick="openTravelAiModal()" class="o-btn" style="background:#1A4D3A;color:#fff;font-size:13px;padding:6px 14px;">🤖 Oszacuj trasę AI</button>
    </div>
    <div class="o-grid-4" style="margin-bottom:14px;">
        <div>
            <label class="o-label">Odległość (km, jedna strona)</label>
            <input type="number" name="distance_km" id="distance_km" step="0.1" min="0" value="{{ old('distance_km', 0) }}" class="o-input" oninput="calcTravel()">
        </div>
        <div>
            <label class="o-label">Stawka za km (zł/km)</label>
            <input type="number" name="km_rate" id="km_rate" step="0.01" min="0" value="{{ old('km_rate', 1.50) }}" class="o-input" oninput="calcTravel()">
        </div>
        <div>
            <label class="o-label">Czas jazdy (h, jedna strona)</label>
            <input type="number" name="travel_hours" id="travel_hours" step="0.25" min="0" value="{{ old('travel_hours', 0) }}" class="o-input" oninput="calcTravel()">
        </div>
        <div>
            <label class="o-label">Stawka za godz. jazdy (zł/h)</label>
            <input type="number" name="hour_rate" id="hour_rate" step="0.01" min="0" value="{{ old('hour_rate', 80.00) }}" class="o-input" oninput="calcTravel()">
        </div>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;background:#fff;border-radius:10px;padding:12px 16px;">
        <div style="font-size:13px;color:var(--ink-mute);">Tam i z powrotem: <span id="travel-formula" style="font-family:monospace;">0 km × 1,50 zł × 2 + 0 h × 80,00 zł × 2</span></div>
        <div style="font-size:22px;font-weight:800;color:#1A4D3A;" id="travel-cost-display">0,00 zł</div>
    </div>
    <input type="hidden" name="travel_cost" id="travel_cost_input" value="{{ old('travel_cost', 0) }}">
</div>

{{-- ⑤ GODZINY AUDYTU --}}
<div class="o-card">
    <div style="display:flex;align-items:center;gap:16px;">
        <div style="flex:0 0 200px;">
            <label class="o-label">Liczba godzin audytu (h)</label>
            <input type="number" name="auditor_hours" id="auditor_hours" step="0.5" min="0" value="{{ old('auditor_hours', 8) }}" class="o-input">
        </div>
    </div>
</div>

{{-- ⑥ SEKCJE CENOWE --}}
@foreach([['services','Usługi'], ['works','Prace własne'], ['materials','Materiały']] as [$sectionId, $sectionLabel])
<div class="o-card">
    <div class="o-card-header" onclick="toggleSection('{{ $sectionId }}')">
        <h3>{{ $sectionLabel }} <span style="font-size:13px;color:var(--ink-mute);font-weight:400;" id="{{ $sectionId }}-header-sum"></span></h3>
        <svg id="{{ $sectionId }}-icon" style="width:20px;height:20px;transition:transform .2s;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </div>
    <div id="{{ $sectionId }}-content" class="o-card-body" style="display:none;">
        <div style="overflow-x:auto;">
            <table class="o-tbl"><thead><tr>
                <th style="width:30px;">Nr</th><th>Nazwa</th><th>Opis/Typ</th>
                <th style="width:60px;">Ilość</th><th style="width:110px;">Cena (zł)</th>
                <th style="width:110px;">Cena kat.</th><th style="width:110px;">Wartość (zł)</th>
                <th style="width:80px;"></th>
            </tr></thead>
            <tbody id="{{ $sectionId }}-table">
                <tr>
                    <td><input type="number" class="o-tbl-input" style="width:40px;" value="1" readonly></td>
                    <td><input type="text" name="{{ $sectionId }}[0][name]" class="o-tbl-input"></td>
                    <td><input type="text" name="{{ $sectionId }}[0][type]" class="o-tbl-input"></td>
                    <td><input type="number" min="0" step="0.01" value="1" name="{{ $sectionId }}[0][quantity]" class="o-tbl-input quantity-input" data-section="{{ $sectionId }}" onchange="calculateRowValue(this)"></td>
                    <td><input type="number" step="0.01" min="0" name="{{ $sectionId }}[0][price]" class="o-tbl-input price-input" data-section="{{ $sectionId }}" onchange="calculateRowValue(this)"></td>
                    <td><input type="number" step="0.01" min="0" name="{{ $sectionId }}[0][catalog_price]" class="o-tbl-input catalog-price-input" placeholder="kat." oninput="updateBuiltInProfit()"></td>
                    <td><input type="text" name="{{ $sectionId }}[0][value]" value="0,00 zł" data-raw="0" class="o-tbl-input value-input" data-section="{{ $sectionId }}" readonly style="background:#f3f8f7;"></td>
                    <td><div style="display:flex;gap:2px;">
                        <button type="button" onclick="moveRow(this,'up','{{ $sectionId }}')" class="o-btn-sm" style="background:#e2e8f0;color:var(--ink);">↑</button>
                        <button type="button" onclick="moveRow(this,'down','{{ $sectionId }}')" class="o-btn-sm" style="background:#e2e8f0;color:var(--ink);">↓</button>
                        <button type="button" onclick="removeRow(this,'{{ $sectionId }}')" class="o-btn-sm" style="background:#fee2e2;color:#991b1b;">✕</button>
                    </div></td>
                </tr>
            </tbody></table>
        </div>
        <div style="display:flex;gap:8px;margin-top:10px;">
            <button type="button" onclick="addRow('{{ $sectionId }}')" class="o-btn o-btn-blue" style="font-size:13px;padding:7px 14px;">+ Dodaj wiersz</button>
        </div>
        <div class="o-section-sumrow"><span>Suma sekcji: <strong id="{{ $sectionId }}-total">0,00 zł</strong></span></div>
    </div>
</div>
@endforeach

<div id="custom-sections-container"></div>
<div style="margin-bottom:14px;">
    <button type="button" onclick="addCustomSection()" class="o-btn o-btn-green">+ Dodaj sekcję niestandardową</button>
</div>

{{-- ⑦ KALKULATOR ZYSKU --}}
<div class="o-profit-box">
    <h3 style="margin:0 0 14px;font-size:16px;">Kalkulator zysku</h3>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:14px;">
        <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;">
            <div style="font-size:11px;text-transform:uppercase;color:var(--ink-mute);letter-spacing:.4px;margin-bottom:4px;">Łącznie (koszty)</div>
            <div style="font-size:18px;font-weight:700;" id="grand-total">0,00 zł</div>
        </div>
        <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;">
            <div style="font-size:11px;text-transform:uppercase;color:var(--ink-mute);letter-spacing:.4px;margin-bottom:4px;">Wbudowany zysk</div>
            <div style="font-size:14px;font-weight:700;color:var(--green-primary);" id="built-in-profit-display">0,00 zł (0,0%)</div>
        </div>
        <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;">
            <div style="font-size:11px;text-transform:uppercase;color:var(--ink-mute);letter-spacing:.4px;margin-bottom:4px;">Łącznie z zysku</div>
            <div style="font-size:14px;font-weight:700;color:var(--green-primary);" id="total-profit-display">0,00 zł (0,0%)</div>
        </div>
        <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;">
            <div style="font-size:11px;text-transform:uppercase;color:var(--ink-mute);letter-spacing:.4px;margin-bottom:4px;">Suma z zyskiem</div>
            <div style="font-size:20px;font-weight:800;color:var(--ink);" id="total-with-profit">0,00 zł</div>
        </div>
    </div>
    <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
        <div class="o-form-row" style="margin:0;">
            <label class="o-label">Dodatkowy zysk (%)</label>
            <input type="number" step="0.01" min="0" id="profit-percent" name="profit_percent" value="0" class="o-input" style="width:120px;" oninput="updateProfitFromPercent()">
        </div>
        <div class="o-form-row" style="margin:0;">
            <label class="o-label">Dodatkowy zysk (zł)</label>
            <input type="number" step="0.01" min="0" id="profit-amount-input" name="profit_amount" value="0" class="o-input" style="width:140px;" oninput="updateProfitFromAmount()">
        </div>
    </div>
</div>

{{-- ⑧ OPIS OFERTY --}}
<div class="o-card">
    <h3 style="margin:0 0 12px;font-size:16px;">Opis oferty <span style="font-size:12px;font-weight:400;color:var(--ink-mute);">— trafi do sekcji "Przedmiot oferty" w szablonie HTML</span></h3>
    <div id="quill-editor"></div>
    <input type="hidden" name="offer_description" id="offer_description_input">
</div>

{{-- ⑨ HARMONOGRAM --}}
<div class="o-schedule-box">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
        <input type="checkbox" id="schedule_enabled" name="schedule_enabled" value="1" onchange="toggleSchedule(this.checked)" style="width:16px;height:16px;">
        <label for="schedule_enabled" style="font-size:16px;font-weight:600;cursor:pointer;">Harmonogram realizacji</label>
    </div>
    <div id="schedule-section" style="display:none;">
        <table class="o-tbl" style="margin-bottom:10px;">
            <thead><tr><th style="width:40px;">Nr</th><th>Etap</th><th>Opis</th><th style="width:40px;"></th></tr></thead>
            <tbody id="schedule-table"></tbody>
        </table>
        <button type="button" onclick="addScheduleRow()" class="o-btn o-btn-blue" style="font-size:13px;padding:7px 14px;">+ Dodaj etap</button>
    </div>
</div>

{{-- ⑩ WARUNKI PŁATNOŚCI --}}
<div class="o-payment-box">
    <h3 style="margin:0 0 12px;font-size:16px;">Warunki płatności</h3>
    <table class="o-tbl" style="margin-bottom:10px;">
        <thead><tr><th style="width:40px;">Nr</th><th>Opis raty</th><th style="width:80px;">%</th><th style="width:120px;">Termin</th><th style="width:40px;"></th></tr></thead>
        <tbody id="payment-table"></tbody>
    </table>
    <button type="button" onclick="addPaymentRow()" class="o-btn" style="font-size:13px;padding:7px 14px;background:#d97706;color:#fff;">+ Dodaj ratę</button>
</div>

{{-- OPCJE --}}
<div class="o-card">
    <div style="display:flex;align-items:center;gap:10px;">
        <input type="checkbox" id="show_unit_prices" name="show_unit_prices" value="1" checked style="width:16px;height:16px;">
        <label for="show_unit_prices" style="font-size:14px;cursor:pointer;">Pokazuj ceny jednostkowe w PDF/Word</label>
    </div>
</div>

{{-- PRZYCISKI --}}
<div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;">
    <a href="{{ route('offers.index') }}" class="o-btn o-btn-gray">Anuluj</a>
    <button type="submit" class="o-btn o-btn-green">💾 Zapisz ofertę</button>
</div>

</form>
</div>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
// ─── Quill ───
const quill = new Quill('#quill-editor', {
    theme: 'snow',
    placeholder: 'Opisz zakres i przedmiot oferty...',
    modules: { toolbar: [
        [{ header: [1,2,3,false] }],
        ['bold','italic','underline','strike'],
        [{ color: [] }, { background: [] }],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ indent: '-1' }, { indent: '+1' }],
        ['link','blockquote'],
        ['clean']
    ]}
});
document.getElementById('offer-form').addEventListener('submit', function() {
    document.getElementById('offer_description_input').value = quill.root.innerHTML;
    document.querySelectorAll('.value-input').forEach(inp => { if (inp.dataset.raw !== undefined) inp.value = inp.dataset.raw; });
});

// ─── Template selector ───
function selectTemplate(id) {
    document.querySelectorAll('.tpl-card').forEach(c => c.classList.remove('selected'));
    const card = document.getElementById(id ? 'tpl-' + id : 'tpl-none');
    if (card) card.classList.add('selected');
    document.getElementById('offer_template_id').value = id;
    if (!id) return;
    const km   = parseFloat(card.dataset.km   || 1.5);
    const hour = parseFloat(card.dataset.hour || 80);
    const ah   = parseFloat(card.dataset.auditH || 8);
    document.getElementById('km_rate').value       = km.toFixed(2);
    document.getElementById('hour_rate').value     = hour.toFixed(2);
    document.getElementById('auditor_hours').value = ah.toFixed(1);
    calcTravel();
    const items = JSON.parse(card.dataset.items || '[]');
    if (items.length) {
        document.getElementById('services-table').innerHTML = '';
        rowCounters['services'] = 0;
        items.forEach(item => addRowWithData('services', item.name||'', item.type||'', item.quantity||1, item.price||0));
        const content = document.getElementById('services-content');
        if (content.style.display === 'none') toggleSection('services');
    }
}

// ─── Travel ───
function calcTravel() {
    const dist  = parseFloat(document.getElementById('distance_km').value) || 0;
    const kmR   = parseFloat(document.getElementById('km_rate').value)     || 0;
    const tH    = parseFloat(document.getElementById('travel_hours').value)|| 0;
    const hourR = parseFloat(document.getElementById('hour_rate').value)   || 0;
    const cost  = (dist * kmR * 2) + (tH * hourR * 2);
    document.getElementById('travel-formula').textContent =
        dist + ' km × ' + kmR.toFixed(2) + ' zł × 2 + ' + tH + ' h × ' + hourR.toFixed(2) + ' zł × 2';
    document.getElementById('travel-cost-display').textContent = formatPrice(cost);
    document.getElementById('travel_cost_input').value = cost.toFixed(2);
}
calcTravel();

// ─── CRM fill ───
function fillCustomerFromCrm(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) return;
    ['customer_name','customer_nip','customer_phone','customer_email','customer_address','customer_city','customer_postal_code']
        .forEach(id => { const el = document.getElementById(id); if (el) el.value = opt.dataset[id.replace('customer_','')] || ''; });
    document.getElementById('customer_address').value    = opt.dataset.address || '';
    document.getElementById('customer_postal_code').value= opt.dataset.postal  || '';
}

// ─── Sections ───
function toggleSection(sectionId) {
    const content = document.getElementById(sectionId + '-content');
    const icon    = document.getElementById(sectionId + '-icon');
    const hidden  = content.style.display === 'none';
    content.style.display = hidden ? '' : 'none';
    if (icon) icon.style.transform = hidden ? 'rotate(180deg)' : '';
}
function formatPrice(val) { return (+val).toLocaleString('pl-PL', { minimumFractionDigits:2, maximumFractionDigits:2 }) + ' zł'; }

let rowCounters = { services:1, works:1, materials:1 };
let customSectionCounter = 0, customSections = [], _grandTotalRaw = 0;

function addRow(section) {
    addRowWithData(section,'','',1,'');
}
function addRowWithData(section, name, type, qty, price) {
    const tbody = document.getElementById(section + '-table');
    const idx   = rowCounters[section] || 0;
    const val   = (parseFloat(qty)||0) * (parseFloat(price)||0);
    const tr    = document.createElement('tr');
    tr.innerHTML = buildRowHtml(section, idx, name, type, qty||1, price||'', price||'', val.toFixed(2), formatPrice(val));
    tbody.appendChild(tr);
    rowCounters[section] = (rowCounters[section] || 0) + 1;
    reindexSection(section);
    calculateTotal(section);
}
function buildRowHtml(section, idx, name, type, qty, price, catPrice, rawVal, dispVal) {
    const e = v => String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    return `<td><input type="number" class="o-tbl-input" style="width:40px;" value="${idx+1}" readonly></td>
        <td><input type="text" name="${section}[${idx}][name]" value="${e(name)}" class="o-tbl-input"></td>
        <td><input type="text" name="${section}[${idx}][type]" value="${e(type)}" class="o-tbl-input"></td>
        <td><input type="number" min="0" step="0.01" value="${qty}" name="${section}[${idx}][quantity]" class="o-tbl-input quantity-input" data-section="${section}" onchange="calculateRowValue(this)"></td>
        <td><input type="number" step="0.01" min="0" value="${price}" name="${section}[${idx}][price]" class="o-tbl-input price-input" data-section="${section}" onchange="calculateRowValue(this)"></td>
        <td><input type="number" step="0.01" min="0" value="${catPrice}" name="${section}[${idx}][catalog_price]" class="o-tbl-input catalog-price-input" placeholder="kat." oninput="updateBuiltInProfit()"></td>
        <td><input type="text" name="${section}[${idx}][value]" value="${dispVal}" data-raw="${rawVal}" class="o-tbl-input value-input" data-section="${section}" readonly style="background:#f3f8f7;"></td>
        <td><div style="display:flex;gap:2px;">
            <button type="button" onclick="moveRow(this,'up','${section}')" class="o-btn-sm" style="background:#e2e8f0;color:var(--ink);">↑</button>
            <button type="button" onclick="moveRow(this,'down','${section}')" class="o-btn-sm" style="background:#e2e8f0;color:var(--ink);">↓</button>
            <button type="button" onclick="removeRow(this,'${section}')" class="o-btn-sm" style="background:#fee2e2;color:#991b1b;">✕</button>
        </div></td>`;
}
function removeRow(btn, section) { btn.closest('tr').remove(); reindexSection(section); calculateTotal(section); }
function moveRow(btn, direction, section) {
    const row = btn.closest('tr'), tbody = row.closest('tbody');
    direction === 'up' ? (row.previousElementSibling && tbody.insertBefore(row, row.previousElementSibling))
                       : (row.nextElementSibling && tbody.insertBefore(row.nextElementSibling, row));
    reindexSection(section);
}
function reindexSection(section) {
    const tbody = document.getElementById(section + '-table');
    if (!tbody) return;
    const rows = tbody.querySelectorAll('tr');
    rows.forEach((row, i) => {
        const num = row.querySelector('input[type="number"][readonly]');
        if (num) num.value = i + 1;
        row.querySelectorAll('[name]').forEach(el => {
            if (section.startsWith('custom')) {
                const sNum = section.replace('custom','');
                el.name = el.name.replace(/custom_sections\[\d+\]\[items\]\[\d+\]/, `custom_sections[${sNum}][items][${i}]`);
            } else {
                el.name = el.name.replace(new RegExp('^' + section + '\\[\\d+\\]'), `${section}[${i}]`);
            }
        });
    });
    if (!section.startsWith('custom')) rowCounters[section] = rows.length;
}
function calculateRowValue(input) {
    const row = input.closest('tr');
    const qty = parseFloat(row.querySelector('.quantity-input')?.value) || 0;
    const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
    const value = qty * price;
    const valInput = row.querySelector('.value-input');
    if (valInput) { valInput.dataset.raw = value.toFixed(2); valInput.value = formatPrice(value); }
    const catInput = row.querySelector('.catalog-price-input');
    if (catInput && !catInput.value && price) catInput.value = price.toFixed(2);
    calculateTotal(input.dataset.section);
    updateBuiltInProfit();
}
function calculateTotal(section) {
    let total = 0;
    document.querySelectorAll(`#${section}-table .value-input`).forEach(inp => total += parseFloat(inp.dataset.raw||inp.value)||0);
    const el = document.getElementById(section+'-total');
    if (el) el.textContent = formatPrice(total);
    const hs = document.getElementById(section+'-header-sum');
    if (hs) hs.textContent = '— ' + formatPrice(total);
    calculateGrandTotal();
}
function calculateGrandTotal() {
    let grandTotal = 0;
    ['services','works','materials'].forEach(s => document.querySelectorAll(`#${s}-table .value-input`).forEach(inp => grandTotal += parseFloat(inp.dataset.raw||inp.value)||0));
    customSections.forEach(num => document.querySelectorAll(`#custom${num}-table .value-input`).forEach(inp => grandTotal += parseFloat(inp.dataset.raw||inp.value)||0));
    _grandTotalRaw = grandTotal;
    document.getElementById('grand-total').textContent = formatPrice(grandTotal);
    updateProfitFromPercent();
}
function updateBuiltInProfit() {
    let builtIn = 0;
    document.querySelectorAll('.catalog-price-input').forEach(catInp => {
        const row = catInp.closest('tr'); if (!row) return;
        const price = parseFloat(row.querySelector('.price-input')?.value)||0;
        const cat   = parseFloat(catInp.value)||price;
        const qty   = parseFloat(row.querySelector('.quantity-input')?.value)||1;
        if (cat > price) builtIn += (cat-price)*qty;
    });
    const pct = _grandTotalRaw > 0 ? builtIn/_grandTotalRaw*100 : 0;
    document.getElementById('built-in-profit-display').textContent = formatPrice(builtIn)+' ('+pct.toFixed(1)+'%)';
    updateProfitDisplay();
}
function updateProfitFromPercent() {
    const pct = parseFloat(document.getElementById('profit-percent')?.value)||0;
    const inp = document.getElementById('profit-amount-input');
    if (inp) inp.value = (_grandTotalRaw*pct/100).toFixed(2);
    updateProfitDisplay();
}
function updateProfitFromAmount() {
    const amount = parseFloat(document.getElementById('profit-amount-input')?.value)||0;
    const inp = document.getElementById('profit-percent');
    if (inp) inp.value = (_grandTotalRaw>0 ? amount/_grandTotalRaw*100 : 0).toFixed(2);
    updateProfitDisplay();
}
function updateProfitDisplay() {
    const amount = parseFloat(document.getElementById('profit-amount-input')?.value)||0;
    document.getElementById('total-with-profit').textContent = formatPrice(_grandTotalRaw+amount);
}

// Schedule
let scheduleCount = 0;
function toggleSchedule(checked) { document.getElementById('schedule-section').style.display = checked?'':'none'; }
function addScheduleRow(milestone,description) {
    const tbody = document.getElementById('schedule-table');
    const idx = scheduleCount++;
    const tr = document.createElement('tr');
    tr.innerHTML = `<td style="text-align:center;color:var(--ink-mute);">${idx+1}</td>
        <td><input type="text" name="schedule[${idx}][milestone]" value="${esc(milestone||'')}" class="o-tbl-input"></td>
        <td><input type="text" name="schedule[${idx}][description]" value="${esc(description||'')}" class="o-tbl-input"></td>
        <td style="text-align:center;"><button type="button" onclick="this.closest('tr').remove()" class="o-btn-sm" style="background:#fee2e2;color:#991b1b;">✕</button></td>`;
    tbody.appendChild(tr);
}

// Payment
let paymentCount = 0;
function addPaymentRow(description,percent,deadline) {
    const tbody = document.getElementById('payment-table');
    const idx = paymentCount++;
    const tr = document.createElement('tr');
    tr.innerHTML = `<td style="text-align:center;color:var(--ink-mute);">${idx+1}</td>
        <td><input type="text" name="payment_terms[${idx}][description]" value="${esc(description||'')}" class="o-tbl-input"></td>
        <td><input type="number" step="0.01" min="0" max="100" name="payment_terms[${idx}][percent]" value="${percent||''}" class="o-tbl-input" style="text-align:right;"></td>
        <td><input type="text" name="payment_terms[${idx}][deadline]" value="${esc(deadline||'')}" class="o-tbl-input"></td>
        <td style="text-align:center;"><button type="button" onclick="this.closest('tr').remove()" class="o-btn-sm" style="background:#fee2e2;color:#991b1b;">✕</button></td>`;
    tbody.appendChild(tr);
}

// Custom sections
function addCustomSection() {
    const sectionName = prompt('Podaj nazwę nowej sekcji:');
    if (!sectionName || !sectionName.trim()) return;
    customSectionCounter++;
    const num = customSectionCounter, sId = 'custom'+num;
    customSections.push(num);
    rowCounters[sId] = 1;
    const container = document.getElementById('custom-sections-container');
    const div = document.createElement('div');
    div.className = 'o-card'; div.id = 'section-'+sId;
    div.innerHTML = `<div class="o-card-header" onclick="toggleSection('${sId}')">
        <h3>${esc(sectionName.trim())} <span style="font-size:13px;color:var(--ink-mute);font-weight:400;" id="${sId}-header-sum"></span></h3>
        <div style="display:flex;gap:6px;"><button type="button" onclick="event.stopPropagation();removeCustomSection('${sId}')" class="o-btn-sm" style="background:#fee2e2;color:#991b1b;">✕ Usuń</button></div>
    </div>
    <div id="${sId}-content" class="o-card-body" style="display:none;">
        <input type="hidden" name="custom_sections[${num}][name]" value="${esc(sectionName.trim())}">
        <div style="overflow-x:auto;"><table class="o-tbl">
        <thead><tr><th>Nr</th><th>Nazwa</th><th>Opis/Typ</th><th>Ilość</th><th>Cena (zł)</th><th>Cena kat.</th><th>Wartość (zł)</th><th></th></tr></thead>
        <tbody id="${sId}-table"></tbody></table></div>
        <div style="display:flex;gap:8px;margin-top:10px;">
            <button type="button" onclick="addCustomRow('${sId}',${num})" class="o-btn o-btn-blue" style="font-size:13px;padding:7px 14px;">+ Dodaj wiersz</button>
        </div>
        <div class="o-section-sumrow"><span>Suma sekcji: <strong id="${sId}-total">0,00 zł</strong></span></div>
    </div>`;
    container.appendChild(div);
    addCustomRow(sId, num);
    toggleSection(sId);
}
function addCustomRow(sId, sNum) {
    const tbody = document.getElementById(sId+'-table');
    const idx = rowCounters[sId]||0;
    const tr = document.createElement('tr');
    tr.innerHTML = `<td><input type="number" class="o-tbl-input" style="width:40px;" value="${idx+1}" readonly></td>
        <td><input type="text" name="custom_sections[${sNum}][items][${idx}][name]" class="o-tbl-input"></td>
        <td><input type="text" name="custom_sections[${sNum}][items][${idx}][type]" class="o-tbl-input"></td>
        <td><input type="number" min="0" step="0.01" value="1" name="custom_sections[${sNum}][items][${idx}][quantity]" class="o-tbl-input quantity-input" data-section="${sId}" onchange="calculateRowValue(this)"></td>
        <td><input type="number" step="0.01" min="0" name="custom_sections[${sNum}][items][${idx}][price]" class="o-tbl-input price-input" data-section="${sId}" onchange="calculateRowValue(this)"></td>
        <td><input type="number" step="0.01" min="0" name="custom_sections[${sNum}][items][${idx}][catalog_price]" class="o-tbl-input catalog-price-input" placeholder="kat." oninput="updateBuiltInProfit()"></td>
        <td><input type="text" name="custom_sections[${sNum}][items][${idx}][value]" value="0,00 zł" data-raw="0" class="o-tbl-input value-input" data-section="${sId}" readonly style="background:#f3f8f7;"></td>
        <td><div style="display:flex;gap:2px;">
            <button type="button" onclick="moveRow(this,'up','${sId}')" class="o-btn-sm" style="background:#e2e8f0;color:var(--ink);">↑</button>
            <button type="button" onclick="moveRow(this,'down','${sId}')" class="o-btn-sm" style="background:#e2e8f0;color:var(--ink);">↓</button>
            <button type="button" onclick="removeRow(this,'${sId}')" class="o-btn-sm" style="background:#fee2e2;color:#991b1b;">✕</button>
        </div></td>`;
    tbody.appendChild(tr);
    rowCounters[sId] = (rowCounters[sId]||0)+1;
    reindexSection(sId);
}
function removeCustomSection(sId) {
    if (!confirm('Usunąć sekcję?')) return;
    const el = document.getElementById('section-'+sId); if (el) el.remove();
    const num = parseInt(sId.replace('custom',''));
    customSections = customSections.filter(n=>n!==num);
    calculateGrandTotal();
}
function esc(text) { return String(text).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// ── AI Travel Modal ──────────────────────────────────────────────────────────
function openTravelAiModal() {
    const clientCity = document.getElementById('customer_city')?.value || '';
    document.getElementById('ai-base-city').value   = '{{ \App\Models\SystemSetting::get('company_base_city', 'Wrocław') }}';
    document.getElementById('ai-client-city').value = clientCity;
    document.getElementById('ai-travel-note').textContent  = '';
    document.getElementById('ai-travel-error').textContent = '';
    document.getElementById('ai-travel-modal').style.display = 'flex';
}
function closeTravelAiModal() {
    document.getElementById('ai-travel-modal').style.display = 'none';
}
async function runTravelAiEstimate() {
    const baseCity   = document.getElementById('ai-base-city').value.trim();
    const clientCity = document.getElementById('ai-client-city').value.trim();
    const btn        = document.getElementById('ai-estimate-btn');
    const noteEl     = document.getElementById('ai-travel-note');
    const errEl      = document.getElementById('ai-travel-error');

    errEl.textContent  = '';
    noteEl.textContent = '';

    if (!baseCity || !clientCity) { errEl.textContent = 'Uzupełnij oba miasta.'; return; }

    btn.disabled = true;
    btn.textContent = '⏳ Szacuję…';

    try {
        const res = await fetch('{{ route('offers.estimateTravelAi') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}',
            },
            body: JSON.stringify({ base_city: baseCity, client_city: clientCity }),
        });
        const json = await res.json();
        if (!res.ok || json.error) { errEl.textContent = json.error || 'Błąd serwera.'; return; }

        document.getElementById('distance_km').value   = json.distance_km;
        document.getElementById('travel_hours').value  = json.travel_hours;
        calcTravel();
        noteEl.textContent = json.note ? '💡 ' + json.note : '';
        closeTravelAiModal();
    } catch(e) {
        errEl.textContent = 'Błąd połączenia: ' + e.message;
    } finally {
        btn.disabled = false;
        btn.textContent = '🤖 Oblicz';
    }
}
</script>

{{-- AI Travel Modal --}}
<div id="ai-travel-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:28px 32px;width:100%;max-width:440px;box-shadow:0 8px 40px rgba(0,0,0,.18);">
        <h3 style="margin:0 0 18px;font-size:17px;color:#1A4D3A;">🤖 Oszacuj trasę AI</h3>
        <div style="margin-bottom:12px;">
            <label class="o-label">Miasto startowe (Twoja firma)</label>
            <input type="text" id="ai-base-city" class="o-input" placeholder="np. Wrocław">
        </div>
        <div style="margin-bottom:18px;">
            <label class="o-label">Miasto klienta</label>
            <input type="text" id="ai-client-city" class="o-input" placeholder="np. Warszawa">
        </div>
        <div id="ai-travel-error" style="color:#dc2626;font-size:13px;margin-bottom:8px;"></div>
        <div id="ai-travel-note"  style="color:#059669;font-size:13px;margin-bottom:14px;"></div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button type="button" onclick="closeTravelAiModal()" class="o-btn o-btn-gray">Anuluj</button>
            <button type="button" id="ai-estimate-btn" onclick="runTravelAiEstimate()" class="o-btn" style="background:#1A4D3A;color:#fff;">🤖 Oblicz</button>
        </div>
    </div>
</div>
</x-layouts.app>
