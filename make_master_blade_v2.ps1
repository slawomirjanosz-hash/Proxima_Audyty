
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

// 1. Override enesaStorage.get — serwer FORM_DATA przed localStorage
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

// 2. Zaladuj FORM_DATA do pol DOM (loadSavedData uruchomil sie przed override)
(function loadFormDataIntoDom() {
  if (typeof FORM_DATA === 'undefined' || !FORM_DATA) return;
  let loaded = 0;
  for (const [fieldId, value] of Object.entries(FORM_DATA)) {
    if (fieldId.startsWith('_') || value === null || value === undefined || String(value) === '') continue;
    const el = document.querySelector('[data-id="' + fieldId + '"]');
    if (el && !el.value) {
      el.value = String(value);
      el.classList.add('filled');
      loaded++;
    }
  }
  if (loaded > 0) updateAllProgress();
})();

// 3. Override scheduleAutoSave — zapis na serwer
const _origSchedule = scheduleAutoSave;
scheduleAutoSave = function() {
  if (saveTimer) clearTimeout(saveTimer);
  saveTimer = setTimeout(() => {
    const fields = getAllFields();
    const data = {};
    for (const f of fields) {
      const v = f.value;
      if (v !== '' && v !== null) data[f.dataset.id] = v;
      enesaStorage.set(STORAGE_PREFIX + f.dataset.id, v);
    }
    data['_meta_n_wydz'] = getNWydz();
    data['_meta_n_hal'] = getNHal();
    enesaStorage.set(STORAGE_PREFIX + '_meta_n_wydz', data['_meta_n_wydz']);
    enesaStorage.set(STORAGE_PREFIX + '_meta_n_hal', data['_meta_n_hal']);
    if (typeof SAVE_URL === 'undefined' || !SAVE_URL) { showSaveIndicator('Brak URL zapisu'); return; }
    const total = document.querySelectorAll('[data-id]').length;
    const pct = total > 0 ? Math.round(Object.keys(data).filter(k=>!k.startsWith('_')).length / total * 100) : 0;
    fetch(SAVE_URL, {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
      body: JSON.stringify({fields: data, completion_percent: pct, company_id: COMPANY_ID})
    }).then(r => r.json()).then(d => {
      showSaveIndicator('Zapisano (' + Object.keys(data).filter(k=>!k.startsWith('_')).length + ' pol)');
    }).catch(err => {
      showSaveIndicator('Blad zapisu!');
      console.error('Save error:', err);
    });
  }, 800);
};

// 4. Reczny zapis (przycisk)
function masterManualSave() {
  const btn = document.getElementById('btn-save-now');
  if (btn) { btn.disabled = true; btn.textContent = 'Zapisywanie...'; }
  scheduleAutoSave();
  setTimeout(() => {
    if (btn) { btn.disabled = false; btn.textContent = 'Zapisano!'; setTimeout(() => { btn.textContent = 'Zapisz dane'; }, 2000); }
  }, 1200);
}

// 5. Autozapis co 30 sekund
setInterval(() => scheduleAutoSave(), 30000);

// 6. Prefill danych firmy
function setIfEmpty(fieldId, value) {
  if (!value) return;
  const el = document.querySelector('[data-id="' + fieldId + '"]');
  if (el && !el.value) { el.value = value; el.dispatchEvent(new Event('input', {bubbles: true})); }
}

