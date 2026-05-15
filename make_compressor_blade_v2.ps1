
# Compressor blade generator - clean approach
$html = [IO.File]::ReadAllText("HTMLe\2_ENESA_Formularz_HTML_Kompresory_v1.html")

$cssStart = $html.IndexOf("<style>") + 7
$cssEnd = $html.IndexOf("</style>")
$css = $html.Substring($cssStart, $cssEnd - $cssStart)

$bodyStart = $html.IndexOf("<body>") + 6
$scriptStart = $html.IndexOf("<script>")
$bodyHtml = $html.Substring($bodyStart, $scriptStart - $bodyStart)

$jsStart = $scriptStart + 8
$jsEnd = $html.LastIndexOf("</script>")
$js = $html.Substring($jsStart, $jsEnd - $jsStart)

# CSS adaptations
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
$css = [regex]::Replace($css, '(?s)(\.main\s*\{[^}]*)margin-left\s*:\s*[^;]+;', '$1')
$css = $css -replace 'body::before', '.enesa-form-body::before'
$css = [regex]::Replace($css, '(?sm)^body\s*\{.*?\}', 'body { margin: 0; }')

# Rename Sprężarkownia → Kompresory in body HTML
$bodyHtml = $bodyHtml -replace 'Sprężarkownia', 'Kompresory'
$bodyHtml = $bodyHtml -replace 'sprężarkownia', 'kompresory'

# Inject save button into sidenav (before closing </nav>)
$saveBtnHtml = @'
  <div style="padding: 16px 12px 8px;">
    <button id="btn-save-now" onclick="manualSave()" style="
      width:100%; padding:10px 0; background:#2E7D5C; color:#fff;
      border:none; border-radius:6px; font-size:13px; font-weight:700;
      cursor:pointer; letter-spacing:0.03em; transition:background .2s;
    " onmouseover="this.style.background='#1A4D3A'" onmouseout="this.style.background='#2E7D5C'">
      💾 Zapisz dane
    </button>
    <div style="font-size:10px; color:rgba(255,255,255,0.5); text-align:center; margin-top:5px;">
      Autozapis co 30 sek.
    </div>
  </div>
</nav>
'@
$bodyHtml = $bodyHtml -replace '</nav>', $saveBtnHtml

# JS override - injected at END of script tag, overrides enesaStorage.get and scheduleAutoSave
$jsOverride = @'

// === LARAVEL BLADE OVERRIDES ===
// Override isMasterConnected to check MASTER_DATA (from PHP/DB) instead of localStorage
// Must use variable assignment (not function declaration) to override hoisted original
isMasterConnected = function() {
  if (typeof MASTER_DATA !== 'undefined' && MASTER_DATA) {
    return Object.keys(MASTER_DATA).length > 0;
  }
  return false;
};

// Override enesaStorage to read from FORM_DATA for own (ca:) keys and MASTER_DATA for master: keys
if (typeof enesaStorage !== 'undefined') {
  const _origGet = enesaStorage.get.bind(enesaStorage);
  enesaStorage.get = function(key) {
    if (key && key.startsWith(MASTER_PREFIX)) {
      return readMasterField(key.slice(MASTER_PREFIX.length));
    }
    if (key && key.startsWith(STORAGE_PREFIX)) {
      const fieldId = key.slice(STORAGE_PREFIX.length);
      if (typeof FORM_DATA !== 'undefined' && FORM_DATA && FORM_DATA[fieldId] !== undefined) {
        return String(FORM_DATA[fieldId]);
      }
    }
    return _origGet(key);
  };
}

// Override scheduleAutoSave to POST to Laravel backend
scheduleAutoSave = function() {
  if (typeof saveTimer !== 'undefined' && saveTimer) clearTimeout(saveTimer);
  saveTimer = setTimeout(() => {
    const data = {};
    document.querySelectorAll('[data-id]').forEach(el => {
      if (!el.dataset.masterSource && !el.closest('[data-master-source]')) {
        const v = el.value;
        if (v !== '' && v !== null) data[el.dataset.id] = v;
        try { localStorage.setItem(STORAGE_PREFIX + el.dataset.id, v); } catch {}
      }
    });
    if (typeof SAVE_URL === 'undefined' || !SAVE_URL) return;
    const total = document.querySelectorAll('[data-id]:not([data-master-source])').length;
    const pct = total > 0 ? Math.round(Object.keys(data).length / total * 100) : 0;
    fetch(SAVE_URL, {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
      body: JSON.stringify({fields: data, completion_percent: pct})
    }).then(r => r.json()).then(d => {
      if (typeof showSaveIndicator === 'function') showSaveIndicator('Zapisano (' + (d.saved || '?') + ' pól)');
    }).catch(err => {
      if (typeof showSaveIndicator === 'function') showSaveIndicator('Błąd zapisu!');
      console.error('Save error:', err);
    });
  }, 1500);
};

