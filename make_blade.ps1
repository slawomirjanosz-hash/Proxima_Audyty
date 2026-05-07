
$masterHtml = [IO.File]::ReadAllText("HTMLe\1_ENESA_Formularz_HTML_Master_v1.html")

# Extract sections
$cssStart = $masterHtml.IndexOf("<style>") + 7
$cssEnd = $masterHtml.IndexOf("</style>")
$css = $masterHtml.Substring($cssStart, $cssEnd - $cssStart)

$bodyStart = $masterHtml.IndexOf("<body>") + 6
$scriptStart = $masterHtml.IndexOf("<script>")
$bodyHtml = $masterHtml.Substring($bodyStart, $scriptStart - $bodyStart)

$jsStart = $scriptStart + 8
$jsEnd = $masterHtml.LastIndexOf("</script>")
$js = $masterHtml.Substring($jsStart, $jsEnd - $jsStart)

Write-Host "CSS: $($css.Length), Body: $($bodyHtml.Length), JS: $($js.Length)"

# Adapt CSS: Fix sidenav from position:fixed to sticky
$css = $css -replace '\.sidenav\s*\{[^}]*\}', @'
.sidenav {
  width: 220px;
  background: var(--forest);
  color: var(--paper);
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;
  flex-shrink: 0;
  padding-top: 24px;
  box-sizing: border-box;
  z-index: 10;
}
'@

# Remove margin-left from .main and body flex
$css = $css -replace '\.main\s*\{[^}]*margin-left[^}]*\}', '.main { flex: 1; min-width: 0; padding: 40px 48px 80px; overflow-x: hidden; }'
$css = $css -replace 'body\s*\{[^}]*display\s*:\s*flex[^}]*\}', 'body { margin: 0; background: var(--paper); font-family: var(--font-body); font-size: 15px; color: var(--ink); }'

# Change body::before to .enesa-form-body::before
$css = $css -replace 'body::before', '.enesa-form-body::before'

# Adapt body HTML: remove wrapping div that may set position
$bodyHtml = $bodyHtml.TrimStart()

# Adapt JS: Replace enesaStorage.get/set with localStorage fallback + server sync
# Key changes:
# 1. loadSavedData reads from FORM_DATA first
# 2. saveField queues for AJAX + also writes localStorage master: prefix  
# 3. scheduleAutoSave does AJAX POST
$js = $js -replace 'const STORAGE_PREFIX = ''master:'';', 'const STORAGE_PREFIX = ''master:'';'

# Replace enesaStorage object
$enesaStorageOld = @'
  const enesaStorage = {
    get: (k) => {
      try { return localStorage.getItem(k) ?? '' } catch { return '' }
    },
    set: (k, v) => {
      try { localStorage.setItem(k, v) } catch {}
    }
  };
'@
$enesaStorageNew = @'
  const enesaStorage = {
    get: (k) => {
      // First try FORM_DATA (from server), then localStorage as fallback
      const serverKey = k.startsWith(STORAGE_PREFIX) ? k.slice(STORAGE_PREFIX.length) : k;
      if (typeof FORM_DATA !== 'undefined' && FORM_DATA && FORM_DATA[serverKey] !== undefined) {
        return FORM_DATA[serverKey];
      }
      try { return localStorage.getItem(k) ?? '' } catch { return '' }
    },
    set: (k, v) => {
      try { localStorage.setItem(k, v) } catch {}
    }
  };
'@
$js = $js.Replace($enesaStorageOld, $enesaStorageNew)

# Replace saveField to also do AJAX
$saveFieldOld = @'
  function saveField(id, value) {
    enesaStorage.set(STORAGE_PREFIX + id, value);
    showSaveIndicator();
  }
'@
$saveFieldNew = @'
  function saveField(id, value) {
    enesaStorage.set(STORAGE_PREFIX + id, value);
    pendingChanges[id] = value;
  }
  const pendingChanges = {};
'@
$js = $js.Replace($saveFieldOld, $saveFieldNew)

# Replace scheduleAutoSave
$schedOld = @'
  let saveTimer = null;
  function scheduleAutoSave() {
    if (saveTimer) clearTimeout(saveTimer);
    saveTimer = setTimeout(() => {
      showSaveIndicator();
    }, 800);
  }
'@
$schedNew = @'
  let saveTimer = null;
  function scheduleAutoSave() {
    if (saveTimer) clearTimeout(saveTimer);
    saveTimer = setTimeout(() => {
      const fields = {};
      document.querySelectorAll('[data-id]').forEach(el => {
        const v = el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value;
        if (v !== '') fields[el.dataset.id] = v;
        // Also mirror to localStorage with master: prefix for other scope forms
        enesaStorage.set(STORAGE_PREFIX + el.dataset.id, v);
      });
      // Save _meta fields
      fields['_meta_n_wydz'] = document.querySelectorAll('[data-wyd-row]').length;
      fields['_meta_n_hal'] = document.querySelectorAll('[data-hal-row]').length;
      enesaStorage.set(STORAGE_PREFIX + '_meta_n_wydz', fields['_meta_n_wydz']);
      enesaStorage.set(STORAGE_PREFIX + '_meta_n_hal', fields['_meta_n_hal']);
      if (typeof SAVE_URL === 'undefined' || !SAVE_URL) return;
      const total = document.querySelectorAll('[data-id]').length;
      const filled = Object.keys(fields).filter(k => !k.startsWith('_meta')).length;
      const pct = total > 0 ? Math.round(filled / total * 100) : 0;
      fetch(SAVE_URL, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
        body: JSON.stringify({fields: fields, completion_percent: pct, company_id: COMPANY_ID})
      }).then(r => r.json()).then(d => {
        showSaveIndicator('Zapisano');
      }).catch(() => {
        showSaveIndicator('Błąd zapisu!');
      });
    }, 800);
  }