function prefillFromCompanyData() {
  if (!COMPANY_DATA) return;
  setIfEmpty('AUD-V1-NAZWA',   COMPANY_DATA.name);
  setIfEmpty('AUD-V2-NIP',     COMPANY_DATA.nip);
  setIfEmpty('AUD-V3-REGON',   COMPANY_DATA.regon);
  const companyAddress = (COMPANY_DATA.address || '') + (COMPANY_DATA.city ? ', ' + COMPANY_DATA.city : '');
  setIfEmpty('AUD-V4-ADRES',   companyAddress);
  setIfEmpty('ZAK-V2-LOK-ADRES', companyAddress);
  setIfEmpty('AUD-V5-KRS',     COMPANY_DATA.krs);
  setIfEmpty('AUD-V6-KONTAKT', COMPANY_DATA.contactName);
  setIfEmpty('AUD-V7-EMAIL',   COMPANY_DATA.contactEmail);
  setIfEmpty('AUD-V8-TEL',     COMPANY_DATA.contactPhone);
  setIfEmpty('AUD-V9-AUDYTOR', COMPANY_DATA.auditorName);
  setIfEmpty('AUD-V10-AUD-EMAIL', COMPANY_DATA.auditorEmail);
}

// 7. Klimat — auto-uzupelnienie
function masterClimateFieldsEmpty() {
  const zone = document.querySelector('[data-id="ZAK-V4-KLIMAT"]');
  const hdd  = document.querySelector('[data-id="ZAK-V5-HDD"]');
  const cdd  = document.querySelector('[data-id="ZAK-V6-CDD"]');
  return (!zone||!zone.value) && (!hdd||!hdd.value||hdd.value==='0') && (!cdd||!cdd.value||cdd.value==='0');
}
async function masterLocForceAutoFill() {
  const adresEl = document.querySelector('[data-id="ZAK-V2-LOK-ADRES"]');
  if (!adresEl||!adresEl.value.trim()) return;
  ['ZAK-V4-KLIMAT','ZAK-V5-HDD','ZAK-V6-CDD','ZAK-V7-ALTITUDE'].forEach(id => { const el=document.querySelector('[data-id="'+id+'"]'); if(el) el.value=''; });
  await masterLocAutoFillIfNeeded();
}
async function masterLocAutoFillIfNeeded() {
  const adresEl = document.querySelector('[data-id="ZAK-V2-LOK-ADRES"]');
  if (!adresEl||!adresEl.value.trim()||!masterClimateFieldsEmpty()) return;
  const q = adresEl.value.trim();
  const statusEl = document.getElementById('master-climate-status');
  if (statusEl) { statusEl.innerHTML = 'Szukam warunkow klimatycznych dla ' + q + '...'; statusEl.style.display='block'; }
  try {
    const url = 'https://nominatim.openstreetmap.org/search?q='+encodeURIComponent(q)+'&countrycodes=pl&addressdetails=1&format=json&limit=5';
    const resp = await fetch(url, {headers:{'Accept-Language':'pl'}});
    const results = await resp.json();
    if (!results||!results.length) { if(statusEl) statusEl.innerHTML='Nie znaleziono lokalizacji — wpisz recznie.'; return; }
    const place = results.find(r=>['city','town','village','hamlet','suburb','municipality'].includes(r.type)||['city','town','village','hamlet'].includes(r.addresstype))||results[0];
    const addr = place.address||{};
    const cityName = addr.city||addr.town||addr.village||addr.hamlet||addr.suburb||place.display_name.split(',')[0];
    masterLocSelectPlace(place.lat, place.lon, cityName, addr.state||'', place.display_name);
  } catch(e) { if(statusEl){statusEl.innerHTML='Blad polaczenia z geolokalizacja.';statusEl.style.display='block';} }
}
const MASTER_CLIMATE_DB = [
  {name:'Bialystok',lat:53.1325,lon:23.1688,alt:148,hdd:3450,cdd:155,zone:'I'},
  {name:'Bielsko-Biala',lat:49.8224,lon:19.0444,alt:355,hdd:3350,cdd:160,zone:'III'},
  {name:'Bydgoszcz',lat:53.1235,lon:18.0084,alt:64,hdd:2950,cdd:195,zone:'II'},
  {name:'Czestochowa',lat:50.8118,lon:19.1203,alt:293,hdd:3050,cdd:185,zone:'III'},
  {name:'Gdansk',lat:54.3521,lon:18.6466,alt:0,hdd:3050,cdd:130,zone:'I'},
  {name:'Gdynia',lat:54.5189,lon:18.5305,alt:5,hdd:3000,cdd:120,zone:'I'},
  {name:'Gorzow Wlkp.',lat:52.7326,lon:15.2287,alt:69,hdd:2850,cdd:195,zone:'II'},
  {name:'Jelenia Gora',lat:50.9044,lon:15.7299,alt:342,hdd:3300,cdd:170,zone:'III'},
  {name:'Kalisz',lat:51.7619,lon:18.0910,alt:104,hdd:2920,cdd:210,zone:'II'},
  {name:'Katowice',lat:50.2587,lon:19.0216,alt:285,hdd:3050,cdd:180,zone:'III'},
  {name:'Kielce',lat:50.8661,lon:20.6286,alt:295,hdd:3200,cdd:165,zone:'III'},
  {name:'Koszalin',lat:54.1942,lon:16.1722,alt:33,hdd:3000,cdd:100,zone:'I'},
  {name:'Krakow',lat:50.0647,lon:19.9450,alt:220,hdd:3180,cdd:210,zone:'III'},
  {name:'Krosno',lat:49.6897,lon:21.7712,alt:295,hdd:3300,cdd:170,zone:'III'},
  {name:'Legnica',lat:51.2070,lon:16.1551,alt:122,hdd:2700,cdd:255,zone:'II'},
  {name:'Lublin',lat:51.2465,lon:22.5684,alt:238,hdd:3130,cdd:195,zone:'II'},
  {name:'Lodz',lat:51.7592,lon:19.4560,alt:187,hdd:3020,cdd:200,zone:'II'},
  {name:'Nowy Sacz',lat:49.6245,lon:20.6947,alt:291,hdd:3350,cdd:175,zone:'III'},
  {name:'Olsztyn',lat:53.7784,lon:20.4801,alt:135,hdd:3250,cdd:140,zone:'I'},
  {name:'Opole',lat:50.6677,lon:17.9236,alt:176,hdd:2800,cdd:230,zone:'II'},
  {name:'Poznan',lat:52.4064,lon:16.9252,alt:92,hdd:2900,cdd:215,zone:'II'},
  {name:'Radom',lat:51.4027,lon:21.1471,alt:188,hdd:3050,cdd:195,zone:'II'},
  {name:'Rzeszow',lat:50.0413,lon:22.0023,alt:209,hdd:3150,cdd:200,zone:'III'},
  {name:'Suwalki',lat:54.1017,lon:22.9303,alt:184,hdd:3650,cdd:130,zone:'I'},
  {name:'Szczecin',lat:53.4285,lon:14.5528,alt:1,hdd:2850,cdd:185,zone:'I'},
  {name:'Tarnow',lat:50.0122,lon:20.9862,alt:209,hdd:3100,cdd:220,zone:'III'},
  {name:'Torun',lat:53.0138,lon:18.5981,alt:50,hdd:2950,cdd:200,zone:'II'},
  {name:'Walbrzych',lat:50.7762,lon:16.2846,alt:429,hdd:3400,cdd:130,zone:'III'},
  {name:'Warszawa',lat:52.2297,lon:21.0122,alt:113,hdd:3005,cdd:212,zone:'II'},
  {name:'Wroclaw',lat:51.1079,lon:17.0385,alt:120,hdd:2750,cdd:250,zone:'II'},
  {name:'Zakopane',lat:49.2994,lon:19.9497,alt:858,hdd:4500,cdd:15,zone:'III'},
  {name:'Zielona Gora',lat:51.9356,lon:15.5062,alt:192,hdd:2880,cdd:200,zone:'II'},
];
function masterClimateDist(a,b,c,d){const R=6371,r=Math.PI/180,dL=(c-a)*r,dO=(d-b)*r,x=Math.sin(dL/2)**2+Math.cos(a*r)*Math.cos(c*r)*Math.sin(dO/2)**2;return R*2*Math.atan2(Math.sqrt(x),Math.sqrt(1-x));}
function masterClimateNearest(lat,lon){let best=null,bd=Infinity;for(const c of MASTER_CLIMATE_DB){const d=masterClimateDist(lat,lon,c.lat,c.lon);if(d<bd){bd=d;best=c;}}return{station:best,dist:Math.round(bd)};}
let masterLocSearchTimer=null;
function masterLocDebouncedSearch(val){
  clearTimeout(masterLocSearchTimer);
  const box=document.getElementById('master-loc-suggestions');
  if(!box)return;
  if(val.trim().length<3){box.style.display='none';return;}
  box.innerHTML='<div style="padding:9px 14px;font-size:13px;">Szukam...</div>';
  box.style.display='block';
  masterLocSearchTimer=setTimeout(()=>masterLocSearchNominatim(val.trim()),400);
}
async function masterLocSearchNominatim(q){
  const box=document.getElementById('master-loc-suggestions');
  if(!box)return;
  try{
    const url='https://nominatim.openstreetmap.org/search?q='+encodeURIComponent(q)+'&countrycodes=pl&addressdetails=1&format=json&limit=8';
    const resp=await fetch(url,{headers:{'Accept-Language':'pl'}});
    const results=await resp.json();
    const places=results.filter(r=>['city','town','village','hamlet','suburb','municipality'].includes(r.type)||['city','town','village','hamlet'].includes(r.addresstype));
    if(!places.length){box.innerHTML='<div style="padding:9px 14px;">Nie znaleziono miejscowosci.</div>';return;}
    box.innerHTML=places.slice(0,6).map(r=>{
      const addr=r.address||{};
      const cn=(addr.city||addr.town||addr.village||addr.hamlet||addr.suburb||r.display_name.split(',')[0]).replace(/'/g,"\\'");
      const st=(addr.state||'').replace(/'/g,"\\'");
      const dn=r.display_name.replace(/'/g,"\\'");
      return '<div onclick="masterLocSelectPlace('+r.lat+','+r.lon+',\''+cn+'\',\''+st+'\',\''+dn+'\')"\''+
        ' style="padding:9px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #eee;" onmouseover="this.style.background=\'#eef8f0\'" onmouseout="this.style.background=\'\'">'+
        '<strong>'+cn+'</strong> <span style="color:#888;font-size:11px;">'+st+'</span></div>';
    }).join('');
    box.style.display='block';
  }catch(e){box.innerHTML='<div style="padding:9px 14px;">Blad polaczenia.</div>';}
}
function masterLocSelectPlace(lat,lon,cityName,stateName,displayName){
  const box=document.getElementById('master-loc-suggestions');
  if(box)box.style.display='none';
  const{station,dist}=masterClimateNearest(parseFloat(lat),parseFloat(lon));
  if(!station)return;
  const gpsEl=document.querySelector('[data-id="ZAK-V3-GPS"]');
  if(gpsEl&&!gpsEl.value){gpsEl.value=parseFloat(lat).toFixed(4)+'N '+parseFloat(lon).toFixed(4)+'E';gpsEl.dispatchEvent(new Event('input',{bubbles:true}));}
  const zoneEl=document.querySelector('[data-id="ZAK-V4-KLIMAT"]');
  if(zoneEl){const opt=Array.from(zoneEl.options).find(o=>o.text.startsWith(station.zone+' ')||o.value.startsWith(station.zone+' '));if(opt){zoneEl.value=opt.value||opt.text;zoneEl.dispatchEvent(new Event('change',{bubbles:true}));}}
  const hddEl=document.querySelector('[data-id="ZAK-V5-HDD"]');
  if(hddEl){hddEl.value=station.hdd;hddEl.dispatchEvent(new Event('input',{bubbles:true}));}
  const cddEl=document.querySelector('[data-id="ZAK-V6-CDD"]');
  if(cddEl){cddEl.value=station.cdd;cddEl.dispatchEvent(new Event('input',{bubbles:true}));}
  const altEl=document.querySelector('[data-id="ZAK-V7-ALTITUDE"]');
  if(altEl){altEl.value=station.alt;altEl.dispatchEvent(new Event('input',{bubbles:true}));}
  const statusEl=document.getElementById('master-climate-status');
  if(statusEl){statusEl.innerHTML='Klimat uzupelniony: stacja '+station.name+' ('+dist+' km) · Strefa '+station.zone+' · HDD='+station.hdd+' · CDD='+station.cdd;statusEl.style.display='block';}
}
document.addEventListener('click',function(e){
  const box=document.getElementById('master-loc-suggestions');
  const inp=document.getElementById('master-loc-adres-input');
  if(box&&!box.contains(e.target)&&e.target!==inp)box.style.display='none';
});

// 8. DOMContentLoaded — prefill + watcher adresu + klimat
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
  const siedzibaEl = document.querySelector('[data-id="AUD-V4-ADRES"]');
  const lokAdresEl = document.querySelector('[data-id="ZAK-V2-LOK-ADRES"]');
  if (siedzibaEl && lokAdresEl) {
    let lastSiedziba = siedzibaEl.value;
    siedzibaEl.addEventListener('input', () => {
      const newSiedziba = siedzibaEl.value;
      if (!lokAdresEl.value || lokAdresEl.value === lastSiedziba) {
        lokAdresEl.value = newSiedziba;
        lokAdresEl.dispatchEvent(new Event('input', { bubbles: true }));
      }
      lastSiedziba = newSiedziba;
    });
  }
  setTimeout(masterLocAutoFillIfNeeded, 200);
});
window.addEventListener('load', function() { setTimeout(masterLocAutoFillIfNeeded, 300); });

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
        'address'       => implode(', ', array_filter([$company->street ?? '', $company->postal_code ?? ''])),
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

# Inject save button into sidenav before </nav>
$saveBtnMaster = @'
  <div style="padding: 16px 12px 8px;">
    <button id="btn-save-now" onclick="masterManualSave()" style="
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
$bodyHtml = $bodyHtml -replace '</nav>', $saveBtnMaster

# Inject climate UI for ZAK-V2-LOK-ADRES address field
$climateInputHtml = @'
<div style="position:relative;">
  <input type="text" class="field-input" data-id="ZAK-V2-LOK-ADRES" id="master-loc-adres-input" placeholder="ul. Główna 12, 43-100 Tychy" autocomplete="off" oninput="masterLocDebouncedSearch(this.value)">
  <div id="master-loc-suggestions" style="position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid var(--paper-deep);border-radius:10px;box-shadow:0 6px 24px rgba(14,55,85,.12);z-index:300;max-height:240px;overflow-y:auto;display:none;margin-top:2px;"></div>
</div>
<div id="master-climate-status" style="display:none;font-size:11px;color:var(--green-deep);margin-top:5px;padding:5px 10px;background:var(--green-bg,#eef8f0);border-radius:6px;border:1px solid var(--green-light,#a8ddb8);line-height:1.5;"></div>
<div style="display:flex;align-items:center;gap:8px;margin-top:5px;">
  <button type="button" onclick="masterLocForceAutoFill()" style="font-size:11px;padding:4px 10px;border:1px solid var(--green-light,#a8ddb8);border-radius:6px;background:var(--green-bg,#eef8f0);color:var(--green-deep,#1a5c3a);cursor:pointer;white-space:nowrap;font-family:inherit;">
    🌡 Uzupełnij klimat
  </button>
  <div class="field-hint" style="margin:0;">Fizyczna lokalizacja zakładu — domyślnie adres siedziby. System uzupełnia klimat automatycznie.</div>
</div>
'@
$bodyHtml = $bodyHtml -replace '(?s)<input[^>]*data-id="ZAK-V2-LOK-ADRES"[^>]*>\s*<div class="field-hint">.*?</div>', $climateInputHtml

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
