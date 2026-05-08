<x-layouts.app>
@if(isset($isStaff) && $isStaff && isset($audit) && $audit->company)
<div style="background:#1d4f73;color:#fff;padding:8px 20px;font-size:13px;display:flex;align-items:center;gap:12px;">
  <span>âš™ Tryb administratora: {{ $audit->company->name }}</span>
  <a href="{{ route('firma.show', $audit->company) }}" style="color:#a0d4f5;margin-left:auto;">â† WrĂłÄ‡ do firmy</a>
</div>
@endif

<style>

/* === ENESA palette - identyczna jak w Master HTML === */
:root {
  --green-deep: #1A4D3A;
  --green-primary: #2E7D5C;
  --green-light: #A4C2A8;
  --green-bg: #E7EEE5;
  --paper: #F5EFE0;
  --paper-deep: #EBE3D0;
  --paper-paper: #FAF5E8;
  --gold: #A87F2A;
  --rose: #B8485A;
  --rose-light: #FFE0E0;
  --ok: #2E7D5C;
  --ok-light: #A8D5BA;
  --warning: #FFD580;
  --readonly: #FFE9C7;
  --readonly-deep: #F5D88E;
  --ink: #1A1612;
  --ink-soft: #3D352C;
  --ink-mute: #76695A;
  --forest: #1A4D3A;
  --serif: 'Fraunces', Georgia, 'Times New Roman', serif;
  --sans: 'Manrope', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  --mono: 'JetBrains Mono', 'Consolas', 'Monaco', monospace;
}

@import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600;700&family=Manrope:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; }
body { margin: 0; }

.enesa-form-body::before {
  content: 'POUFNE';
  position: fixed;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%) rotate(-30deg);
  font-family: var(--serif);
  font-size: 220px;
  font-weight: 700;
  color: var(--paper-deep);
  opacity: 0.35;
  pointer-events: none;
  z-index: 0;
  white-space: nowrap;
  user-select: none;
}

.serif { font-family: var(--serif); }
.mono { font-family: var(--mono); }

/* === Sidenav 220px === */
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
.sidenav-brand {
  padding: 0 20px 20px;
  border-bottom: 1px solid rgba(255,255,255,0.1);
  margin-bottom: 16px;
}
.sidenav-logo {
  font-family: var(--serif);
  font-size: 22px;
  font-weight: 700;
  color: var(--paper);
  letter-spacing: 0.5px;
}
.sidenav-sub {
  font-size: 11px;
  color: var(--green-light);
  margin-top: 4px;
  font-style: italic;
}
.sidenav-scope-badge {
  display: inline-block;
  margin-top: 6px;
  padding: 2px 8px;
  background: var(--gold);
  color: white;
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.5px;
  border-radius: 3px;
  font-family: var(--mono);
  text-transform: uppercase;
}
.sidenav-list { list-style: none; }
.sidenav-item {
  padding: 9px 18px 9px 20px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
  color: var(--paper);
  border-left: 3px solid transparent;
  transition: all 0.15s ease;
}
.sidenav-item:hover {
  background: rgba(255,255,255,0.08);
  border-left-color: var(--gold);
}
.sidenav-item.active {
  background: rgba(255,255,255,0.12);
  border-left-color: var(--gold);
  font-weight: 600;
}
.sidenav-num {
  font-size: 11px;
  background: rgba(255,255,255,0.15);
  padding: 2px 6px;
  border-radius: 3px;
  min-width: 28px;
  text-align: center;
  font-family: var(--mono);
  flex-shrink: 0;
}
.sidenav-name { flex: 1; font-size: 12.5px; }
.sidenav-count {
  font-size: 10.5px;
  color: var(--green-light);
  font-family: var(--mono);
  flex-shrink: 0;
}

/* Master integration status */
.master-status {
  margin: 16px 16px 8px;
  padding: 10px 12px;
  background: rgba(255,255,255,0.08);
  border-left: 3px solid var(--gold);
  border-radius: 0 4px 4px 0;
  font-size: 11px;
  line-height: 1.4;
}
.master-status-label {
  color: var(--green-light);
  font-size: 10px;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  margin-bottom: 3px;
  font-weight: 600;
}
.master-status-value {
  color: var(--paper);
  font-family: var(--mono);
  font-size: 11px;
}
.master-status-value.connected { color: var(--ok-light); }
.master-status-value.disconnected { color: var(--readonly-deep); }

/* === Main content === */
.main {
  flex: 1;
  
  padding: 0;
  position: relative;
  z-index: 1;
}

.header {
  background: var(--paper-paper);
  padding: 32px 48px 24px;
  border-bottom: 1px solid var(--paper-deep);
  position: sticky; top: 0; z-index: 5;
  backdrop-filter: blur(8px);
  background: rgba(245, 239, 224, 0.92);
}
.header-eyebrow {
  font-size: 11px;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--gold);
  font-weight: 600;
  margin-bottom: 6px;
}
.header-title {
  font-family: var(--serif);
  font-size: 28px;
  font-weight: 600;
  color: var(--green-deep);
  letter-spacing: -0.3px;
  line-height: 1.2;
}
.header-sub {
  font-size: 13px;
  color: var(--ink-soft);
  margin-top: 6px;
  font-style: italic;
}
.header-meta {
  display: flex;
  gap: 24px;
  align-items: center;
  margin-top: 14px;
  padding-top: 14px;
  border-top: 1px dashed var(--paper-deep);
  flex-wrap: wrap;
}
.header-meta-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.header-meta-label {
  font-size: 10.5px;
  text-transform: uppercase;
  color: var(--ink-mute);
  letter-spacing: 1px;
}
.header-meta-val {
  font-size: 13px;
  color: var(--green-deep);
  font-weight: 600;
}

/* === Section === */
.section {
  padding: 32px 48px 48px;
  scroll-margin-top: 130px;
}
.section + .section {
  border-top: 1px dashed var(--paper-deep);
}

.section-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 24px;
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 2px solid var(--green-deep);
}
.section-eyebrow {
  font-size: 11px;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--gold);
  font-weight: 600;
  margin-bottom: 6px;
}
.section-title {
  font-family: var(--serif);
  font-size: 24px;
  font-weight: 600;
  color: var(--green-deep);
  letter-spacing: -0.2px;
  line-height: 1.2;
}
.section-desc {
  font-size: 13px;
  color: var(--ink-soft);
  margin-top: 6px;
  max-width: 720px;
}
.section-meta { text-align: right; flex-shrink: 0; }
.section-progress {
  font-family: var(--mono);
  font-size: 13px;
  font-weight: 600;
  color: var(--green-primary);
  background: var(--green-bg);
  padding: 6px 12px;
  border-radius: 4px;
}

.section-body { padding: 4px 0; }

