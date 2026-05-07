
# Master blade generator - clean approach
$masterHtml = [IO.File]::ReadAllText("HTMLe\1_ENESA_Formularz_HTML_Master_v1.html")

$cssStart = $masterHtml.IndexOf("<style>") + 7
$cssEnd = $masterHtml.IndexOf("</style>")
$css = $masterHtml.Substring($cssStart, $cssEnd - $cssStart)

$bodyStart = $masterHtml.IndexOf("<body>") + 6
$scriptStart = $masterHtml.IndexOf("<script>")
$bodyHtml = $masterHtml.Substring($bodyStart, $scriptStart - $bodyStart)

$jsStart = $scriptStart + 8
$jsEnd = $masterHtml.LastIndexOf("</script>")
$js = $masterHtml.Substring($jsStart, $jsEnd - $jsStart)

# Adapt CSS: sidenav fixed -> sticky, body flex -> none, body::before -> .enesa-form-body::before
# Use multiline regex
$css = [regex]::Replace($css, '(?s)\.sidenav\s*\{.*?\}', '.sidenav {
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
}')

# Remove margin-left from .main  
$css = [regex]::Replace($css, '(?s)(\.main\s*\{[^}]*)margin-left\s*:\s*[^;]+;', '$1')

# Change body::before to .enesa-form-body::before
$css = $css -replace 'body::before', '.enesa-form-body::before'

# Remove position:fixed from body display:flex rule
$css = [regex]::Replace($css, '(?s)^body\s*\{.*?\}', 'body { margin: 0; }', [System.Text.RegularExpressions.RegexOptions]::Multiline)

# Override JS to inject at the END of script
# This overrides enesaStorage.get to read from FORM_DATA first, 
# and replaces scheduleAutoSave with AJAX version

$jsOverride = @'

// === LARAVEL BLADE OVERRIDES ===
// Override enesaStorage.get to read from server FORM_DATA first
const _origGet = enesaStorage.get.bind(enesaStorage);
enesaStorage.get = function(key) {
  if (key && key.startsWith(STORAGE_PREFIX)) {
    const fieldId = key.slice(STORAGE_PREFIX.length);
    if (typeof FORM_DATA !== 'undefined' && FORM_DATA && FORM_DATA[fieldId] !== undefined && FORM_DATA[fieldId] !== null) {
      return String(FORM_DATA[fieldId]);
    }
  }
  return _origGet(key);
};

// Override scheduleAutoSave to POST to Laravel backend
const _origSchedule = scheduleAutoSave;
scheduleAutoSave = function() {
  if (saveTimer) clearTimeout(saveTimer);
  saveTimer = setTimeout(() => {
    const fields = getAllFields();
    const data = {};
    for (const f of fields) {
      const v = f.value;
      if (v !== '' && v !== null) data[f.dataset.id] = v;
      // Mirror to localStorage for cross-scope reads
      enesaStorage.set(STORAGE_PREFIX + f.dataset.id, v);
    }
    // Save meta
    data['_meta_n_wydz'] = getNWydz();
    data['_meta_n_hal'] = getNHal();
    enesaStorage.set(STORAGE_PREFIX + '_meta_n_wydz', data['_meta_n_wydz']);
    enesaStorage.set(STORAGE_PREFIX + '_meta_n_hal', data['_meta_n_hal']);
    if (typeof SAVE_URL === 'undefined' || !SAVE_URL) {
      showSaveIndicator('Brak URL zapisu');
      return;
    }
    const total = document.querySelectorAll('[data-id]').length;
    const pct = total > 0 ? Math.round(Object.keys(data).filter(k=>!k.startsWith('_')).length / total * 100) : 0;
    fetch(SAVE_URL, {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
      body: JSON.stringify({fields: data, completion_percent: pct, company_id: COMPANY_ID})
    }).then(r => r.json()).then(d => {
      showSaveIndicator('Zapisano (' + (d.saved || '?') + ' pól)');
    }).catch(err => {
      showSaveIndicator('Błąd zapisu!');
      console.error('Save error:', err);
    });
  }, 800);
};

// Prefill company/team data if empty
function setIfEmpty(fieldId, value) {
  if (!value) return;
  const el = document.querySelector('[data-id="' + fieldId + '"]');
  if (el && !el.value) {
    el.value = value;
    el.dispatchEvent(new Event('input', {bubbles: true}));
  }
}

