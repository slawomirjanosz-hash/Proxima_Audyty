<x-layouts.app>
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
#html-textarea { flex:1; width:100%; height:100%; border:none; outline:none; resize:none; font-family:'Fira Code','Courier New',monospace; font-size:12.5px; line-height:1.6; padding:16px; background:#1e1e2e; color:#cdd6f4; tab-size:2; }
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
        <a href="{{ route('offer-templates.index') }}" class="ot-btn ot-btn-gray">← Wróć</a>
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

    <div class="placeholder-tags" style="margin-bottom:10px;">
        <strong style="font-size:11px;color:var(--ink-mute);align-self:center;margin-right:4px;">Wstaw:</strong>
        @foreach(['offer_title','offer_number','offer_date','auditor_hours','customer_name','customer_nip','customer_address','customer_postal_code','customer_city','customer_phone','customer_email','description','items_table','distance_km','km_rate','travel_hours','hour_rate','travel_cost','total_price','payment_terms'] as $ph)
        <span onclick="insertPlaceholder('{{"{{"}}{{ $ph }}{!! '}}' !!}')">{{"{{"}}{{ $ph }}{!! '}}' !!}</span>
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

<script>
const htmlTa = document.getElementById('html-textarea');

function insertPlaceholder(ph) {
    const s = htmlTa.selectionStart, e = htmlTa.selectionEnd;
    htmlTa.value = htmlTa.value.substring(0,s) + ph + htmlTa.value.substring(e);
    htmlTa.selectionStart = htmlTa.selectionEnd = s + ph.length;
    htmlTa.focus();
    refreshPreview();
}

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
        .replace(/\{\{description\}\}/g, '<p>Przeprowadzenie audytu energetycznego zgodnie z EN 16247.</p>')
        .replace(/\{\{items_table\}\}/g, '<table style="width:100%;border-collapse:collapse;"><thead><tr><th style="background:#1A4D3A;color:#fff;padding:8px;">Pozycja</th><th style="background:#1A4D3A;color:#fff;padding:8px;text-align:right;">Wartość</th></tr></thead><tbody><tr><td style="padding:8px;">Audyt energetyczny</td><td style="padding:8px;text-align:right;">10 000,00 zł</td></tr></tbody><tfoot><tr><td style="padding:8px;font-weight:700;background:#1A4D3A;color:#fff;">Razem</td><td style="padding:8px;font-weight:700;background:#1A4D3A;color:#fff;text-align:right;">10 000,00 zł</td></tr></tfoot></table>')
        .replace(/\{\{distance_km\}\}/g, '120')
        .replace(/\{\{km_rate\}\}/g, '1,50')
        .replace(/\{\{travel_hours\}\}/g, '1,5')
        .replace(/\{\{hour_rate\}\}/g, '80,00')
        .replace(/\{\{travel_cost\}\}/g, '600,00')
        .replace(/\{\{total_price\}\}/g, '10 600,00')
        .replace(/\{\{auditor_hours\}\}/g, '8,0')
        .replace(/\{\{payment_terms\}\}/g, '<ul><li>100% — 14 dni od wystawienia faktury</li></ul>');
    iframe.srcdoc = demo;
}

htmlTa.addEventListener('input', function() {
    clearTimeout(previewTimer);
    previewTimer = setTimeout(refreshPreview, 800);
});
refreshPreview();

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
