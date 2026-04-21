<x-layouts.app>
<style>
.o-card { background:#fff; border:1px solid #d5e0ea; border-radius:16px; padding:20px; margin-bottom:14px; }
.o-card-header { display:flex; justify-content:space-between; align-items:center; padding:14px 18px; background:#f3f8f7; border-radius:10px; cursor:pointer; user-select:none; }
.o-card-header h3 { margin:0; font-size:16px; }
.o-card-body { padding:16px 0 4px; }
.o-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.o-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }
.o-form-row { margin-bottom:12px; }
.o-label { display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:4px; text-transform:uppercase; letter-spacing:.4px; }
.o-input { padding:8px 10px; border-radius:9px; border:1px solid #c9d7e3; font-size:14px; width:100%; box-sizing:border-box; }
.o-select { padding:8px 10px; border-radius:9px; border:1px solid #c9d7e3; font-size:14px; width:100%; box-sizing:border-box; background:#fff; }
.o-btn { padding:9px 18px; border-radius:9px; border:0; cursor:pointer; font-size:14px; font-weight:600; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
.o-btn-blue { background:#0e89d8; color:#fff; }
.o-btn-green { background:#1ba84a; color:#fff; }
.o-btn-gray { background:#718096; color:#fff; }
.o-btn-sm { padding:4px 8px; border-radius:6px; border:0; cursor:pointer; font-size:12px; font-weight:600; }
.o-tbl { width:100%; border-collapse:collapse; font-size:13px; }
.o-tbl th { padding:6px 4px; font-size:11px; text-transform:uppercase; letter-spacing:.4px; color:#4c6373; background:#f3f8f7; text-align:left; }
.o-tbl td { padding:3px 4px; border-bottom:1px solid #e4edf3; }
.o-tbl-input { padding:4px 6px; border-radius:6px; border:1px solid #c9d7e3; font-size:13px; width:100%; box-sizing:border-box; }
.o-section-sumrow { display:flex; justify-content:flex-end; margin-top:8px; gap:16px; font-size:14px; }
.o-profit-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px; margin-bottom:14px; }
.o-schedule-box { background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:16px; margin-bottom:14px; }
.o-payment-box { background:#fff7ed; border:1px solid #fed7aa; border-radius:12px; padding:16px; margin-bottom:14px; }
</style>

<div class="panel">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h2 style="margin:0;font-size:20px;">Nowa oferta</h2>
        <a href="{{ route('offers.index') }}" class="o-btn o-btn-gray">← Wróć</a>
    </div>

    @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:14px;color:#991b1b;">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($prefill)
        <div style="background:#e0f2fe;border:1px solid #7dd3fc;border-radius:10px;padding:10px 16px;margin-bottom:14px;color:#0369a1;font-size:13px;font-weight:600;">
            📋 Tworzysz ofertę dla firmy: <strong>{{ $prefill->name }}</strong>. Dane klienta zostały wstępnie wypełnione.
        </div>
    @endif

    <form method="POST" action="{{ route('offers.store') }}" id="offer-form">
        @csrf

        {{-- PODSTAWOWE DANE --}}
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
                            <option value="{{ $deal->id }}" {{ old('crm_deal_id') == $deal->id ? 'selected' : '' }}>
                                {{ $deal->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="o-form-row">
                    <label class="o-label">Status</label>
                    <select name="status" class="o-select">
                        <option value="portfolio" {{ old('status', 'portfolio') === 'portfolio' ? 'selected' : '' }}>Portfolio</option>
                        <option value="inprogress" {{ old('status') === 'inprogress' ? 'selected' : '' }}>W toku</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- DANE KLIENTA --}}
        <div class="o-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                <h3 style="margin:0;font-size:16px;">Dane klienta</h3>
                <div style="display:flex;gap:8px;align-items:center;">
                    <label class="o-label" style="margin:0;">Wybierz z CRM:</label>
                    <select id="crm-company-select" class="o-select" style="width:auto;min-width:200px;" onchange="fillCustomerFromCrm(this)">
                        <option value="">-- wybierz --</option>
                        @foreach($crmCompanies as $company)
                            <option value="{{ $company->id }}"
                                data-name="{{ $company->name }}"
                                data-nip="{{ $company->nip ?? '' }}"
                                data-phone="{{ $company->phone ?? '' }}"
                                data-email="{{ $company->email ?? '' }}"
                                data-address="{{ $company->address ?? '' }}"
                                data-city="{{ $company->city ?? '' }}"
                                data-postal="{{ $company->postal_code ?? '' }}">
                                {{ $company->name }}
                            </option>
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
                @if($prefill)
                    <input type="hidden" name="company_id" value="{{ $prefill->id }}">
                @endif
                @if($fromInquiry)
                    <input type="hidden" name="inquiry_id" value="{{ $fromInquiry->id }}">
                @endif
            </div>
        </div>

        {{-- SEKCJE: USŁUGI, PRACE WŁASNE, MATERIAŁY --}}
        @foreach([['services','Usługi'], ['works','Prace własne'], ['materials','Materiały']] as [$sectionId, $sectionLabel])
        <div class="o-card">
            <div class="o-card-header" onclick="toggleSection('{{ $sectionId }}')">
                <h3>
                    {{ $sectionLabel }}
                    <span style="font-size:13px;color:#4c6373;font-weight:400;" id="{{ $sectionId }}-header-sum"></span>
                </h3>
                <svg id="{{ $sectionId }}-icon" style="width:20px;height:20px;transition:transform .2s;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div id="{{ $sectionId }}-content" class="o-card-body" style="display:none;">
                <div style="overflow-x:auto;">
                    <table class="o-tbl">
                        <thead>
                            <tr>
                                <th style="width:30px;">Nr</th>
                                <th>Nazwa</th>
                                <th>Opis/Typ</th>
                                <th style="width:60px;">Ilość</th>
                                <th style="width:110px;">Cena (zł)</th>
                                <th style="width:110px;">Cena kat.</th>
                                <th style="width:110px;">Wartość (zł)</th>
                                <th style="width:80px;"></th>
                            </tr>
                        </thead>
                        <tbody id="{{ $sectionId }}-table">
                            <tr>
                                <td><input type="number" class="o-tbl-input" style="width:40px;" value="1" readonly></td>
                                <td><input type="text" name="{{ $sectionId }}[0][name]" class="o-tbl-input"></td>
                                <td><input type="text" name="{{ $sectionId }}[0][type]" class="o-tbl-input"></td>
                                <td><input type="number" min="0" step="0.01" value="1" name="{{ $sectionId }}[0][quantity]" class="o-tbl-input quantity-input" data-section="{{ $sectionId }}" onchange="calculateRowValue(this)"></td>
                                <td><input type="number" step="0.01" min="0" name="{{ $sectionId }}[0][price]" class="o-tbl-input price-input" data-section="{{ $sectionId }}" onchange="calculateRowValue(this)"></td>
                                <td><input type="number" step="0.01" min="0" name="{{ $sectionId }}[0][catalog_price]" class="o-tbl-input catalog-price-input" placeholder="kat." oninput="updateBuiltInProfit()"></td>
                                <td><input type="text" name="{{ $sectionId }}[0][value]" value="0,00 zł" data-raw="0" class="o-tbl-input value-input" data-section="{{ $sectionId }}" readonly style="background:#f3f8f7;"></td>
                                <td>
                                    <div style="display:flex;gap:2px;">
                                        <button type="button" onclick="moveRow(this,'up','{{ $sectionId }}')" class="o-btn-sm" style="background:#e2e8f0;color:#0f2330;" title="Wyżej">↑</button>
                                        <button type="button" onclick="moveRow(this,'down','{{ $sectionId }}')" class="o-btn-sm" style="background:#e2e8f0;color:#0f2330;" title="Niżej">↓</button>
                                        <button type="button" onclick="removeRow(this,'{{ $sectionId }}')" class="o-btn-sm" style="background:#fee2e2;color:#991b1b;" title="Usuń">✕</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="display:flex;gap:8px;margin-top:10px;">
                    <button type="button" onclick="addRow('{{ $sectionId }}')" class="o-btn o-btn-blue" style="font-size:13px;padding:7px 14px;">+ Dodaj wiersz</button>
                </div>
                <div class="o-section-sumrow">
                    <span>Suma sekcji: <strong id="{{ $sectionId }}-total">0,00 zł</strong></span>
                </div>
            </div>
        </div>
        @endforeach

        {{-- SEKCJE NIESTANDARDOWE --}}
        <div id="custom-sections-container"></div>
        <div style="margin-bottom:14px;">
            <button type="button" onclick="addCustomSection()" class="o-btn o-btn-green">+ Dodaj sekcję niestandardową</button>
        </div>

        {{-- KALKULATOR ZYSKU --}}
        <div class="o-profit-box">
            <h3 style="margin:0 0 14px;font-size:16px;">Kalkulator zysku</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:14px;">
                <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;">
                    <div style="font-size:11px;text-transform:uppercase;color:#4c6373;letter-spacing:.4px;margin-bottom:4px;">Łącznie (koszty)</div>
                    <div style="font-size:18px;font-weight:700;" id="grand-total">0,00 zł</div>
                </div>
                <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;">
                    <div style="font-size:11px;text-transform:uppercase;color:#4c6373;letter-spacing:.4px;margin-bottom:4px;">Wbudowany zysk</div>
                    <div style="font-size:14px;font-weight:700;color:#1ba84a;" id="built-in-profit-display">0,00 zł (0,0%)</div>
                </div>
                <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;">
                    <div style="font-size:11px;text-transform:uppercase;color:#4c6373;letter-spacing:.4px;margin-bottom:4px;">Łącznie z zysku</div>
                    <div style="font-size:14px;font-weight:700;color:#0e89d8;" id="total-profit-display">0,00 zł (0,0%)</div>
                </div>
                <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;">
                    <div style="font-size:11px;text-transform:uppercase;color:#4c6373;letter-spacing:.4px;margin-bottom:4px;">Suma z zyskiem</div>
                    <div style="font-size:20px;font-weight:800;color:#0f2330;" id="total-with-profit">0,00 zł</div>
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

        {{-- HARMONOGRAM --}}
        <div class="o-schedule-box">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                <input type="checkbox" id="schedule_enabled" name="schedule_enabled" value="1" onchange="toggleSchedule(this.checked)" style="width:16px;height:16px;">
                <label for="schedule_enabled" style="font-size:16px;font-weight:600;cursor:pointer;">Harmonogram realizacji</label>
            </div>
            <div id="schedule-section" style="display:none;">
                <table class="o-tbl" style="margin-bottom:10px;">
                    <thead>
                        <tr>
                            <th style="width:40px;">Nr</th>
                            <th>Kamień milowy / Etap</th>
                            <th>Opis</th>
                            <th style="width:40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="schedule-table"></tbody>
                </table>
                <button type="button" onclick="addScheduleRow()" class="o-btn o-btn-blue" style="font-size:13px;padding:7px 14px;">+ Dodaj etap</button>
            </div>
        </div>

        {{-- WARUNKI PŁATNOŚCI --}}
        <div class="o-payment-box">
            <h3 style="margin:0 0 12px;font-size:16px;">Warunki płatności</h3>
            <table class="o-tbl" style="margin-bottom:10px;">
                <thead>
                    <tr>
                        <th style="width:40px;">Nr</th>
                        <th>Opis raty</th>
                        <th style="width:80px;">% wartości</th>
                        <th style="width:120px;">Termin</th>
                        <th style="width:40px;"></th>
                    </tr>
                </thead>
                <tbody id="payment-table"></tbody>
            </table>
            <button type="button" onclick="addPaymentRow()" class="o-btn" style="font-size:13px;padding:7px 14px;background:#d97706;color:#fff;">+ Dodaj ratę</button>
        </div>

        {{-- OPIS OFERTY (Quill.js) --}}
        <div class="o-card">
            <h3 style="margin:0 0 12px;font-size:16px;">Opis oferty</h3>
            <div id="quill-editor" style="min-height:150px;background:#fff;border:1px solid #c9d7e3;border-radius:9px;"></div>
            <input type="hidden" name="offer_description" id="offer_description_input">
        </div>

        {{-- OPCJE DRUKU --}}
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

{{-- Quill.js --}}
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
// ── Quill ──
const quill = new Quill('#quill-editor', { theme: 'snow', placeholder: 'Opis oferty...' });
document.getElementById('offer-form').addEventListener('submit', function() {
    document.getElementById('offer_description_input').value = quill.root.innerHTML;
});

// ── Wypełnij dane klienta z CRM ──
function fillCustomerFromCrm(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) return;
    document.getElementById('customer_name').value    = opt.dataset.name    || '';
    document.getElementById('customer_nip').value     = opt.dataset.nip     || '';
    document.getElementById('customer_phone').value   = opt.dataset.phone   || '';
    document.getElementById('customer_email').value   = opt.dataset.email   || '';
    document.getElementById('customer_address').value = opt.dataset.address || '';
    document.getElementById('customer_city').value    = opt.dataset.city    || '';
    document.getElementById('customer_postal_code').value = opt.dataset.postal || '';
}

// ── Sekcje ──
function toggleSection(sectionId) {
    const content = document.getElementById(sectionId + '-content');
    const icon    = document.getElementById(sectionId + '-icon');
    const hidden  = content.style.display === 'none';
    content.style.display = hidden ? '' : 'none';
    icon.style.transform  = hidden ? 'rotate(180deg)' : '';
}

function formatPrice(val) {
    return val.toLocaleString('pl-PL', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' zł';
}

// ── Liczniki wierszy ──
let rowCounters = { services: 1, works: 1, materials: 1 };
let customSectionCounter = 0;
let customSections = [];
let _grandTotalRaw = 0;
let _sectionTotals = {};

function addRow(section) {
    const tbody = document.getElementById(section + '-table');
    const idx   = rowCounters[section];
    const tr    = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="number" class="o-tbl-input" style="width:40px;" value="${idx + 1}" readonly></td>
        <td><input type="text" name="${section}[${idx}][name]" class="o-tbl-input"></td>
        <td><input type="text" name="${section}[${idx}][type]" class="o-tbl-input"></td>
        <td><input type="number" min="0" step="0.01" value="1" name="${section}[${idx}][quantity]" class="o-tbl-input quantity-input" data-section="${section}" onchange="calculateRowValue(this)"></td>
        <td><input type="number" step="0.01" min="0" name="${section}[${idx}][price]" class="o-tbl-input price-input" data-section="${section}" onchange="calculateRowValue(this)"></td>
        <td><input type="number" step="0.01" min="0" name="${section}[${idx}][catalog_price]" class="o-tbl-input catalog-price-input" placeholder="kat." oninput="updateBuiltInProfit()"></td>
        <td><input type="text" name="${section}[${idx}][value]" value="0,00 zł" data-raw="0" class="o-tbl-input value-input" data-section="${section}" readonly style="background:#f3f8f7;"></td>
        <td>
            <div style="display:flex;gap:2px;">
                <button type="button" onclick="moveRow(this,'up','${section}')" class="o-btn-sm" style="background:#e2e8f0;color:#0f2330;" title="Wyżej">↑</button>
                <button type="button" onclick="moveRow(this,'down','${section}')" class="o-btn-sm" style="background:#e2e8f0;color:#0f2330;" title="Niżej">↓</button>
                <button type="button" onclick="removeRow(this,'${section}')" class="o-btn-sm" style="background:#fee2e2;color:#991b1b;" title="Usuń">✕</button>
            </div>
        </td>
    `;
    tbody.appendChild(tr);
    rowCounters[section]++;
    reindexSection(section);
}

function removeRow(btn, section) {
    btn.closest('tr').remove();
    reindexSection(section);
    calculateTotal(section);
}

function moveRow(btn, direction, section) {
    const row   = btn.closest('tr');
    const tbody = row.closest('tbody');
    if (direction === 'up') {
        const prev = row.previousElementSibling;
        if (prev) tbody.insertBefore(row, prev);
    } else {
        const next = row.nextElementSibling;
        if (next) tbody.insertBefore(next, row);
    }
    reindexSection(section);
}

function reindexSection(section) {
    const tbody  = document.getElementById(section + '-table');
    if (!tbody) return;
    const rows   = tbody.querySelectorAll('tr');
    rows.forEach((row, i) => {
        const num = row.querySelector('input[type="number"][readonly]');
        if (num) num.value = i + 1;
        row.querySelectorAll('[name]').forEach(el => {
            if (section.startsWith('custom')) {
                const sNum = section.replace('custom', '');
                el.name = el.name.replace(
                    /custom_sections\[\d+\]\[items\]\[\d+\]/,
                    `custom_sections[${sNum}][items][${i}]`
                );
            } else {
                el.name = el.name.replace(
                    new RegExp('^' + section + '\\[\\d+\\]'),
                    `${section}[${i}]`
                );
            }
        });
    });
    if (!section.startsWith('custom')) rowCounters[section] = rows.length;
}

function calculateRowValue(input) {
    const row      = input.closest('tr');
    const qty      = parseFloat(row.querySelector('.quantity-input')?.value) || 0;
    const price    = parseFloat(row.querySelector('.price-input')?.value) || 0;
    const value    = qty * price;
    const valInput = row.querySelector('.value-input');
    if (valInput) {
        valInput.dataset.raw = value.toFixed(2);
        valInput.value       = formatPrice(value);
    }
    const catInput = row.querySelector('.catalog-price-input');
    if (catInput && !catInput.value) catInput.value = price ? price.toFixed(2) : '';
    calculateTotal(input.dataset.section);
    updateBuiltInProfit();
}

function calculateTotal(section) {
    let total = 0;
    document.querySelectorAll(`#${section}-table .value-input`).forEach(inp => {
        total += parseFloat(inp.dataset.raw || inp.value) || 0;
    });
    _sectionTotals[section] = total;
    const el = document.getElementById(section + '-total');
    if (el) el.textContent = formatPrice(total);
    const headerSum = document.getElementById(section + '-header-sum');
    if (headerSum) headerSum.textContent = '— ' + formatPrice(total);
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let grandTotal = 0;
    ['services', 'works', 'materials'].forEach(s => {
        document.querySelectorAll(`#${s}-table .value-input`).forEach(inp => {
            grandTotal += parseFloat(inp.dataset.raw || inp.value) || 0;
        });
    });
    customSections.forEach(num => {
        document.querySelectorAll(`#custom${num}-table .value-input`).forEach(inp => {
            grandTotal += parseFloat(inp.dataset.raw || inp.value) || 0;
        });
    });
    _grandTotalRaw = grandTotal;
    document.getElementById('grand-total').textContent = formatPrice(grandTotal);
    updateProfitFromPercent();
}

function updateBuiltInProfit() {
    let builtIn = 0;
    document.querySelectorAll('.catalog-price-input').forEach(catInp => {
        const row      = catInp.closest('tr');
        if (!row) return;
        const priceInp = row.querySelector('.price-input');
        const qtyInp   = row.querySelector('.quantity-input');
        const price    = parseFloat(priceInp?.value) || 0;
        const cat      = parseFloat(catInp.value) || price;
        const qty      = parseFloat(qtyInp?.value) || 1;
        if (cat > price) builtIn += (cat - price) * qty;
    });
    const pct    = _grandTotalRaw > 0 ? (builtIn / _grandTotalRaw * 100) : 0;
    const el     = document.getElementById('built-in-profit-display');
    if (el) el.textContent = formatPrice(builtIn) + ' (' + pct.toFixed(1) + '%)';
    const addAmt = parseFloat(document.getElementById('profit-amount-input')?.value || '0') || 0;
    const total  = builtIn + addAmt;
    const totPct = _grandTotalRaw > 0 ? (total / _grandTotalRaw * 100) : 0;
    const tel    = document.getElementById('total-profit-display');
    if (tel) tel.textContent = formatPrice(total) + ' (' + totPct.toFixed(1) + '%)';
}

function updateProfitFromPercent() {
    const pct    = parseFloat(document.getElementById('profit-percent')?.value) || 0;
    const amount = _grandTotalRaw * pct / 100;
    const inp    = document.getElementById('profit-amount-input');
    if (inp) inp.value = amount.toFixed(2);
    updateProfitDisplay();
}

function updateProfitFromAmount() {
    const amount = parseFloat(document.getElementById('profit-amount-input')?.value) || 0;
    const pct    = _grandTotalRaw > 0 ? (amount / _grandTotalRaw * 100) : 0;
    const inp    = document.getElementById('profit-percent');
    if (inp) inp.value = pct.toFixed(2);
    updateProfitDisplay();
}

function updateProfitDisplay() {
    const amount = parseFloat(document.getElementById('profit-amount-input')?.value) || 0;
    const el     = document.getElementById('total-with-profit');
    if (el) el.textContent = formatPrice(_grandTotalRaw + amount);
    updateBuiltInProfit();
}

// ── Harmonogram ──
let scheduleCount = 0;
function toggleSchedule(checked) {
    document.getElementById('schedule-section').style.display = checked ? '' : 'none';
}
function addScheduleRow(milestone, date, description) {
    const tbody = document.getElementById('schedule-table');
    const idx   = scheduleCount++;
    const tr    = document.createElement('tr');
    tr.innerHTML = `
        <td style="text-align:center;color:#4c6373;">${idx + 1}</td>
        <td><input type="text" name="schedule[${idx}][milestone]" value="${escapeHtml(milestone||'')}" class="o-tbl-input"></td>
        <td><input type="text" name="schedule[${idx}][description]" value="${escapeHtml(description||'')}" class="o-tbl-input"></td>
        <td style="text-align:center;"><button type="button" onclick="this.closest('tr').remove(); reindexSchedule()" class="o-btn-sm" style="background:#fee2e2;color:#991b1b;">✕</button></td>
    `;
    tbody.appendChild(tr);
}
function reindexSchedule() {
    document.querySelectorAll('#schedule-table tr').forEach((row, i) => {
        row.querySelector('td:first-child').textContent = i + 1;
        row.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/schedule\[\d+\]/, `schedule[${i}]`);
        });
    });
}

// ── Warunki płatności ──
let paymentCount = 0;
function addPaymentRow(description, percent, deadline) {
    const tbody = document.getElementById('payment-table');
    const idx   = paymentCount++;
    const tr    = document.createElement('tr');
    tr.innerHTML = `
        <td style="text-align:center;color:#4c6373;">${idx + 1}</td>
        <td><input type="text" name="payment_terms[${idx}][description]" value="${escapeHtml(description||'')}" class="o-tbl-input"></td>
        <td><input type="number" step="0.01" min="0" max="100" name="payment_terms[${idx}][percent]" value="${percent||''}" class="o-tbl-input" style="text-align:right;"></td>
        <td><input type="text" name="payment_terms[${idx}][deadline]" value="${escapeHtml(deadline||'')}" class="o-tbl-input"></td>
        <td style="text-align:center;"><button type="button" onclick="this.closest('tr').remove(); reindexPayment()" class="o-btn-sm" style="background:#fee2e2;color:#991b1b;">✕</button></td>
    `;
    tbody.appendChild(tr);
}
function reindexPayment() {
    document.querySelectorAll('#payment-table tr').forEach((row, i) => {
        row.querySelector('td:first-child').textContent = i + 1;
        row.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/payment_terms\[\d+\]/, `payment_terms[${i}]`);
        });
    });
}

// ── Sekcje niestandardowe ──
function addCustomSection() {
    const sectionName = prompt('Podaj nazwę nowej sekcji:');
    if (!sectionName || !sectionName.trim()) return;
    customSectionCounter++;
    const num     = customSectionCounter;
    const sId     = 'custom' + num;
    customSections.push(num);
    rowCounters[sId] = 1;

    const container = document.getElementById('custom-sections-container');
    const div       = document.createElement('div');
    div.className   = 'o-card';
    div.id          = 'section-' + sId;
    div.innerHTML   = `
        <div class="o-card-header" onclick="toggleSection('${sId}')" style="justify-content:space-between;">
            <h3 id="${sId}-name-label">${escapeHtml(sectionName.trim())}
                <span style="font-size:13px;color:#4c6373;font-weight:400;" id="${sId}-header-sum"></span>
            </h3>
            <div style="display:flex;gap:6px;align-items:center;">
                <button type="button" onclick="event.stopPropagation();editSectionName('${sId}')" class="o-btn-sm" style="background:#dbeafe;color:#1d4ed8;">✏️</button>
                <button type="button" onclick="event.stopPropagation();removeCustomSection('${sId}')" class="o-btn-sm" style="background:#fee2e2;color:#991b1b;">✕</button>
                <svg id="${sId}-icon" style="width:20px;height:20px;transition:transform .2s;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
        <div id="${sId}-content" class="o-card-body" style="display:none;">
            <input type="hidden" id="${sId}-name-input" name="custom_sections[${num}][name]" value="${escapeHtml(sectionName.trim())}">
            <div style="overflow-x:auto;">
                <table class="o-tbl">
                    <thead>
                        <tr>
                            <th style="width:30px;">Nr</th>
                            <th>Nazwa</th>
                            <th>Opis/Typ</th>
                            <th style="width:60px;">Ilość</th>
                            <th style="width:110px;">Cena (zł)</th>
                            <th style="width:110px;">Cena kat.</th>
                            <th style="width:110px;">Wartość (zł)</th>
                            <th style="width:80px;"></th>
                        </tr>
                    </thead>
                    <tbody id="${sId}-table">
                        <tr>
                            <td><input type="number" class="o-tbl-input" style="width:40px;" value="1" readonly></td>
                            <td><input type="text" name="custom_sections[${num}][items][0][name]" class="o-tbl-input"></td>
                            <td><input type="text" name="custom_sections[${num}][items][0][type]" class="o-tbl-input"></td>
                            <td><input type="number" min="0" step="0.01" value="1" name="custom_sections[${num}][items][0][quantity]" class="o-tbl-input quantity-input" data-section="${sId}" onchange="calculateRowValue(this)"></td>
                            <td><input type="number" step="0.01" min="0" name="custom_sections[${num}][items][0][price]" class="o-tbl-input price-input" data-section="${sId}" onchange="calculateRowValue(this)"></td>
                            <td><input type="number" step="0.01" min="0" name="custom_sections[${num}][items][0][catalog_price]" class="o-tbl-input catalog-price-input" placeholder="kat." oninput="updateBuiltInProfit()"></td>
                            <td><input type="text" name="custom_sections[${num}][items][0][value]" value="0,00 zł" data-raw="0" class="o-tbl-input value-input" data-section="${sId}" readonly style="background:#f3f8f7;"></td>
                            <td>
                                <div style="display:flex;gap:2px;">
                                    <button type="button" onclick="moveRow(this,'up','${sId}')" class="o-btn-sm" style="background:#e2e8f0;color:#0f2330;">↑</button>
                                    <button type="button" onclick="moveRow(this,'down','${sId}')" class="o-btn-sm" style="background:#e2e8f0;color:#0f2330;">↓</button>
                                    <button type="button" onclick="removeRow(this,'${sId}')" class="o-btn-sm" style="background:#fee2e2;color:#991b1b;">✕</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="display:flex;gap:8px;margin-top:10px;">
                <button type="button" onclick="addCustomRow('${sId}',${num})" class="o-btn o-btn-blue" style="font-size:13px;padding:7px 14px;">+ Dodaj wiersz</button>
            </div>
            <div class="o-section-sumrow">
                <span>Suma sekcji: <strong id="${sId}-total">0,00 zł</strong></span>
            </div>
        </div>
    `;
    container.appendChild(div);
    toggleSection(sId);
}

function addCustomRow(sId, sNum) {
    const tbody = document.getElementById(sId + '-table');
    const idx   = rowCounters[sId] || 0;
    const tr    = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="number" class="o-tbl-input" style="width:40px;" value="${idx + 1}" readonly></td>
        <td><input type="text" name="custom_sections[${sNum}][items][${idx}][name]" class="o-tbl-input"></td>
        <td><input type="text" name="custom_sections[${sNum}][items][${idx}][type]" class="o-tbl-input"></td>
        <td><input type="number" min="0" step="0.01" value="1" name="custom_sections[${sNum}][items][${idx}][quantity]" class="o-tbl-input quantity-input" data-section="${sId}" onchange="calculateRowValue(this)"></td>
        <td><input type="number" step="0.01" min="0" name="custom_sections[${sNum}][items][${idx}][price]" class="o-tbl-input price-input" data-section="${sId}" onchange="calculateRowValue(this)"></td>
        <td><input type="number" step="0.01" min="0" name="custom_sections[${sNum}][items][${idx}][catalog_price]" class="o-tbl-input catalog-price-input" placeholder="kat." oninput="updateBuiltInProfit()"></td>
        <td><input type="text" name="custom_sections[${sNum}][items][${idx}][value]" value="0,00 zł" data-raw="0" class="o-tbl-input value-input" data-section="${sId}" readonly style="background:#f3f8f7;"></td>
        <td>
            <div style="display:flex;gap:2px;">
                <button type="button" onclick="moveRow(this,'up','${sId}')" class="o-btn-sm" style="background:#e2e8f0;color:#0f2330;">↑</button>
                <button type="button" onclick="moveRow(this,'down','${sId}')" class="o-btn-sm" style="background:#e2e8f0;color:#0f2330;">↓</button>
                <button type="button" onclick="removeRow(this,'${sId}')" class="o-btn-sm" style="background:#fee2e2;color:#991b1b;">✕</button>
            </div>
        </td>
    `;
    tbody.appendChild(tr);
    rowCounters[sId] = (rowCounters[sId] || 0) + 1;
    reindexSection(sId);
}

function editSectionName(sId) {
    const label  = document.getElementById(sId + '-name-label');
    const hidden = document.getElementById(sId + '-name-input');
    const cur    = hidden ? hidden.value : '';
    const newName = prompt('Edytuj nazwę sekcji:', cur);
    if (newName && newName.trim()) {
        if (hidden) hidden.value = newName.trim();
        if (label) label.childNodes[0].textContent = newName.trim();
    }
}

function removeCustomSection(sId) {
    if (!confirm('Usunąć sekcję?')) return;
    const el = document.getElementById('section-' + sId);
    if (el) el.remove();
    const num = parseInt(sId.replace('custom', ''));
    customSections = customSections.filter(n => n !== num);
    calculateGrandTotal();
}

function escapeHtml(text) {
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
</script>
</x-layouts.app>