function prefillFromCompanyData() {
  if (!COMPANY_DATA) return;
  setIfEmpty('AUD-V1-NAZWA',   COMPANY_DATA.name);
  setIfEmpty('AUD-V2-NIP',     COMPANY_DATA.nip);
  setIfEmpty('AUD-V3-REGON',   COMPANY_DATA.regon);
  setIfEmpty('AUD-V4-ADRES',   (COMPANY_DATA.address || '') + (COMPANY_DATA.city ? ', ' + COMPANY_DATA.city : ''));
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
      setIfEmpty('AUD-V13-ZES-' + n + '-IMIE', m.name || '');
      setIfEmpty('AUD-V13-ZES-' + n + '-EMAIL', m.email || '');
      setIfEmpty('AUD-V13-ZES-' + n + '-ROLA', m.role || '');
    });
  }
});
// === END LARAVEL BLADE OVERRIDES ===
'@

$companyDataPhp = @'
@php
$_companyData = null;
if (isset($company) && $company) {
    $_auditorName = '';
    $_auditorEmail = '';
    if (method_exists($company, 'assignedUsers')) {
        $_auditor = $company->assignedUsers->where('role', 'auditor')->first();
        if ($_auditor) { $_auditorName = $_auditor->name; $_auditorEmail = $_auditor->email; }
    }
    $_companyData = [
        'name'          => $company->name ?? '',
        'nip'           => $company->nip ?? '',
        'regon'         => $company->regon ?? '',
        'address'       => $company->address ?? '',
        'city'          => $company->city ?? '',
        'auditorName'   => $_auditorName,
        'auditorEmail'  => $_auditorEmail,
        'krs'           => $company->krs ?? '',
        'contactName'   => optional($company->contacts->first())->name ?? '',
        'contactEmail'  => optional($company->contacts->first())->email ?? '',
        'contactPhone'  => optional($company->contacts->first())->phone ?? '',
    ];
}
$_teamMembers = [];
if (isset($currentUser) && $currentUser) {
    $_teamMembers[] = ['name' => $currentUser->name, 'email' => $currentUser->email, 'role' => 'Audytor prowadzący'];
}
@endphp
'@

$blade = "<x-layouts.app>`n"
$blade += $companyDataPhp
$blade += "`n"
$blade += "@if(isset(`$isStaff) && `$isStaff && isset(`$company) && `$company)`n"
$blade += "<div style=""background:#1d4f73;color:#fff;padding:8px 20px;font-size:13px;display:flex;align-items:center;gap:12px;"">`n"
$blade += "  <span>&#9881; Tryb administratora: {{ `$company->name }}</span>`n"
$blade += "  <a href=""{{ route('firma.show', `$company) }}"" style=""color:#a0d4f5;margin-left:auto;"">&#8592; Wr&#243;&#263; do firmy</a>`n"
$blade += "</div>`n"
$blade += "@elseif(isset(`$isStaff) && `$isStaff)`n"
$blade += "<div style=""background:#0f2330;color:#ccc;padding:8px 20px;font-size:13px;"">Tryb podgl&#261;du &#8212; dane nie s&#261; przypisane do konkretnej firmy</div>`n"
$blade += "@endif`n`n"
$blade += "<style>`n$css`n"
$blade += ".enesa-form-body { display: flex; min-height: calc(100vh - 60px); position: relative; }`n"
$blade += ".enesa-form-body::before { content: 'POUFNE'; position: fixed; top: 50%; left: 50%; transform: translate(-50%,-50%) rotate(-45deg); font-size: 120px; font-weight: 900; color: rgba(0,0,0,0.03); pointer-events: none; z-index: 0; letter-spacing: 0.2em; }`n"
$blade += "</style>`n`n"
$blade += "<div class=""save-indicator"" id=""save-indicator"">Zapisano</div>`n`n"
$blade += "<div class=""enesa-form-body"">`n"
$blade += $bodyHtml
$blade += "</div>`n`n"
$blade += "<script>`n"
$blade += "const SAVE_URL   = '{{ isset(`$company) && `$company ? route(""client.energy-audit-master.save"") : """" }}';" + "`n"
$blade += "const CSRF       = '{{ csrf_token() }}';" + "`n"
$blade += "const FORM_DATA  = @json(`$formData ?? []);" + "`n"
$blade += "const COMPANY_ID = {{ isset(`$company) && `$company ? `$company->id : 'null' }};" + "`n"
$blade += "const PREVIEW_MODE = {{ (!empty(`$previewMode) && `$previewMode) ? 'true' : 'false' }};" + "`n"
$blade += "const COMPANY_DATA = @json(`$_companyData);" + "`n"
$blade += "const TEAM_MEMBERS = @json(`$_teamMembers);" + "`n`n"
$blade += $js
$blade += $jsOverride
$blade += "`n</script>`n"
$blade += "</x-layouts.app>`n"

[IO.File]::WriteAllText("resources\views\client\energy-audit-master.blade.php", $blade, [Text.Encoding]::UTF8)
Write-Host "Done! Blade file length: $($blade.Length)"