/* === Group / sub-section === */
.group {
  margin-top: 28px;
  background: var(--paper-paper);
  border: 1px solid var(--paper-deep);
  border-radius: 6px;
  padding: 20px 24px;
}
.group-title {
  font-family: var(--serif);
  font-size: 16px;
  font-weight: 600;
  color: var(--green-deep);
  margin-bottom: 4px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.group-title::before {
  content: '▼';
  font-size: 10px;
  color: var(--gold);
}
.group-desc {
  font-size: 12px;
  color: var(--ink-mute);
  font-style: italic;
  margin-bottom: 16px;
}
.group-info {
  background: var(--green-bg);
  border-left: 3px solid var(--green-primary);
  padding: 12px 16px;
  margin: 16px 0;
  font-size: 12.5px;
  color: var(--ink-soft);
  border-radius: 0 4px 4px 0;
}
.group-info ul { margin-left: 20px; margin-top: 6px; }
.group-info li { margin-bottom: 4px; }
.group-info strong { color: var(--green-deep); }

/* === Group header — scope read-only marker === */
.group.readonly-group {
  background: linear-gradient(to right, rgba(255, 233, 199, 0.4), var(--paper-paper));
  border-color: var(--readonly-deep);
}
.group.readonly-group .group-title::before { content: '🔒'; font-size: 12px; }

/* === Field row === */
.field {
  display: grid;
  grid-template-columns: 240px 1fr 280px 70px;
  gap: 16px;
  align-items: start;
  padding: 12px 0;
  border-bottom: 1px dashed var(--paper-deep);
}
.field:last-child { border-bottom: none; }

.field-label {
  display: flex;
  flex-direction: column;
  gap: 3px;
}
.field-q {
  font-size: 13px;
  color: var(--ink);
  font-weight: 500;
  line-height: 1.3;
}
.field-id {
  font-size: 10px;
  color: var(--ink-mute);
  font-family: var(--mono);
}
.field-input-wrap {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.field-input,
.field-select,
.field-textarea {
  font-family: var(--sans);
  font-size: 13px;
  padding: 9px 12px;
  border: 1px solid var(--paper-deep);
  border-radius: 4px;
  background: white;
  color: var(--ink);
  width: 100%;
  transition: all 0.15s ease;
}
.field-input:focus,
.field-select:focus,
.field-textarea:focus {
  outline: none;
  border-color: var(--green-primary);
  box-shadow: 0 0 0 3px rgba(46, 125, 92, 0.12);
}
.field-input.filled,
.field-select.filled,
.field-textarea.filled {
  background: var(--green-bg);
  border-color: var(--green-primary);
}
.field-textarea { min-height: 80px; resize: vertical; line-height: 1.4; }

/* === READ-ONLY z Master (pomarańczowe) === */
.field-readonly {
  background: var(--readonly) !important;
  border-color: var(--readonly-deep) !important;
  color: var(--ink-soft) !important;
  cursor: not-allowed;
  font-family: var(--mono);
  font-size: 12px;
}
.field-readonly:focus { box-shadow: 0 0 0 3px rgba(168, 127, 42, 0.15) !important; }
.readonly-marker {
  display: inline-block;
  font-size: 10px;
  color: var(--gold);
  font-weight: 600;
  letter-spacing: 0.5px;
  margin-bottom: 3px;
  padding: 1px 6px;
  background: rgba(168, 127, 42, 0.12);
  border-radius: 2px;
  font-family: var(--mono);
  text-transform: uppercase;
}

.field-hint {
  font-size: 11.5px;
  color: var(--ink-mute);
  font-style: italic;
  line-height: 1.4;
}
.field-unit {
  font-size: 11.5px;
  color: var(--ink-mute);
  font-family: var(--mono);
  text-align: right;
  padding-top: 11px;
}

/* === Tag (KTO) === */
.tag {
  display: inline-block;
  padding: 2px 7px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.4px;
  border-radius: 3px;
  font-family: var(--mono);
  vertical-align: middle;
}
.tag.kon { background: var(--green-deep); color: var(--paper); }
.tag.em { background: var(--gold); color: var(--paper); }
.tag.ur { background: var(--ink-soft); color: var(--paper); }
.tag.top { background: var(--rose); color: white; }
.tag.spec { background: var(--green-primary); color: white; }
.tag.kier { background: var(--ink-mute); color: white; }
.tag.auto { background: var(--paper-deep); color: var(--ink-soft); }
.tag.small { font-size: 9px; padding: 1px 5px; margin-right: 3px; }

.kto-cell {
  display: flex;
  flex-wrap: wrap;
  gap: 3px;
  align-items: center;
  justify-content: flex-start;
  padding-top: 11px;
}

/* === Devices table (transponowana) === */
.devices-wrap {
  overflow-x: auto;
  margin-top: 20px;
  border: 1px solid var(--paper-deep);
  border-radius: 6px;
  background: white;
}
.devices-table {
  border-collapse: collapse;
  min-width: 100%;
  font-size: 12.5px;
}
.devices-table th,
.devices-table td {
  padding: 8px 10px;
  border: 1px solid var(--paper-deep);
  text-align: left;
  vertical-align: middle;
}
.devices-table thead th {
  background: var(--green-deep);
  color: var(--paper);
  font-weight: 600;
  text-align: center;
  font-size: 11.5px;
  letter-spacing: 0.3px;
  position: sticky;
  top: 0;
  z-index: 2;
}
.devices-table .th-question {
  background: var(--green-deep);
  text-align: left;
  min-width: 240px;
  position: sticky;
  left: 0;
  z-index: 3;
}
.devices-table .th-comp {
  min-width: 70px;
  text-align: center;
}
.devices-table .th-instance {
  min-width: 130px;
  text-align: center;
  font-size: 11px;
}
.devices-table .td-question {
  background: var(--paper-paper);
  position: sticky;
  left: 0;
  z-index: 1;
  border-right: 2px solid var(--paper-deep);
}
.devices-table .td-question .q-label {
  font-size: 12.5px;
  color: var(--ink);
  font-weight: 500;
  margin-bottom: 2px;
}
.devices-table .td-question .q-id {
  font-size: 9.5px;
  color: var(--ink-mute);
  font-family: var(--mono);
}
.devices-table .td-question .q-hint {
  font-size: 10.5px;
  color: var(--ink-mute);
  font-style: italic;
  margin-top: 3px;
  line-height: 1.3;
}
.devices-table .td-comp {
  text-align: center;
  background: var(--paper-paper);
}
.devices-table .td-input {
  text-align: center;
  padding: 4px;
}
.cell-input {
  width: 100%;
  padding: 6px 8px;
  font-size: 12px;
  font-family: var(--sans);
  border: 1px solid var(--paper-deep);
  border-radius: 3px;
  background: white;
  text-align: center;
  transition: all 0.15s ease;
}
.cell-input:focus {
  outline: none;
  border-color: var(--green-primary);
  background: var(--green-bg);
}
.cell-input.filled {
  background: var(--green-bg);
  border-color: var(--green-primary);
}
.cell-input.readonly {
  background: var(--readonly) !important;
  border-color: var(--readonly-deep) !important;
  color: var(--ink-soft);
  font-family: var(--mono);
  font-size: 11px;
  cursor: not-allowed;
}

.devices-table tr.row-section-header td {
  background: var(--paper-deep);
  font-weight: 600;
  color: var(--green-deep);
  text-align: left;
  padding: 10px 12px;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  position: sticky;
  left: 0;
}
.devices-table tr.row-readonly-section td {
  background: linear-gradient(to right, rgba(255, 233, 199, 0.6), var(--paper-deep));
  color: var(--gold);
}

.row-add {
  margin-top: 12px;
  padding: 10px 16px;
  background: white;
  color: var(--green-primary);
  border: 1px dashed var(--green-primary);
  border-radius: 4px;
  font-family: var(--sans);
  font-size: 12px;
  cursor: pointer;
  transition: all 0.15s ease;
  font-weight: 500;
}
.row-add:hover {
  background: var(--green-bg);
  border-style: solid;
}

/* === Status indicator === */
.status-ok { color: var(--ok); font-weight: 600; }
.status-error { color: var(--rose); font-weight: 600; }
.status-warning { color: var(--gold); font-weight: 600; }

.save-indicator {
  position: fixed;
  top: 16px;
  right: 24px;
  background: var(--green-deep);
  color: var(--paper);
  padding: 8px 14px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  opacity: 0;
  transform: translateY(-10px);
  transition: all 0.3s ease;
  z-index: 100;
  pointer-events: none;
}
.save-indicator.show {
  opacity: 1;
  transform: translateY(0);
}
.save-indicator::before {
  content: '✓ ';
  color: var(--ok-light);
  font-weight: 700;
}

/* === Flag KPI (czerwone flagi) === */
.flag-row {
  display: grid;
  grid-template-columns: 80px 1fr 240px 100px;
  gap: 12px;
  padding: 8px 14px;
  margin: 5px 0;
  border-radius: 4px;
  background: white;
  border: 1px solid var(--paper-deep);
  align-items: center;
  font-size: 12px;
}
.flag-row.flag-on {
  background: var(--rose-light);
  border-color: var(--rose);
  font-weight: 500;
}
.flag-row.flag-off {
  background: var(--green-bg);
  border-color: var(--ok-light);
}
.flag-id { font-family: var(--mono); font-size: 10px; color: var(--ink-mute); }
.flag-name { color: var(--ink); }
.flag-rule { font-size: 11px; color: var(--ink-mute); font-style: italic; }
.flag-status { font-weight: 700; text-align: center; }
.flag-status.on { color: var(--rose); }
.flag-status.off { color: var(--ok); }

/* === Responsive === */
@media (max-width: 1024px) {
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
  .sidenav-brand .sidenav-logo { font-size: 16px; }
  .sidenav-brand .sidenav-sub { display: none; }
  .sidenav-name, .sidenav-count { display: none; }
  .sidenav-item { justify-content: center; padding: 12px; }
  .master-status { display: none; }
  .main {  }
  .header, .section { padding-left: 24px; padding-right: 24px; }
  .field { grid-template-columns: 1fr; gap: 6px; }
  .field-unit { text-align: left; padding-top: 0; }
}

/* === E2b ALOKACJA — status badges i banner === */
.alokacja-banner {
  padding: 10px 14px;
  border-radius: 6px;
  font-size: 13px;
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}
.alokacja-banner.status-ok {
  background: var(--green-bg);
  border-left: 3px solid var(--green-primary);
  color: var(--green-deep);
}
.alokacja-banner.status-warn {
  background: var(--warning-bg, #fef6e7);
  border-left: 3px solid var(--gold);
  color: var(--gold);
}
.alokacja-banner.status-error {
  background: var(--rose-light, #FFE0E0);
  border-left: 3px solid var(--rose);
  color: var(--rose);
}
.alok-status-badge {
  display: inline-block;
  padding: 3px 8px;
  font-size: 11px;
  font-weight: 600;
  border-radius: 3px;
  font-family: var(--mono);
  text-align: center;
  min-width: 60px;
}
.alok-status-badge.ok    { background: var(--ok-light); color: var(--green-deep); }
.alok-status-badge.warn  { background: var(--warning); color: var(--ink); }
.alok-status-badge.error { background: var(--rose);    color: var(--paper); }
.alok-status-badge.empty { background: var(--paper-deep); color: var(--ink-mute); }

.alok-cell-input {
  text-align: center !important;
  font-family: var(--mono) !important;
  font-size: 12px !important;
}
.alok-cell-input.over   { background: var(--rose-light) !important; border-color: var(--rose) !important; }
.alok-cell-sum {
  font-weight: 700;
  text-align: center;
  font-family: var(--mono);
  font-size: 12px;
  padding: 8px 6px;
}
.alok-cell-sum.ok    { color: var(--green-deep); background: var(--green-bg); }
.alok-cell-sum.warn  { color: var(--gold);       background: var(--warning); }
.alok-cell-sum.error { color: var(--rose);       background: var(--rose-light); }


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


<div class="save-indicator" id="save-indicator">Zapisano lokalnie</div>

<!-- ====== SIDENAV ====== -->
<nav class="sidenav">
  <div class="sidenav-brand">
    <div class="sidenav-logo serif">ENESA</div>
    <div class="sidenav-sub">CA Form · v1.0</div>
    <div class="sidenav-scope-badge">SCOPE 3 · SPRĘŻONE POWIETRZE</div>
  </div>
  
  <div class="master-status">
    <div class="master-status-label">Master Form</div>
    <div class="master-status-value" id="master-status-text">Sprawdzanie...</div>
  </div>
  
  <ul class="sidenav-list">
    <li class="sidenav-item active" data-target="etap-0"><span class="sidenav-num mono">E0</span><span class="sidenav-name">Zespół CA</span><span class="sidenav-count mono" data-count-for="etap-0">0/0</span></li>
    <li class="sidenav-item" data-target="etap-1"><span class="sidenav-num mono">E1</span><span class="sidenav-name">Kontekst</span><span class="sidenav-count mono" data-count-for="etap-1">0/1</span></li>
    <li class="sidenav-item" data-target="etap-2"><span class="sidenav-num mono">E2</span><span class="sidenav-name">Sprężarki</span><span class="sidenav-count mono" data-count-for="etap-2">0/0</span></li>
    <li class="sidenav-item" data-target="etap-2b"><span class="sidenav-num mono">E2b</span><span class="sidenav-name">Alokacja %</span><span class="sidenav-count mono" data-count-for="etap-2b">0/0</span></li>
    <li class="sidenav-item" data-target="etap-3"><span class="sidenav-num mono">E3</span><span class="sidenav-name">System</span><span class="sidenav-count mono" data-count-for="etap-3">0/7</span></li>
    <li class="sidenav-item" data-target="etap-3-5"><span class="sidenav-num mono">E3.5</span><span class="sidenav-name">Zasilanie</span><span class="sidenav-count mono" data-count-for="etap-3-5">0/10</span></li>
    <li class="sidenav-item" data-target="etap-4"><span class="sidenav-num mono">E4</span><span class="sidenav-name">Treatment</span><span class="sidenav-count mono" data-count-for="etap-4">0/9</span></li>
    <li class="sidenav-item" data-target="etap-5"><span class="sidenav-num mono">E5</span><span class="sidenav-name">Sieć</span><span class="sidenav-count mono" data-count-for="etap-5">0/12</span></li>
    <li class="sidenav-item" data-target="etap-6"><span class="sidenav-num mono">E6</span><span class="sidenav-name">Odbiorcy</span><span class="sidenav-count mono" data-count-for="etap-6">0/12</span></li>
    <li class="sidenav-item" data-target="etap-7"><span class="sidenav-num mono">E7</span><span class="sidenav-name">Eksploatacja</span><span class="sidenav-count mono" data-count-for="etap-7">0/6</span></li>
    <li class="sidenav-item" data-target="etap-8"><span class="sidenav-num mono">E8</span><span class="sidenav-name">KPI · Flagi</span><span class="sidenav-count mono" data-count-for="etap-8" id="sidenav-flag-count">0/14</span></li>
  </ul>
</nav>

<!-- ====== MAIN CONTENT ====== -->
<main class="main">

  <!-- ====== HEADER ====== -->
  <header class="header">
    <div class="header-eyebrow">FORMULARZ AUDYT SCOPE 3 · SPRĘŻONE POWIETRZE (CA) · v1.0</div>
    <h1 class="header-title serif">Audyt sprężonego powietrza — scope formularz</h1>
    <div class="header-sub">ISO 11011:2013 · PN-EN 16247-1/2/3 · ISO 50002 · Czyta dane globalne z Master Form</div>
    <div class="header-meta">
      <div class="header-meta-item">
        <div class="header-meta-label">Norma</div>
        <div class="header-meta-val">ISO 11011:2013 · PN-EN 16247</div>
      </div>
      <div class="header-meta-item">
        <div class="header-meta-label">Architektura</div>
        <div class="header-meta-val">Master + Scope (czyta z Master)</div>
      </div>
      <div class="header-meta-item">
        <div class="header-meta-label">Wypełnia</div>
        <div class="header-meta-val">KIER UR + specjalista CA / pneumatyk</div>
      </div>
      <div class="header-meta-item">
        <div class="header-meta-label">Zakład</div>
        <div class="header-meta-val" id="header-zaklad-name">[czyta z Master.E0]</div>
      </div>
      <div class="header-meta-item">
        <div class="header-meta-label">Status</div>
        <div class="header-meta-val" id="overall-progress">Wczytywanie...</div>
      </div>
    </div>
  </header>

  <!-- ============================================================ -->
  <!-- ETAP 0 · ZESPÓŁ AUDYTOWY CA -->
  <!-- ============================================================ -->
  <section class="section" id="etap-0">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 0</div>
        <h2 class="section-title serif">Zespół audytowy CA</h2>
        <p class="section-desc">Lokalny zespół scope CA — KIER UR + specjalista pneumatyk delegowani z Master.E0 · 5 osób × 8 atrybutów + 1 RO (nazwa zakładu) · czas: 3-5 min</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-0">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Zespół audytowy CA — czyta z Master.E0:</strong>
        <ul>
          <li><strong>Główny respondent</strong> dla CA = zwykle KIER UR lub specjalista pneumatyk / mechanik sprężarek</li>
          <li>Pozostali = specjaliści delegowani: technolog produkcji, automatyk BMS, energetyk</li>
          <li>Skład zespołu wpisuje się <strong>raz w Master.E0</strong> (AUD-V13-*) i jest wspólny dla wszystkich scope (LH, AHU, CA)</li>
          <li>Aby zmienić — popraw w Master Form, dane odświeżą się tutaj automatycznie (co 5s)</li>
        </ul>
      </div>

      <!-- Pole pojedyncze: nazwa zakładu (atrybut audytu, nie osoby) -->
      <div class="field-list" style="margin-bottom: 24px;">
        <div class="field">
          <div class="field-head">
            <span class="field-id mono">CA-REQ-00-ZAKLAD</span>
            <span class="field-label">Nazwa i lokalizacja zakładu</span>
            <span class="tag em">wszyscy</span>
          </div>
          <span class="readonly-marker">🔒 RO z Master.E0</span>
          <input type="text" class="field-input field-readonly" data-id="CA-REQ-00-ZAKLAD" data-master-source="AUD-V1-NAZWA" readonly placeholder="[Master.E0-AUD-V1-NAZWA]"/>
          <span class="field-hint">🔒 Z Master.E0 — nazwa zakładu (AUD-V1-NAZWA). Aby zmienić — popraw w Master.</span>
        </div>
      </div>

      <!-- Tabela transponowana zespołu audytowego — CAŁA RO z Master.AUD-V13 -->
      <div class="devices-wrap">
        <div style="background: var(--green-glow, #e8f0e3); border-left: 3px solid var(--green-primary); padding: 10px 14px; margin: 12px 0; border-radius: 0 4px 4px 0; font-size: 12px; line-height: 1.5; color: var(--ink-soft);">
          <strong style="color: var(--green-deep)">🔒 Cała tabela zespołu audytowego — read-only z Master.E0</strong><br>
          Skład zespołu (AUD-V13-*-Un) wpisuje się raz w Master.E0 i jest wspólny dla wszystkich scope (LH, AHU, CA). Aby zmienić — popraw w Master.
        </div>
        <table class="devices-table" id="team-table">
          <thead>
            <tr>
              <th class="th-question">ATRYBUT</th>
              <th class="th-comp">KTO</th>
              <th class="th-instance" style="background: var(--gold); color: var(--paper);">UCZ-1 (główny)</th>
              <th class="th-instance">UCZ-2</th>
              <th class="th-instance">UCZ-3</th>
              <th class="th-instance">UCZ-4</th>
              <th class="th-instance">UCZ-5</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="td-question">
                <div class="q-label">Imię i nazwisko</div>
                <div class="q-id mono">CA-REQ-00-IMIE</div>
                <div class="q-hint">Pełne imię i nazwisko</div>
              </td>
              <td class="td-comp"><span class="tag kon small">KON</span></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-IMIE-U1" data-master-source="AUD-V13-IMIE-U1" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-IMIE-U2" data-master-source="AUD-V13-IMIE-U2" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-IMIE-U3" data-master-source="AUD-V13-IMIE-U3" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-IMIE-U4" data-master-source="AUD-V13-IMIE-U4" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-IMIE-U5" data-master-source="AUD-V13-IMIE-U5" class="cell-input readonly" readonly placeholder="[Master]"/></td>
            </tr>
            <tr>
              <td class="td-question">
                <div class="q-label">Stanowisko</div>
                <div class="q-id mono">CA-REQ-00-STAN</div>
                <div class="q-hint">np. Kierownik UR / Energy Manager / Specjalista pneumatyk</div>
              </td>
              <td class="td-comp"><span class="tag kon small">KON</span></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-STAN-U1" data-master-source="AUD-V13-STAN-U1" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-STAN-U2" data-master-source="AUD-V13-STAN-U2" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-STAN-U3" data-master-source="AUD-V13-STAN-U3" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-STAN-U4" data-master-source="AUD-V13-STAN-U4" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-STAN-U5" data-master-source="AUD-V13-STAN-U5" class="cell-input readonly" readonly placeholder="[Master]"/></td>
            </tr>
            <tr>
              <td class="td-question">
                <div class="q-label">Dział / komórka organizacyjna</div>
                <div class="q-id mono">CA-REQ-00-DZIAL</div>
                <div class="q-hint">Z którego działu firmy klienta</div>
              </td>
              <td class="td-comp"><span class="tag kon small">KON</span></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-DZIAL-U1" data-master-source="AUD-V13-DZIAL-U1" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-DZIAL-U2" data-master-source="AUD-V13-DZIAL-U2" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-DZIAL-U3" data-master-source="AUD-V13-DZIAL-U3" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-DZIAL-U4" data-master-source="AUD-V13-DZIAL-U4" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-DZIAL-U5" data-master-source="AUD-V13-DZIAL-U5" class="cell-input readonly" readonly placeholder="[Master]"/></td>
            </tr>
            <tr class="row-critical">
              <td class="td-question">
                <div class="q-label">▶ Rola w audycie CA</div>
                <div class="q-id mono">CA-REQ-00-ROLA</div>
                <div class="q-hint">Określa do kogo Audytor AI eskaluje pytania (UR/EM/KON/SPEC/KIER/INNE)</div>
              </td>
              <td class="td-comp"><span class="tag kon small">KON</span></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-ROLA-U1" data-master-source="AUD-V13-ROLA-U1" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-ROLA-U2" data-master-source="AUD-V13-ROLA-U2" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-ROLA-U3" data-master-source="AUD-V13-ROLA-U3" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-ROLA-U4" data-master-source="AUD-V13-ROLA-U4" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-ROLA-U5" data-master-source="AUD-V13-ROLA-U5" class="cell-input readonly" readonly placeholder="[Master]"/></td>
            </tr>
            <tr class="row-critical">
              <td class="td-question">
                <div class="q-label">▶ Email służbowy</div>
                <div class="q-id mono">CA-REQ-00-MAIL</div>
                <div class="q-hint">Mail kontaktowy dla Audytora AI / Konsultanta — niezbędny do eskalacji pytań</div>
              </td>
              <td class="td-comp"><span class="tag kon small">KON</span></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-MAIL-U1" data-master-source="AUD-V13-MAIL-U1" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-MAIL-U2" data-master-source="AUD-V13-MAIL-U2" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-MAIL-U3" data-master-source="AUD-V13-MAIL-U3" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-MAIL-U4" data-master-source="AUD-V13-MAIL-U4" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-MAIL-U5" data-master-source="AUD-V13-MAIL-U5" class="cell-input readonly" readonly placeholder="[Master]"/></td>
            </tr>
            <tr>
              <td class="td-question">
                <div class="q-label">Telefon</div>
                <div class="q-id mono">CA-REQ-00-TEL</div>
                <div class="q-hint">Tylko jeśli osoba zgadza się na kontakt telefoniczny</div>
              </td>
              <td class="td-comp"><span class="tag kon small">KON</span></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-TEL-U1" data-master-source="AUD-V13-TEL-U1" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-TEL-U2" data-master-source="AUD-V13-TEL-U2" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-TEL-U3" data-master-source="AUD-V13-TEL-U3" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-TEL-U4" data-master-source="AUD-V13-TEL-U4" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-TEL-U5" data-master-source="AUD-V13-TEL-U5" class="cell-input readonly" readonly placeholder="[Master]"/></td>
            </tr>
            <tr>
              <td class="td-question">
                <div class="q-label">Data dołączenia</div>
                <div class="q-id mono">CA-REQ-00-DATA</div>
                <div class="q-hint">AUTO — wypełniane przez platformę przy pierwszym logowaniu</div>
              </td>
              <td class="td-comp"><span class="tag spec small">AUTO</span></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-DATA-U1" data-master-source="AUD-V13-DATA-U1" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-DATA-U2" data-master-source="AUD-V13-DATA-U2" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-DATA-U3" data-master-source="AUD-V13-DATA-U3" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-DATA-U4" data-master-source="AUD-V13-DATA-U4" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-DATA-U5" data-master-source="AUD-V13-DATA-U5" class="cell-input readonly" readonly placeholder="[Master]"/></td>
            </tr>
            <tr>
              <td class="td-question">
                <div class="q-label">Główny respondent (TAK/NIE)</div>
                <div class="q-id mono">CA-REQ-00-MAIN</div>
                <div class="q-hint">Domyślnie dla CA: KIER UR lub specjalista pneumatyk</div>
              </td>
              <td class="td-comp"><span class="tag kon small">KON</span></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-MAIN-U1" data-master-source="AUD-V13-MAIN-U1" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-MAIN-U2" data-master-source="AUD-V13-MAIN-U2" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-MAIN-U3" data-master-source="AUD-V13-MAIN-U3" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-MAIN-U4" data-master-source="AUD-V13-MAIN-U4" class="cell-input readonly" readonly placeholder="[Master]"/></td>
              <td class="td-input"><input type="text" data-id="CA-REQ-00-MAIN-U5" data-master-source="AUD-V13-MAIN-U5" class="cell-input readonly" readonly placeholder="[Master]"/></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </section>


  <!-- ============================================================ -->
  <!-- ETAP 1 · KONTEKST CA -->
  <!-- ============================================================ -->
  <section class="section" id="etap-1">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 1</div>
        <h2 class="section-title serif">Kontekst CA</h2>
        <p class="section-desc">4 pola READ-ONLY z Master + 1 CA-specific (procesy krytyczne) · czas: 1 min weryfikacja</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-1">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Kontekst CA — 4 pola READ-ONLY z Master + 1 pole CA-specific:</strong>
        <ul>
          <li>Branża, tryb pracy, dni pracy w roku, plany inwestycyjne — czytane bezpośrednio z Master Form</li>
          <li>Procesy krytyczne dla sprężonego powietrza — wpisuje EM (lakiernia, spawalnia, prasy hydrauliczne...)</li>
          <li>Czas: ~1 min weryfikacja danych Master + 1-2 min wpis procesów krytycznych</li>
        </ul>
      </div>

      <div class="field-list">

        <!-- CTX-V1-BR — Branża wiodąca → RO z Master.ZAK-V1-BRANZA -->
        <div class="field">
          <div class="field-head">
            <span class="field-id mono">CTX-V1-BR</span>
            <span class="field-label">Branża wiodąca</span>
            <span class="tag spec small">AUTO</span>
          </div>
          <span class="readonly-marker">🔒 RO z Master.E2</span>
          <input type="text" class="field-input field-readonly" data-id="CTX-V1-BR" data-master-source="ZAK-V1-BRANZA" readonly placeholder="[Master.E2-ZAK-V1-BRANZA]"/>
          <span class="field-hint">🔒 Z Master.E2 — branża wiodąca zakładu (ZAK-V1-BRANZA). Aby zmienić: popraw w Master.</span>
        </div>

        <!-- CTX-V2-ZM — Tryb pracy → RO z Master.PRO-V8-TRYB-PRACY -->
        <div class="field">
          <div class="field-head">
            <span class="field-id mono">CTX-V2-ZM</span>
            <span class="field-label">Tryb pracy / liczba zmian</span>
            <span class="tag spec small">AUTO</span>
          </div>
          <span class="readonly-marker">🔒 RO z Master.E3</span>
          <input type="text" class="field-input field-readonly" data-id="CTX-V2-ZM" data-master-source="PRO-V8-TRYB-PRACY" readonly placeholder="[Master.E3-PRO-V8-TRYB-PRACY]"/>
          <span class="field-hint">🔒 Z Master.E3 — tryb pracy (1-zmianowy / 2-zmianowy / 3-zmianowy / 24-7) (PRO-V8-TRYB-PRACY). Kluczowe dla wyliczenia profilu zapotrzebowania CA.</span>
        </div>

        <!-- CTX-V3-DNI — Dni pracy w roku → RO z Master.PRO-V9-DNI-ROK -->
        <div class="field">
          <div class="field-head">
            <span class="field-id mono">CTX-V3-DNI</span>
            <span class="field-label">Liczba dni pracy w roku [dni/rok]</span>
            <span class="tag spec small">AUTO</span>
          </div>
          <span class="readonly-marker">🔒 RO z Master.E3</span>
          <input type="number" class="field-input field-readonly" data-id="CTX-V3-DNI" data-master-source="PRO-V9-DNI-ROK" readonly placeholder="[Master.E3-PRO-V9-DNI-ROK]"/>
          <span class="field-hint">🔒 Z Master.E3 — liczba dni pracy w roku (PRO-V9-DNI-ROK). Typowo 250 (5×50) lub 350 (24-7).</span>
        </div>

        <!-- CTX-V4-KRYT — Procesy krytyczne CA-specific (textarea, EM) -->
        <div class="field">
          <div class="field-head">
            <span class="field-id mono">CTX-V4-KRYT</span>
            <span class="field-label">Procesy krytyczne (które stoją przy braku CA)</span>
            <span class="tag em">EM</span>
            <span class="tag" style="background: var(--rose-light); color: var(--rose); font-size: 10px; padding: 2px 6px; border-radius: 3px;">CA-specific</span>
          </div>
          <textarea class="field-input field-textarea" data-id="CTX-V4-KRYT" placeholder="np. lakiernia (linia spray), spawalnia (zaciski pneumatyczne), prasa hydrauliczna w hali H2, automatyczny pakowacz..."></textarea>
          <span class="field-hint">★ CTX-V4-KRYT · Procesy krytyczne dla sprężonego powietrza — przy braku CA linia produkcyjna stoi. Lista konkretnych odbiorców (np. lakiernia, spawalnia, prasa). To pomaga zidentyfikować ryzyko biznesowe z nieszczelności / niepewności CA. [tekst]</span>
        </div>

        <!-- CTX-V5-PLAN — Plany inwestycyjne → RO z Master.HIS-V1-MODERN-LIST -->
        <div class="field">
          <div class="field-head">
            <span class="field-id mono">CTX-V5-PLAN</span>
            <span class="field-label">Plany inwestycyjne / modernizacyjne</span>
            <span class="tag spec small">AUTO</span>
          </div>
          <span class="readonly-marker">🔒 RO z Master.E12</span>
          <textarea class="field-input field-readonly field-textarea" data-id="CTX-V5-PLAN" data-master-source="HIS-V1-MODERN-LIST" readonly placeholder="[Master.E12-HIS-V1-MODERN-LIST]"></textarea>
          <span class="field-hint">🔒 Z Master.E12 — lista planów modernizacyjnych zakładu (HIS-V1-MODERN-LIST). Kontekst dla Konsultanta — czy planowana wymiana sprężarek już jest w planach inwestycyjnych.</span>
        </div>

      </div>

    </div>
  </section>


  <!-- ============================================================ -->
  <!-- ETAP 2 · SPRĘŻARKI — KATALOG TECHNICZNY -->
  <!-- ============================================================ -->
  <section class="section" id="etap-2">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 2</div>
        <h2 class="section-title serif">Sprężarki — katalog techniczny</h2>
        <p class="section-desc">Każda KOLUMNA = jedna sprężarka · 28 pytań × N sprężarek · czas: ~5 min/sprężarkę · zgodnie z ISO 11011:2013</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-2">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Co to jest „sprężarka" w katalogu CA:</strong>
        <ul>
          <li>Każda <strong>kolumna SPR-1, SPR-2, ...</strong> = jedna fizyczna sprężarka w sprężarkowni.</li>
          <li>Domyślnie 3 kolumny — przycisk „<strong>+ Dodaj sprężarkę</strong>" rozszerza tabelę o kolejną.</li>
          <li>Identyfikacja w E2b alokacji: <strong>numer inwentarzowy + lokalizacja</strong> (V1+V2).</li>
          <li>Klucz dla CA: <strong>motogodziny Total / Load / Unload</strong> — odczyt ze sterownika (instrukcja niżej).</li>
        </ul>
      </div>

      <div class="devices-wrap">
        <table class="devices-table" id="sprezarki-table">
          <thead>
            <tr>
              <th class="th-question">Pytanie</th>
              <th class="th-comp">KTO</th>
              <th class="th-instance">SPR-1</th>
              <th class="th-instance">SPR-2</th>
              <th class="th-instance">SPR-3</th>
            </tr>
          </thead>
          <tbody>
            <tr class="row-section-header"><td colspan="5">▼ Identyfikacja sprężarki</td></tr>
            <tr>
              <td class="td-question"><div class="q-label">Numer inwentarzowy</div><div class="q-id mono">SPR-V1-INWENT</div><div class="q-hint">★ SPR-V1-INWENT · Z tabliczki znamionowej lub systemu UR. Identyfikator dla E2b alokacji.</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><input type="text" class="cell-input spr-name-input" data-id="SPR-V1-INWENT-S1" placeholder="np. SP-001"></td><td class="td-input"><input type="text" class="cell-input spr-name-input" data-id="SPR-V1-INWENT-S2" placeholder="np. SP-001"></td><td class="td-input"><input type="text" class="cell-input spr-name-input" data-id="SPR-V1-INWENT-S3" placeholder="np. SP-001"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Lokalizacja</div><div class="q-id mono">SPR-V2-LOK</div><div class="q-hint">★ SPR-V2-LOK · Pomieszczenie/hala gdzie stoi sprężarka.</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><input type="text" class="cell-input spr-name-input" data-id="SPR-V2-LOK-S1" placeholder="np. Sprężarkownia główna, Hala G2"></td><td class="td-input"><input type="text" class="cell-input spr-name-input" data-id="SPR-V2-LOK-S2" placeholder="np. Sprężarkownia główna, Hala G2"></td><td class="td-input"><input type="text" class="cell-input spr-name-input" data-id="SPR-V2-LOK-S3" placeholder="np. Sprężarkownia główna, Hala G2"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Producent</div><div class="q-id mono">SPR-V3-PROD</div><div class="q-hint">★ SPR-V3-PROD · Z tabliczki znamionowej.</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="SPR-V3-PROD-S1" placeholder="np. Atlas Copco / Kaeser / Boge / Ingersoll Rand"></td><td class="td-input"><input type="text" class="cell-input" data-id="SPR-V3-PROD-S2" placeholder="np. Atlas Copco / Kaeser / Boge / Ingersoll Rand"></td><td class="td-input"><input type="text" class="cell-input" data-id="SPR-V3-PROD-S3" placeholder="np. Atlas Copco / Kaeser / Boge / Ingersoll Rand"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Model / oznaczenie</div><div class="q-id mono">SPR-V4-MODEL</div><div class="q-hint">★ SPR-V4-MODEL · Z tabliczki znamionowej.</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="SPR-V4-MODEL-S1" placeholder="np. GA 75 VSD+, CSD 105"></td><td class="td-input"><input type="text" class="cell-input" data-id="SPR-V4-MODEL-S2" placeholder="np. GA 75 VSD+, CSD 105"></td><td class="td-input"><input type="text" class="cell-input" data-id="SPR-V4-MODEL-S3" placeholder="np. GA 75 VSD+, CSD 105"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Rodzaj sprężarki</div><div class="q-id mono">SPR-V5-RODZAJ</div><div class="q-hint">★ SPR-V5-RODZAJ · Najczęstsze: śrubowa (przemysł), tłokowa (warsztat), spiralna (małe zastosowania).</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><select class="cell-input" data-id="SPR-V5-RODZAJ-S1"><option value="">—</option><option>śrubowa</option><option>tłokowa</option><option>spiralna (scroll)</option><option>odśrodkowa</option><option>łopatkowa</option><option>inny</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V5-RODZAJ-S2"><option value="">—</option><option>śrubowa</option><option>tłokowa</option><option>spiralna (scroll)</option><option>odśrodkowa</option><option>łopatkowa</option><option>inny</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V5-RODZAJ-S3"><option value="">—</option><option>śrubowa</option><option>tłokowa</option><option>spiralna (scroll)</option><option>odśrodkowa</option><option>łopatkowa</option><option>inny</option></select></td>
            </tr>
            <tr class="row-section-header"><td colspan="5">▼ Parametry znamionowe</td></tr>
            <tr>
              <td class="td-question"><div class="q-label">Moc nominalna silnika</div><div class="q-id mono">SPR-V6-PNOM</div><div class="q-hint">★ SPR-V6-PNOM · Z tabliczki znamionowej silnika. [kW]</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="SPR-V6-PNOM-S1" placeholder="np. 75"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V6-PNOM-S2" placeholder="np. 75"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V6-PNOM-S3" placeholder="np. 75"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Wydajność (FAD)</div><div class="q-id mono">SPR-V7-Q</div><div class="q-hint">★ SPR-V7-Q · Free Air Delivery przy nominalnym ciśnieniu (zwykle 7 bar(g)). [m³/min]</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="SPR-V7-Q-S1" placeholder="np. 12.5" step="0.1"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V7-Q-S2" placeholder="np. 12.5" step="0.1"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V7-Q-S3" placeholder="np. 12.5" step="0.1"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Pmax — maks. ciśnienie</div><div class="q-id mono">SPR-V8-PMAX</div><div class="q-hint">★ SPR-V8-PMAX · Maksymalne ciśnienie robocze z tabliczki znamionowej. [bar(g)]</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="SPR-V8-PMAX-S1" placeholder="np. 10" step="0.1"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V8-PMAX-S2" placeholder="np. 10" step="0.1"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V8-PMAX-S3" placeholder="np. 10" step="0.1"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Rok produkcji / instalacji</div><div class="q-id mono">SPR-V9-ROK</div><div class="q-hint">★ SPR-V9-ROK · Rok produkcji lub uruchomienia.</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="SPR-V9-ROK-S1" placeholder="np. 2015" min="1970" max="2030"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V9-ROK-S2" placeholder="np. 2015" min="1970" max="2030"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V9-ROK-S3" placeholder="np. 2015" min="1970" max="2030"></td>
            </tr>
            <tr class="row-section-header"><td colspan="5">▼ Stan techniczny i historia</td></tr>
            <tr>
              <td class="td-question"><div class="q-label">Klasa sprawności silnika IE</div><div class="q-id mono">SPR-V10-IE</div><div class="q-hint">★ SPR-V10-IE · IE3 to obecny minimum dla nowych. IE4/IE5 — premium efficiency.</div></td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><select class="cell-input" data-id="SPR-V10-IE-S1"><option value="">—</option><option>IE1</option><option>IE2</option><option>IE3</option><option>IE4</option><option>IE5</option><option>nieoznaczone</option><option>inny</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V10-IE-S2"><option value="">—</option><option>IE1</option><option>IE2</option><option>IE3</option><option>IE4</option><option>IE5</option><option>nieoznaczone</option><option>inny</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V10-IE-S3"><option value="">—</option><option>IE1</option><option>IE2</option><option>IE3</option><option>IE4</option><option>IE5</option><option>nieoznaczone</option><option>inny</option></select></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Stan techniczny</div><div class="q-id mono">SPR-V11-STAN</div><div class="q-hint">★ SPR-V11-STAN · Subiektywna ocena klienta na podstawie eksploatacji.</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><select class="cell-input" data-id="SPR-V11-STAN-S1"><option value="">—</option><option>bardzo dobry</option><option>dobry</option><option>średni</option><option>zły</option><option>do wymiany</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V11-STAN-S2"><option value="">—</option><option>bardzo dobry</option><option>dobry</option><option>średni</option><option>zły</option><option>do wymiany</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V11-STAN-S3"><option value="">—</option><option>bardzo dobry</option><option>dobry</option><option>średni</option><option>zły</option><option>do wymiany</option></select></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Ostatni serwis (data)</div><div class="q-id mono">SPR-V12-SERW</div><div class="q-hint">★ SPR-V12-SERW · yyyy-mm-dd · z dokumentacji serwisowej.</div></td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="date" class="cell-input" data-id="SPR-V12-SERW-S1"></td><td class="td-input"><input type="date" class="cell-input" data-id="SPR-V12-SERW-S2"></td><td class="td-input"><input type="date" class="cell-input" data-id="SPR-V12-SERW-S3"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Awarie w ostatnich 12 mies.</div><div class="q-id mono">SPR-V13-AWAR</div><div class="q-hint">★ SPR-V13-AWAR · Liczba zarejestrowanych awarii wymagających serwisu.</div></td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="SPR-V13-AWAR-S1" placeholder="np. 2" min="0"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V13-AWAR-S2" placeholder="np. 2" min="0"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V13-AWAR-S3" placeholder="np. 2" min="0"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Wibracje / hałas</div><div class="q-id mono">SPR-V14-DBA</div><div class="q-hint">★ SPR-V14-DBA · Z pomiaru sonometrem przy obudowie. Norma typowo &lt;75 dB(A) dla nowych. [dB(A)]</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="SPR-V14-DBA-S1" placeholder="np. 72" step="0.1"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V14-DBA-S2" placeholder="np. 72" step="0.1"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V14-DBA-S3" placeholder="np. 72" step="0.1"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">UDT zbiornika ważne do</div><div class="q-id mono">SPR-V15-UDT</div><div class="q-hint">★ SPR-V15-UDT · Data ważności protokołu UDT zbiornika ciśnieniowego.</div></td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="date" class="cell-input" data-id="SPR-V15-UDT-S1"></td><td class="td-input"><input type="date" class="cell-input" data-id="SPR-V15-UDT-S2"></td><td class="td-input"><input type="date" class="cell-input" data-id="SPR-V15-UDT-S3"></td>
            </tr>
            <tr class="row-section-header"><td colspan="5">▼ Motogodziny (★ kluczowe dla CA)</td></tr>
            <tr>
              <td class="td-question"><div class="q-label">Motogodziny — Total</div><div class="q-id mono">SPR-V16-HTOT</div><div class="q-hint">★ SPR-V16-HTOT · Łączny czas pracy z licznika sterownika. Zasada: Total ≥ Load + Unload. [h]</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="SPR-V16-HTOT-S1" placeholder="np. 35420" min="0"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V16-HTOT-S2" placeholder="np. 35420" min="0"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V16-HTOT-S3" placeholder="np. 35420" min="0"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Motogodziny — Load</div><div class="q-id mono">SPR-V17-HLOAD</div><div class="q-hint">★ SPR-V17-HLOAD · Czas pracy pod obciążeniem (sprężanie). Wskaźnik wykorzystania = (Load+Unload)/Total. [h]</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="SPR-V17-HLOAD-S1" placeholder="np. 22150" min="0"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V17-HLOAD-S2" placeholder="np. 22150" min="0"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V17-HLOAD-S3" placeholder="np. 22150" min="0"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Motogodziny — Unload</div><div class="q-id mono">SPR-V18-HUNL</div><div class="q-hint">★ SPR-V18-HUNL · Czas pracy bez obciążenia (jałowy bieg). Wysoki Unload = niewykorzystany potencjał VSD. [h]</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="SPR-V18-HUNL-S1" placeholder="np. 8700" min="0"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V18-HUNL-S2" placeholder="np. 8700" min="0"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V18-HUNL-S3" placeholder="np. 8700" min="0"></td>
            </tr>
            <tr class="row-section-header"><td colspan="5">▼ Eksploatacja</td></tr>
            <tr>
              <td class="td-question"><div class="q-label">Dni pracy / tydzień</div><div class="q-id mono">SPR-V19-DNIT</div><div class="q-hint">★ SPR-V19-DNIT · 1-7. Wartość dla bieżącego trybu pracy.</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="SPR-V19-DNIT-S1" placeholder="np. 5" min="1" max="7"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V19-DNIT-S2" placeholder="np. 5" min="1" max="7"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V19-DNIT-S3" placeholder="np. 5" min="1" max="7"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Obciążenie typowe</div><div class="q-id mono">SPR-V20-OBC</div><div class="q-hint">★ SPR-V20-OBC · Średnie obciążenie sprężarki (Load/Total × 100). Cel: ~85% dla bazowych. [%]</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="SPR-V20-OBC-S1" placeholder="np. 75" min="0" max="100"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V20-OBC-S2" placeholder="np. 75" min="0" max="100"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V20-OBC-S3" placeholder="np. 75" min="0" max="100"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Tryb pracy w systemie</div><div class="q-id mono">SPR-V21-TRYB</div><div class="q-hint">★ SPR-V21-TRYB · Bazowa = pracuje stale, szczytowa = włącza się przy zapotrzebowaniu, rezerwa = back-up.</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><select class="cell-input" data-id="SPR-V21-TRYB-S1"><option value="">—</option><option>bazowa (baseload)</option><option>szczytowa (peak)</option><option>rezerwa (standby)</option><option>sezonowa</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V21-TRYB-S2"><option value="">—</option><option>bazowa (baseload)</option><option>szczytowa (peak)</option><option>rezerwa (standby)</option><option>sezonowa</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V21-TRYB-S3"><option value="">—</option><option>bazowa (baseload)</option><option>szczytowa (peak)</option><option>rezerwa (standby)</option><option>sezonowa</option></select></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Sterowanie</div><div class="q-id mono">SPR-V22-STER</div><div class="q-hint">★ SPR-V22-STER · VSD/VFD = energochłonne ale efektywne. Load/unload = stary system.</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><select class="cell-input" data-id="SPR-V22-STER-S1"><option value="">—</option><option>load/unload</option><option>modulacja (modulating)</option><option>VSD / VFD (falownik)</option><option>start-stop</option><option>kaskada nadrzędna</option><option>inny</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V22-STER-S2"><option value="">—</option><option>load/unload</option><option>modulacja (modulating)</option><option>VSD / VFD (falownik)</option><option>start-stop</option><option>kaskada nadrzędna</option><option>inny</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V22-STER-S3"><option value="">—</option><option>load/unload</option><option>modulacja (modulating)</option><option>VSD / VFD (falownik)</option><option>start-stop</option><option>kaskada nadrzędna</option><option>inny</option></select></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Uruchomienia / h</div><div class="q-id mono">SPR-V23-URUCH</div><div class="q-hint">★ SPR-V23-URUCH · Liczba startów silnika na godzinę. &gt;10/h to ryzyko zużycia silnika.</div></td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="SPR-V23-URUCH-S1" placeholder="np. 4" step="0.1"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V23-URUCH-S2" placeholder="np. 4" step="0.1"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V23-URUCH-S3" placeholder="np. 4" step="0.1"></td>
            </tr>
            <tr class="row-section-header"><td colspan="5">▼ Chłodzenie i pomieszczenie</td></tr>
            <tr>
              <td class="td-question"><div class="q-label">Sposób chłodzenia</div><div class="q-id mono">SPR-V24-CHL</div><div class="q-hint">★ SPR-V24-CHL · Powietrzne = standard. Wodne = większe efektywność, ale wymaga wody chłodzącej.</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><select class="cell-input" data-id="SPR-V24-CHL-S1"><option value="">—</option><option>powietrze</option><option>woda</option><option>mieszane</option><option>brak (zewnętrzne)</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V24-CHL-S2"><option value="">—</option><option>powietrze</option><option>woda</option><option>mieszane</option><option>brak (zewnętrzne)</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V24-CHL-S3"><option value="">—</option><option>powietrze</option><option>woda</option><option>mieszane</option><option>brak (zewnętrzne)</option></select></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Woda chłodząca</div><div class="q-id mono">SPR-V25-WODA</div><div class="q-hint">★ SPR-V25-WODA · Tylko jeśli chł. wodą. Z licznika lub szacunku. [m³/h]</div></td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="SPR-V25-WODA-S1" placeholder="np. 8" step="0.1" min="0"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V25-WODA-S2" placeholder="np. 8" step="0.1" min="0"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V25-WODA-S3" placeholder="np. 8" step="0.1" min="0"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Temperatura w pomieszczeniu</div><div class="q-id mono">SPR-V26-TEMP</div><div class="q-hint">★ SPR-V26-TEMP · Typowa temp. w sprężarkowni. &gt;35°C → spadek wydajności. [°C]</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="SPR-V26-TEMP-S1" placeholder="np. 28" step="0.5"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V26-TEMP-S2" placeholder="np. 28" step="0.5"></td><td class="td-input"><input type="number" class="cell-input" data-id="SPR-V26-TEMP-S3" placeholder="np. 28" step="0.5"></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Wentylacja sprężarkowni</div><div class="q-id mono">SPR-V27-WENT</div><div class="q-hint">★ SPR-V27-WENT · Niedostateczna wentylacja → przegrzewanie się sprężarek.</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><select class="cell-input" data-id="SPR-V27-WENT-S1"><option value="">—</option><option>mechaniczna z odciągiem</option><option>mechaniczna nawiewno-wywiewna</option><option>grawitacyjna</option><option>brak / niedostateczna</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V27-WENT-S2"><option value="">—</option><option>mechaniczna z odciągiem</option><option>mechaniczna nawiewno-wywiewna</option><option>grawitacyjna</option><option>brak / niedostateczna</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V27-WENT-S3"><option value="">—</option><option>mechaniczna z odciągiem</option><option>mechaniczna nawiewno-wywiewna</option><option>grawitacyjna</option><option>brak / niedostateczna</option></select></td>
            </tr>
            <tr>
              <td class="td-question"><div class="q-label">Recyrkulacja powietrza ssania</div><div class="q-id mono">SPR-V28-RECYR</div><div class="q-hint">★ SPR-V28-RECYR · Czy sprężarka zasysa powietrze z hali (cieplejsze, brudniejsze) czy z zewnątrz (lepsze).</div></td>
              <td class="td-comp"><span class="tag ur small">UR</span></td>
              <td class="td-input"><select class="cell-input" data-id="SPR-V28-RECYR-S1"><option value="">—</option><option>TAK</option><option>NIE</option><option>częściowa</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V28-RECYR-S2"><option value="">—</option><option>TAK</option><option>NIE</option><option>częściowa</option></select></td><td class="td-input"><select class="cell-input" data-id="SPR-V28-RECYR-S3"><option value="">—</option><option>TAK</option><option>NIE</option><option>częściowa</option></select></td>
            </tr>
          </tbody>
        </table>
      </div>
      <button class="row-add" type="button" id="add-sprezarka-btn">+ Dodaj sprężarkę (SPR-4, SPR-5...)</button>

      <div class="group-info" style="margin-top: 24px; background: var(--green-bg); border-left: 3px solid var(--green-primary);">
        <strong style="color: var(--green-deep)">📖 Jak odczytać motogodziny ze sterownika sprężarki:</strong>
        <ul>
          <li><strong>Atlas Copco</strong> (Elektronikon): MENU → COUNTERS → „Total hours", „Hours load", „Hours unload"</li>
          <li><strong>Kaeser</strong> (Sigma Control): MENU → Operating data → „Operation hours", „Load hours", „Idle hours"</li>
          <li><strong>Boge</strong> (focus 2.0): Menu → Eksploatacja → „Czas pracy całkowity", „Czas pod obciążeniem"</li>
          <li><strong>Ingersoll Rand</strong> (Xe-145M): Status → Hours → „Total run", „Loaded run"</li>
        </ul>
        <div style="margin-top: 8px; padding: 8px 12px; background: var(--paper-paper); border-radius: 4px; font-size: 12px; color: var(--ink-soft);">
          <strong>Zasada:</strong> Total ≥ Load + Unload (różnica = czas postoju/serwisu).<br>
          <strong>Wskaźnik wykorzystania:</strong> (Load + Unload) / Total × 100% → &lt;30% sprężarka rezerwowa, 30-70% typowa, &gt;70% intensywna (kandydat na VSD lub dokupienie kolejnej).
        </div>
      </div>

    </div>
  </section>


  <!-- ============================================================ -->
  <!-- ETAP 2b · MACIERZ ALOKACJI SPRĘŻARKI × WYDZIAŁY (%) -->
  <!-- ============================================================ -->
  <section class="section" id="etap-2b">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 2b</div>
        <h2 class="section-title serif">Macierz alokacji Sprężarki × Wydziały (%)</h2>
        <p class="section-desc">WIERSZE = sprężarki (z E2), KOLUMNY = wydziały (RO z Master.E4) · Σ wiersza = 100% · wymóg EnPI per SEU dla sprężonego powietrza</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-2b">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Macierz alokacji Sprężarki × Wydziały — wymóg EnPI per SEU:</strong>
        <ul>
          <li><strong>WIERSZE</strong> = sprężarki (z E2, dynamicznie). Etykieta: numer inwentarzowy + lokalizacja.</li>
          <li><strong>KOLUMNY</strong> = wydziały (RO z Master.E4 — zmiana w Master odświeży się tutaj automatycznie).</li>
          <li><strong>Komórka</strong> = % udziału danej sprężarki w obsłudze danego wydziału.</li>
          <li><strong>Σ wiersza musi = 100%</strong> (±2% tolerancji). Status pokazuje OK / Brakuje / Nadmiar.</li>
          <li><strong>Przykład:</strong> SPR-1 obsługuje Press shop 30% + Lakiernia 50% + Montaż 20% = 100%.</li>
        </ul>
      </div>

      <div id="alokacja-status-banner" class="alokacja-banner" style="margin: 12px 0;"></div>

      <div class="devices-wrap">
        <table class="devices-table" id="alokacja-table">
          <thead>
            <tr id="alokacja-header-row">
              <th class="th-question">Sprężarka</th>
              <!-- Kolumny wydziałów + Σ + Status — generowane w JS -->
            </tr>
          </thead>
          <tbody id="alokacja-body">
            <!-- Wiersze sprężarek — generowane w JS na podstawie N z E2 -->
          </tbody>
          <tfoot id="alokacja-foot">
            <!-- Σ kolumn — generowane w JS -->
          </tfoot>
        </table>
      </div>

      <div id="alokacja-empty-state" style="display:none; padding: 32px; text-align: center; color: var(--ink-mute); background: var(--paper-paper); border-radius: 8px; margin-top: 16px;">
        <div style="font-size: 24px; margin-bottom: 8px;">⚠</div>
        <div style="font-size: 14px;"><strong>Brak danych do macierzy.</strong></div>
        <div style="font-size: 12px; margin-top: 4px;">Wypełnij <strong>wydziały w Master.E4</strong> oraz dodaj <strong>sprężarki w E2</strong> powyżej.</div>
      </div>

    </div>
  </section>


  <!-- ============================================================ -->
  <!-- ETAP 3 · KOMPRESORY JAKO SYSTEM -->
  <!-- ============================================================ -->
  <section class="section" id="etap-3">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 3</div>
        <h2 class="section-title serif">KOMPRESORY JAKO SYSTEM</h2>
        <p class="section-desc">Specific Power, ciśnienie robocze, sterowanie sekwencyjne, monitoring BMS/SCADA, zbiornik buforowy · 7 pól · czas: 3-4 min</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-3">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>KOMPRESORY JAKO SYSTEM — 7 pól · czas: 3-4 min</strong>
        <ul>
          <li><strong>Specific Power</strong> — kluczowy KPI. Cel: &lt;6,5 kW/(m³/min). Powyżej 8,5 → kandydat do modernizacji.</li>
          <li><strong>Praca w nocy/weekend</strong> — to KLUCZOWY wskaźnik nieszczelności. Jeśli zakład nie pracuje a sprężarka chodzi → wycieki.</li>
          <li><strong>BMS/SCADA</strong> bez monitoringu = brak danych do EnPI dla ISO 50001.</li>
        </ul>
      </div>

      <div class="field-list">
        
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-B1-SP</span>
        <span class="field-label">Specific Power [kW/(m³/min)]</span>
        <span class="tag em">EM</span><span class="tag kon">KON</span>
      </div>
          <input type="number" class="field-input" data-id="CA-B1-SP" placeholder="np. 7.2" step="0.01">
          <span class="field-hint">★ CA-B1-SP · Cel: &lt;6,5 kW/(m³/min) dla sprawnych systemów. Powyżej 8,5 — kandydat do modernizacji. [kW/(m³/min)]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-B1-PROB</span>
        <span class="field-label">Ciśnienie robocze sieci [bar(g)]</span>
        <span class="tag ur">UR</span>
      </div>
          <input type="number" class="field-input" data-id="CA-B1-PROB" placeholder="np. 7.5" step="0.1">
          <span class="field-hint">★ CA-B1-PROB · Z manometru w sprężarkowni. Każdy 1 bar nadciśnienia = 7-10% wyższe zużycie energii. [bar(g)]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-B1-SEKW</span>
        <span class="field-label">Sterowanie sekwencyjne (kaskada)</span>
        <span class="tag em">EM</span>
      </div>
          <input type="text" class="field-input" data-id="CA-B1-SEKW" placeholder="np. Atlas Copco ES 130 / brak kaskady">
          <span class="field-hint">★ CA-B1-SEKW · Producent + model lub wpis &bdquo;brak&rdquo;. Kaskada optymalizuje załączanie sprężarek pod aktualne zapotrzebowanie.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-B1-SCADA</span>
        <span class="field-label">Monitoring BMS/SCADA</span>
        <span class="tag em">EM</span>
      </div>
          <select class="field-select" data-id="CA-B1-SCADA"><option value="">— wybierz —</option><option>TAK — pełny (parametry + zużycie)</option><option>TAK — częściowy (tylko parametry)</option><option>TAK — tylko alarmy</option><option>NIE — brak monitoringu</option><option>nie wiem</option></select>
          <span class="field-hint">★ CA-B1-SCADA · Czy sprężarki są monitorowane przez BMS/SCADA. Brak monitoringu = brak danych do EnPI.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-B1-ZB</span>
        <span class="field-label">Zbiornik buforowy</span>
        <span class="tag ur">UR</span>
      </div>
          <select class="field-select" data-id="CA-B1-ZB"><option value="">— wybierz —</option><option>TAK — jeden</option><option>TAK — kilka</option><option>NIE</option><option>nie wiem</option></select>
          <span class="field-hint">★ CA-B1-ZB · Zbiornik wygładza zapotrzebowanie i redukuje liczbę uruchomień sprężarek.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-B1-POJ</span>
        <span class="field-label">Pojemność zbiornika [m³]</span>
        <span class="tag ur">UR</span>
      </div>
          <input type="number" class="field-input" data-id="CA-B1-POJ" placeholder="np. 5" step="0.1">
          <span class="field-hint">★ CA-B1-POJ · Pojemność geometryczna z tabliczki znamionowej. Reguła: ~1 m³ na 1 m³/min wydajności. [m³]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-B1-NOC</span>
        <span class="field-label">Sprężarka pracuje w nocy/weekend [h/tydz]</span>
        <span class="tag ur">UR</span><span class="tag em">EM</span>
      </div>
          <input type="number" class="field-input" data-id="CA-B1-NOC" placeholder="np. 36" min="0">
          <span class="field-hint">★ CA-B1-NOC · ★ KLUCZOWY wskaźnik nieszczelności. Jeśli zakład nie pracuje a sprężarka chodzi → leakage. 0 = brak leaków, 24+ = poważny problem. [h/tydz]</span>
        </div>
      </div>

    </div>
  </section>


  <!-- ============================================================ -->
  <!-- ETAP 3.5 · ZASILANIE ELEKTRYCZNE -->
  <!-- ============================================================ -->
  <section class="section" id="etap-3-5">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 3.5</div>
        <h2 class="section-title serif">Zasilanie elektryczne</h2>
        <p class="section-desc">Transformatory, kompensacja mocy biernej, harmoniczne, filtry EMC · 10 pól · czas: 3-4 min</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-3-5">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Zasilanie elektryczne sprężarek — 10 pól · czas: 3-4 min</strong>
        <ul>
          <li><strong>cos φ &lt; 0,95</strong> = opłaty za moc bierną · kompensacja redukuje koszty.</li>
          <li><strong>Stare transformatory</strong> (&gt;30 lat) = straty 5-7% (vs 3-4% nowe).</li>
          <li><strong>Sprężarki z VSD</strong> generują harmoniczne — filtry EMC i AHF zmniejszają THD.</li>
          <li><strong>THD &gt; 8%</strong> prądu (PN-EN 50160) → przeciążenie kabli, straty wysokoczęstotliwościowe.</li>
        </ul>
      </div>

      <div class="field-list">
        
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">E1-NAP</span>
        <span class="field-label">Napięcie zasilania sprężarek</span>
        <span class="tag em">EM</span>
      </div>
          <select class="field-select" data-id="E1-NAP"><option value="">— wybierz —</option><option>400 V (niskie)</option><option>690 V (niskie)</option><option>6 kV (średnie)</option><option>10 kV (średnie)</option><option>inne</option><option>mieszane</option></select>
          <span class="field-hint">★ E1-NAP · Z dokumentacji elektrycznej / tabliczki znamionowej silnika.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">E1-TRAFO</span>
        <span class="field-label">Liczba i moc transformatorów [kVA]</span>
        <span class="tag em">EM</span>
      </div>
          <input type="text" class="field-input" data-id="E1-TRAFO" placeholder="np. 2× 3 MVA + 1× 1250 kVA">
          <span class="field-hint">★ E1-TRAFO · Wszystkie transformatory zasilające zakład (nie tylko sprężarki) — kontekst dla strat. [kVA]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">E1-ROK</span>
        <span class="field-label">Rok instalacji transformatorów</span>
        <span class="tag em">EM</span>
      </div>
          <input type="number" class="field-input" data-id="E1-ROK" placeholder="np. 1995" min="1950" max="2030">
          <span class="field-hint">★ E1-ROK · Najstarszy z transformatorów. Stare (&gt;30 lat) = wyższe straty (do 5-7%).</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">E1-TYP</span>
        <span class="field-label">Typ transformatora</span>
        <span class="tag em">EM</span>
      </div>
          <select class="field-select" data-id="E1-TYP"><option value="">— wybierz —</option><option>olejowy hermetyczny</option><option>olejowy oddychający</option><option>suchy żywiczny</option><option>suchy</option><option>mieszany</option><option>nie wiem</option></select>
          <span class="field-hint">★ E1-TYP · Suche żywiczne = niższe straty, brak ryzyka pożaru olejowego. Olejowe = tańsze ale wymagają konserwacji.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">E1-COSF</span>
        <span class="field-label">Współczynnik mocy cos φ</span>
        <span class="tag em">EM</span><span class="tag spec">SPEC</span>
      </div>
          <input type="number" class="field-input" data-id="E1-COSF" placeholder="np. 0.92" step="0.01" min="0" max="1">
          <span class="field-hint">★ E1-COSF · Z faktury dystrybutora EE lub pomiaru. Typowo 0,85-0,98. &lt;0,95 = opłaty za moc bierną.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">E1-KOMP</span>
        <span class="field-label">Kompensacja mocy biernej</span>
        <span class="tag em">EM</span>
      </div>
          <select class="field-select" data-id="E1-KOMP"><option value="">— wybierz —</option><option>TAK — automatyczna (regulator)</option><option>TAK — stała (kondensatory)</option><option>TAK — częściowa</option><option>NIE — brak kompensacji</option><option>nie wiem</option></select>
          <span class="field-hint">★ E1-KOMP · Kompensacja redukuje opłaty za moc bierną. Automatyczna = optymalna.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">E1-FILTRY</span>
        <span class="field-label">Filtry harmoniczne</span>
        <span class="tag em">EM</span>
      </div>
          <select class="field-select" data-id="E1-FILTRY"><option value="">— wybierz —</option><option>TAK — aktywne (AHF)</option><option>TAK — pasywne</option><option>TAK — częściowo</option><option>NIE — brak</option><option>nie wiem</option></select>
          <span class="field-hint">★ E1-FILTRY · Falowniki VSD generują harmoniczne. Filtry obniżają THD i straty.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">E1-THD</span>
        <span class="field-label">THD (zakłócenia harmoniczne) [%]</span>
        <span class="tag spec">SPEC</span>
      </div>
          <input type="number" class="field-input" data-id="E1-THD" placeholder="np. 8.5" step="0.1">
          <span class="field-hint">★ E1-THD · Wymaga pomiaru analizatorem sieci. Cel: &lt;5% napięcia, &lt;8% prądu (PN-EN 50160). [%]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">E1-STRATY</span>
        <span class="field-label">Straty na transformatorach [%]</span>
        <span class="tag spec">SPEC</span>
      </div>
          <input type="number" class="field-input" data-id="E1-STRATY" placeholder="np. 3.5" step="0.01">
          <span class="field-hint">★ E1-STRATY · Typowo 3-4% mocy znamionowej. Wymaga pomiaru lub szacunku z dokumentacji DTR. [%]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">E1-EMC</span>
        <span class="field-label">Filtr EMC w falowniku VSD</span>
        <span class="tag em">EM</span>
      </div>
          <select class="field-select" data-id="E1-EMC"><option value="">— wybierz —</option><option>TAK — wbudowany</option><option>TAK — zewnętrzny</option><option>NIE — brak</option><option>brak VSD w sprężarkach</option><option>nie wiem</option></select>
          <span class="field-hint">★ E1-EMC · Filtr EMC w VSD chroni sieć przed zakłóceniami i redukuje straty wysokoczęstotliwościowe.</span>
        </div>
      </div>

    </div>
  </section>


  <!-- ============================================================ -->
  <!-- ETAP 4 · UZDATNIANIE POWIETRZA (TREATMENT) -->
  <!-- ============================================================ -->
  <section class="section" id="etap-4">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 4</div>
        <h2 class="section-title serif">Uzdatnianie powietrza (Treatment)</h2>
        <p class="section-desc">Osuszacze, filtry, klasa jakości ISO 8573 · 9 pól · czas: 3-4 min</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-4">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Uzdatnianie powietrza (Treatment) — 9 pól · czas: 3-4 min</strong>
        <ul>
          <li><strong>Osuszacz adsorpcyjny</strong> może zużywać 10-20% energii sprężarek (purge). HOC z dmuchawą = 2-5%.</li>
          <li><strong>Brudny filtr</strong> (Δp 0,3-0,5 bar) = ~3% energii sprężarek. Wymiana wg Δp, nie wg czasu.</li>
          <li><strong>Klasa ISO 8573</strong> dobierana do najważniejszego odbiorcy (malowanie 1.4.1 vs narzędzia 4.4.4).</li>
        </ul>
      </div>

      <div class="field-list">
        
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-T1-OSU</span>
        <span class="field-label">Osuszacz (typ)</span>
        <span class="tag ur">UR</span>
      </div>
          <select class="field-select" data-id="CA-T1-OSU"><option value="">— wybierz —</option><option>chłodniczy</option><option>adsorpcyjny — dmuchawowy (HOC)</option><option>adsorpcyjny — purge</option><option>adsorpcyjny — heated</option><option>membranowy</option><option>brak osuszacza</option><option>mieszany</option></select>
          <span class="field-hint">★ CA-T1-OSU · Chłodniczy = energooszczędny, ale punkt rosy +3°C. Adsorpcyjny = niski PR (-40°C), wyższe zużycie.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-T1-PR</span>
        <span class="field-label">Punkt rosy [°C]</span>
        <span class="tag em">EM</span>
      </div>
          <input type="number" class="field-input" data-id="CA-T1-PR" placeholder="np. -40" step="1">
          <span class="field-hint">★ CA-T1-PR · Chłodniczy: +3 do +10°C. Adsorpcyjny: -20 do -70°C. ISO 8573 klasa zależy od PR. [°C]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-T1-ZUZ</span>
        <span class="field-label">Zużycie energii osuszaczy [kWh/rok]</span>
        <span class="tag em">EM</span>
      </div>
          <input type="number" class="field-input" data-id="CA-T1-ZUZ" placeholder="np. 35000" min="0">
          <span class="field-hint">★ CA-T1-ZUZ · Z liczników lub szacunku. Adsorpcyjny może zużywać 10-20% energii sprężarek. [kWh/rok]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-T1-PURGE</span>
        <span class="field-label">Purge powietrza (adsorp.) [%]</span>
        <span class="tag em">EM</span><span class="tag spec">SPEC</span>
      </div>
          <input type="number" class="field-input" data-id="CA-T1-PURGE" placeholder="np. 12" step="0.5" min="0" max="30">
          <span class="field-hint">★ CA-T1-PURGE · Typowo 10-20% wydajności sprężarek. Z dmuchawą zewn. (HOC) — 2-5%. Wysoki purge = duża strata. [%]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-T1-RED</span>
        <span class="field-label">Redundancja osuszacza</span>
        <span class="tag em">EM</span>
      </div>
          <select class="field-select" data-id="CA-T1-RED"><option value="">— wybierz —</option><option>TAK — pełna 100% (n+n)</option><option>TAK — n+1</option><option>TAK — częściowa</option><option>NIE — pojedynczy</option><option>nie wiem</option></select>
          <span class="field-hint">★ CA-T1-RED · Brak redundancji = ryzyko przestoju produkcji przy awarii osuszacza.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-T1-FILTR</span>
        <span class="field-label">Liczba stopni filtracji</span>
        <span class="tag ur">UR</span><span class="tag em">EM</span>
      </div>
          <input type="number" class="field-input" data-id="CA-T1-FILTR" placeholder="np. 3" min="0" max="10">
          <span class="field-hint">★ CA-T1-FILTR · Typowo 2-3 stopnie (wstępny + dokładny + węglowy). Każdy stopień = spadek ciśnienia + koszt energii.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-T1-DPF</span>
        <span class="field-label">Spadek ciśnienia na filtrach [bar]</span>
        <span class="tag ur">UR</span><span class="tag em">EM</span><span class="tag spec">SPEC</span>
      </div>
          <input type="number" class="field-input" data-id="CA-T1-DPF" placeholder="np. 0.25" step="0.01" min="0">
          <span class="field-hint">★ CA-T1-DPF · Brudny filtr = 0,3-0,5 bar = ~3% energii sprężarek. Cel: &lt;0,2 bar (czysty). [bar]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-T1-DPO</span>
        <span class="field-label">Spadek ciśnienia na osuszaczu [bar]</span>
        <span class="tag ur">UR</span><span class="tag em">EM</span><span class="tag spec">SPEC</span>
      </div>
          <input type="number" class="field-input" data-id="CA-T1-DPO" placeholder="np. 0.15" step="0.01" min="0">
          <span class="field-hint">★ CA-T1-DPO · Typowo 0,1-0,3 bar. Wysoki = zatkany lub źle dobrany osuszacz. [bar]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-T1-ISO8573</span>
        <span class="field-label">Wymagana klasa ISO 8573</span>
        <span class="tag em">EM</span>
      </div>
          <input type="text" class="field-input" data-id="CA-T1-ISO8573" placeholder="np. 1.4.2 (Solid.Water.Oil)">
          <span class="field-hint">★ CA-T1-ISO8573 · Klasa zależy od zastosowania: malowanie wymaga 1.4.1, narzędzia pneumatyczne 4.4.4. Format: Solid.Water.Oil.</span>
        </div>
      </div>

    </div>
  </section>


  <!-- ============================================================ -->
  <!-- ETAP 5 · SIEĆ DYSTRYBUCJI (TRANSMISSION) -->
  <!-- ============================================================ -->
  <section class="section" id="etap-5">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 5</div>
        <h2 class="section-title serif">Sieć dystrybucji (Transmission)</h2>
        <p class="section-desc">Topologia, materiał rurociągów, długość, krytyczny odbiorca, kondensat · 12 pól · czas: 4-5 min</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-5">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Sieć dystrybucji (Transmission) — 12 pól · czas: 4-5 min</strong>
        <ul>
          <li><strong>Pierścień (ring)</strong> = najefektywniejsza topologia. Promieniowa = długie odcinki, większy spadek ciśnienia.</li>
          <li><strong>Spadek ciśnienia &gt; 10% Ptłoczenia</strong> = za mała DN lub zatkania w sieci.</li>
          <li><strong>Stara sieć ze stali czarnej</strong> rdzewieje wewnątrz — dodatkowe spadki + zanieczyszczenia w powietrzu.</li>
          <li><strong>Zawory odcinające stref</strong> = duże oszczędności (izolacja nieczynnych obszarów nocą/weekendem).</li>
        </ul>
      </div>

      <div class="field-list">
        
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-N1-TOPO</span>
        <span class="field-label">Topologia sieci</span>
        <span class="tag ur">UR</span><span class="tag em">EM</span>
      </div>
          <select class="field-select" data-id="CA-N1-TOPO"><option value="">— wybierz —</option><option>pierścień (ring)</option><option>promieniowa (drzewo)</option><option>mieszana</option><option>magistralna (linia)</option><option>nie wiem</option></select>
          <span class="field-hint">★ CA-N1-TOPO · Pierścień = najefektywniejsza (równomierne ciśnienie). Promieniowa = długie odcinki, większy spadek ciśnienia.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-N1-MAT</span>
        <span class="field-label">Materiał głównych rurociągów</span>
        <span class="tag ur">UR</span><span class="tag em">EM</span>
      </div>
          <select class="field-select" data-id="CA-N1-MAT"><option value="">— wybierz —</option><option>stal czarna (typowa)</option><option>stal nierdzewna</option><option>aluminium (lekkie systemy)</option><option>miedź</option><option>PE-RT / kompozyty</option><option>mieszany</option><option>inny</option></select>
          <span class="field-hint">★ CA-N1-MAT · Stal czarna rdzewieje wewnątrz → spada ciśnienie + zanieczyszczenia. Aluminium / nierdzewka = czyste.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-N1-DN</span>
        <span class="field-label">Średnica głównego rurociągu (DN)</span>
        <span class="tag em">EM</span>
      </div>
          <input type="text" class="field-input" data-id="CA-N1-DN" placeholder="np. DN 100 / DN 150">
          <span class="field-hint">★ CA-N1-DN · Z dokumentacji lub pomiaru. Zbyt mała DN = duży spadek ciśnienia.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-N1-DLUG</span>
        <span class="field-label">Łączna długość sieci [m]</span>
        <span class="tag em">EM</span>
      </div>
          <input type="number" class="field-input" data-id="CA-N1-DLUG" placeholder="np. 850" min="0">
          <span class="field-hint">★ CA-N1-DLUG · Orientacyjnie z planu zakładu. Każde 100 m = potencjalne miejsce wycieku. [m]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-N1-STREFY</span>
        <span class="field-label">Liczba stref / gałęzi</span>
        <span class="tag em">EM</span>
      </div>
          <input type="number" class="field-input" data-id="CA-N1-STREFY" placeholder="np. 6" min="0">
          <span class="field-hint">★ CA-N1-STREFY · Ile niezależnie zarządzanych grup odbiorców. Więcej stref = lepsza możliwość izolacji nocą.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-N1-ZAW</span>
        <span class="field-label">Zawory odcinające między strefami</span>
        <span class="tag ur">UR</span><span class="tag em">EM</span>
      </div>
          <select class="field-select" data-id="CA-N1-ZAW"><option value="">— wybierz —</option><option>TAK — wszystkie strefy</option><option>TAK — większość</option><option>TAK — częściowo</option><option>NIE — brak</option><option>nie wiem</option></select>
          <span class="field-hint">★ CA-N1-ZAW · Możliwość izolacji nocą/weekendem = duże oszczędności (eliminacja leaków w nieczynnych strefach).</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-N1-PODB</span>
        <span class="field-label">Wymagane ciśnienie u krytycznego odbiorcy [bar(g)]</span>
        <span class="tag em">EM</span>
      </div>
          <input type="number" class="field-input" data-id="CA-N1-PODB" placeholder="np. 6.5" step="0.1">
          <span class="field-hint">★ CA-N1-PODB · Najwyższe wymagane ciśnienie spośród wszystkich odbiorców. Determinuje ciśnienie tłoczenia sprężarki. [bar(g)]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-N1-SPADEK</span>
        <span class="field-label">Spadek ciśnienia w sieci [bar]</span>
        <span class="tag em">EM</span><span class="tag spec">SPEC</span>
      </div>
          <input type="number" class="field-input" data-id="CA-N1-SPADEK" placeholder="np. 0.5" step="0.01" min="0">
          <span class="field-hint">★ CA-N1-SPADEK · Cel: &lt;10% ciśnienia tłoczenia (np. 0,7 bar przy 7 bar). Wymaga pomiaru w 2 punktach. [bar]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-N1-MON</span>
        <span class="field-label">Monitoring ciśnienia w sieci</span>
        <span class="tag ur">UR</span>
      </div>
          <select class="field-select" data-id="CA-N1-MON"><option value="">— wybierz —</option><option>TAK — ciągły (loger)</option><option>TAK — okresowy ręczny</option><option>TAK — tylko manometry punktowe</option><option>NIE — brak</option></select>
          <span class="field-hint">★ CA-N1-MON · Ciągły monitoring = wykrywanie problemów ciśnienia, profili zapotrzebowania.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-N1-SPUST</span>
        <span class="field-label">Typ spustów kondensatu</span>
        <span class="tag ur">UR</span>
      </div>
          <select class="field-select" data-id="CA-N1-SPUST"><option value="">— wybierz —</option><option>elektroniczne (zero-loss)</option><option>czasowe (timer)</option><option>manualne (ręczne)</option><option>mieszane</option><option>brak / niedrożne</option></select>
          <span class="field-hint">★ CA-N1-SPUST · Czasowe źle ustawione = ciągły wyciek powietrza. Elektroniczne zero-loss = najefektywniejsze.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-N1-SEPAR</span>
        <span class="field-label">Separator olej-woda</span>
        <span class="tag ur">UR</span><span class="tag em">EM</span>
      </div>
          <select class="field-select" data-id="CA-N1-SEPAR"><option value="">— wybierz —</option><option>TAK — certyfikowany aktywny</option><option>TAK — starszy / bez certyfikatu</option><option>NIE — brak</option><option>nie wiem</option></select>
          <span class="field-hint">★ CA-N1-SEPAR · Wymóg ochrony środowiska — kondensat z oleju nie może iść do kanalizacji.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-N1-WIEK</span>
        <span class="field-label">Wiek sieci [lat]</span>
        <span class="tag em">EM</span>
      </div>
          <input type="number" class="field-input" data-id="CA-N1-WIEK" placeholder="np. 20" min="0" max="100">
          <span class="field-hint">★ CA-N1-WIEK · Od daty budowy / ostatniej modernizacji. Sieć &gt;25 lat ze stali czarnej = duże straty wewnętrzne i wycieki. [lat]</span>
        </div>
      </div>

    </div>
  </section>


  <!-- ============================================================ -->
  <!-- ETAP 6 · ODBIORCY KOŃCOWI (DEMAND) -->
  <!-- ============================================================ -->
  <section class="section" id="etap-6">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 6</div>
        <h2 class="section-title serif">Odbiorcy końcowi (Demand)</h2>
        <p class="section-desc">Inwentaryzacja głównych odbiorców, niewłaściwe użycia (misuse), nieszczelności · 12 pól · czas: 5-6 min · alokacja per wydział → patrz E2b</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-6">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Odbiorcy końcowi (Demand) — 12 pól w 3 grupach · czas: 5-6 min</strong>
        <ul>
          <li><strong>Misuse</strong> (niewłaściwe użycia) = potencjalne 10-30% oszczędności. Najczęstszy: przedmuch otwartą rurką.</li>
          <li><strong>Leak rate &gt; 10%</strong> = priorytet do uszczelnienia. Każdy 1% leaków przy 75 kW ≈ 700 PLN/rok.</li>
          <li><strong>Alokacja per wydział</strong> → patrz E2b (macierz Sprężarki × Wydziały %).</li>
        </ul>
      </div>

      
      <div class="group">
        <div class="group-title">Grupa 1 — Inwentaryzacja i charakterystyka</div>
        <div class="field-list">
          
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-D1-INV</span>
        <span class="field-label">Inwentaryzacja głównych odbiorców</span>
        <span class="tag em">EM</span>
      </div>
          <textarea class="field-input field-textarea" data-id="CA-D1-INV" placeholder="np. Linia montażowa A1 — 6 bar — ~1500 m³/h
Prasa hydrauliczna H2 — 8 bar — ~400 m³/h
Lakiernia spray — 6 bar — ~800 m³/h
... (min. 5 największych)"></textarea>
          <span class="field-hint">★ CA-D1-INV · Lista głównych odbiorców z parametrami: maszyna — wymagane ciśnienie — zużycie. Min. 5 największych.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-D1-KRYT</span>
        <span class="field-label">Krytyczny odbiorca (najwyższe ciśnienie)</span>
        <span class="tag em">EM</span>
      </div>
          <input type="text" class="field-input" data-id="CA-D1-KRYT" placeholder="np. Prasa H2 — 8 bar(g)">
          <span class="field-hint">★ CA-D1-KRYT · Pojedynczy odbiorca o najwyższym wymaganiu ciśnieniowym — determinuje minimum ciśnienia w sieci.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-D1-PROFIL</span>
        <span class="field-label">Profil zapotrzebowania</span>
        <span class="tag em">EM</span>
      </div>
          <select class="field-select" data-id="CA-D1-PROFIL"><option value="">— wybierz —</option><option>stały (24/7 stabilne)</option><option>zmienny (dzień/noc)</option><option>szczytowy (peak hours)</option><option>nieregularny (start-stop)</option><option>sezonowy</option></select>
          <span class="field-hint">★ CA-D1-PROFIL · Profil determinuje wybór sterowania (kaskada vs VSD) i wielkość zbiornika.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-D1-RED</span>
        <span class="field-label">Lokalne reduktory ciśnienia</span>
        <span class="tag ur">UR</span><span class="tag em">EM</span>
      </div>
          <select class="field-select" data-id="CA-D1-RED"><option value="">— wybierz —</option><option>TAK — większość maszyn</option><option>TAK — częściowo</option><option>NIE — wszyscy odbiorcy z ciśnienia sieci</option><option>nie wiem</option></select>
          <span class="field-hint">★ CA-D1-RED · Lokalne reduktory pozwalają obniżyć ciśnienie sieci do minimum krytycznego odbiorcy.</span>
        </div>
        </div>
      </div>

      
      <div class="group">
        <div class="group-title">Grupa 2 — Niewłaściwe użycia (Misuse) — 5 typów</div>
        <div class="field-list">
          
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-D2-CHL</span>
        <span class="field-label">Misuse: chłodzenie sprężonym powietrzem</span>
        <span class="tag ur">UR</span>
      </div>
          <select class="field-select" data-id="CA-D2-CHL"><option value="">— wybierz —</option><option>TAK — powszechnie</option><option>TAK — lokalnie / sporadycznie</option><option>NIE — brak</option><option>nie wiem</option></select>
          <span class="field-hint">★ CA-D2-CHL · Chłodzenie ludzi/urządzeń/paneli sprężarką — bardzo nieefektywne. Powinny być wentylatory lub chłodzenie wodne.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-D2-PRZED</span>
        <span class="field-label">Misuse: przedmuch otwartą rurką</span>
        <span class="tag ur">UR</span>
      </div>
          <select class="field-select" data-id="CA-D2-PRZED"><option value="">— wybierz —</option><option>TAK — powszechnie</option><option>TAK — lokalnie / sporadycznie</option><option>NIE — brak</option><option>nie wiem</option></select>
          <span class="field-hint">★ CA-D2-PRZED · Czyszczenie sprężonym zamiast szczotek/odkurzaczy. Najczęstszy misuse — łatwy do eliminacji.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-D2-MIES</span>
        <span class="field-label">Misuse: mieszanie / napowietrzanie zbiorników</span>
        <span class="tag ur">UR</span>
      </div>
          <select class="field-select" data-id="CA-D2-MIES"><option value="">— wybierz —</option><option>TAK — powszechnie</option><option>TAK — lokalnie / sporadycznie</option><option>NIE — brak</option><option>nie wiem</option></select>
          <span class="field-hint">★ CA-D2-MIES · Mieszanie sprężonym zamiast mieszadeł mechanicznych — kosztowna alternatywa.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-D2-PROZ</span>
        <span class="field-label">Misuse: wytwarzanie próżni ejektorami</span>
        <span class="tag ur">UR</span>
      </div>
          <select class="field-select" data-id="CA-D2-PROZ"><option value="">— wybierz —</option><option>TAK — powszechnie</option><option>TAK — lokalnie / sporadycznie</option><option>NIE — brak</option><option>nie wiem</option></select>
          <span class="field-hint">★ CA-D2-PROZ · Ejektory zasilane sprężarką zamiast pompy próżniowej. Sprawność 5-10% vs pompa 60-80%.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-D2-TRANS</span>
        <span class="field-label">Misuse: transport pneumatyczny</span>
        <span class="tag ur">UR</span>
      </div>
          <select class="field-select" data-id="CA-D2-TRANS"><option value="">— wybierz —</option><option>TAK — powszechnie</option><option>TAK — lokalnie / sporadycznie</option><option>NIE — brak</option><option>nie wiem</option></select>
          <span class="field-hint">★ CA-D2-TRANS · Transport materiałów sprężarką zamiast dmuchaw niskociśnieniowych (3-5x więcej energii).</span>
        </div>
        </div>
      </div>

      
      <div class="group">
        <div class="group-title">Grupa 3 — Nieszczelności i leak test</div>
        <div class="field-list">
          
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-D3-LEAK</span>
        <span class="field-label">Test nieszczelności (leak test) w ostatnich 24m</span>
        <span class="tag em">EM</span>
      </div>
          <select class="field-select" data-id="CA-D3-LEAK"><option value="">— wybierz —</option><option>TAK — w ciągu ostatnich 12m</option><option>TAK — 12-24m temu</option><option>TAK — &gt;24m temu</option><option>NIE — nigdy</option><option>zaplanowany w najbliższych 12m</option></select>
          <span class="field-hint">★ CA-D3-LEAK · Standardowy test detekcji ultradźwiękowej. Powinien być wykonywany co 12-24 miesiące.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-D3-LEAKPCT</span>
        <span class="field-label">Wynik leak test [%]</span>
        <span class="tag em">EM</span>
      </div>
          <input type="number" class="field-input" data-id="CA-D3-LEAKPCT" placeholder="np. 18" step="0.1" min="0" max="100">
          <span class="field-hint">★ CA-D3-LEAKPCT · Tylko jeśli test wykonany. Typowo 5-30% wydajności sprężarek. &gt;10% = priorytet do uszczelnienia. Każdy 1% = ~700 PLN/rok przy 75 kW. [%]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-D3-SLUCH</span>
        <span class="field-label">Słyszalne nieszczelności (na ucho)</span>
        <span class="tag ur">UR</span>
      </div>
          <select class="field-select" data-id="CA-D3-SLUCH"><option value="">— wybierz —</option><option>TAK — liczne (ciągły syk)</option><option>TAK — pojedyncze punkty</option><option>NIE — brak słyszalnych</option><option>trudno powiedzieć</option></select>
          <span class="field-hint">★ CA-D3-SLUCH · Subiektywne — proxy leaków. Jeśli słychać sykanie w cichym otoczeniu = jest co uszczelniać.</span>
        </div>
        </div>
      </div>

    </div>
  </section>


  <!-- ============================================================ -->
  <!-- ETAP 7 · EKSPLOATACJA, KOSZTY, KONSERWACJA -->
  <!-- ============================================================ -->
  <section class="section" id="etap-7">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 7</div>
        <h2 class="section-title serif">Eksploatacja, koszty, konserwacja</h2>
        <p class="section-desc">4 pola READ-ONLY z Master + 6 CA-specific (odzysk ciepła, kWh/rok, koszt serwisu) · czas: 3 min</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-7">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Eksploatacja, koszty, konserwacja — 10 pól (6 CA-spec + 4 RO z Master) · czas: 3 min</strong>
        <ul>
          <li><strong>CA-O2-EE</strong> (roczne kWh) to <strong>NAJWAŻNIEJSZE pole</strong> — bazis dla wszystkich KPI i kosztów.</li>
          <li><strong>Odzysk ciepła</strong> — sprężarki śrubowe oddają 70-90% energii jako ciepło. Brak odzysku &lt;50m od odbiorcy = duża strata.</li>
          <li><strong>Cena EE</strong> automatycznie z Master.E7 — koszt eksploatacji wyliczany na żywo.</li>
        </ul>
      </div>

      <div class="group">
        <div class="group-title">Odzysk ciepła i koszty (CA-specific)</div>
        <div class="field-list">
          
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-O1-OC</span>
        <span class="field-label">Odzysk ciepła ze sprężarek</span>
        <span class="tag ur">UR</span><span class="tag em">EM</span>
      </div>
          
          <select class="field-select" data-id="CA-O1-OC"><option value="">— wybierz —</option><option>TAK — w pełni wykorzystywany</option><option>TAK — częściowo</option><option>TAK — zainstalowany ale nieużywany</option><option>NIE — brak instalacji</option></select>
          <span class="field-hint">★ CA-O1-OC · Sprężarki śrubowe oddają 70-90% energii jako ciepło. Odzysk = duży potencjał oszczędności (CO/CW/proces).</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-O1-OCUSE</span>
        <span class="field-label">Co odzyskuje (jeśli odzysk TAK)</span>
        <span class="tag ur">UR</span><span class="tag em">EM</span>
      </div>
          
          <input type="text" class="field-input" data-id="CA-O1-OCUSE" placeholder="np. CO budynku biurowego, CWU socjalna, podgrzew procesowy">
          <span class="field-hint">★ CA-O1-OCUSE · Konkretny odbiorca odzyskanego ciepła. Wpisać tylko jeśli powyżej TAK.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-O1-OCPOT</span>
        <span class="field-label">Potencjał odzysku (jeśli brak)</span>
        <span class="tag ur">UR</span><span class="tag em">EM</span>
      </div>
          
          <input type="text" class="field-input" data-id="CA-O1-OCPOT" placeholder="np. budynek biurowy w odległości 30 m, hala produkcyjna 50 m">
          <span class="field-hint">★ CA-O1-OCPOT · Identyfikacja odbiorcy w promieniu &lt;50 m od sprężarkowni. Tylko jeśli brak instalacji.</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-O2-EE</span>
        <span class="field-label">Roczne zużycie energii sprężarek [kWh/rok]</span>
        <span class="tag em">EM</span>
      </div>
          
          <input type="number" class="field-input " data-id="CA-O2-EE" placeholder="np. 750000" min="0">
          <span class="field-hint">★ CA-O2-EE · ★ NAJWAŻNIEJSZE pole CA. Z liczników sub-meteringu lub z systemu monitoringu (Asix/SCADA). Bazis dla wszystkich KPI i kosztów. [kWh/rok]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-O3-SERW</span>
        <span class="field-label">Roczny koszt serwisu sprężarek [PLN/rok]</span>
        <span class="tag em">EM</span>
      </div>
          
          <input type="number" class="field-input " data-id="CA-O3-SERW" placeholder="np. 35000" min="0">
          <span class="field-hint">★ CA-O3-SERW · Części + robocizna + oleje + filtry. Z faktur serwisowych za 12 miesięcy. [PLN/rok]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-O3-PREV</span>
        <span class="field-label">Konserwacja prewencyjna</span>
        <span class="tag em">EM</span>
      </div>
          
          <select class="field-select" data-id="CA-O3-PREV"><option value="">— wybierz —</option><option>TAK — pełen plan z harmonogramem</option><option>TAK — częściowy</option><option>NIE — tylko reaktywny serwis</option><option>nie wiem</option></select>
          <span class="field-hint">★ CA-O3-PREV · Plan konserwacji prewencyjnej redukuje awarie i przedłuża żywotność. Brak planu = wyższe koszty serwisu.</span>
        </div>
        </div>
      </div>

      <div class="group readonly-group">
        <div class="group-title">Ceny i Energy Manager (read-only z Master)</div>
        <div class="group-desc">
          <span class="readonly-marker">🔒 RO z Master</span>
          Dane czytane automatycznie z Master.E7 (cena EE), Master.E11 (Energy Manager), Master.E12 (Białe certyfikaty), Master.E0 (cel audytu).
        </div>
        <div class="field-list">
          
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-O2-CENA</span>
        <span class="field-label">Średnia cena en. el. [PLN/MWh]</span>
        <span class="tag spec small">AUTO</span>
      </div>
          <span class="readonly-marker">🔒 RO z Master</span>
          <input type="number" class="field-input field-readonly " data-id="CA-O2-CENA" data-master-source="NOS-EE-CENA-NETTO" readonly placeholder="[Master.NOS-EE-CENA-NETTO]">
          <span class="field-hint">★ CA-O2-CENA · 🔒 Z Master.E7-NOS-EE-CENA-NETTO. Cena netto en. el. w PLN/MWh. [PLN/MWh]</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-O3-EM</span>
        <span class="field-label">Dedykowany Energy Manager</span>
        <span class="tag spec small">AUTO</span>
      </div>
          <span class="readonly-marker">🔒 RO z Master</span>
          <textarea class="field-input field-textarea field-readonly" data-id="CA-O3-EM" data-master-source="KON-V12-EM-DEDIKACJA" readonly placeholder="[Master.KON-V12-EM-DEDIKACJA]"></textarea>
          <span class="field-hint">★ CA-O3-EM · 🔒 Z Master.E11-KON-V12-EM-DEDIKACJA. Czy klient ma dedykowanego EM (% czasu, kompetencje ISO 50001).</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-O4-BC</span>
        <span class="field-label">Białe certyfikaty</span>
        <span class="tag spec small">AUTO</span>
      </div>
          <span class="readonly-marker">🔒 RO z Master</span>
          <select class="field-select field-readonly" data-id="CA-O4-BC" data-master-source="HIS-V3-BC" readonly placeholder="[Master.HIS-V3-BC]"><option value="">— wybierz —</option><option>TAK</option><option>NIE</option><option>w trakcie</option><option>nie wiem</option></select>
          <span class="field-hint">★ CA-O4-BC · 🔒 Z Master.E12-HIS-V3-BC. Czy klient korzystał z mechanizmu Białych Certyfikatów (ESCO/efektywność).</span>
        </div>
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-O4-EED</span>
        <span class="field-label">Cel audytu (EED detection)</span>
        <span class="tag spec small">AUTO</span>
      </div>
          <span class="readonly-marker">🔒 RO z Master</span>
          <input type="text" class="field-input field-readonly" data-id="CA-O4-EED" data-master-source="AUD-V14-CEL" readonly placeholder="[Master.AUD-V14-CEL]">
          <span class="field-hint">★ CA-O4-EED · 🔒 Z Master.E0-AUD-V14-CEL. Cel audytu — jeśli zawiera EED → obowiązek audytu energetycznego.</span>
        </div>
        </div>
      </div>

      <div class="group" style="background: var(--green-bg); border-left: 3px solid var(--green-primary);">
        <div class="group-title" style="color: var(--green-deep);">⚙ Auto-wyliczenie kosztu eksploatacji</div>
        <div class="group-desc">Wzór: <strong>Koszt [PLN/rok]</strong> = (CA-O2-EE [kWh/rok] ÷ 1000) × cena Master.NOS-EE-CENA-NETTO [PLN/MWh]</div>
        <div class="field-list">
          
        <div class="field">
          <div class="field-head">
        <span class="field-id mono">CA-O2-KOSZT</span>
        <span class="field-label">Koszt energii sprężarek [PLN/rok]</span>
        
      </div>
          
          <input type="text" class="field-input field-auto" data-id="CA-O2-KOSZT" readonly placeholder="[wpisz CA-O2-EE i sprawdź Master.NOS-EE-CENA aby auto-wyliczyć]">
          <span class="field-hint">★ CA-O2-KOSZT · AUTO = (kWh/rok ÷ 1000) × cena PLN/MWh. Aktualizuje się gdy zmienisz CA-O2-EE lub gdy Master.NOS-EE-CENA-NETTO się odświeży. [PLN/rok]</span>
        </div>
        </div>
      </div>

    </div>
  </section>


  <!-- ============================================================ -->
  <!-- ETAP 8 · KPI · ~14 CZERWONYCH FLAG CA -->
  <!-- ============================================================ -->
  <section class="section" id="etap-8">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 8</div>
        <h2 class="section-title serif">KPI · ~14 czerwonych flag CA</h2>
        <p class="section-desc">Wskaźniki efektywności + automatyczne flagi dla CA (leak rate, SFP, brak BMS, stare sprężarki, brak osuszacza, niewykorzystane VFD) · podsumowanie audytu CA</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-8">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Jak działają flagi CA:</strong>
        <ul>
          <li>14 reguł sprawdzanych <strong>automatycznie</strong> na podstawie danych z E2-E7</li>
          <li>Flaga 🔴 aktywna = problem energetyczny / techniczny do raportu</li>
          <li>Flaga 🟢 nieaktywna = OK lub brak danych do oceny (potrzeba wpisać)</li>
          <li>Aktualizacja na żywo gdy klient wpisuje dane w innych etapach</li>
        </ul>
      </div>

      <div class="group">
        <div class="group-title">Wskaźniki KPI (auto-wyliczane z E2-E7)</div>

        <div id="kpi-summary" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; margin-top: 14px;">
          <!-- KPI cards generowane dynamicznie -->
        </div>
      </div>

      <div class="group">
        <div class="group-title">14 czerwonych flag (audytora sprężonego powietrza)</div>
        <div class="group-desc">Reguły aktywują się gdy spełnione są warunki energetyczne / techniczne w danych z E2-E7</div>

        <div id="flagi-list" style="margin-top: 14px;">
          <!-- 14 flag generowanych dynamicznie -->
        </div>

        <div style="margin-top: 16px; padding: 12px 16px; background: var(--paper-deep); border-radius: 4px; font-size: 12px; color: var(--ink-soft);">
          <strong>Łącznie aktywnych flag:</strong>
          <span id="flagi-active-count" style="font-weight: 700; color: var(--rose);">0</span>
          z 14 ·
          <strong>Status audytu CA:</strong>
          <span id="audyt-status" style="font-weight: 700;">brak danych</span>
        </div>
      </div>

      <!-- Końcowy banner -->
      <div class="group" style="background: linear-gradient(135deg, var(--green-deep) 0%, var(--green-primary) 100%); color: white; margin-top: 32px; padding: 32px;">
        <div style="font-family: var(--serif); font-size: 22px; font-weight: 600; margin-bottom: 8px;">
          ▮ Koniec scope formularza CA — Sprężone Powietrze
        </div>
        <div style="font-size: 14px; line-height: 1.6; opacity: 0.95;">
          Po wypełnieniu wszystkich etapów CA jest gotowy do <strong>analizy audytora</strong>:
          <ul style="margin: 12px 0 12px 24px; line-height: 1.8;">
            <li><strong>Inwentaryzacja sprężarek</strong> — typy, parametry, motogodziny Total/Load/Unload</li>
            <li><strong>Macierz alokacji</strong> Sprężarki × Wydziały (%) — wymóg EnPI per SEU</li>
            <li><strong>System</strong> — sterowanie sekwencyjne, BMS/SCADA, zbiornik buforowy</li>
            <li><strong>Zasilanie</strong> — transformatory, cos φ, harmoniczne THD, kompensacja</li>
            <li><strong>Treatment</strong> — osuszacze, filtry, klasa ISO 8573</li>
            <li><strong>Sieć dystrybucji</strong> — topologia, materiał, spadki ciśnienia, kondensat</li>
            <li><strong>Odbiorcy</strong> — inwentaryzacja, misuse, leak test</li>
            <li><strong>Eksploatacja</strong> — kWh/rok, odzysk ciepła, plan konserwacji</li>
            <li><strong>14 czerwonych flag CA</strong> — zidentyfikowane problemy do raportu</li>
          </ul>
          Konsultant ENESA przegląda wyniki i przygotowuje rekomendacje + szacunek oszczędności wg ISO 11011:2013.
        </div>
      </div>

    </div>
  </section>




</main>



</div>

<script>
const STORAGE_PREFIX = 'ca:';
const MASTER_PREFIX  = 'master:';
const SAVE_URL  = '{{ $audit->id ? route("client.audit.compressor.questionnaire.ajax-save", $audit) : "" }}';
const CSRF      = '{{ csrf_token() }}';
const MASTER_DATA = @json($masterFormData ?? []);
const FORM_DATA   = @json($answers ?? []);
const AUDIT_ID  = {{ $audit->id ?? 0 }};

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

// ============================================================
// ENESA CA Form - JavaScript (v1.0) — Sprężone Powietrze · scope SCOPE 3
// - Persistent storage przez localStorage
// - Czytanie pól RO z Master Form (klucze master:*)
// - Auto-save z debounce
// - Postęp per etap + globalny
// - Sidenav navigation
// ============================================================

const STORAGE_PREFIX = 'ca:';
const MASTER_PREFIX = 'master:';
let saveTimer = null;
let storageMode = 'localStorage';
let memoryStore = {};

// === Storage detection ===
function detectStorage() {
  try {
    const testKey = '__enesa_test__';
    localStorage.setItem(testKey, '1');
    localStorage.removeItem(testKey);
    storageMode = 'localStorage';
    return true;
  } catch (e) {
    storageMode = 'memory';
    return false;
  }
}

// === Storage API ===
const enesaStorage = {
  set(key, value) {
    if (storageMode === 'localStorage') {
      try { localStorage.setItem(key, value); return true; }
      catch (e) {
        storageMode = 'memory';
        memoryStore[key] = value;
        return true;
      }
    } else {
      memoryStore[key] = value;
      return true;
    }
  },
  get(key) {
    if (storageMode === 'localStorage') {
      try { return localStorage.getItem(key); } catch (e) { return null; }
    } else {
      return memoryStore[key] !== undefined ? memoryStore[key] : null;
    }
  },
  delete(key) {
    if (storageMode === 'localStorage') {
      try { localStorage.removeItem(key); return true; } catch (e) { return false; }
    } else {
      delete memoryStore[key];
      return true;
    }
  },
  listKeys(prefix) {
    if (storageMode === 'localStorage') {
      const keys = [];
      try {
        for (let i = 0; i < localStorage.length; i++) {
          const k = localStorage.key(i);
          if (k && k.startsWith(prefix)) keys.push(k);
        }
      } catch (e) {}
      return keys;
    } else {
      return Object.keys(memoryStore).filter(k => k.startsWith(prefix));
    }
  }
};

// === MASTER INTEGRATION — czytanie pól z HTML Master Form ===
// Klucze master:* są pisane przez HTML Master Form do localStorage.
// AHU czyta je read-only.

function readMasterField(masterFieldId) {
  // Specjalne klucze
  if (masterFieldId === '_meta_n_wydz') {
    return enesaStorage.get(MASTER_PREFIX + '_meta_n_wydz') || '';
  }
  if (masterFieldId === '_meta_wydz_list') {
    // Konkatenacja nazw wydziałów z Master (WYD-V2-NAZWA-W1, W2, ...)
    const n = parseInt(enesaStorage.get(MASTER_PREFIX + '_meta_n_wydz') || '5', 10);
    const names = [];
    for (let w = 1; w <= n; w++) {
      const v = enesaStorage.get(MASTER_PREFIX + 'WYD-V2-NAZWA-W' + w);
      if (v) names.push(v);
    }
    return names.join(', ');
  }
  // Standardowe pole
  return enesaStorage.get(MASTER_PREFIX + masterFieldId);
}

function isMasterConnected() {
  // Sprawdzamy czy w localStorage są jakiekolwiek klucze master:*
  return enesaStorage.listKeys(MASTER_PREFIX).length > 0;
}

function refreshMasterFields() {
  // Aktualizuje wszystkie pola data-master-source
  document.querySelectorAll('[data-master-source]').forEach(el => {
    const source = el.dataset.masterSource;
    const value = readMasterField(source);
    if (value !== null && value !== '') {
      el.value = value;
    } else {
      el.value = '';  // pokaże placeholder
    }
  });
  
  // Status Master w sidenav
  const masterText = document.getElementById('master-status-text');
  if (masterText) {
    if (isMasterConnected()) {
      const nWydz = enesaStorage.get(MASTER_PREFIX + '_meta_n_wydz') || '?';
      const nHal = enesaStorage.get(MASTER_PREFIX + '_meta_n_hal') || '?';
      const klient = enesaStorage.get(MASTER_PREFIX + 'AUD-V1-NAZWA');
      masterText.className = 'master-status-value connected';
      masterText.innerHTML = '✓ podłączony<br>' + 
        (klient ? '<span style="color: var(--paper); font-size: 10px">' + klient.substring(0, 22) + '</span><br>' : '') +
        nWydz + ' wydz · ' + nHal + ' hal';
    } else {
      masterText.className = 'master-status-value disconnected';
      masterText.innerHTML = '⚠ brak danych<br><span style="font-size: 10px">wypełnij Master Form</span>';
    }
  }
  
  // Header zakład
  const headerZaklad = document.getElementById('header-zaklad-name');
  if (headerZaklad) {
    const klient = enesaStorage.get(MASTER_PREFIX + 'AUD-V1-NAZWA');
    headerZaklad.textContent = klient || '[wypełnij Master.E0]';
  }
}

// Helper: wszystkie pola formularza (poza read-only z Master)
function getAllFields() {
  return Array.from(document.querySelectorAll('[data-id]')).filter(el =>
    (el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'TEXTAREA')
    && !el.dataset.masterSource  // RO z Master nie liczymy
  );
}

// === Wczytaj zapisane dane (AHU-specific) ===
function loadSavedData() {
  detectStorage();
  
  const keys = enesaStorage.listKeys(STORAGE_PREFIX);
  let loadedCount = 0;
  
  for (const key of keys) {
    const fieldId = key.replace(STORAGE_PREFIX, '');
    const value = enesaStorage.get(key);
    if (value !== null && value !== undefined && value !== '') {
      const el = document.querySelector(`[data-id="${fieldId}"]`);
      if (el && !el.dataset.masterSource) {
        el.value = value;
        el.classList.add('filled');
        loadedCount++;
      }
    }
  }
  
  if (loadedCount > 0) {
    const mode = storageMode === 'localStorage' ? 'localStorage' : 'pamięć (sesja)';
    showSaveIndicator(`Wczytano ${loadedCount} pól z ${mode}`);
  } else if (storageMode === 'memory') {
    showSaveIndicator('⚠ localStorage zablokowany — dane tylko w sesji');
  }
  
  refreshMasterFields();
  updateAllProgress();
}

function saveField(fieldId, value) {
  if (value === '' || value === null || value === undefined) {
    enesaStorage.delete(STORAGE_PREFIX + fieldId);
  } else {
    enesaStorage.set(STORAGE_PREFIX + fieldId, String(value));
  }
}

function scheduleAutoSave() {
  if (saveTimer) clearTimeout(saveTimer);
  saveTimer = setTimeout(() => {
    const fields = getAllFields();
    let savedCount = 0;
    for (const f of fields) {
      const fieldId = f.dataset.id;
      const value = f.value;
      saveField(fieldId, value);
      if (value !== '' && value !== null) savedCount++;
    }
    const mode = storageMode === 'localStorage' ? 'lokalnie' : 'w sesji';
    showSaveIndicator(`Zapisano ${mode} (${savedCount} pól)`);
  }, 800);
}

function showSaveIndicator(text) {
  const el = document.getElementById('save-indicator');
  if (!el) return;
  el.textContent = text;
  el.classList.add('show');
  setTimeout(() => el.classList.remove('show'), 2000);
}

function updateAllProgress() {
  const sections = document.querySelectorAll('.section');
  let totalFilled = 0;
  let totalAll = 0;

  sections.forEach(sec => {
    const id = sec.id;
    if (!id || !id.startsWith('etap-')) return;
    
    // Liczymy tylko pola NIE-RO (klient sam wypełnia)
    const fields = sec.querySelectorAll('[data-id]:not([data-master-source])');
    let filled = 0;
    fields.forEach(f => {
      const v = (f.value || '').trim();
      if (v !== '' && v !== '— wybierz —') filled++;
    });
    const total = fields.length;
    
    totalFilled += filled;
    totalAll += total;

    const badge = sec.querySelector('.section-progress');
    if (badge) badge.textContent = `${filled} / ${total}`;
    
    const sideCount = document.querySelector(`[data-count-for="${id}"]`);
    if (sideCount) sideCount.textContent = `${filled}/${total}`;
  });
  
  const globalEl = document.getElementById('overall-progress');
  if (globalEl) {
    const pct = totalAll > 0 ? Math.round(totalFilled / totalAll * 100) : 0;
    globalEl.textContent = `${totalFilled} / ${totalAll} pól (${pct}%)`;
  }
}

function onFieldInput(e) {
  const f = e.target;
  if (!f.dataset.id) return;
  if (f.dataset.masterSource) return;  // RO nie zapisujemy
  
  if ((f.value || '').trim()) {
    f.classList.add('filled');
  } else {
    f.classList.remove('filled');
  }
  
  scheduleAutoSave();
  updateAllProgress();
  // Live refresh KPI i flag (kosztowne, ale natychmiast widoczne)
  refreshKpiAndFlagi();
}

// === Sidenav navigation ===
document.querySelectorAll('.sidenav-item').forEach(item => {
  item.addEventListener('click', () => {
    document.querySelectorAll('.sidenav-item').forEach(i => i.classList.remove('active'));
    item.classList.add('active');
    const target = item.dataset.target;
    const el = document.getElementById(target);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting && entry.intersectionRatio > 0.3) {
      const id = entry.target.id;
      document.querySelectorAll('.sidenav-item').forEach(i => {
        i.classList.toggle('active', i.dataset.target === id);
      });
    }
  });
}, { threshold: [0.3, 0.6] });

document.querySelectorAll('.section').forEach(sec => observer.observe(sec));

// === ADD TEAM MEMBER (E0) — analog do Master ===
const addTeamBtn = document.getElementById('add-team-btn');
if (addTeamBtn) {
  addTeamBtn.addEventListener('click', () => {
    const table = document.getElementById('team-table');
    const headerRow = table.querySelector('thead tr');
    const instanceHeaders = headerRow.querySelectorAll('.th-instance');
    const newIdx = instanceHeaders.length + 1;
    const oldSuffix = `-U${instanceHeaders.length}`;
    const newSuffix = `-U${newIdx}`;
    
    const newTh = document.createElement('th');
    newTh.className = 'th-instance';
    newTh.textContent = `Osoba ${newIdx}`;
    headerRow.appendChild(newTh);
    
    const bodyRows = table.querySelectorAll('tbody tr');
    bodyRows.forEach(tr => {
      const tds = tr.querySelectorAll('td.td-input');
      if (tds.length === 0) return;
      const lastTd = tds[tds.length - 1];
      const newTd = lastTd.cloneNode(true);
      newTd.querySelectorAll('[data-id]').forEach(el => {
        const oldId = el.getAttribute('data-id');
        if (oldId && oldId.endsWith(oldSuffix)) {
          el.setAttribute('data-id', oldId.slice(0, -oldSuffix.length) + newSuffix);
        }
        el.value = '';
        el.classList.remove('filled');
      });
      tr.appendChild(newTd);
    });
    
    bindAllFields();
    updateAllProgress();
  });
}

// === CA-specific dynamic logic ===
// Dynamiczne kolumny sprężarek (E2), macierz alokacji E2b, KPI/flagi E8
// → uzupełniane w iteracjach 3 i 5

// ============================================================
// === E2 SPRĘŻARKI — dynamiczne kolumny + nazwy =============
// ============================================================
const STATE_KEY_SPREZ = STORAGE_PREFIX + '_meta_n_sprez';

function getNSprezarek() {
  const v = enesaStorage.get(STATE_KEY_SPREZ);
  return v ? parseInt(v, 10) : 3;
}
function setNSprezarek(n) {
  enesaStorage.set(STATE_KEY_SPREZ, String(n));
}

// Pobierz etykietę sprężarki (numer inwentarzowy + lokalizacja, fallback SPR-i)
function getSprezarkaLabel(i) {
  const inwent = document.querySelector(`[data-id="SPR-V1-INWENT-S${i}"]`);
  const lok = document.querySelector(`[data-id="SPR-V2-LOK-S${i}"]`);
  const inwentVal = inwent && inwent.value ? inwent.value.trim() : '';
  const lokVal = lok && lok.value ? lok.value.trim() : '';
  if (inwentVal && lokVal) return `${inwentVal} (${lokVal})`;
  if (inwentVal) return inwentVal;
  if (lokVal) return `SPR-${i} · ${lokVal}`;
  return `SPR-${i}`;
}

// === ADD SPRĘŻARKA (E2) — dynamiczne dodawanie kolumn ===
const addSprezarkaBtn = document.getElementById('add-sprezarka-btn');
if (addSprezarkaBtn) {
  addSprezarkaBtn.addEventListener('click', () => {
    const table = document.getElementById('sprezarki-table');
    const headerRow = table.querySelector('thead tr');
    const instanceHeaders = headerRow.querySelectorAll('.th-instance');
    const newIdx = instanceHeaders.length + 1;
    const oldSuffix = `-S${instanceHeaders.length}`;
    const newSuffix = `-S${newIdx}`;

    // Nowy nagłówek
    const newTh = document.createElement('th');
    newTh.className = 'th-instance';
    newTh.textContent = `SPR-${newIdx}`;
    headerRow.appendChild(newTh);

    // Klonuj ostatnią komórkę w każdym wierszu (poza row-section-header)
    const bodyRows = table.querySelectorAll('tbody tr');
    bodyRows.forEach(tr => {
      if (tr.classList.contains('row-section-header')) {
        const td = tr.querySelector('td[colspan]');
        if (td) td.setAttribute('colspan', String(newIdx + 2));
        return;
      }
      const tds = tr.querySelectorAll('td.td-input');
      if (tds.length === 0) return;
      const lastTd = tds[tds.length - 1];
      const newTd = lastTd.cloneNode(true);
      newTd.querySelectorAll('[data-id]').forEach(el => {
        const oldId = el.getAttribute('data-id');
        if (oldId && oldId.endsWith(oldSuffix)) {
          el.setAttribute('data-id', oldId.slice(0, -oldSuffix.length) + newSuffix);
        }
        el.value = '';
        el.classList.remove('filled');
      });
      tr.appendChild(newTd);
    });

    setNSprezarek(newIdx);
    bindAllFields();
    rebuildAlokacja();
    updateAllProgress();
    showSaveIndicator(`Dodano SPR-${newIdx}`);
  });
}

// Reagowanie na zmianę nazw sprężarek (V1-INWENT, V2-LOK) → odbuduj nagłówki E2b
function onSprNameChange() {
  rebuildAlokacja();
}


// ============================================================
// === E2b MACIERZ ALOKACJI — Sprężarki × Wydziały (%) ========
// ============================================================
function rebuildAlokacja() {
  const headerRow = document.getElementById('alokacja-header-row');
  const body = document.getElementById('alokacja-body');
  const foot = document.getElementById('alokacja-foot');
  const emptyState = document.getElementById('alokacja-empty-state');
  if (!headerRow || !body || !foot) return;

  const nSprez = getNSprezarek();
  const nWydz = parseInt(enesaStorage.get(MASTER_PREFIX + '_meta_n_wydz') || '5', 10);

  // Pobierz nazwy wydziałów z Master
  const wydzNames = [];
  for (let w = 1; w <= nWydz; w++) {
    const masterName = enesaStorage.get(MASTER_PREFIX + 'WYD-V2-NAZWA-W' + w);
    const masterNum = enesaStorage.get(MASTER_PREFIX + 'WYD-V1-NUMER-W' + w);
    const name = masterName || (masterNum ? `Wydz. ${masterNum}` : `Wydz. ${w}`);
    wydzNames.push(name);
  }

  // Empty state — gdy brak Master (i jednocześnie brak wpisów alok w localStorage)
  const emptyMaster = !isMasterConnected();
  if (emptyState) {
    emptyState.style.display = emptyMaster && nWydz === 0 ? 'block' : 'none';
  }

  // Zachowaj istniejące wartości
  const existing = {};
  body.querySelectorAll('input[data-alok-cell]').forEach(inp => {
    existing[inp.dataset.id] = inp.value;
  });

  // === Header: pusty + wydziały + Σ + Status ===
  while (headerRow.children.length > 1) headerRow.removeChild(headerRow.lastChild);

  for (let w = 1; w <= nWydz; w++) {
    const th = document.createElement('th');
    th.className = 'th-instance';
    th.style.minWidth = '80px';
    th.style.background = 'linear-gradient(to bottom, var(--green-deep) 70%, var(--gold) 100%)';
    th.textContent = wydzNames[w-1];
    th.title = '🔒 z Master.E4 — wydz. ' + w;
    headerRow.appendChild(th);
  }
  // Σ wiersza
  const thSum = document.createElement('th');
  thSum.className = 'th-instance';
  thSum.style.minWidth = '70px';
  thSum.style.background = 'var(--gold)';
  thSum.textContent = 'Σ %';
  headerRow.appendChild(thSum);
  // Status
  const thStatus = document.createElement('th');
  thStatus.className = 'th-instance';
  thStatus.style.minWidth = '80px';
  thStatus.style.background = 'var(--gold)';
  thStatus.textContent = 'Status';
  headerRow.appendChild(thStatus);

  // === Body: wiersze sprężarek ===
  body.innerHTML = '';
  for (let s = 1; s <= nSprez; s++) {
    const tr = document.createElement('tr');

    // Etykieta sprężarki
    const tdS = document.createElement('td');
    tdS.className = 'td-question';
    tdS.innerHTML = `<div class="q-label">${getSprezarkaLabel(s)}</div><div class="q-id mono">SPR-${s}</div>`;
    tr.appendChild(tdS);

    // Komórki % per wydział
    for (let w = 1; w <= nWydz; w++) {
      const td = document.createElement('td');
      td.className = 'td-input';
      const inp = document.createElement('input');
      inp.type = 'number';
      inp.className = 'cell-input alok-cell-input';
      inp.dataset.id = `ALOK-S${s}-W${w}`;
      inp.dataset.alokCell = '1';
      inp.dataset.row = String(s);
      inp.dataset.col = String(w);
      inp.min = '0';
      inp.max = '100';
      inp.step = '1';
      inp.placeholder = '0';
      // Restore z prywatnej cache lub localStorage
      if (existing[inp.dataset.id] !== undefined) {
        inp.value = existing[inp.dataset.id];
      } else {
        const stored = enesaStorage.get(STORAGE_PREFIX + inp.dataset.id);
        if (stored !== null) inp.value = stored;
      }
      if (inp.value && inp.value !== '0') inp.classList.add('filled');
      td.appendChild(inp);
      tr.appendChild(td);
    }

    // Σ wiersza
    const tdSum = document.createElement('td');
    tdSum.className = 'alok-cell-sum';
    tdSum.dataset.alokSumFor = String(s);
    tdSum.textContent = '0%';
    tr.appendChild(tdSum);

    // Status
    const tdStatus = document.createElement('td');
    tdStatus.style.padding = '8px';
    tdStatus.style.textAlign = 'center';
    const badge = document.createElement('span');
    badge.className = 'alok-status-badge empty';
    badge.dataset.alokStatusFor = String(s);
    badge.textContent = '—';
    tdStatus.appendChild(badge);
    tr.appendChild(tdStatus);

    body.appendChild(tr);
  }

  // === Foot: Σ kolumn (informacyjnie) ===
  foot.innerHTML = '';
  if (nWydz > 0) {
    const trFoot = document.createElement('tr');
    const tdLabel = document.createElement('td');
    tdLabel.className = 'td-question';
    tdLabel.style.fontWeight = '700';
    tdLabel.style.background = 'var(--paper-deep)';
    tdLabel.textContent = 'Σ alokacji w wydziale';
    trFoot.appendChild(tdLabel);

    for (let w = 1; w <= nWydz; w++) {
      const td = document.createElement('td');
      td.style.fontWeight = '700';
      td.style.background = 'var(--paper-deep)';
      td.style.textAlign = 'center';
      td.style.fontFamily = 'var(--mono)';
      td.dataset.alokColSumFor = String(w);
      td.textContent = '0%';
      trFoot.appendChild(td);
    }
    // Pusta komórka pod Σ
    const tdEmpty1 = document.createElement('td');
    tdEmpty1.style.background = 'var(--paper-deep)';
    trFoot.appendChild(tdEmpty1);
    // Pusta komórka pod Status
    const tdEmpty2 = document.createElement('td');
    tdEmpty2.style.background = 'var(--paper-deep)';
    trFoot.appendChild(tdEmpty2);

    foot.appendChild(trFoot);
  }

  // Bind nowe inputy
  body.querySelectorAll('input.alok-cell-input').forEach(inp => {
    inp.addEventListener('input', onAlokInput);
  });

  updateAlokSums();
}

function onAlokInput(e) {
  const inp = e.target;
  // Validate 0-100
  let v = parseFloat(inp.value);
  if (!isNaN(v)) {
    if (v < 0) { inp.value = '0'; v = 0; }
    if (v > 100) inp.classList.add('over'); else inp.classList.remove('over');
  } else {
    inp.classList.remove('over');
  }
  if (inp.value && inp.value !== '0') inp.classList.add('filled'); else inp.classList.remove('filled');
  saveField(inp.dataset.id, inp.value);
  scheduleAutoSave();
  updateAlokSums();
  updateAllProgress();
}

function updateAlokSums() {
  const nSprez = getNSprezarek();
  const nWydz = parseInt(enesaStorage.get(MASTER_PREFIX + '_meta_n_wydz') || '5', 10);

  let nOk = 0, nWarn = 0, nError = 0, nEmpty = 0;

  // Σ wiersza + status
  for (let s = 1; s <= nSprez; s++) {
    let sum = 0;
    let hasAny = false;
    for (let w = 1; w <= nWydz; w++) {
      const inp = document.querySelector(`input[data-id="ALOK-S${s}-W${w}"]`);
      if (inp && inp.value !== '' && inp.value !== null) {
        const v = parseFloat(inp.value);
        if (!isNaN(v)) {
          sum += v;
          if (v > 0) hasAny = true;
        }
      }
    }
    const tdSum = document.querySelector(`td[data-alok-sum-for="${s}"]`);
    const badge = document.querySelector(`span[data-alok-status-for="${s}"]`);

    let status = 'empty';
    let statusText = '—';
    if (!hasAny) {
      status = 'empty';
      statusText = '—';
      nEmpty++;
    } else if (sum >= 98 && sum <= 102) {
      status = 'ok';
      statusText = 'OK';
      nOk++;
    } else if (sum < 98) {
      status = 'warn';
      statusText = 'Brakuje';
      nWarn++;
    } else {
      status = 'error';
      statusText = 'Nadmiar';
      nError++;
    }

    if (tdSum) {
      tdSum.textContent = sum.toFixed(0) + '%';
      tdSum.className = 'alok-cell-sum ' + (status === 'empty' ? '' : status);
    }
    if (badge) {
      badge.className = 'alok-status-badge ' + status;
      badge.textContent = statusText;
    }
  }

  // Σ kolumn (informacyjnie)
  for (let w = 1; w <= nWydz; w++) {
    let sum = 0;
    for (let s = 1; s <= nSprez; s++) {
      const inp = document.querySelector(`input[data-id="ALOK-S${s}-W${w}"]`);
      if (inp && inp.value) sum += parseFloat(inp.value) || 0;
    }
    const td = document.querySelector(`td[data-alok-col-sum-for="${w}"]`);
    if (td) td.textContent = sum.toFixed(0) + '%';
  }

  // Banner status — globalny
  const banner = document.getElementById('alokacja-status-banner');
  if (banner) {
    if (nError > 0) {
      banner.className = 'alokacja-banner status-error';
      banner.innerHTML = `<strong>⛔ ${nError} sprężark${nError === 1 ? 'a ma' : (nError < 5 ? 'i mają' : ' ma')} alokację &gt; 100%</strong> · popraw aby Σ wiersza = 100%`;
    } else if (nWarn > 0) {
      banner.className = 'alokacja-banner status-warn';
      banner.innerHTML = `<strong>⚠ ${nWarn} sprężark${nWarn === 1 ? 'a' : 'i'}: alokacja &lt; 100%</strong> · uzupełnij brakujące wydziały`;
    } else if (nOk > 0 && nEmpty === 0) {
      banner.className = 'alokacja-banner status-ok';
      banner.innerHTML = `<strong>✓ Wszystkie ${nOk} sprężarki mają poprawną alokację (Σ ≈ 100%)</strong>`;
    } else if (nOk > 0) {
      banner.className = 'alokacja-banner status-ok';
      banner.innerHTML = `<strong>✓ ${nOk} OK</strong> · ${nEmpty} sprężark${nEmpty === 1 ? 'a' : 'i'} bez alokacji`;
    } else {
      banner.className = 'alokacja-banner';
      banner.style.background = 'var(--paper-paper)';
      banner.innerHTML = '<span style="color: var(--ink-mute);">Wpisz % alokacji dla każdej sprężarki — Σ wiersza ma być 100%</span>';
    }
  }
}



// ============================================================
// === E7 AUTO-KOSZT energii sprężarek ========================
// ============================================================
function autoCalcKoszt() {
  // CA-O2-EE: kWh/rok (klient)
  const kwhRok = parseFloat((document.querySelector('[data-id="CA-O2-EE"]') || {}).value) || 0;
  // CA-O2-CENA: PLN/MWh (RO z Master)
  const cenaMWh = parseFloat((document.querySelector('[data-id="CA-O2-CENA"]') || {}).value) || 0;

  // Koszt = (kWh/rok ÷ 1000) × cena PLN/MWh
  const koszt = (kwhRok / 1000) * cenaMWh;

  const target = document.querySelector('[data-id="CA-O2-KOSZT"]');
  if (target) {
    if (koszt > 0) {
      target.value = koszt.toFixed(0) + ' PLN/rok';
      target.style.color = 'var(--green-deep)';
      target.style.fontWeight = '700';
    } else {
      target.value = '';
      target.style.color = '';
      target.style.fontWeight = '';
    }
  }
}


// ============================================================
// === E8 KPI SUMMARY — auto-wyliczane wskaźniki CA ===========
// ============================================================
function buildKpiSummary() {
  const container = document.getElementById('kpi-summary');
  if (!container) return;

  const nSprez = getNSprezarek();
  const kpis = [];

  // === KPI 1: Średnia Specific Power ===
  // Z E3 lub estymacja per sprężarka: PNOM × 60 / Q (kW/(m³/min))
  const sp = parseFloat((document.querySelector('[data-id="CA-B1-SP"]') || {}).value);
  let spClass = '—', spColor = 'var(--ink-mute)', spNote = 'wpisz E3-CA-B1-SP';
  if (!isNaN(sp) && sp > 0) {
    if (sp <= 6.5) { spClass = 'OK (≤6,5)'; spColor = 'var(--ok)'; }
    else if (sp <= 8.5) { spClass = 'Średnia (6,5-8,5)'; spColor = 'var(--gold)'; }
    else { spClass = 'Słaba (>8,5)'; spColor = 'var(--rose)'; }
    spNote = spClass;
  }
  kpis.push({
    label: 'Specific Power',
    value: !isNaN(sp) && sp > 0 ? sp.toFixed(2) : '—',
    unit: 'kW/(m³/min)',
    note: spNote,
    color: spColor
  });

  // === KPI 2: Wskaźnik wykorzystania (Load+Unload)/Total ===
  // Średnia po wszystkich sprężarkach
  let sumPct = 0, countPct = 0;
  for (let s = 1; s <= nSprez; s++) {
    const tot = parseFloat((document.querySelector(`[data-id="SPR-V16-HTOT-S${s}"]`) || {}).value);
    const load = parseFloat((document.querySelector(`[data-id="SPR-V17-HLOAD-S${s}"]`) || {}).value);
    const unl = parseFloat((document.querySelector(`[data-id="SPR-V18-HUNL-S${s}"]`) || {}).value);
    if (tot > 0 && (load > 0 || unl > 0)) {
      const pct = ((load + unl) / tot) * 100;
      if (pct > 0 && pct <= 110) { sumPct += pct; countPct++; }
    }
  }
  const avgPct = countPct > 0 ? (sumPct / countPct) : null;
  let pctNote = '—', pctColor = 'var(--ink-mute)';
  if (avgPct !== null) {
    if (avgPct < 30) { pctNote = 'rezerwowe (<30%)'; pctColor = 'var(--gold)'; }
    else if (avgPct <= 70) { pctNote = 'typowe (30-70%)'; pctColor = 'var(--ok)'; }
    else { pctNote = 'intensywne (>70%) → kandydat na VSD'; pctColor = 'var(--rose)'; }
  }
  kpis.push({
    label: 'Wskaźnik wykorzystania',
    value: avgPct !== null ? avgPct.toFixed(0) : '—',
    unit: '%',
    note: pctNote,
    color: pctColor
  });

  // === KPI 3: Leak rate (z E6) ===
  const leak = parseFloat((document.querySelector('[data-id="CA-D3-LEAKPCT"]') || {}).value);
  let leakNote = 'wpisz E6-CA-D3-LEAKPCT', leakColor = 'var(--ink-mute)';
  if (!isNaN(leak)) {
    if (leak < 5) { leakNote = 'OK (<5%)'; leakColor = 'var(--ok)'; }
    else if (leak <= 10) { leakNote = 'Średni (5-10%)'; leakColor = 'var(--gold)'; }
    else { leakNote = 'Wysoki (>10%) → uszczelnić'; leakColor = 'var(--rose)'; }
  }
  kpis.push({
    label: 'Leak rate',
    value: !isNaN(leak) ? leak.toFixed(1) : '—',
    unit: '%',
    note: leakNote,
    color: leakColor
  });

  // === KPI 4: Σ moc nominalna sprężarek ===
  let sumP = 0;
  for (let s = 1; s <= nSprez; s++) {
    const p = parseFloat((document.querySelector(`[data-id="SPR-V6-PNOM-S${s}"]`) || {}).value);
    if (p > 0) sumP += p;
  }
  kpis.push({
    label: 'Σ moc nominalna sprężarek',
    value: sumP > 0 ? sumP.toFixed(0) : '—',
    unit: 'kW',
    note: nSprez + ' szt sprężarek',
    color: 'var(--green-deep)'
  });

  // === KPI 5: Roczne zużycie EE (z E7) ===
  const kwhRok = parseFloat((document.querySelector('[data-id="CA-O2-EE"]') || {}).value);
  let kwhMWh = !isNaN(kwhRok) && kwhRok > 0 ? (kwhRok / 1000) : null;
  kpis.push({
    label: 'Roczne zużycie EE',
    value: kwhMWh !== null ? kwhMWh.toFixed(1) : '—',
    unit: 'MWh/rok',
    note: !isNaN(kwhRok) && kwhRok > 0 ? kwhRok.toFixed(0) + ' kWh/rok' : 'wpisz E7-CA-O2-EE',
    color: kwhMWh !== null ? 'var(--green-deep)' : 'var(--ink-mute)'
  });

  // === KPI 6: Koszt eksploatacji (auto) ===
  const cenaMWh = parseFloat((document.querySelector('[data-id="CA-O2-CENA"]') || {}).value);
  const koszt = (kwhRok > 0 && cenaMWh > 0) ? (kwhRok / 1000) * cenaMWh : null;
  kpis.push({
    label: 'Koszt eksploatacji',
    value: koszt !== null ? Math.round(koszt).toLocaleString('pl-PL') : '—',
    unit: 'PLN/rok',
    note: koszt !== null ? 'AUTO = kWh × cena Master' : 'wymaga E7 + Master',
    color: koszt !== null ? 'var(--green-deep)' : 'var(--ink-mute)'
  });

  // Render
  container.innerHTML = '';
  kpis.forEach(k => {
    const card = document.createElement('div');
    card.style.cssText = 'background: white; border: 1px solid var(--paper-deep); border-radius: 6px; padding: 14px;';
    card.innerHTML = `
      <div style="font-size: 11px; color: var(--ink-mute); text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 4px">${k.label}</div>
      <div style="font-size: 24px; font-weight: 700; color: ${k.color}; font-family: var(--serif); line-height: 1">
        ${k.value}<span style="font-size: 13px; font-weight: 400; color: var(--ink-mute); margin-left: 4px">${k.unit}</span>
      </div>
      <div style="font-size: 11px; color: ${k.color}; margin-top: 6px; font-style: italic;">${k.note}</div>
    `;
    container.appendChild(card);
  });
}


// ============================================================
// === E8 14 CZERWONYCH FLAG CA ================================
// ============================================================
const FLAGI_DEFS = [
  {id: 'F1',  name: 'SFP > 8,5 kW/(m³/min)',                  rule: 'CA-B1-SP > 8,5',                              desc: 'Słaba sprawność systemu — modernizacja sprężarek / kontrola obciążenia'},
  {id: 'F2',  name: 'Leak rate > 10%',                        rule: 'CA-D3-LEAKPCT > 10',                          desc: 'Priorytet do uszczelnienia — każdy 1% ≈ 700 PLN/rok przy 75 kW'},
  {id: 'F3',  name: 'Brak monitoringu BMS/SCADA',             rule: 'CA-B1-SCADA = NIE',                            desc: 'Brak danych do EnPI — instalacja monitoringu (wymóg ISO 50001)'},
  {id: 'F4',  name: 'Stare sprężarki (>15 lat)',              rule: 'SPR-V9-ROK ≤ rok bież. − 15',                  desc: 'Wymiana — niskie sprawności, brak VSD, drogie części'},
  {id: 'F5',  name: 'Brak osuszacza',                          rule: 'CA-T1-OSU = brak',                             desc: 'Wilgoć w sieci → korozja, awarie końcówek pneumatycznych'},
  {id: 'F6',  name: 'Niewykorzystane VFD/VSD',                 rule: 'wszystkie sprężarki = load/unload + profil zmienny', desc: 'Modernizacja sterowania na VSD/VFD — 15-30% oszczędności'},
  {id: 'F7',  name: 'Praca 24/7 + zakład nie 24/7',           rule: 'CA-B1-NOC > 16 + tryb pracy ≠ 24/7',           desc: 'Setback nocny / izolacja stref nocą — duże oszczędności (eliminacja leaków)'},
  {id: 'F8',  name: 'Misuse obecny (≥1 z 5 typów)',           rule: 'CA-D2-* zawiera TAK',                          desc: 'Eliminacja niewłaściwych użyć — 10-30% oszczędności'},
  {id: 'F9',  name: 'Brak odzysku ciepła',                     rule: 'CA-O1-OC = NIE',                               desc: 'Sprężarki śrubowe oddają 70-90% jako ciepło — duża strata'},
  {id: 'F10', name: 'cos φ < 0,95',                            rule: 'E1-COSF < 0,95',                               desc: 'Opłaty za moc bierną — kompensacja regulatorem'},
  {id: 'F11', name: 'THD > 8% prądu',                          rule: 'E1-THD > 8',                                   desc: 'Zakłócenia harmoniczne — instalacja filtrów AHF'},
  {id: 'F12', name: 'Brak separatora olej-woda',               rule: 'CA-N1-SEPAR = NIE',                            desc: 'Naruszenie ochrony środowiska — instalacja separatora'},
  {id: 'F13', name: 'Spusty czasowe (źle ustawione)',          rule: 'CA-N1-SPUST = czasowe',                        desc: 'Ciągły wyciek powietrza — wymiana na elektroniczne zero-loss'},
  {id: 'F14', name: 'Sieć ze stali czarnej > 25 lat',          rule: 'CA-N1-MAT = stal czarna + CA-N1-WIEK > 25',    desc: 'Korozja wewnętrzna — straty ciśnienia + zanieczyszczenia. Modernizacja na Al/nierdzewkę'},
];

const ROK_BIEZ = new Date().getFullYear();

function checkFlagi() {
  const nSprez = getNSprezarek();
  const results = {};

  // Helper — wartość pola lub null
  const val = (id) => {
    const el = document.querySelector(`[data-id="${id}"]`);
    return el ? el.value : null;
  };
  const num = (id) => {
    const v = parseFloat(val(id));
    return isNaN(v) ? null : v;
  };

  // F1: SFP > 8,5
  const sp = num('CA-B1-SP');
  if (sp !== null && sp > 8.5) results['F1'] = true;

  // F2: Leak rate > 10%
  const leak = num('CA-D3-LEAKPCT');
  if (leak !== null && leak > 10) results['F2'] = true;

  // F3: Brak BMS/SCADA
  const scada = val('CA-B1-SCADA');
  if (scada && scada.indexOf('NIE') === 0) results['F3'] = true;

  // F4: Stare sprężarki (>15 lat) — przynajmniej 1 sprężarka
  for (let s = 1; s <= nSprez; s++) {
    const rok = num(`SPR-V9-ROK-S${s}`);
    if (rok !== null && rok > 0 && (ROK_BIEZ - rok) > 15) {
      results['F4'] = true;
      break;
    }
  }

  // F5: Brak osuszacza
  const osu = val('CA-T1-OSU');
  if (osu === 'brak osuszacza') results['F5'] = true;

  // F6: Niewykorzystane VFD — wszystkie sprężarki na load/unload + profil zmienny
  let allLoadUnload = true, anySprezData = false;
  for (let s = 1; s <= nSprez; s++) {
    const ster = val(`SPR-V22-STER-S${s}`);
    if (ster) {
      anySprezData = true;
      if (ster.indexOf('VSD') !== -1 || ster.indexOf('VFD') !== -1 || ster.indexOf('modulacja') !== -1) {
        allLoadUnload = false;
        break;
      }
    }
  }
  const profil = val('CA-D1-PROFIL');
  const profilZmienny = profil && (profil.indexOf('zmienny') !== -1 || profil.indexOf('szczytowy') !== -1 || profil.indexOf('nieregularny') !== -1);
  if (anySprezData && allLoadUnload && profilZmienny) results['F6'] = true;

  // F7: NOC > 16 h/tydz + tryb pracy zakładu ≠ 24/7
  const noc = num('CA-B1-NOC');
  const tryb = val('CTX-V2-ZM');  // RO z Master
  const trybNon247 = tryb && tryb.indexOf('24') === -1;
  if (noc !== null && noc > 16 && trybNon247) results['F7'] = true;

  // F8: Misuse — ≥1 z 5 ma "TAK"
  const misuseIds = ['CA-D2-CHL', 'CA-D2-PRZED', 'CA-D2-MIES', 'CA-D2-PROZ', 'CA-D2-TRANS'];
  for (const mid of misuseIds) {
    const v = val(mid);
    if (v && v.indexOf('TAK') === 0) {
      results['F8'] = true;
      break;
    }
  }

  // F9: Brak odzysku ciepła
  const oc = val('CA-O1-OC');
  if (oc && oc.indexOf('NIE') === 0) results['F9'] = true;

  // F10: cos φ < 0,95
  const cosf = num('E1-COSF');
  if (cosf !== null && cosf > 0 && cosf < 0.95) results['F10'] = true;

  // F11: THD > 8%
  const thd = num('E1-THD');
  if (thd !== null && thd > 8) results['F11'] = true;

  // F12: Brak separatora
  const separ = val('CA-N1-SEPAR');
  if (separ && separ.indexOf('NIE') === 0) results['F12'] = true;

  // F13: Spusty czasowe
  const spust = val('CA-N1-SPUST');
  if (spust && spust.indexOf('czasowe') === 0) results['F13'] = true;

  // F14: Stal czarna + sieć > 25 lat
  const mat = val('CA-N1-MAT');
  const wiek = num('CA-N1-WIEK');
  if (mat && mat.indexOf('stal czarna') === 0 && wiek !== null && wiek > 25) results['F14'] = true;

  return results;
}

function buildFlagi() {
  const container = document.getElementById('flagi-list');
  if (!container) return;

  const results = checkFlagi();
  let activeCount = 0;

  container.innerHTML = '';
  FLAGI_DEFS.forEach(f => {
    const isOn = !!results[f.id];
    if (isOn) activeCount++;

    const row = document.createElement('div');
    row.className = 'flag-row ' + (isOn ? 'flag-on' : 'flag-off');
    row.innerHTML = `
      <div class="flag-id">${f.id}</div>
      <div>
        <div class="flag-name">${f.name}</div>
        <div class="flag-rule">${f.desc}</div>
      </div>
      <div style="font-size: 11px; color: var(--ink-mute); font-style: italic">${f.rule}</div>
      <div class="flag-status ${isOn ? 'on' : 'off'}">${isOn ? '🔴 AKTYWNA' : '🟢 OK'}</div>
    `;
    container.appendChild(row);
  });

  // Counter w sekcji
  const cnt = document.getElementById('flagi-active-count');
  if (cnt) cnt.textContent = String(activeCount);

  // Sidenav counter
  const sidenavFlagCount = document.getElementById('sidenav-flag-count');
  if (sidenavFlagCount) sidenavFlagCount.textContent = `${activeCount}/14`;

  // Status audytu
  const statusEl = document.getElementById('audyt-status');
  if (statusEl) {
    if (activeCount === 0) {
      statusEl.textContent = 'brak danych lub OK';
      statusEl.style.color = 'var(--ink-mute)';
    } else if (activeCount <= 3) {
      statusEl.textContent = '✓ Niski (≤3 flagi)';
      statusEl.style.color = 'var(--ok)';
    } else if (activeCount <= 7) {
      statusEl.textContent = '⚠ Średni (4-7 flag)';
      statusEl.style.color = 'var(--gold)';
    } else {
      statusEl.textContent = '⚠⚠ Wysoki (>7 flag)';
      statusEl.style.color = 'var(--rose)';
    }
  }

  // Section progress: aktywne / 14
  const sectionProg = document.querySelector('.section-progress[data-etap="etap-8"]');
  if (sectionProg) sectionProg.textContent = `${activeCount} / 14`;
}

function refreshKpiAndFlagi() {
  if (typeof autoCalcKoszt === 'function') autoCalcKoszt();
  if (typeof buildKpiSummary === 'function') buildKpiSummary();
  if (typeof buildFlagi === 'function') buildFlagi();
}


// === Restore dynamic state CA ===
function restoreDynamicCa() {
  // Przywróć liczbę sprężarek (jeśli zapisano > 3)
  const targetSprez = getNSprezarek();
  const sprezTable = document.getElementById('sprezarki-table');
  if (sprezTable) {
    const currentSprez = sprezTable.querySelectorAll('thead tr .th-instance').length;
    for (let i = currentSprez; i < targetSprez; i++) {
      document.getElementById('add-sprezarka-btn')?.click();
    }
  }
}

function bindAllFields() {
  document.querySelectorAll('[data-id]').forEach(el => {
    if ((el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'TEXTAREA')
        && !el.dataset.masterSource) {
      el.removeEventListener('input', onFieldInput);
      el.addEventListener('input', onFieldInput);
      el.removeEventListener('change', onFieldInput);
      el.addEventListener('change', onFieldInput);
    }
  });
  // Listener: zmiana nazw sprężarek (V1-INWENT, V2-LOK) → rebuild nagłówków E2b
  document.querySelectorAll('.spr-name-input').forEach(el => {
    el.removeEventListener('input', onSprNameChange);
    el.addEventListener('input', onSprNameChange);
  });
}

// === Periodic refresh of Master fields (klient mógł otworzyć Master w innej zakładce) ===
setInterval(() => {
  refreshMasterFields();
  // Master mógł się zmienić (np. dodano wydział) → odśwież nagłówki macierzy E2b
  if (typeof rebuildAlokacja === 'function') rebuildAlokacja();
  if (typeof refreshKpiAndFlagi === 'function') refreshKpiAndFlagi();
}, 5000);

// === INIT ===
restoreDynamicCa();    // odbuduj N sprężarek (jeśli zapisano > 3)
bindAllFields();
loadSavedData();
refreshMasterFields();
rebuildAlokacja();     // zbuduj macierz E2b
refreshKpiAndFlagi();  // E7 auto-koszt + E8 KPI + 14 flag CA


</script>
</x-layouts.app>