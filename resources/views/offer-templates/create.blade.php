<x-layouts.app>
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
#html-textarea { flex:1; width:100%; height:100%; border:none; outline:none; resize:none; font-family:'Fira Code','Courier New',monospace; font-size:12.5px; line-height:1.6; padding:16px; background:#1e1e2e; color:#cdd6f4; tab-size:2; }
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

<script>
// ─── Placeholder insert ───
const htmlTa = document.getElementById('html-textarea');

function insertPlaceholder(ph) {
    const start = htmlTa.selectionStart;
    const end   = htmlTa.selectionEnd;
    const val   = htmlTa.value;
    htmlTa.value = val.substring(0, start) + ph + val.substring(end);
    htmlTa.selectionStart = htmlTa.selectionEnd = start + ph.length;
    htmlTa.focus();
    refreshPreview();
}

// ─── Live preview ───
let previewTimer = null;
function refreshPreview() {
    const iframe = document.getElementById('html-preview');
    const html   = htmlTa.value;
    const demo   = html
        .replace(/\{\{offer_title\}\}/g, 'Audyt Energetyczny Zakładu XYZ')
        .replace(/\{\{offer_number\}\}/g, 'OF-2026/0001')
        .replace(/\{\{offer_date\}\}/g, new Date().toLocaleDateString('pl-PL'))
        .replace(/\{\{customer_name\}\}/g, 'Zakłady Przemysłowe Sp. z o.o.')
        .replace(/\{\{customer_nip\}\}/g, '123-456-78-90')
        .replace(/\{\{customer_address\}\}/g, 'ul. Fabryczna 12')
        .replace(/\{\{customer_postal_code\}\}/g, '44-100')
        .replace(/\{\{customer_city\}\}/g, 'Gliwice')
        .replace(/\{\{customer_phone\}\}/g, '+48 32 123 45 67')
        .replace(/\{\{customer_email\}\}/g, 'kontakt@firma.pl')
        .replace(/\{\{description\}\}/g, '<p>Niniejsza oferta dotyczy przeprowadzenia audytu energetycznego zgodnie z wymaganiami normy EN 16247.</p>')
        .replace(/\{\{items_table\}\}/g, '<table style="width:100%;border-collapse:collapse;font-size:13px;"><thead><tr><th style="background:#1A4D3A;color:#fff;padding:8px;">Nr</th><th style="background:#1A4D3A;color:#fff;padding:8px;text-align:left;">Pozycja</th><th style="background:#1A4D3A;color:#fff;padding:8px;text-align:right;">Wartość</th></tr></thead><tbody><tr><td style="padding:8px;text-align:center;">1</td><td style="padding:8px;">Audyt energetyczny — etap I</td><td style="padding:8px;text-align:right;">8 000,00 zł</td></tr><tr><td style="padding:8px;text-align:center;background:#f7faf9;">2</td><td style="padding:8px;background:#f7faf9;">Raport końcowy</td><td style="padding:8px;text-align:right;background:#f7faf9;">2 000,00 zł</td></tr></tbody><tfoot><tr><td colspan="2" style="padding:10px;text-align:right;font-weight:700;background:#1A4D3A;color:#fff;">Razem</td><td style="padding:10px;text-align:right;font-weight:700;background:#1A4D3A;color:#fff;">10 000,00 zł</td></tr></tfoot></table>')
        .replace(/\{\{distance_km\}\}/g, '120')
        .replace(/\{\{km_rate\}\}/g, '1,50')
        .replace(/\{\{travel_hours\}\}/g, '1,5')
        .replace(/\{\{hour_rate\}\}/g, '80,00')
        .replace(/\{\{travel_cost\}\}/g, '600,00')
        .replace(/\{\{total_price\}\}/g, '10 600,00')
        .replace(/\{\{auditor_hours\}\}/g, '8,0')
        .replace(/\{\{payment_terms\}\}/g, '<ul><li>100% — płatność po wykonaniu audytu, 14 dni od faktury</li></ul>');

    iframe.srcdoc = demo;
}

htmlTa.addEventListener('input', function() {
    clearTimeout(previewTimer);
    previewTimer = setTimeout(refreshPreview, 800);
});

// Initial preview
refreshPreview();

// ─── Default HTML ───
const DEFAULT_HTML = @json($defaultHtml);
function loadDefaultHtml() {
    if (htmlTa.value.trim() && !confirm('Zastąpić obecny kod domyślnym szablonem?')) return;
    htmlTa.value = DEFAULT_HTML;
    refreshPreview();
}

function clearEditor() {
    if (!confirm('Wyczyścić kod HTML?')) return;
    htmlTa.value = '';
    refreshPreview();
}

function openFullPreview() {
    const w = window.open('', '_blank');
    w.document.write(document.getElementById('html-preview').srcdoc || '<p>Brak podglądu.</p>');
    w.document.close();
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
