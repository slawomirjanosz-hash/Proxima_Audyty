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
<input type="hidden" name="audit_category" value="{{ $template->audit_category }}">

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
    <p style="margin:0 0 14px;font-size:12px;color:var(--ink-mute);">Wartości używane gdy pole nie jest uzupełnione przy tworzeniu oferty. Pola klienta, numer i data oferty, sumy — wypełniane automatycznie z danych oferty.</p>

    <div class="ot-grid-2" style="margin-bottom:14px;">
        <div class="ot-form-row" style="margin:0;">
            <label class="ot-label">Tytuł oferty <code style="font-size:10px;">@{{offer_title}}</code></label>
            <input type="text" name="df_offer_title" value="{{ old('df_offer_title', $df['offer_title'] ?? '') }}" class="ot-input" placeholder="np. Oferta na audyt energetyczny">
        </div>
        <div class="ot-form-row" style="margin:0;">
            <label class="ot-label">Przedmiot oferty <code style="font-size:10px;">@{{offer_subject}}</code></label>
            <input type="text" name="df_offer_subject" value="{{ old('df_offer_subject', $df['offer_subject'] ?? '') }}" class="ot-input" placeholder="np. Przeprowadzenie audytu energetycznego">
        </div>
    </div>
    <div class="ot-form-row">
        <label class="ot-label">Opis / wstęp oferty <code style="font-size:10px;">@{{description}}</code></label>
        <textarea name="df_offer_description" class="ot-input" rows="3" placeholder="Domyślny opis lub wstęp oferty...">{{ old('df_offer_description', $df['offer_description'] ?? '') }}</textarea>
    </div>
    <div class="ot-grid-3" style="margin-bottom:14px;">
        <div class="ot-form-row" style="margin:0;">
            <label class="ot-label">Rodzaj klienta <code style="font-size:10px;">@{{customer_type}}</code></label>
            <input type="text" name="df_customer_type" value="{{ old('df_customer_type', $df['customer_type'] ?? 'Firma') }}" class="ot-input" placeholder="np. Firma / Osoba fizyczna">
        </div>
        <div class="ot-form-row" style="margin:0;">
            <label class="ot-label">Termin ważności oferty <code style="font-size:10px;">@{{offer_validity}}</code></label>
            <input type="text" name="df_offer_validity" value="{{ old('df_offer_validity', $df['offer_validity'] ?? '30 dni') }}" class="ot-input" placeholder="np. 30 dni">
        </div>
        <div class="ot-form-row" style="margin:0;">
            <label class="ot-label">Termin realizacji <code style="font-size:10px;">@{{delivery_deadline}}</code></label>
            <input type="text" name="df_delivery_deadline" value="{{ old('df_delivery_deadline', $df['delivery_deadline'] ?? '') }}" class="ot-input" placeholder="np. 30 dni roboczych">
        </div>
    </div>
    <div class="ot-grid-2">
        <div class="ot-form-row" style="margin:0;">
            <label class="ot-label">Warunki płatności <code style="font-size:10px;">@{{payment_terms}}</code></label>
            <textarea name="df_payment_terms_text" class="ot-input" rows="3" placeholder="np. Płatność 100% po wykonaniu audytu, 14 dni od faktury.">{{ old('df_payment_terms_text', $df['payment_terms_text'] ?? 'Płatność na podstawie faktury VAT, 14 dni od wystawienia.') }}</textarea>
        </div>
        <div class="ot-form-row" style="margin:0;">
            <label class="ot-label">Stawka VAT % <code style="font-size:10px;">@{{vat_rate}}</code></label>
            <input type="number" name="df_vat_rate" value="{{ old('df_vat_rate', $df['vat_rate'] ?? '23') }}" class="ot-input" min="0" max="100" step="1" placeholder="23">
            <div style="font-size:11px;color:var(--ink-mute);margin-top:4px;">Używana do obliczenia @{{total_price_vat}} i @{{total_price}} (brutto).</div>
        </div>
    </div>
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

{{-- NIEBEZPIECZNA STREFA --}}
@if(!$template->offers()->exists())
<div class="ot-panel" style="border-color:#fca5a5;">
    <h3 style="margin:0 0 10px;font-size:15px;color:#dc2626;">Strefa usunięcia</h3>
    <form method="POST" action="{{ route('offer-templates.destroy', $template) }}" onsubmit="return confirm('Na pewno usunąć szablon {{ addslashes($template->name) }}?')">
        @csrf @method('DELETE')
        <button type="submit" class="ot-btn ot-btn-red" style="font-size:13px;">🗑 Usuń szablon</button>
    </form>
</div>
@endif

{{-- PRZYCISKI --}}
<div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;">
    <a href="{{ route('offer-templates.index') }}" class="ot-btn ot-btn-gray">Anuluj</a>
    <button type="submit" class="ot-btn ot-btn-green">💾 Zapisz zmiany</button>
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

const DEMO_ITEMS = '<table style="width:100%;border-collapse:collapse;font-size:13px;"><thead><tr><th style="background:#1A4D3A;color:#fff;padding:8px;">Nr</th><th style="background:#1A4D3A;color:#fff;padding:8px;text-align:left;">Pozycja</th><th style="background:#1A4D3A;color:#fff;padding:8px;text-align:right;">Wartość</th></tr></thead><tbody><tr><td style="padding:8px;text-align:center;">1</td><td style="padding:8px;">Audyt energetyczny — etap I</td><td style="padding:8px;text-align:right;">8 000,00 zł</td></tr><tr><td style="padding:8px;text-align:center;background:#f7faf9;">2</td><td style="padding:8px;background:#f7faf9;">Raport końcowy</td><td style="padding:8px;text-align:right;background:#f7faf9;">2 000,00 zł</td></tr></tbody><tfoot><tr><td colspan="2" style="padding:10px;text-align:right;font-weight:700;background:#1A4D3A;color:#fff;">Razem netto</td><td style="padding:10px;text-align:right;font-weight:700;background:#1A4D3A;color:#fff;">10 000,00 zł</td></tr></tfoot></table>';

let previewTimer = null;
function refreshPreview() {
    const iframe = document.getElementById('html-preview');
    const vatRate = parseFloat(getV('[name="df_vat_rate"]') || '23') || 23;
    const demoNet = 10000;
    const vatAmt  = Math.round(demoNet * vatRate) / 100;
    const gross   = demoNet + vatAmt;
    const fmt     = n => n.toLocaleString('pl-PL', {minimumFractionDigits: 2, maximumFractionDigits: 2});

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
        'distance_km':          '120',
        'km_rate':              null,
        'travel_hours':         '1,5',
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
        <td style="text-align:center;"><button type="button" onclick="this.closest('tr').remove()" style="background:#fee2e2;color:#991b1b;border:0;border-radius:6px;padding:5px 9px;cursor:pointer;">✕</button></td>
    `;
    tbody.appendChild(tr);
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