'@
$js = $js.Replace($schedOld, $schedNew)

# Build the blade file content
$blade = @"
<x-layouts.app>
@php
`$_companyData = null;
if (`$company) {
    `$_auditorName = '';
    `$_auditorEmail = '';
    if (method_exists(`$company, 'assignedUsers')) {
        `$_auditor = `$company->assignedUsers->where('role', 'auditor')->first();
        if (`$_auditor) { `$_auditorName = `$_auditor->name; `$_auditorEmail = `$_auditor->email; }
    }
    `$_companyData = [
        'name'          => `$company->name ?? '',
        'nip'           => `$company->nip ?? '',
        'regon'         => `$company->regon ?? '',
        'address'       => `$company->address ?? '',
        'city'          => `$company->city ?? '',
        'auditorName'   => `$_auditorName,
        'auditorEmail'  => `$_auditorEmail,
        'krs'           => `$company->krs ?? '',
        'contactName'   => optional(`$company->contacts->first())->name ?? '',
        'contactEmail'  => optional(`$company->contacts->first())->email ?? '',
        'contactPhone'  => optional(`$company->contacts->first())->phone ?? '',
    ];
}
`$_teamMembers = [];
if (isset(`$currentUser) && `$currentUser) {
    `$_teamMembers[] = ['name' => `$currentUser->name, 'email' => `$currentUser->email, 'role' => 'Audytor prowadzący'];
}
@endphp

@if($isStaff && $company)
<div style="background:#1d4f73;color:#fff;padding:8px 20px;font-size:13px;display:flex;align-items:center;gap:12px;">
  <span>⚙ Tryb administratora: {{ $company->name }}</span>
  <a href="{{ route('firma.show', $company) }}" style="color:#a0d4f5;margin-left:auto;">← Wróć do firmy</a>
</div>
@elseif($isStaff)
<div style="background:#0f2330;color:#ccc;padding:8px 20px;font-size:13px;">
  Tryb podglądu — dane nie są przypisane do konkretnej firmy
</div>
@endif

<style>
$css
.enesa-form-body {
  display: flex;
  min-height: calc(100vh - 60px);
  position: relative;
}
.enesa-form-body::before {
  content: 'POUFNE';
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%,-50%) rotate(-45deg);
  font-size: 120px;
  font-weight: 900;
  color: rgba(0,0,0,0.03);
  pointer-events: none;
  z-index: 0;
  letter-spacing: 0.2em;
}
</style>

<div class="save-indicator" id="save-indicator">Zapisano</div>

<div class="enesa-form-body">
$bodyHtml
</div>

<script>
const SAVE_URL   = '{{ isset($company) && $company ? route("client.energy-audit-master.save") : "" }}';
const CSRF       = '{{ csrf_token() }}';
const FORM_DATA  = @json($formData ?? []);
const COMPANY_ID = {{ isset(\$company) && \$company ? \$company->id : 'null' }};
const PREVIEW_MODE = {{ (!empty(\$previewMode) && \$previewMode) ? 'true' : 'false' }};
const COMPANY_DATA = @json(\$_companyData);
const TEAM_MEMBERS = @json(\$_teamMembers);

$js

// Prefill company and team data if fields are empty
function setIfEmpty(fieldId, value) {
  if (!value) return;
  const el = document.querySelector('[data-id="' + fieldId + '"]');
  if (el && !el.value) {
    el.value = value;
    el.dispatchEvent(new Event('input'));
  }
}

function prefillFromCompanyData() {
  if (!COMPANY_DATA) return;
  setIfEmpty('AUD-V1-NAZWA',   COMPANY_DATA.name);
  setIfEmpty('AUD-V2-NIP',     COMPANY_DATA.nip);
  setIfEmpty('AUD-V3-REGON',   COMPANY_DATA.regon);
  setIfEmpty('AUD-V4-ADRES',   COMPANY_DATA.address);
  setIfEmpty('AUD-V5-KRS',     COMPANY_DATA.krs);
  setIfEmpty('AUD-V6-KONTAKT', COMPANY_DATA.contactName);
  setIfEmpty('AUD-V7-EMAIL',   COMPANY_DATA.contactEmail);
  setIfEmpty('AUD-V8-TEL',     COMPANY_DATA.contactPhone);
  setIfEmpty('AUD-V9-AUDYTOR', COMPANY_DATA.auditorName);
  setIfEmpty('AUD-V10-AUD-EMAIL', COMPANY_DATA.auditorEmail);
}

document.addEventListener('DOMContentLoaded', () => {
  prefillFromCompanyData();
  if (TEAM_MEMBERS && TEAM_MEMBERS.length > 0) {
    TEAM_MEMBERS.forEach((m, i) => {
      const n = i + 1;
      setIfEmpty('AUD-V13-ZES-' + n + '-IMIE', m.name);
      setIfEmpty('AUD-V13-ZES-' + n + '-EMAIL', m.email);
      setIfEmpty('AUD-V13-ZES-' + n + '-ROLA', m.role || '');
    });
  }
});
</script>
</x-layouts.app>
"@

[IO.File]::WriteAllText("resources\views\client\energy-audit-master.blade.php", $blade, [Text.Encoding]::UTF8)
Write-Host "Done! Blade file length: $($blade.Length)"