// Ręczny zapis (przycisk)
function manualSave() {
  const btn = document.getElementById('btn-save-now');
  if (btn) { btn.disabled = true; btn.textContent = 'Zapisywanie…'; }
  if (typeof saveTimer !== 'undefined' && saveTimer) clearTimeout(saveTimer);
  saveTimer = setTimeout(() => {
    const data = {};
    document.querySelectorAll('[data-id]').forEach(el => {
      if (!el.dataset.masterSource && !el.closest('[data-master-source]')) {
        const v = el.value;
        if (v !== '' && v !== null) data[el.dataset.id] = v;
        try { localStorage.setItem(STORAGE_PREFIX + el.dataset.id, v); } catch {}
      }
    });
    if (typeof SAVE_URL === 'undefined' || !SAVE_URL) {
      if (btn) { btn.disabled = false; btn.textContent = '💾 Zapisz dane'; }
      return;
    }
    const total = document.querySelectorAll('[data-id]:not([data-master-source])').length;
    const pct = total > 0 ? Math.round(Object.keys(data).length / total * 100) : 0;
    fetch(SAVE_URL, {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
      body: JSON.stringify({fields: data, completion_percent: pct})
    }).then(r => r.json()).then(d => {
      if (typeof showSaveIndicator === 'function') showSaveIndicator('✓ Zapisano (' + Object.keys(data).length + ' pól)');
      if (btn) { btn.disabled = false; btn.textContent = '✓ Zapisano!'; setTimeout(() => { btn.textContent = '💾 Zapisz dane'; }, 2000); }
    }).catch(err => {
      if (typeof showSaveIndicator === 'function') showSaveIndicator('⚠ Błąd zapisu!');
      if (btn) { btn.disabled = false; btn.textContent = '⚠ Błąd'; setTimeout(() => { btn.textContent = '💾 Zapisz dane'; }, 2000); }
      console.error('Save error:', err);
    });
  }, 0);
}

// Autozapis co 30 sekund
setInterval(() => scheduleAutoSave(), 30000);

// Załaduj FORM_DATA do pól DOM (loadSavedData uruchomił się przed override)
if (typeof FORM_DATA !== 'undefined' && FORM_DATA) {
  document.querySelectorAll('[data-id]').forEach(el => {
    const id = el.dataset.id;
    if (!el.dataset.masterSource && FORM_DATA[id] !== undefined && FORM_DATA[id] !== '' && !el.value) {
      el.value = String(FORM_DATA[id]);
    }
  });
}

// === END LARAVEL BLADE OVERRIDES ===
'@

$blade = "<x-layouts.app>`n"
$blade += "@if(isset(`$isStaff) && `$isStaff && isset(`$audit) && `$audit->company)`n"
$blade += "<div style=""background:#1d4f73;color:#fff;padding:8px 20px;font-size:13px;display:flex;align-items:center;gap:12px;"">`n"
$blade += "  <span>&#9881; Tryb administratora: {{ `$audit->company->name }}</span>`n"
$blade += "  <a href=""{{ route('firma.show', `$audit->company) }}"" style=""color:#a0d4f5;margin-left:auto;"">&#8592; Wr&#243;&#263; do firmy</a>`n"
$blade += "</div>`n"
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
$blade += "const STORAGE_PREFIX = 'ca:';" + "`n"
$blade += "const MASTER_PREFIX  = 'master:';" + "`n"
$blade += "const SAVE_URL  = '{{ route(""client.audit.compressor.questionnaire.ajax-save"", `$audit) }}';" + "`n"
$blade += "const CSRF      = '{{ csrf_token() }}';" + "`n"
$blade += "const MASTER_DATA = @json(`$masterFormData ?? []);" + "`n"
$blade += "const FORM_DATA   = @json(`$answers ?? []);" + "`n"
$blade += "const AUDIT_ID  = {{ `$audit->id }};" + "`n`n"
$blade += $js
$blade += $jsOverride
$blade += "`n</script>`n"
$blade += "</x-layouts.app>`n"

[IO.File]::WriteAllText("resources\views\client\compressor-questionnaire.blade.php", $blade, [Text.Encoding]::UTF8)
Write-Host "Done! Blade file length: $($blade.Length)"
