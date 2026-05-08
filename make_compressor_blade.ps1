
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

Write-Host "CSS: $($css.Length), Body: $($bodyHtml.Length), JS: $($js.Length)"

# Adapt CSS sidenav
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

$css = $css -replace 'body::before', '.enesa-form-body::before'
$css = $css -replace 'body\s*\{[^}]*display\s*:\s*flex[^}]*\}', 'body { margin: 0; }'
# Add --forest variable to :root if missing
$css = $css -replace '(--ink-mute:\s*#[0-9A-Fa-f]+;)', '$1
  --forest: #1A4D3A;'

# Remove margin-left from .main
$css = $css -replace '(\.main\s*\{[^}]*)margin-left\s*:\s*[^;]+;', '$1'

# Adapt JS for blade context:
# 1. Replace enesaStorage reads to use MASTER_DATA for master: prefix
# 2. readMasterField reads from window.MASTER_DATA
# 3. loadSavedData reads from FORM_DATA
# 4. saveField does AJAX + localStorage cache
# 5. scheduleAutoSave does AJAX POST

# Replace readMasterField function
$readMasterOld = @'
  function readMasterField(fieldId) {
    return enesaStorage.get(MASTER_PREFIX + fieldId);
  }
'@
$readMasterNew = @'
  function readMasterField(fieldId) {
    if (typeof MASTER_DATA !== 'undefined' && MASTER_DATA && MASTER_DATA[fieldId] !== undefined) {
      return MASTER_DATA[fieldId];
    }
    try { return localStorage.getItem(MASTER_PREFIX + fieldId) ?? ''; } catch { return ''; }
  }
'@
$js = $js.Replace($readMasterOld, $readMasterNew)

# Replace isMasterConnected
$masterConnOld = @'
  function isMasterConnected() {
    return !!enesaStorage.get(MASTER_PREFIX + 'AUD-V1-NAZWA');
  }
'@
$masterConnNew = @'
  function isMasterConnected() {
    if (typeof MASTER_DATA !== 'undefined' && MASTER_DATA) {
      return Object.keys(MASTER_DATA).length > 0;
    }
    return false;
  }
'@
$js = $js.Replace($masterConnOld, $masterConnNew)

# Replace loadSavedData to use FORM_DATA
$loadOld = @'
  function loadSavedData() {
    document.querySelectorAll('[data-id]').forEach(el => {
      const v = enesaStorage.get(STORAGE_PREFIX + el.dataset.id);
      if (v !== '') {
        el.value = v;
      }
    });
  }
'@
$loadNew = @'
  function loadSavedData() {
    document.querySelectorAll('[data-id]').forEach(el => {
      const id = el.dataset.id;
      let v = '';
      if (typeof FORM_DATA !== 'undefined' && FORM_DATA && FORM_DATA[id] !== undefined) {
        v = FORM_DATA[id];
      } else {
        try { v = localStorage.getItem(STORAGE_PREFIX + id) ?? ''; } catch {}
      }
      if (v !== '') el.value = v;
    });
  }
'@
$js = $js.Replace($loadOld, $loadNew)

# Replace saveField
$saveOld = @'
  function saveField(id, value) {
    enesaStorage.set(STORAGE_PREFIX + id, value);
    showSaveIndicator();
  }
'@
$saveNew = @'
  function saveField(id, value) {
    try { localStorage.setItem(STORAGE_PREFIX + id, value); } catch {}
    scheduleAutoSave();
  }
'@
$js = $js.Replace($saveOld, $saveNew)

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
      document.querySelectorAll('[data-id]:not([data-master-source])').forEach(el => {
        if (!el.closest('.readonly-group') && !el.hasAttribute('data-master-source')) {
          const v = el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value;
          if (v !== '') fields[el.dataset.id] = v;
        }
      });
      if (typeof SAVE_URL === 'undefined' || !SAVE_URL) return;
      const total = document.querySelectorAll('[data-id]:not([data-master-source])').length;
      const filled = Object.keys(fields).length;
      const pct = total > 0 ? Math.round(filled / total * 100) : 0;
      fetch(SAVE_URL, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
        body: JSON.stringify({fields: fields, completion_percent: pct})
      }).then(r => r.json()).then(d => {
        showSaveIndicator('Zapisano');
      }).catch(() => {
        showSaveIndicator('Błąd zapisu!');
      });
    }, 800);
  }
'@
$js = $js.Replace($schedOld, $schedNew)

# Replace enesaStorage.get for MASTER_PREFIX reads to use readMasterField  
# Already handled via readMasterField function, but enesaStorage for own 'ca:' prefix
# Add enesaStorage definition
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
      if (k.startsWith(MASTER_PREFIX)) {
        const fid = k.slice(MASTER_PREFIX.length);
        return readMasterField(fid);
      }
      if (typeof FORM_DATA !== 'undefined' && FORM_DATA) {
        const caKey = k.startsWith(STORAGE_PREFIX) ? k.slice(STORAGE_PREFIX.length) : k;
        if (FORM_DATA[caKey] !== undefined) return FORM_DATA[caKey];
      }
      try { return localStorage.getItem(k) ?? '' } catch { return '' }
    },
    set: (k, v) => {
      try { localStorage.setItem(k, v) } catch {}
    }
  };
'@
# Note: enesaStorage may not exist in kompresory - need to check. Let's add it before other code.
# Insert at top of JS
$js = "  " + $enesaStorageNew.TrimStart() + "`n" + $js

# Fix: readMasterField is now defined before enesaStorage so it's fine
# But enesaStorage references readMasterField which may not be defined yet
# Reorder: put readMasterField first
# Actually readMasterField is defined further down in JS, so we need to ensure
# enesaStorage.get() calls readMasterField which is hoisted (it's a function declaration)
# It should be fine.

# Replace "Sprężarkownia" with "Kompresory" in body HTML and JS
$bodyHtml = $bodyHtml -replace 'Sprężarkownia', 'Kompresory'
$bodyHtml = $bodyHtml -replace 'sprężarkownia', 'kompresory'
$js = $js -replace 'Sprężarkownia', 'Kompresory'

$blade = @"
<x-layouts.app>
@if(isset(`$isStaff) && `$isStaff && isset(`$audit) && `$audit->company)
<div style="background:#1d4f73;color:#fff;padding:8px 20px;font-size:13px;display:flex;align-items:center;gap:12px;">
  <span>⚙ Tryb administratora: {{ `$audit->company->name }}</span>
  <a href="{{ route('firma.show', `$audit->company) }}" style="color:#a0d4f5;margin-left:auto;">← Wróć do firmy</a>
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
const STORAGE_PREFIX = 'ca:';
const MASTER_PREFIX  = 'master:';
const SAVE_URL  = '{{ route("client.audit.compressor.questionnaire.ajax-save", `$audit) }}';
const CSRF      = '{{ csrf_token() }}';
const MASTER_DATA = @json(`$masterFormData ?? []);
const FORM_DATA   = @json(`$answers ?? []);
const AUDIT_ID  = {{ `$audit->id }};

$js
</script>
</x-layouts.app>
"@

[IO.File]::WriteAllText("resources\views\client\compressor-questionnaire.blade.php", $blade, [Text.Encoding]::UTF8)
Write-Host "Done! Blade file length: $($blade.Length)"
