<x-layouts.app>
@php
$_companyData = null;
if (isset($company) && $company) {
    $_auditorName = '';
    $_auditorEmail = '';
    $_auditorPhone = '';
    // Audytor przydzielony do firmy (auditor_id), fallback: assignedUsers z rolą audytor
    // 1. auditor_id na Company (ustawiany przez sync przy edycji audytu)
    $_auditor = $company->auditor;
    // 2. fallback: audytor z ostatniego EnergyAudit tej firmy
    if (!$_auditor && $company->relationLoaded('energyAudits')) {
        $_auditor = $company->energyAudits
            ->whereNotNull('auditor_id')
            ->sortByDesc('updated_at')
            ->first()
            ?->auditor;
    }
    // 3. fallback: assignedUsers z rolą audytor
    if (!$_auditor && method_exists($company, 'assignedUsers')) {
        $_auditor = $company->assignedUsers->first(fn($u) => ($u->role->value ?? $u->role) === 'auditor');
    }
    if ($_auditor) { $_auditorName = $_auditor->name; $_auditorEmail = $_auditor->email; $_auditorPhone = $_auditor->phone ?? ''; }
    // Nr oferty z ostatniego audytu powiązanego z ofertą
    $_offerNumber = '';
    if ($company->relationLoaded('energyAudits')) {
        $_offerNumber = $company->energyAudits
            ->whereNotNull('offer_id')
            ->sortByDesc('updated_at')
            ->first()
            ?->offer
            ?->offer_number ?? '';
    }
    // Osoba rejestrująca firmę — client (fallback jeśli brak CompanyContact)
    $_client  = $company->client ?? null;
    $_contact = $company->contacts->first();
    $_companyData = [
        'name'          => $company->name ?? '',
        'nip'           => $company->nip ?? '',
        'regon'         => $company->regon ?? '',
        'street'        => $company->street ?? '',
        'postalCode'    => $company->postal_code ?? '',
        'city'          => $company->city ?? '',
        'address'       => implode(', ', array_filter([$company->street ?? '', $company->postal_code ?? ''])), // legacy
        'auditorName'   => $_auditorName,
        'auditorEmail'  => $_auditorEmail,
        'auditorPhone'  => $_auditorPhone,
        'offerNumber'   => $_offerNumber,
        'krs'           => $company->krs ?? '',
        // Kontakt: CompanyContact → jeśli brak, użyj klienta (osoby rejestrującej)
        'contactName'   => optional($_contact)->name  ?: optional($_client)->name  ?? '',
        'contactEmail'  => optional($_contact)->email ?: optional($_client)->email ?? '',
        'contactPhone'  => optional($_contact)->phone ?: optional($_client)->phone ?? '',
    ];
}
$_teamMembers = [];
if (isset($currentUser) && $currentUser) {
    $_teamMembers[] = ['name' => $currentUser->name, 'email' => $currentUser->email, 'role' => 'Audytor prowadzÄ…cy'];
}
@endphp
@if(isset($isStaff) && $isStaff && isset($company) && $company)
<div style="background:#1d4f73;color:#fff;padding:8px 20px;font-size:13px;display:flex;align-items:center;gap:12px;">
  <span>&#9881; Tryb administratora: {{ $company->name }}</span>
  <a href="{{ route('firma.show', $company) }}" style="color:#a0d4f5;margin-left:auto;">&#8592; Wr&#243;&#263; do firmy</a>
</div>
@elseif(isset($isStaff) && $isStaff)
<div style="background:#0f2330;color:#ccc;padding:8px 20px;font-size:13px;">Tryb podgl&#261;du &#8212; dane nie s&#261; przypisane do konkretnej firmy</div>
@endif

<div class="mode-enesa-banner">⚠ TRYB AUDYTORA ENESA — wszystkie pola widoczne · używaj tylko jeśli jesteś z zespołu audytowego</div>

<style>

/* === ENESA palette - identyczna jak w LH HTML === */
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

/* === Watermark POUFNE === */
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

/* === Sidenav 200px === */
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
.section-meta {
  text-align: right;
  flex-shrink: 0;
}
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

/* === Field row (lista pól) === */
.field {
  display: grid;
  grid-template-columns: 220px 1fr 280px 60px;
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

/* === Save indicator — mały toast prawy górny róg === */
.save-indicator {
  position: fixed;
  top: 12px;
  right: 16px;
  background: rgba(26,77,58,0.92);
  color: #d4edda;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 500;
  opacity: 0;
  transform: translateY(-6px);
  transition: opacity 0.25s, transform 0.25s;
  z-index: 10001;
  pointer-events: none;
  white-space: nowrap;
  max-width: 220px;
  overflow: hidden;
  text-overflow: ellipsis;
  box-shadow: 0 2px 8px rgba(0,0,0,0.18);
}
.save-indicator.show {
  opacity: 1;
  transform: translateY(0);
}

/* === Responsive === */
@media (max-width: 1024px) {
  .sidenav { width: 60px; padding-top: 12px; }
  .sidenav-brand .sidenav-logo { font-size: 16px; }
  .sidenav-brand .sidenav-sub { display: none; }
  .sidenav-name, .sidenav-count { display: none; }
  .sidenav-item { justify-content: center; padding: 12px; }
  .main {  }
  .header, .section { padding-left: 24px; padding-right: 24px; }
  .field { grid-template-columns: 1fr; gap: 6px; }
  .field-unit { text-align: left; padding-top: 0; }
}

.enesa-form-body { display: flex; min-height: calc(100vh - 60px); position: relative; }
.enesa-form-body::before { content: 'POUFNE'; position: fixed; top: 50%; left: 50%; transform: translate(-50%,-50%) rotate(-45deg); font-size: 120px; font-weight: 900; color: rgba(0,0,0,0.03); pointer-events: none; z-index: 0; letter-spacing: 0.2em; }
/* === Master E13 === */
.scope-matrix-wrapper {
  overflow-x: auto;
  border: 1px solid var(--paper-deep, #d6cdb6);
  border-radius: 4px;
  background: white;
  margin-top: 12px;
}
.scope-matrix {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}
.scope-matrix th, .scope-matrix td {
  border: 1px solid var(--paper-deep, #d6cdb6);
  padding: 10px 12px;
  vertical-align: top;
  text-align: left;
}
.scope-matrix th {
  background: var(--green-soft, #c8d5c2);
  color: var(--green-deep, #1a4d3a);
  font-family: var(--serif);
  font-weight: 600;
  font-size: 13px;
  text-align: center;
}
.scope-matrix .row-cat-header td {
  background: var(--green-deep, #1a4d3a);
  color: white;
  font-family: var(--serif);
  font-size: 13px;
  letter-spacing: 0.4px;
  padding: 8px 12px;
}
.scope-matrix .td-scope-kod {
  background: var(--paper-deep, #f5efe2);
  text-align: center;
  font-family: var(--mono);
  width: 80px;
}
.scope-matrix .td-scope-kod strong {
  font-size: 13px;
  color: var(--green-deep, #1a4d3a);
  font-weight: 700;
}
.scope-matrix .td-scope-name {
  font-size: 12px;
}
.scope-matrix .td-scope-name strong {
  font-size: 13px;
  color: var(--ink, #2d2a24);
  display: block;
  margin-bottom: 2px;
}
.scope-matrix .scope-desc {
  font-size: 11px;
  color: var(--ink-mute, #8b7355);
  font-style: italic;
  line-height: 1.4;
  margin-top: 2px;
}
.scope-matrix .td-scope-exist,
.scope-matrix .td-scope-audit {
  text-align: center;
  vertical-align: middle;
}
.scope-select {
  width: 100%;
  padding: 5px 8px;
  font-size: 12px;
  border: 1px solid var(--paper-deep, #d6cdb6);
  background: white;
  font-family: var(--sans);
}

/* Status badges */
.scope-status-badge {
  display: inline-block;
  font-size: 10px;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 3px;
  letter-spacing: 0.3px;
  margin-top: 4px;
}
.scope-status-badge.status-ready {
  background: rgba(46, 204, 113, 0.15);
  color: #1a8a4f;
}
.scope-status-badge.status-pending {
  background: rgba(231, 195, 73, 0.2);
  color: #8a6f1a;
}

/* Sidenav E13 — wyróżnij */
.sidenav-e13 {
  border-top: 1px dashed var(--paper-deep, #d6cdb6);
  margin-top: 4px;
  padding-top: 4px;
}
.sidenav-e13 .sidenav-name {
  font-weight: 600;
}


/* === Master E13 Dashboard Scope === */
.scope-card {
  background: white;
  border: 1px solid var(--paper-deep, #d6cdb6);
  border-radius: 6px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  transition: all 0.2s;
}
.scope-card:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  border-color: var(--green-primary, #1a4d3a);
}
.scope-card.is-active {
  border-left: 4px solid var(--green-primary, #1a4d3a);
}
.scope-card.is-not-audited {
  opacity: 0.55;
  background: var(--paper-deep, #f5efe2);
}
.scope-card.is-completed {
  border-left: 4px solid var(--ok, #4a8a5e);
  background: rgba(74, 138, 94, 0.04);
}
.scope-card.has-flags {
  border-left: 4px solid var(--rose, #c87a5e);
}
.scope-card-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.scope-card-icon {
  font-size: 22px;
  flex-shrink: 0;
}
.scope-card-name {
  font-family: var(--serif);
  font-size: 14px;
  font-weight: 600;
  color: var(--ink, #2d2a24);
  flex: 1;
  line-height: 1.3;
}
.scope-card-kod {
  font-family: var(--mono);
  font-size: 10px;
  background: var(--paper-deep, #f5efe2);
  padding: 2px 6px;
  border-radius: 3px;
  color: var(--ink-mute, #8b7355);
  letter-spacing: 0.4px;
}

/* Progress bar */
.scope-progress-wrap {
  background: var(--paper-deep, #f5efe2);
  border-radius: 3px;
  overflow: hidden;
  height: 6px;
  margin-top: 4px;
}
.scope-progress-bar {
  height: 100%;
  background: var(--green-primary, #1a4d3a);
  transition: width 0.3s;
}
.scope-progress-bar.complete { background: var(--ok, #4a8a5e); }
.scope-progress-bar.warn { background: var(--gold, #c8a951); }
.scope-progress-bar.low { background: var(--rose, #c87a5e); }

.scope-stats-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 11px;
  color: var(--ink-mute, #8b7355);
  margin-top: 2px;
}
.scope-stats-row strong {
  color: var(--ink, #2d2a24);
  font-weight: 700;
}
.scope-flag-count {
  font-size: 11px;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 3px;
  letter-spacing: 0.3px;
}
.scope-flag-count.ok { background: rgba(74, 138, 94, 0.15); color: #1a8a4f; }
.scope-flag-count.warn { background: rgba(231, 195, 73, 0.2); color: #8a6f1a; }
.scope-flag-count.danger { background: rgba(200, 122, 94, 0.2); color: #8a3f1a; }

.scope-time-est {
  font-size: 11px;
  color: var(--ink-soft, #5e5347);
  font-style: italic;
  margin-top: 4px;
}
.scope-card-actions {
  display: flex;
  gap: 6px;
  margin-top: 8px;
}
.scope-btn {
  flex: 1;
  padding: 6px 10px;
  font-size: 12px;
  border-radius: 4px;
  border: 1px solid var(--paper-deep, #d6cdb6);
  background: white;
  color: var(--ink, #2d2a24);
  cursor: pointer;
  font-family: var(--sans);
  text-decoration: none;
  text-align: center;
  display: inline-block;
  transition: all 0.15s;
}
.scope-btn:hover {
  background: var(--green-primary, #1a4d3a);
  color: white;
  border-color: var(--green-primary, #1a4d3a);
}
.scope-btn.primary {
  background: var(--green-primary, #1a4d3a);
  color: white;
  border-color: var(--green-primary, #1a4d3a);
}
.scope-btn.primary:hover {
  background: var(--green-deep, #0a2c20);
}
.scope-btn.secondary {
  background: var(--paper-deep, #f5efe2);
  color: var(--ink-mute, #8b7355);
}
.scope-btn:disabled, .scope-btn.disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}

.scope-status-line {
  font-size: 11px;
  color: var(--ink-mute, #8b7355);
  font-style: italic;
}
.scope-status-line.ready { color: var(--ok, #4a8a5e); font-weight: 600; }
.scope-status-line.pending { color: var(--gold, #c8a951); font-weight: 600; }

/* Overall summary */
#dashboard-overall {
  font-family: var(--sans);
}
#dashboard-overall .ds-row {
  display: flex;
  flex-wrap: wrap;
  gap: 18px;
  align-items: center;
}
#dashboard-overall .ds-stat {
  display: flex;
  flex-direction: column;
}
#dashboard-overall .ds-stat-label {
  font-size: 10px;
  text-transform: uppercase;
  color: var(--ink-mute, #8b7355);
  letter-spacing: 0.5px;
}
#dashboard-overall .ds-stat-value {
  font-size: 18px;
  font-weight: 700;
  font-family: var(--serif);
  color: var(--green-deep, #0a2c20);
}


/* === Master E13 Iter 3 — Toolbar, Print, Validation === */
.e13-toolbar {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  align-items: center;
  background: var(--paper-deep, #f5efe2);
  border-radius: 6px;
  padding: 12px 14px;
  margin-bottom: 18px;
  border-left: 3px solid var(--green-primary, #1a4d3a);
}
.e13-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  font-size: 13px;
  border-radius: 4px;
  border: 1px solid var(--paper-deep, #d6cdb6);
  background: white;
  color: var(--ink, #2d2a24);
  cursor: pointer;
  font-family: var(--sans);
  font-weight: 500;
  transition: all 0.15s;
}
.e13-btn:hover {
  background: var(--green-primary, #1a4d3a);
  color: white;
  border-color: var(--green-primary, #1a4d3a);
}
.e13-btn-primary {
  background: var(--green-primary, #1a4d3a);
  color: white;
  border-color: var(--green-primary, #1a4d3a);
  font-weight: 600;
}
.e13-btn-primary:hover {
  background: var(--green-deep, #0a2c20);
}
.e13-btn-secondary {
  background: white;
}
.e13-toolbar-info {
  flex: 1;
  font-size: 11px;
  color: var(--ink-mute, #8b7355);
  font-style: italic;
  margin-left: 8px;
}

/* Validation result */
.validation-result {
  margin-top: 16px;
  padding: 14px 18px;
  border-radius: 6px;
  font-size: 13px;
  line-height: 1.6;
}
.validation-result.ok {
  background: rgba(74, 138, 94, 0.1);
  border-left: 3px solid var(--ok, #4a8a5e);
  color: #1a4d3a;
}
.validation-result.warn {
  background: rgba(231, 195, 73, 0.15);
  border-left: 3px solid var(--gold, #c8a951);
  color: #5a4d10;
}
.validation-result.error {
  background: rgba(200, 122, 94, 0.12);
  border-left: 3px solid var(--rose, #c87a5e);
  color: #5a2010;
}
.validation-result h4 {
  margin: 0 0 8px 0;
  font-family: var(--serif);
  font-size: 15px;
}
.validation-result ul {
  margin: 4px 0 0 24px;
  padding: 0;
}
.validation-result li {
  margin: 4px 0;
}

/* Save indicator — nadpisany przez .save-indicator w górnej sekcji CSS */

/* Print stylesheet — E13 na 1 stronę A4 */
@media print {
  /* Ukryj wszystko poza E13 */
  body * {
    visibility: hidden;
  }
  #etap-13, #etap-13 * {
    visibility: visible;
  }
  #etap-13 {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    padding: 0;
    margin: 0;
  }
  /* Ukryj toolbar + dashboard wrapper przy druku */
  .e13-toolbar, #scope-dashboard-wrapper, #e13-summary, .group-info,
  .validation-result, #global-toolbar {
    display: none !important;
  }
  /* Sidenav */
  nav, .sidenav, .header, .footer {
    display: none !important;
  }
  /* Tabela na druku */
  .scope-matrix {
    font-size: 10pt;
    page-break-inside: avoid;
  }
  .scope-matrix th, .scope-matrix td {
    padding: 4pt 6pt;
  }
  .scope-status-badge { display: none; }
  .scope-desc { font-size: 8pt; }
  .scope-matrix .row-cat-header td {
    background: #1a4d3a !important;
    color: white !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  .scope-select {
    border: none;
    background: transparent !important;
    font-size: 9pt;
    -webkit-appearance: none;
    appearance: none;
  }
  /* SCOPE-V2-OTHER textarea */
  textarea {
    border: 1px solid #999 !important;
    background: white !important;
  }
  /* Strona */
  @page {
    size: A4;
    margin: 12mm;
  }
}

/* === Global Toolbar (Eksport/Import/Print/Validate) — globalny pod headerem === */
.global-toolbar {
  background: linear-gradient(135deg, var(--paper-deep, #f5efe2) 0%, var(--paper, #faf6ec) 100%);
  border-radius: 8px;
  padding: 16px 20px;
  margin: 0 0 20px 0;
  border: 1px solid var(--paper-deep, #d6cdb6);
  border-left: 4px solid var(--green-primary, #1a4d3a);
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.global-toolbar-title {
  font-family: var(--serif);
  font-size: 14px;
  font-weight: 600;
  color: var(--green-deep, #0a2c20);
  margin-bottom: 10px;
}
.global-toolbar-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  align-items: center;
  margin-bottom: 10px;
}
.global-toolbar-info {
  font-size: 12px;
  color: var(--ink-soft, #5e5347);
  line-height: 1.5;
  font-style: italic;
}
.global-toolbar-info strong {
  font-style: normal;
  color: var(--green-deep, #0a2c20);
}

/* === KRS lookup field (E0) === */
.krs-lookup-field {
  background: linear-gradient(135deg, var(--paper-deep, #ebe3d0) 0%, var(--paper, #f5efe0) 100%);
  border-radius: 6px;
  padding: 12px;
  margin-bottom: 12px;
  border-left: 3px solid var(--gold, #c8a951);
}
.krs-badge {
  display: inline-block;
  font-size: 10px;
  font-weight: 600;
  background: var(--gold, #c8a951);
  color: white;
  padding: 1px 6px;
  border-radius: 3px;
  margin-left: 6px;
  letter-spacing: 0.3px;
  vertical-align: middle;
}
.krs-input-row {
  display: flex;
  gap: 8px;
  align-items: stretch;
}
.krs-input {
  flex: 1;
  font-family: var(--mono, Consolas, monospace);
  font-size: 14px;
  letter-spacing: 1px;
}
.krs-fetch-btn {
  padding: 8px 16px;
  background: var(--green-primary, #1a4d3a);
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  white-space: nowrap;
  font-family: var(--sans);
  transition: all 0.15s;
}
.krs-fetch-btn:hover {
  background: var(--green-deep, #0a2c20);
}
.krs-fetch-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
#krs-status {
  margin-top: 8px;
  font-size: 12px;
  min-height: 0;
}
#krs-status.show {
  padding: 8px 12px;
  border-radius: 4px;
}
#krs-status.ok {
  background: rgba(74, 138, 94, 0.12);
  border-left: 3px solid var(--ok, #4a8a5e);
  color: #1a4d3a;
}
#krs-status.error {
  background: rgba(200, 122, 94, 0.12);
  border-left: 3px solid var(--rose, #c87a5e);
  color: #5a2010;
}
#krs-status.loading {
  background: rgba(231, 195, 73, 0.15);
  border-left: 3px solid var(--gold, #c8a951);
  color: #5a4d10;
}
.krs-spinner {
  display: inline-block;
  width: 12px;
  height: 12px;
  border: 2px solid var(--paper-deep, #d6cdb6);
  border-top-color: var(--green-primary, #1a4d3a);
  border-radius: 50%;
  animation: krs-spin 0.8s linear infinite;
  vertical-align: middle;
  margin-right: 6px;
}
@keyframes krs-spin {
  to { transform: rotate(360deg); }
}

/* Highlight pól wypełnionych z KRS — pulsuje 3 razy */
.krs-filled {
  animation: krs-highlight 2s ease-out 3;
  background: #fffbed !important;
}
@keyframes krs-highlight {
  0%   { background: #fffbed; box-shadow: 0 0 0 0 rgba(231, 195, 73, 0.5); }
  50%  { background: #fff4d0; box-shadow: 0 0 0 6px rgba(231, 195, 73, 0); }
  100% { background: #fffbed; box-shadow: 0 0 0 0 rgba(231, 195, 73, 0); }
}


/* === Meta + Profile audytu (refaktor B) === */

/* Confidence indicators (włączane przez agenta) */
[data-confidence="low"]::before { content: "🔴"; display: none; font-size: 12px; margin-right: 4px; }
[data-confidence="medium"]::before { content: "🟡"; display: none; font-size: 12px; margin-right: 4px; }
[data-confidence="high"]::before, [data-confidence="measured"]::before { 
  content: "🟢"; display: none; font-size: 12px; margin-right: 4px;
}
body.show-confidence [data-confidence="low"]::before,
body.show-confidence [data-confidence="medium"]::before,
body.show-confidence [data-confidence="high"]::before,
body.show-confidence [data-confidence="measured"]::before { display: inline; }

/* Phases (kolorowe ramki - tylko gdy włączone) */
body.show-phases [data-phase="client"] { border-left: 3px solid var(--ok, #4a8a5e); }
body.show-phases [data-phase="agent"] { border-left: 3px solid var(--gold, #c8a951); }
body.show-phases [data-phase="consultant"] { border-left: 3px solid #5b8db3; }

/* Profile audytu - kolorowanie field wg wybranego profilu */
/* Domyślnie: pole nie ma kolorowania profilu */
[data-audit-profile] {
  /* baseline */
}

/* Gdy aktywny profil — pola MUST są zielone, OPTIONAL są wyszarzone */
body.profile-active [data-audit-profile] {
  background-color: rgba(245, 239, 224, 0.3);
}
body.profile-active .field-must {
  border-left: 4px solid var(--ok, #4a8a5e) !important;
  background-color: rgba(74, 138, 94, 0.05) !important;
}
body.profile-active .field-must::after {
  content: " ✓ MUST";
  font-size: 9px;
  color: var(--ok, #4a8a5e);
  font-weight: bold;
  margin-left: 4px;
}
body.profile-active .field-optional {
  opacity: 0.55;
  border-left: 4px solid var(--paper-deep, #ebe3d0);
}
body.profile-active .field-optional::after {
  content: " (opt)";
  font-size: 9px;
  color: var(--ink-mute, #8b7355);
  margin-left: 4px;
}

/* Panel wyboru profilu w Master */
.profile-selector-panel {
  background: linear-gradient(135deg, var(--paper-deep, #ebe3d0) 0%, var(--paper, #f5efe0) 100%);
  border: 2px solid var(--gold, #c8a951);
  border-radius: 8px;
  padding: 16px;
  margin: 20px 0;
}
.profile-selector-panel h3 {
  margin: 0 0 8px 0;
  color: var(--green-deep, #1a4d3a);
  font-family: var(--serif);
  font-size: 16px;
}
.profile-selector-panel .profile-desc {
  font-size: 12px;
  color: var(--ink-soft, #5e5347);
  margin-bottom: 12px;
}
.profile-selector-panel .profile-options {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 10px;
}
.profile-option {
  border: 2px solid var(--paper-deep, #d6cdb6);
  border-radius: 6px;
  padding: 10px 12px;
  cursor: pointer;
  background: white;
  transition: all 0.15s;
}
.profile-option:hover { border-color: var(--gold, #c8a951); }
.profile-option.selected {
  border-color: var(--green-primary, #1a4d3a);
  background: rgba(74, 138, 94, 0.08);
}
.profile-option input[type="radio"] { margin-right: 6px; }
.profile-option .profile-name {
  font-weight: 700;
  color: var(--green-deep, #1a4d3a);
  font-size: 13px;
}
.profile-option .profile-meta {
  font-size: 11px;
  color: var(--ink-mute, #8b7355);
  margin-top: 4px;
}
.profile-option.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.profile-option.disabled .profile-name::after {
  content: " (wkrótce)";
  font-size: 10px;
  color: var(--gold, #c8a951);
}

.profile-stats {
  margin-top: 12px;
  padding: 8px 12px;
  background: white;
  border-radius: 4px;
  font-size: 12px;
  display: none;
}
body.profile-active .profile-stats { display: block; }

/* ============================================================ */
/* === ARCHITEKTURA C v1.6 — wybór typu audytu + ukrywanie === */
/* ============================================================ */

/* Ekran startowy - karty wyboru audytu */
.audit-type-selector {
  background: linear-gradient(135deg, var(--paper-deep, #ebe3d0) 0%, var(--paper, #f5efe0) 100%);
  border: 2px solid var(--gold, #c8a951);
  border-radius: 12px;
  padding: 24px;
  margin: 20px 0;
}
.audit-type-selector h2 {
  font-family: var(--serif);
  font-size: 22px;
  color: var(--green-deep);
  margin: 0 0 8px 0;
}
.audit-type-selector .selector-subtitle {
  font-size: 14px;
  color: var(--ink-soft);
  margin-bottom: 20px;
}
.audit-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 16px;
  margin: 20px 0;
}
.audit-card {
  background: white;
  border: 2px solid var(--paper-deep);
  border-radius: 8px;
  padding: 18px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
}
.audit-card:hover:not(.disabled) {
  border-color: var(--gold);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(168, 127, 42, 0.15);
}
.audit-card.selected {
  border-color: var(--green-primary);
  background: linear-gradient(135deg, rgba(46, 125, 92, 0.05) 0%, white 100%);
  box-shadow: 0 2px 8px rgba(46, 125, 92, 0.2);
}
.audit-card.selected::before {
  content: '✓';
  position: absolute;
  top: 12px;
  right: 12px;
  width: 28px;
  height: 28px;
  background: var(--green-primary);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}
.audit-card.disabled {
  opacity: 0.55;
  cursor: not-allowed;
  background: var(--paper-deep);
}
.audit-card .audit-card-badge {
  position: absolute;
  top: 0;
  left: 0;
  background: var(--gold);
  color: white;
  font-size: 10px;
  font-weight: 700;
  padding: 3px 10px;
  border-bottom-right-radius: 6px;
  letter-spacing: 0.5px;
}
.audit-card .audit-card-name {
  font-family: var(--serif);
  font-size: 18px;
  font-weight: 700;
  color: var(--green-deep);
  margin: 8px 0 4px 0;
}
.audit-card .audit-card-desc {
  font-size: 12px;
  color: var(--ink-soft);
  line-height: 1.4;
  margin-bottom: 14px;
  flex-grow: 1;
}
.audit-card .audit-card-stats {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
  padding: 10px;
  background: var(--paper);
  border-radius: 6px;
  margin-bottom: 12px;
}
.audit-card .audit-card-stat {
  display: flex;
  flex-direction: column;
}
.audit-card .audit-card-stat-label {
  font-size: 9px;
  color: var(--ink-mute);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.audit-card .audit-card-stat-value {
  font-size: 13px;
  font-weight: 600;
  color: var(--ink);
}
.audit-card .audit-card-stat-value.field-count {
  font-family: var(--mono);
  color: var(--green-primary);
  font-size: 15px;
}
.audit-card .audit-card-button {
  padding: 8px 14px;
  background: var(--green-primary);
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  font-size: 13px;
  width: 100%;
  font-family: var(--sans);
}
.audit-card.selected .audit-card-button {
  background: var(--green-deep);
}
.audit-card.selected .audit-card-button::after {
  content: ' — Wybrano';
}
.audit-card.disabled .audit-card-button {
  background: var(--ink-mute);
  cursor: not-allowed;
}

/* Pasek narzędzi */
.audit-actions {
  display: flex;
  gap: 12px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid var(--paper-deep);
  flex-wrap: wrap;
}
.audit-actions button {
  padding: 8px 14px;
  background: white;
  border: 1px solid var(--paper-deep);
  border-radius: 6px;
  font-size: 12px;
  cursor: pointer;
  color: var(--ink-soft);
  font-family: var(--sans);
}
.audit-actions button:hover {
  border-color: var(--gold);
  color: var(--ink);
}

/* Tabela porównawcza */
.audit-comparison-table {
  margin-top: 20px;
  display: none;
}
.audit-comparison-table.visible {
  display: block;
}
.audit-comparison-table table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 6px;
  overflow: hidden;
  font-size: 12px;
}
.audit-comparison-table th,
.audit-comparison-table td {
  padding: 8px 10px;
  text-align: left;
  border-bottom: 1px solid var(--paper-deep);
}
.audit-comparison-table th {
  background: var(--paper-deep);
  font-weight: 600;
  color: var(--green-deep);
}
.audit-comparison-table .check {
  color: var(--ok);
  font-weight: bold;
  text-align: center;
}
.audit-comparison-table .cross {
  color: var(--rose);
  text-align: center;
}

/* TRYB AUDYTORA ENESA */
body.mode-enesa {
  /* Tryb audytora ma wszystko widoczne */
}
.mode-enesa-banner {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  background: linear-gradient(90deg, var(--rose) 0%, var(--gold) 50%, var(--rose) 100%);
  color: white;
  padding: 6px 16px;
  font-size: 12px;
  font-weight: 700;
  text-align: center;
  z-index: 10000;
  letter-spacing: 1px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  display: none;
}
body.mode-enesa .mode-enesa-banner {
  display: block;
}
body.mode-enesa {
  padding-top: 28px;
}
body.mode-enesa nav, 
body.mode-enesa .layout {
  margin-top: 28px;
}

/* UKRYWANIE pól i sekcji spoza profilu */
/* Domyślnie nic nie ukryte (profil-active musi być włączony) */

body.profile-active.profile-eed [data-audit-profile]:not([data-audit-profile*="eed"]),
body.profile-active.profile-white-cert [data-audit-profile]:not([data-audit-profile*="white-cert"]),
body.profile-active.profile-iso50001 [data-audit-profile]:not([data-audit-profile*="iso50001"]),
body.profile-active.profile-full-map [data-audit-profile]:not([data-audit-profile*="full-map"]),
body.profile-active.profile-custom [data-audit-profile]:not([data-audit-profile*="custom"]) {
  display: none !important;
}

/* W trybie audytora - NIE ukrywaj nic, ale wyszarz pola spoza profilu */
body.mode-enesa.profile-active [data-audit-profile] {
  display: revert !important;
}
body.mode-enesa.profile-active.profile-eed [data-audit-profile]:not([data-audit-profile*="eed"]),
body.mode-enesa.profile-active.profile-iso50001 [data-audit-profile]:not([data-audit-profile*="iso50001"]),
body.mode-enesa.profile-active.profile-full-map [data-audit-profile]:not([data-audit-profile*="full-map"]) {
  opacity: 0.4;
  background: rgba(0,0,0,0.02);
}

/* Sekcje (etap-N) bez aktywnych pól - chowamy całą sekcję */
section.section.section-hidden-by-profile {
  display: none !important;
}
/* W trybie "pokaż wszystko" - sekcje wracają z oznaczeniem */
body.show-all-sections section.section.section-hidden-by-profile {
  display: block !important;
  position: relative;
  border: 2px dashed var(--gold);
  background: rgba(168, 127, 42, 0.03);
}
body.show-all-sections section.section.section-hidden-by-profile::before {
  content: '👁️ PODGLĄD — sekcja spoza Twojego profilu audytu (tylko do wglądu, nie wypełniaj)';
  position: sticky;
  top: 0;
  display: block;
  padding: 8px 16px;
  background: var(--gold);
  color: white;
  font-size: 12px;
  font-weight: 600;
  z-index: 100;
  margin: -1px;
}

/* Menu boczne - chowanie linków do sekcji */
nav .nav-link.nav-hidden-by-profile {
  display: none;
}
body.show-all-sections nav .nav-link.nav-hidden-by-profile {
  display: block;
  opacity: 0.6;
  border-left: 3px dashed var(--gold);
}
body.show-all-sections nav .nav-link.nav-hidden-by-profile::after {
  content: ' 👁️';
  font-size: 11px;
}

/* Pasek "Pokaż wszystko" w menu */
.show-all-toggle {
  margin: 12px 8px;
  padding: 8px 12px;
  background: var(--paper-deep);
  border: 1px solid var(--gold);
  border-radius: 6px;
  font-size: 11px;
  cursor: pointer;
  width: calc(100% - 16px);
  font-family: var(--sans);
  color: var(--ink-soft);
}
.show-all-toggle:hover {
  background: var(--gold);
  color: white;
}
.show-all-toggle.active {
  background: var(--gold);
  color: white;
}
.show-all-toggle.active::before {
  content: '👁️ ';
}

/* Progress bar v1.6 - per profil */
.audit-progress-info {
  margin: 10px 8px;
  padding: 8px 12px;
  background: var(--paper);
  border-radius: 6px;
  font-size: 11px;
  color: var(--ink-soft);
  border-left: 3px solid var(--green-primary);
}
.audit-progress-info .progress-line {
  font-weight: 600;
  color: var(--green-deep);
  font-size: 13px;
  font-family: var(--mono);
}
.audit-progress-info .progress-bar-bg {
  height: 6px;
  background: var(--paper-deep);
  border-radius: 3px;
  overflow: hidden;
  margin: 6px 0 4px 0;
}
.audit-progress-info .progress-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--green-light), var(--green-primary));
  transition: width 0.3s;
}

/* ============================================================ */


</style>

<div class="save-indicator" id="save-indicator">Zapisano</div>

<div class="enesa-form-body">


<!-- ====== SIDENAV ====== -->
<nav class="sidenav">
  <div class="sidenav-brand">
    <div class="sidenav-logo serif">ENESA</div>
    <div class="sidenav-sub">Master Form · v1.0</div>
  </div>
  <ul class="sidenav-list">
    <li class="sidenav-item active" data-target="etap-0"><span class="sidenav-num mono">E0</span><span class="sidenav-name">Audyt</span><span class="sidenav-count mono" data-count-for="etap-0">0/20</span></li>
    <li class="sidenav-item" data-target="etap-1"><span class="sidenav-num mono">E1</span><span class="sidenav-name">Zakres</span><span class="sidenav-count mono" data-count-for="etap-1">0/9</span></li>
    <li class="sidenav-item" data-target="etap-2"><span class="sidenav-num mono">E2</span><span class="sidenav-name">Zakład</span><span class="sidenav-count mono" data-count-for="etap-2">0/12</span></li>
    <li class="sidenav-item" data-target="etap-3"><span class="sidenav-num mono">E3</span><span class="sidenav-name">Procesy</span><span class="sidenav-count mono" data-count-for="etap-3">0/10</span></li>
    <li class="sidenav-item" data-target="etap-4"><span class="sidenav-num mono">E4</span><span class="sidenav-name">Wydziały</span><span class="sidenav-count mono" data-count-for="etap-4">0/50</span></li>
    <li class="sidenav-item" data-target="etap-5"><span class="sidenav-num mono">E5</span><span class="sidenav-name">Hale</span><span class="sidenav-count mono" data-count-for="etap-5">0/50</span></li>
    <li class="sidenav-item" data-target="etap-6"><span class="sidenav-num mono">E6</span><span class="sidenav-name">Macierz</span><span class="sidenav-count mono" data-count-for="etap-6">0/25</span></li>
    <li class="sidenav-item" data-target="etap-7"><span class="sidenav-num mono">E7</span><span class="sidenav-name">Nośniki</span><span class="sidenav-count mono" data-count-for="etap-7">0/35</span></li>
    <li class="sidenav-item" data-target="etap-8"><span class="sidenav-num mono">E8</span><span class="sidenav-name">Zużycia</span><span class="sidenav-count mono" data-count-for="etap-8">0/324</span></li>
    <li class="sidenav-item" data-target="etap-9"><span class="sidenav-num mono">E9</span><span class="sidenav-name">Zmienne</span><span class="sidenav-count mono" data-count-for="etap-9">0/10</span></li>
    <li class="sidenav-item" data-target="etap-10"><span class="sidenav-num mono">E10</span><span class="sidenav-name">EnMS</span><span class="sidenav-count mono" data-count-for="etap-10">0/8</span></li>
    <li class="sidenav-item" data-target="etap-11"><span class="sidenav-num mono">E11</span><span class="sidenav-name">Kontekst</span><span class="sidenav-count mono" data-count-for="etap-11">0/12</span></li>
    <li class="sidenav-item" data-target="etap-12"><span class="sidenav-num mono">E12</span><span class="sidenav-name">Historia</span><span class="sidenav-count mono" data-count-for="etap-12">0/10</span></li>
        <li class="sidenav-item sidenav-e13" data-target="etap-13"><span class="sidenav-num mono">E13</span><span class="sidenav-name">Zakres audytu ★</span><span class="sidenav-count mono" data-count-for="etap-13">0/18</span></li>
  </ul>
  <div style="padding: 16px 12px 8px;">
    <button id="btn-save-now" onclick="masterManualSave()" style="
      width:100%; padding:10px 0; background:#2E7D5C; color:#fff;
      border:none; border-radius:6px; font-size:13px; font-weight:700;
      cursor:pointer; letter-spacing:0.03em; transition:background .2s;
    " onmouseover="this.style.background='#1A4D3A'" onmouseout="this.style.background='#2E7D5C'">
      Zapisz dane
    </button>
    <div style="font-size:10px; color:rgba(255,255,255,0.5); text-align:center; margin-top:5px;">
      Autozapis co 30 sek.
    </div>
  </div>
</nav>

<!-- ====== MAIN CONTENT ====== -->
<main class="main">

  <div class="global-toolbar" id="global-toolbar">
    <div class="global-toolbar-title">📋 Operacje na całym formularzu Master:</div>
    <div class="global-toolbar-buttons">
      <button type="button" id="btn-export-excel" class="e13-btn e13-btn-primary" title="Pobierz cały formularz Master (E0-E13) + dane audytowanych scope w jednym pliku Excel — wydrukuj i wypełnij ręcznie na obiekcie">
        📊 Eksport całego Master do Excel
      </button>
      <label class="e13-btn e13-btn-secondary" for="file-import-excel" title="Wczytaj wypełniony plik Excel z powrotem do formularza (nadpisuje obecne dane)">
        📤 Import z Excel
        <input type="file" id="file-import-excel" accept=".xlsx,.xls" style="display:none;">
      </label>
      <button type="button" id="btn-print-e13" class="e13-btn e13-btn-secondary" title="Wydruk samej macierzy E13 (zakres audytu) na 1 stronę A4">
        🖨 Drukuj E13 (zakres)
      </button>
      <button type="button" id="btn-validate" class="e13-btn e13-btn-secondary" title="Sprawdź kompletność wyboru zakresu audytu w E13">
        ✓ Waliduj zakres E13
      </button>
    </div>
    <div class="global-toolbar-info">
      💡 <strong>Eksport</strong> generuje plik z pełnym formularzem Master (wszystkie etapy E0-E13) + wszystkimi audytowanymi scope w osobnych zakładkach. Klient drukuje, wypełnia ręcznie na obiekcie, potem wraca do biura i klika <strong>Import</strong> aby wczytać dane z powrotem.
    </div>
  </div>

  <!-- ====== HEADER ====== -->
  <header class="header">
    <div class="header-eyebrow">FORMULARZ AUDYT GLOBAL · MASTER · v1.0</div>
    <h1 class="header-title serif">ENESA Audyt Energetyczny — Master Form</h1>
    <div class="header-sub">Audyt energetyczny + EnMS Foundation · 13 zakładek · Single Source of Truth</div>
    <div class="header-meta">
      <div class="header-meta-item">
        <div class="header-meta-label">Norma</div>
        <div class="header-meta-val">ISO 50001 § 4-6 · PN-EN 16247-1</div>
      </div>
      <div class="header-meta-item">
        <div class="header-meta-label">Wypełnia</div>
        <div class="header-meta-val">Konsultant ENESA + Energy Manager</div>
      </div>
      <div class="header-meta-item">
        <div class="header-meta-label">Czas sesji otwierającej</div>
        <div class="header-meta-val">~3-4h Teams/onsite</div>
      </div>
      <div class="header-meta-item">
        <div class="header-meta-label">Status</div>
        <div class="header-meta-val" id="overall-progress">Wczytywanie...</div>
      </div>
    </div>
  </header>

    <!-- ============================================================ -->
  <!-- ETAP 0 · AUDYT — metadane + zespół audytowy                  -->
  <!-- ============================================================ -->
  <section class="section" id="etap-0">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 0</div>
        <h2 class="section-title serif">Audyt — metadane</h2>
        <p class="section-desc">Klient, zleceniodawca, audytor wiodący, zespół audytowy 5 osób, daty, cel audytu · 20 pól · czas: 5-10 min · zgodność z PN-EN 16247-1 § 5.4</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-0">0 / 20</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">


<div class="audit-type-selector" id="audit-type-selector">
  <h2>🎯 Wybierz typ audytu energetycznego</h2>
  <div class="selector-subtitle">
    Wybierz audyt który najlepiej odpowiada Twoim potrzebom. Liczba pól, czas i cena są szacunkowe — możesz w każdej chwili zmienić wybór.
  </div>
  
  <div class="audit-cards" id="audit-cards-container">
    <!-- Karty zostaną wygenerowane przez JS na bazie AUDIT_PROFILES -->
  </div>
  
  <div class="audit-actions">
    <button type="button" onclick="enesaToggleComparisonTable()">📊 Porównaj profile</button>
    <button type="button" onclick="enesaClearAuditProfile()">↺ Wyczyść wybór</button>
    <button type="button" onclick="enesaShowAllSections()" id="show-all-btn-startup">👁️ Pokaż wszystkie pola (podgląd)</button>
  </div>
  
  <div class="audit-comparison-table" id="audit-comparison-table">
    <!-- Tabela porównawcza wygenerowana przez JS -->
  </div>
</div>




      <div class="profile-selector-panel" id="audit-profile-selector">
  <h3>🎯 Profil audytu energetycznego</h3>
  <div class="profile-desc">
    Wybierz profil audytu — system pokoloruje pola wymagane (zielone <strong>MUST</strong>) 
    i opcjonalne (wyszarzone <em>opt</em>). Możesz zmienić profil w każdej chwili.
  </div>
  <div class="profile-options">
    <label class="profile-option" data-profile="eed">
      <input type="radio" name="audit-profile" value="eed" onchange="enesaSetAuditProfile('eed')">
      <div class="profile-name">EED minimum</div>
      <div class="profile-meta">~600 pól · ~3 dni · 30-80 tys. PLN<br>Audyt obowiązkowy co 4 lata (Art. 36)</div>
    </label>
    <label class="profile-option disabled" data-profile="white-cert" title="Wkrótce dostępne">
      <input type="radio" name="audit-profile" value="white-cert" disabled>
      <div class="profile-name">Białe Certyfikaty</div>
      <div class="profile-meta">~400 pól · ~2 dni · 15-40 tys. PLN<br>Świadectwa Efektywności Energetycznej</div>
    </label>
    <label class="profile-option" data-profile="iso50001">
      <input type="radio" name="audit-profile" value="iso50001" onchange="enesaSetAuditProfile('iso50001')">
      <div class="profile-name">ISO 50001:2018 § 6.3</div>
      <div class="profile-meta">~1500 pól · ~7 dni · 80-180 tys. PLN<br>Pełny przegląd energetyczny ISO 50001</div>
    </label>
    <label class="profile-option disabled" data-profile="full-map" title="Wkrótce dostępne">
      <input type="radio" name="audit-profile" value="full-map" disabled>
      <div class="profile-name">Pełna Mapa Energetyczna</div>
      <div class="profile-meta">~3500 pól · ~14 dni · 250-500 tys. PLN<br>ISO 50001 + ISO 50002 + CSRD</div>
    </label>
    <label class="profile-option disabled" data-profile="custom" title="Wkrótce dostępne">
      <input type="radio" name="audit-profile" value="custom" disabled>
      <div class="profile-name">Custom</div>
      <div class="profile-meta">wariant · audytor wybiera scope</div>
    </label>
  </div>
  <div class="profile-stats" id="profile-stats-display">
    Wybierz profil powyżej aby zobaczyć statystyki MUST/optional dla pól.
  </div>
</div>

<div class="group">
        <div class="group-title">Identyfikacja klienta</div>
        <div class="group-desc">Dane formalne firmy klienta — z KRS / wpisu CEIDG / faktury</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Pełna nazwa firmy klienta</div>
            <div class="field-id mono">AUD-V1-NAZWA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="AUD-V1-NAZWA" placeholder="np. Volkswagen Poznań Sp. z o.o.">
            <div class="field-hint">Z KRS / CEIDG. Pełna forma prawna (Sp. z o.o., S.A., Sp. j.).</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">NIP</div>
            <div class="field-id mono">AUD-V2-NIP</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="AUD-V2-NIP" placeholder="10 cyfr">
            <div class="field-hint">10 cyfr, np. 7000000613</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">REGON</div>
            <div class="field-id mono">AUD-V3-REGON</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="AUD-V3-REGON" placeholder="9 lub 14 cyfr">
            <div style="display:flex;align-items:center;gap:8px;margin-top:6px;">
              <button type="button" onclick="fetchGUSData()" style="font-size:11px;padding:4px 12px;border:1px solid var(--green-light,#a8ddb8);border-radius:6px;background:var(--green-bg,#eef8f0);color:var(--green-deep,#1a5c3a);cursor:pointer;white-space:nowrap;font-family:inherit;font-weight:600;">&#x1F50D; Pobierz z GUS</button>
              <div class="field-hint" style="margin:0;">Auto-uzupe&#322;nia REGON, PKD, adres z Bia&#322;ej Listy MF + KRS</div>
            </div>
            <div id="gus-status" style="display:none;font-size:11px;margin-top:5px;padding:5px 10px;border-radius:6px;border:1px solid;line-height:1.5;"></div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Ulica i numer siedziby</div>
            <div class="field-id mono">AUD-V4-ULICA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="AUD-V4-ULICA" placeholder="ul. Warszawska 100">
            <div class="field-hint">Ulica i numer budynku (adres rejestrowy)</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Miejscowość siedziby</div>
            <div class="field-id mono">AUD-V4-MIASTO</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="AUD-V4-MIASTO" placeholder="61-058 Poznań">
            <div class="field-hint">Kod pocztowy i miejscowość</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Główny kod PKD/NACE</div>
            <div class="field-id mono">AUD-V5-PKD</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="AUD-V5-PKD" placeholder="np. 29.10.E (Produkcja samochodów osobowych)">
            <div class="field-hint">Z CEIDG / GUS. Kod 5-cyfrowy z PKD 2007.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Wielkość przedsiębiorstwa</div>
            <div class="field-id mono">AUD-V6-WIELKOSC</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="AUD-V6-WIELKOSC">
              <option value="">— wybierz —</option>
              <option>mikro (do 10 osób)</option>
              <option>małe (10-50)</option>
              <option>średnie (50-250)</option>
              <option>DUŻE (>250 osób LUB >50 mln EUR)</option>
              <option>nie wiem</option>
            </select>
            <div class="field-hint">Tylko duże mają obowiązek audytu wg Ustawy o EE 2016</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Roczne zużycie energii</div>
            <div class="field-id mono">AUD-V7-ZUZYCIE</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="AUD-V7-ZUZYCIE" placeholder="np. 45.7" step="0.1">
            <div class="field-hint">Σ wszystkich nośników w 2024. Próg: &gt;10 TJ = audyt obowiązkowy (EED 2023), &gt;80 TJ = ISO 50001 obowiązkowe od X.2027</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">TJ/rok</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title" style="display:flex;align-items:center;gap:10px;">Zleceniodawca audytu (jeśli różni się od klienta)
          <button type="button" onclick="fillZleceniodawca()" style="font-size:11px;padding:3px 9px;background:#fef3c7;color:#92400e;border:1px solid #fcd34d;border-radius:5px;cursor:pointer;font-weight:600;line-height:1.3;">&#8635; Uzupełnij danymi firmy</button>
        </div>
        <div class="group-desc">Najczęściej zleceniodawca = klient. Może być korporacja-matka, fundusz inwestycyjny.</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Zleceniodawca audytu</div>
            <div class="field-id mono">AUD-V8-ZLEC</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="AUD-V8-ZLEC" placeholder="Firma zlecająca audyt">
            <div class="field-hint">Domyślnie = firma klienta. Zmień, jeśli zleceniodawcą jest inny podmiot (np. spółka-matka).</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Osoba kontaktowa zleceniodawcy</div>
            <div class="field-id mono">AUD-V9-ZLEC-IMIE</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="AUD-V9-ZLEC-IMIE" placeholder="np. Jan Kowalski">
            <div class="field-hint">Imię i nazwisko osoby decyzyjnej u zleceniodawcy</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Email kontaktu zleceniodawcy</div>
            <div class="field-id mono">AUD-V9-ZLEC-MAIL</div>
          </div>
          <div class="field-input-wrap">
            <input type="email" class="field-input" data-id="AUD-V9-ZLEC-MAIL" placeholder="np. j.kowalski@firma.pl">
            <div class="field-hint">Adres e-mail osoby kontaktowej</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Telefon kontaktu zleceniodawcy</div>
            <div class="field-id mono">AUD-V9-ZLEC-TEL</div>
          </div>
          <div class="field-input-wrap">
            <input type="tel" class="field-input" data-id="AUD-V9-ZLEC-TEL" placeholder="+48 ...">
            <div class="field-hint">Numer telefonu osoby kontaktowej</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Audytor wiodący (ENESA)</div>
        <div class="group-desc">Konsultant ENESA prowadzący audyt</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Audytor wiodący</div>
            <div class="field-id mono">AUD-V10-AUDYTOR</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="AUD-V10-AUDYTOR" placeholder="np. inż. Sławomir Kowalski, audytor URE nr 145/2018">
            <div class="field-hint">Imię + nazwisko + uprawnienia (np. wpis na listę audytorów efektywności energetycznej URE)</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Email audytora</div>
            <div class="field-id mono">AUD-V11-AUDYTOR-MAIL</div>
          </div>
          <div class="field-input-wrap">
            <input type="email" class="field-input" data-id="AUD-V11-AUDYTOR-MAIL" placeholder="s.kowalski@enesa.pl">
            <div class="field-hint">Główny kanał komunikacji</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Telefon audytora</div>
            <div class="field-id mono">AUD-V12-AUDYTOR-TEL</div>
          </div>
          <div class="field-input-wrap">
            <input type="tel" class="field-input" data-id="AUD-V12-AUDYTOR-TEL" placeholder="+48 ...">
            <div class="field-hint">—</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Zespół audytowy klienta — 5 osób</div>
        <div class="group-desc">Lista uczestników audytu po stronie klienta. Główny respondent (TAK) zwykle = Energy Manager.</div>

        <div class="devices-wrap">
          <table class="devices-table" id="team-table">
            <thead>
              <tr>
                <th class="th-question">ATRYBUT</th>
                <th class="th-comp">KTO</th>
                <th class="th-instance">Osoba 1</th>
                <th class="th-instance">Osoba 2</th>
                <th class="th-instance">Osoba 3</th>
                <th class="th-instance">Osoba 4</th>
                <th class="th-instance">Osoba 5</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="td-question"><div class="q-label">Imię i nazwisko</div><div class="q-id mono">AUD-V13-IMIE</div></td>
                <td class="td-comp"><span class="tag kon small">KON</span></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-IMIE-U1"></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-IMIE-U2"></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-IMIE-U3"></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-IMIE-U4"></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-IMIE-U5"></td>
              </tr>
              <tr>
                <td class="td-question"><div class="q-label">Stanowisko</div><div class="q-id mono">AUD-V13-STAN</div></td>
                <td class="td-comp"><span class="tag kon small">KON</span></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-STAN-U1"></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-STAN-U2"></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-STAN-U3"></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-STAN-U4"></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-STAN-U5"></td>
              </tr>
              <tr>
                <td class="td-question"><div class="q-label">Dział / komórka</div><div class="q-id mono">AUD-V13-DZIAL</div></td>
                <td class="td-comp"><span class="tag kon small">KON</span></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-DZIAL-U1"></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-DZIAL-U2"></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-DZIAL-U3"></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-DZIAL-U4"></td>
                <td class="td-input"><input type="text" class="cell-input" data-id="AUD-V13-DZIAL-U5"></td>
              </tr>
              <tr>
                <td class="td-question"><div class="q-label">Rola w audycie</div><div class="q-id mono">AUD-V13-ROLA</div></td>
                <td class="td-comp"><span class="tag kon small">KON</span></td>
                <td class="td-input"><select class="cell-input" data-id="AUD-V13-ROLA-U1"><option value="">—</option><option>UR</option><option>EM</option><option>KON</option><option>SPEC</option><option>KIER</option><option>INNE</option></select></td>
                <td class="td-input"><select class="cell-input" data-id="AUD-V13-ROLA-U2"><option value="">—</option><option>UR</option><option>EM</option><option>KON</option><option>SPEC</option><option>KIER</option><option>INNE</option></select></td>
                <td class="td-input"><select class="cell-input" data-id="AUD-V13-ROLA-U3"><option value="">—</option><option>UR</option><option>EM</option><option>KON</option><option>SPEC</option><option>KIER</option><option>INNE</option></select></td>
                <td class="td-input"><select class="cell-input" data-id="AUD-V13-ROLA-U4"><option value="">—</option><option>UR</option><option>EM</option><option>KON</option><option>SPEC</option><option>KIER</option><option>INNE</option></select></td>
                <td class="td-input"><select class="cell-input" data-id="AUD-V13-ROLA-U5"><option value="">—</option><option>UR</option><option>EM</option><option>KON</option><option>SPEC</option><option>KIER</option><option>INNE</option></select></td>
              </tr>
              <tr>
                <td class="td-question"><div class="q-label">Email</div><div class="q-id mono">AUD-V13-MAIL</div></td>
                <td class="td-comp"><span class="tag kon small">KON</span></td>
                <td class="td-input"><input type="email" class="cell-input" data-id="AUD-V13-MAIL-U1"></td>
                <td class="td-input"><input type="email" class="cell-input" data-id="AUD-V13-MAIL-U2"></td>
                <td class="td-input"><input type="email" class="cell-input" data-id="AUD-V13-MAIL-U3"></td>
                <td class="td-input"><input type="email" class="cell-input" data-id="AUD-V13-MAIL-U4"></td>
                <td class="td-input"><input type="email" class="cell-input" data-id="AUD-V13-MAIL-U5"></td>
              </tr>
              <tr>
                <td class="td-question"><div class="q-label">Telefon</div><div class="q-id mono">AUD-V13-TEL</div></td>
                <td class="td-comp"><span class="tag kon small">KON</span></td>
                <td class="td-input"><input type="tel" class="cell-input" data-id="AUD-V13-TEL-U1"></td>
                <td class="td-input"><input type="tel" class="cell-input" data-id="AUD-V13-TEL-U2"></td>
                <td class="td-input"><input type="tel" class="cell-input" data-id="AUD-V13-TEL-U3"></td>
                <td class="td-input"><input type="tel" class="cell-input" data-id="AUD-V13-TEL-U4"></td>
                <td class="td-input"><input type="tel" class="cell-input" data-id="AUD-V13-TEL-U5"></td>
              </tr>
              <tr>
                <td class="td-question"><div class="q-label">Data dołączenia</div><div class="q-id mono">AUD-V13-DATA</div></td>
                <td class="td-comp"><span class="tag kon small">KON</span></td>
                <td class="td-input"><input type="date" class="cell-input" data-id="AUD-V13-DATA-U1"></td>
                <td class="td-input"><input type="date" class="cell-input" data-id="AUD-V13-DATA-U2"></td>
                <td class="td-input"><input type="date" class="cell-input" data-id="AUD-V13-DATA-U3"></td>
                <td class="td-input"><input type="date" class="cell-input" data-id="AUD-V13-DATA-U4"></td>
                <td class="td-input"><input type="date" class="cell-input" data-id="AUD-V13-DATA-U5"></td>
              </tr>
              <tr>
                <td class="td-question"><div class="q-label">Główny respondent</div><div class="q-id mono">AUD-V13-MAIN</div></td>
                <td class="td-comp"><span class="tag kon small">KON</span></td>
                <td class="td-input"><select class="cell-input" data-id="AUD-V13-MAIN-U1"><option value="">—</option><option>TAK</option><option>NIE</option></select></td>
                <td class="td-input"><select class="cell-input" data-id="AUD-V13-MAIN-U2"><option value="">—</option><option>TAK</option><option>NIE</option></select></td>
                <td class="td-input"><select class="cell-input" data-id="AUD-V13-MAIN-U3"><option value="">—</option><option>TAK</option><option>NIE</option></select></td>
                <td class="td-input"><select class="cell-input" data-id="AUD-V13-MAIN-U4"><option value="">—</option><option>TAK</option><option>NIE</option></select></td>
                <td class="td-input"><select class="cell-input" data-id="AUD-V13-MAIN-U5"><option value="">—</option><option>TAK</option><option>NIE</option></select></td>
              </tr>
            </tbody>
          </table>
        </div>
        <button class="row-add" type="button" id="add-team-btn">+ Dodaj kolejnego uczestnika (Osoba 6, 7...)</button>
      </div>

      <div class="group">
        <div class="group-title">Parametry audytu</div>
        <div class="group-desc">Cel, norma referencyjna, daty, kontrakt</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Cel audytu</div>
            <div class="field-id mono">AUD-V14-CEL</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="AUD-V14-CEL">
              <option value="">— wybierz —</option>
              <option>Compliance Ustawa o EE 2016</option>
              <option>Compliance Dyrektywa EED 2023</option>
              <option>Wsparcie ISO 50001</option>
              <option>Dotacja: Biały certyfikat</option>
              <option>Dotacja: FENG/FE</option>
              <option>Dotacja: NFOŚ</option>
              <option>Dobrowolny</option>
              <option>Inne</option>
            </select>
            <div class="field-hint">Norma + powód audytu — wybór wpływa na zakres raportu</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Norma referencyjna</div>
            <div class="field-id mono">AUD-V15-NORMA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="AUD-V15-NORMA" placeholder="np. PN-EN 16247-1 + ISO 50001:2018">
            <div class="field-hint">PN-EN 16247-1 (ogólna) + PN-EN 16247-3 (procesy) + ISO 50001:2018 (jeśli wsparcie EnMS)</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Data rozpoczęcia audytu</div>
            <div class="field-id mono">AUD-V16-DATA-START</div>
          </div>
          <div class="field-input-wrap">
            <input type="date" class="field-input" data-id="AUD-V16-DATA-START">
            <div class="field-hint">Data sesji otwierającej</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">data</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Data planowanego zakończenia</div>
            <div class="field-id mono">AUD-V17-DATA-END</div>
          </div>
          <div class="field-input-wrap">
            <input type="date" class="field-input" data-id="AUD-V17-DATA-END">
            <div class="field-hint">Termin oddania raportu — typowo 6-12 tygodni po sesji otwierającej</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">data</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Okres bilansowy audytu</div>
            <div class="field-id mono">AUD-V18-OKRES</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="AUD-V18-OKRES" placeholder="np. 1.01.2024 - 31.12.2024">
            <div class="field-hint">Typowo poprzedni pełny rok kalendarzowy lub ostatnie 12 mies.</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Numer umowy ENESA-Klient</div>
            <div class="field-id mono">AUD-V19-UMOWA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="AUD-V19-UMOWA" placeholder="np. UE/2025/123">
            <div class="field-hint">Wewnętrzny numer umowy ENESA</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Wartość kontraktu (netto)</div>
            <div class="field-id mono">AUD-V20-WARTOSC</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="AUD-V20-WARTOSC" placeholder="opcjonalnie">
            <div class="field-hint">Opcjonalnie. Dla raportowania wewnętrznego ENESA.</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN</div>
        </div>
      </div>

    </div>
  </section>

  <!-- ============================================================ -->
  <!-- ETAP 1 · ZAKRES i granice                                    -->
  <!-- ============================================================ -->
  <section class="section" id="etap-1">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 1</div>
        <h2 class="section-title serif">Zakres i granice bilansowe</h2>
        <p class="section-desc">Lokalizacja, lista budynków, wyłączenia, granice fizyczne audytu · 9 pól · czas: 10-15 min · zgodność z PN-EN 16247-1 § 5.4</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-1">0 / 9</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group">
        <div class="group-title">Lokalizacja audytowana</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Nazwa lokalizacji audytowanej</div>
            <div class="field-id mono">ZAK-V1-LOK-NAZWA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="ZAK-V1-LOK-NAZWA" placeholder="np. Zakład Spawalniczo-Lakierniczy w Tychach">
            <div class="field-hint">Może być inna niż siedziba (E0). Nazwa identyfikująca obiekt audytowany.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Adres lokalizacji audytowanej</div>
            <div class="field-id mono">ZAK-V2-LOK-ADRES</div>
          </div>
          <div class="field-input-wrap">
            <div style="position:relative;">
  <input type="text" class="field-input" data-id="ZAK-V2-LOK-ADRES" id="master-loc-adres-input" placeholder="ul. Główna 12, 43-100 Tychy" autocomplete="off" oninput="masterLocDebouncedSearch(this.value)">
  <div id="master-loc-suggestions" style="position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid var(--paper-deep);border-radius:10px;box-shadow:0 6px 24px rgba(14,55,85,.12);z-index:300;max-height:240px;overflow-y:auto;display:none;margin-top:2px;"></div>
</div>
<div id="master-climate-status" style="display:none;font-size:11px;color:var(--green-deep);margin-top:5px;padding:5px 10px;background:var(--green-bg,#eef8f0);border-radius:6px;border:1px solid var(--green-light,#a8ddb8);line-height:1.5;"></div>
<div style="display:flex;align-items:center;gap:8px;margin-top:5px;">
  <button type="button" onclick="masterLocForceAutoFill()" style="font-size:11px;padding:4px 10px;border:1px solid var(--green-light,#a8ddb8);border-radius:6px;background:var(--green-bg,#eef8f0);color:var(--green-deep,#1a5c3a);cursor:pointer;white-space:nowrap;font-family:inherit;">
    Uzupełnij klimat
  </button>
  <div class="field-hint" style="margin:0;">Fizyczna lokalizacja zakładu — domyślnie adres siedziby. System uzupełnia klimat automatycznie.</div>
</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Współrzędne GPS</div>
            <div class="field-id mono">ZAK-V3-GPS</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="ZAK-V3-GPS" placeholder="np. 50.124°N 18.978°E">
            <div class="field-hint">Opcjonalnie — pomocne dla profili klimatycznych (HDD/CDD)</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Obiekty objęte audytem</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Liczba budynków/obiektów</div>
            <div class="field-id mono">ZAK-V4-OBIEKTY-N</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="ZAK-V4-OBIEKTY-N" placeholder="np. 3" min="1">
            <div class="field-hint">Liczba osobnych budynków na działce (np. hala główna + magazyn + biuro = 3)</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">szt</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Lista budynków/obiektów</div>
            <div class="field-id mono">ZAK-V5-OBIEKTY-LIST</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="ZAK-V5-OBIEKTY-LIST" placeholder="np. Hala G1, Hala G2, Magazyn M1, Biurowiec BU">
            <div class="field-hint">Lista oddzielona przecinkami</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Łączna powierzchnia obiektów</div>
            <div class="field-id mono">ZAK-V6-POW-CALK</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="ZAK-V6-POW-CALK" placeholder="np. 25000">
            <div class="field-hint">Σ powierzchni audytowanych budynków</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">m²</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Granice bilansowe</div>
        <div class="group-info">
          <strong>Wymóg Ustawy o EE 2016:</strong> audyt MUSI obejmować ≥90% zużycia energii. Reszta = wymaga wyjaśnienia.
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Wyłączenia z audytu</div>
            <div class="field-id mono">ZAK-V7-WYLACZENIA</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="ZAK-V7-WYLACZENIA" placeholder="np. najemcy w hali G2 (5% powierzchni), flota transportowa (osobny audyt), centrum R&D w innej lokalizacji"></textarea>
            <div class="field-hint">Co NIE wchodzi w audyt — i dlaczego</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">% udziału audytowanego zakresu w całkowitym zużyciu</div>
            <div class="field-id mono">ZAK-V8-UDZIAL-AUDYT</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="ZAK-V8-UDZIAL-AUDYT" placeholder="np. 95" min="0" max="100">
            <div class="field-hint">Zgodnie z Ustawą o EE: ≥90%. Mniej = wymaga wyjaśnienia.</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">%</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Link do mapy / planu zakładu</div>
            <div class="field-id mono">ZAK-V9-MAPA-LINK</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="ZAK-V9-MAPA-LINK" placeholder="URL lub referencja w E12">
            <div class="field-hint">Link do dokumentu (Google Drive, SharePoint) lub odwołanie do E12 (Dokumenty źródłowe)</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

    </div>
  </section>

    <!-- ============================================================ -->
  <!-- ETAP 2 · ZAKŁAD — charakterystyka                            -->
  <!-- ============================================================ -->
  <section class="section" id="etap-2">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 2</div>
        <h2 class="section-title serif">Zakład — charakterystyka</h2>
        <p class="section-desc">Branża, klimat, BAC, dane statyczne · 12 pól · czas: 15-20 min · ISO 50001 § 6.3 (Static Factors)</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-2">0 / 12</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group">
        <div class="group-title">Klasyfikacja branżowa</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Branża wiodąca</div>
            <div class="field-id mono">ZAK-V1-BRANZA</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="ZAK-V1-BRANZA">
              <option value="">— wybierz —</option>
              <option>Automotive</option>
              <option>Spożywcza</option>
              <option>Chemiczna</option>
              <option>Metalurgiczna</option>
              <option>Drzewno-meblarska</option>
              <option>Tekstylna</option>
              <option>Tworzywa sztuczne</option>
              <option>Elektrotechniczna</option>
              <option>Farmaceutyczna</option>
              <option>Inna</option>
            </select>
            <div class="field-hint">Główna branża zakładu — wpływa na typowe SEU i benchmarki EnPI</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Podbranża / specjalizacja</div>
            <div class="field-id mono">ZAK-V2-PODBRANZA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="ZAK-V2-PODBRANZA" placeholder="np. produkcja podzespołów / lakiernia kontraktowa">
            <div class="field-hint">Konkretyzacja branży — np. dla automotive: produkcja podzespołów / montaż finalny / lakiernia kontraktowa</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Główne produkty / kody PKWiU</div>
            <div class="field-id mono">ZAK-V3-PRODUKTY-MAIN</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="ZAK-V3-PRODUKTY-MAIN" placeholder="np. nadwozia samochodowe (PKWiU 29.10), drzwi i klapy"></textarea>
            <div class="field-hint">2-3 najważniejsze produkty po przychodzie. Kody PKWiU jeśli klient zna.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Warunki klimatyczne i lokalizacyjne</div>
        <div class="group-info">
          <strong>HDD/CDD</strong> = stopniodni grzewcze/chłodzące. To kluczowe zmienne istotne dla EnPI baseline (E9). Klient zwykle ich nie zna — Konsultant uzupełnia z danych meteorologicznych.
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Strefa klimatyczna PL</div>
            <div class="field-id mono">ZAK-V4-KLIMAT</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="ZAK-V4-KLIMAT">
              <option value="">— wybierz —</option>
              <option>I (najcieplejsza, np. Wrocław, Zielona Góra)</option>
              <option>II (np. Poznań, Łódź, Warszawa)</option>
              <option>III (np. Tychy, Katowice, Lublin)</option>
              <option>IV (np. Olsztyn, Białystok)</option>
              <option>V (najzimniejsza, np. Suwałki)</option>
            </select>
            <div class="field-hint">Wg PN-EN 12831. Wpływa na zapotrzebowanie cieplne.</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Stopniodni grzewcze (HDD)</div>
            <div class="field-id mono">ZAK-V5-HDD</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="ZAK-V5-HDD" placeholder="np. 3500" step="1">
            <div class="field-hint">Baseline 18°C, roczne, z danych meteorologicznych dla regionu</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">K·dni/rok</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Stopniodni chłodzenia (CDD)</div>
            <div class="field-id mono">ZAK-V6-CDD</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="ZAK-V6-CDD" placeholder="np. 250" step="1">
            <div class="field-hint">Baseline 18°C. W PL znacznie niższe niż HDD.</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">K·dni/rok</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Wysokość n.p.m.</div>
            <div class="field-id mono">ZAK-V7-ALTITUDE</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="ZAK-V7-ALTITUDE" placeholder="np. 250">
            <div class="field-hint">Wpływ na ciśnienie atmosferyczne — istotne dla sprężarek (CA scope)</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">m</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Charakterystyka techniczna budynków</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Klasa BAC budynków</div>
            <div class="field-id mono">ZAK-V8-BAC</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="ZAK-V8-BAC">
              <option value="">— wybierz —</option>
              <option>A (top automatyka, BMS pełen)</option>
              <option>B (dobra automatyka)</option>
              <option>C (standard, podstawowa)</option>
              <option>D (brak BMS, sterowanie ręczne)</option>
              <option>brak (niesklasyfikowany)</option>
              <option>mieszane (per budynek)</option>
              <option>nie wiem</option>
            </select>
            <div class="field-hint">Wg PN-EN ISO 52120-1. Klasa D → największy potencjał oszczędności przez automatykę.</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Klasa energetyczna budynków</div>
            <div class="field-id mono">ZAK-V9-KLASA-EN</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="ZAK-V9-KLASA-EN">
              <option value="">— wybierz —</option>
              <option>A</option>
              <option>B</option>
              <option>C</option>
              <option>D</option>
              <option>E</option>
              <option>F</option>
              <option>G</option>
              <option>brak świadectwa</option>
              <option>mieszane</option>
              <option>nie wiem</option>
            </select>
            <div class="field-hint">Z świadectw charakterystyki energetycznej budynków. Brak = nie ma świadectw.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Średni wiek budynków</div>
            <div class="field-id mono">ZAK-V10-WIEK</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="ZAK-V10-WIEK" placeholder="np. 35">
            <div class="field-hint">Pomocnicze. Wpływ na izolację, infiltrację, BAC.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">lata</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Dane statyczne (ISO 50001)</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Powierzchnia produkcyjna</div>
            <div class="field-id mono">ZAK-V11-POW-PROD</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="ZAK-V11-POW-PROD" placeholder="np. 18000">
            <div class="field-hint">Łączna powierzchnia wykorzystywana produkcyjnie (bez biur, korytarzy, łazienek)</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">m²</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Łączna kubatura</div>
            <div class="field-id mono">ZAK-V12-KUBATURA</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="ZAK-V12-KUBATURA" placeholder="np. 144000">
            <div class="field-hint">Σ kubatur audytowanych budynków (dla bilansu cieplnego)</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">m³</div>
        </div>
      </div>

    </div>
  </section>

  <!-- ============================================================ -->
  <!-- ETAP 3 · PROCESY produkcyjne                                 -->
  <!-- ============================================================ -->
  <section class="section" id="etap-3">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 3</div>
        <h2 class="section-title serif">Procesy produkcyjne</h2>
        <p class="section-desc">Narracja procesu, asortyment, profile produkcji · 10 pól · czas: 30 min · PN-EN 16247-3 (Procesy)</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-3">0 / 10</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group">
        <div class="group-title">Opis procesu produkcyjnego (narracja)</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Opis procesu produkcyjnego</div>
            <div class="field-id mono">PRO-V1-NARRACJA</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="PRO-V1-NARRACJA" placeholder='np. "1. Press shop (tłoczenie blachy stali). 2. Spawalnia (zgrzewanie nadwozia). 3. Lakiernia (kataforeza + lakier nawierzchni). 4. Montaż (instalacja silnika, wnętrza). 5. Magazyn wyrobów."' style="min-height: 120px"></textarea>
            <div class="field-hint">Tekst 200-500 słów. Sekwencja operacji od materiału wejściowego do produktu finalnego.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span><span class="tag kier">KIER</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Link do diagramu procesu</div>
            <div class="field-id mono">PRO-V2-PROCES-DIAGRAM</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="PRO-V2-PROCES-DIAGRAM" placeholder="URL lub referencja do E12">
            <div class="field-hint">Schemat blokowy / diagram przepływu (jeśli istnieje)</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Asortyment i wielkość produkcji</div>
        <div class="group-info">
          <strong>Wielkość produkcji</strong> (V4) jest <strong>kluczowa</strong> — to mianownik dla większości EnPI (np. kWh/szt nadwozia, kWh/tonę produktu).
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Liczba SKU / wariantów produktu</div>
            <div class="field-id mono">PRO-V3-ASORTYMENT</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="PRO-V3-ASORTYMENT" placeholder="np. 12">
            <div class="field-hint">Pomocnicze. Wpływ na zmienność produkcji i EnPI.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">szt</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Wielkość produkcji rocznej</div>
            <div class="field-id mono">PRO-V4-PRODUKCJA-ROK</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="PRO-V4-PRODUKCJA-ROK" placeholder="np. 50000">
            <div class="field-hint">KLUCZOWE — to mianownik dla EnPI. np. 50000 szt nadwozi/rok, 12000 ton/rok</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">jednostek/rok</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Jednostka produkcji</div>
            <div class="field-id mono">PRO-V5-PRODUKCJA-JM</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="PRO-V5-PRODUKCJA-JM">
              <option value="">— wybierz —</option>
              <option>sztuki</option>
              <option>tony</option>
              <option>m³</option>
              <option>m²</option>
              <option>litry</option>
              <option>kg</option>
              <option>inne</option>
            </select>
            <div class="field-hint">Jednostka miary dla pola V4</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Wartość produkcji rocznej (przychód)</div>
            <div class="field-id mono">PRO-V6-WARTOSC-ROK</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="PRO-V6-WARTOSC-ROK" placeholder="opcjonalnie">
            <div class="field-hint">Opcjonalnie. Wpływ na EnPI w kosztach.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">PLN/rok</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Profile produkcji</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Profil produkcji miesięczny</div>
            <div class="field-id mono">PRO-V7-PROFIL-MIES</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="PRO-V7-PROFIL-MIES" placeholder='np. "bez sezonowości / sezon V-IX 70% / minimum styczeń-luty"'></textarea>
            <div class="field-hint">Sezonowość. Tabela 12 mies. lub opis tekstowy.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Tryb pracy zakładu</div>
            <div class="field-id mono">PRO-V8-TRYB-PRACY</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="PRO-V8-TRYB-PRACY">
              <option value="">— wybierz —</option>
              <option>1 zmiana</option>
              <option>2 zmiany</option>
              <option>3 zmiany</option>
              <option>24-7</option>
              <option>sezonowy</option>
              <option>mieszany (per wydział)</option>
            </select>
            <div class="field-hint">Per wydział tryb pracy zostanie doprecyzowany w E4</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Liczba dni pracy w roku</div>
            <div class="field-id mono">PRO-V9-DNI-ROK</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="PRO-V9-DNI-ROK" placeholder="np. 250">
            <div class="field-hint">np. 250 dni (5×52 - urlopy świąteczne) / 365 dni (24-7) / 200 dni (sezonowy)</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">dni/rok</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Planowane przestoje</div>
            <div class="field-id mono">PRO-V10-PRZESTOJE</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="PRO-V10-PRZESTOJE" placeholder='np. "lipiec — przerwa urlopowa 2 tygodnie, święta świąteczne"'></textarea>
            <div class="field-hint">Wpływ na profil zużycia w E8.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

    </div>
  </section>

  <!-- ============================================================ -->
  <!-- ETAP 4 · WYDZIAŁY — SEU candidates (TRANSPONOWANA TABELA)    -->
  <!-- ============================================================ -->
  <section class="section" id="etap-4">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 4</div>
        <h2 class="section-title serif">Wydziały — SEU candidates</h2>
        <p class="section-desc">Lista wydziałów produkcyjnych z klasyfikacją SEU · 10 pól × N wydziałów · ISO 50001 § 6.3 (Significant Energy Uses)</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-4">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Jak to działa:</strong>
        <ul>
          <li>Każda <strong>kolumna</strong> = jeden wydział produkcyjny. Każdy <strong>wiersz</strong> = jedno pytanie.</li>
          <li>Typowy zakład automotive ma 5-7 wydziałów: <em>Press shop, Spawalnia, Lakiernia, Montaż, Magazyn, Utilities, Biuro</em>.</li>
          <li>Każdy wydział = <strong>kandydat SEU</strong> (Significant Energy Use). Klasyfikacja PRIMARY/SECONDARY/SMALL na podstawie % zużycia.</li>
          <li>Lista wydziałów jest używana w <strong>E6 macierzy</strong> Hala × Wydział z alokacją procentową.</li>
        </ul>
      </div>

      <div class="devices-wrap">
        <table class="devices-table" id="wydzialy-table">
          <thead>
            <tr>
              <th class="th-question">Pytanie</th>
              <th class="th-comp">KTO</th>
              <th class="th-instance">Wydział 1</th>
              <th class="th-instance">Wydział 2</th>
              <th class="th-instance">Wydział 3</th>
              <th class="th-instance">Wydział 4</th>
              <th class="th-instance">Wydział 5</th>
            </tr>
          </thead>
          <tbody>
            <tr class="row-section-header"><td colspan="7">▼ Identyfikacja wydziału</td></tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Numer/oznaczenie wydziału</div>
                <div class="q-id mono">WYD-V1-NUMER</div>
                <div class="q-hint">Numer porządkowy 1-15 (referencja w E6)</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="number" class="cell-input wydz-num-input" data-id="WYD-V1-NUMER-W1" data-wydz-idx="1" value="1" min="1"></td>
              <td class="td-input"><input type="number" class="cell-input wydz-num-input" data-id="WYD-V1-NUMER-W2" data-wydz-idx="2" value="2" min="1"></td>
              <td class="td-input"><input type="number" class="cell-input wydz-num-input" data-id="WYD-V1-NUMER-W3" data-wydz-idx="3" value="3" min="1"></td>
              <td class="td-input"><input type="number" class="cell-input wydz-num-input" data-id="WYD-V1-NUMER-W4" data-wydz-idx="4" value="4" min="1"></td>
              <td class="td-input"><input type="number" class="cell-input wydz-num-input" data-id="WYD-V1-NUMER-W5" data-wydz-idx="5" value="5" min="1"></td>
            </tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Nazwa wydziału</div>
                <div class="q-id mono">WYD-V2-NAZWA</div>
                <div class="q-hint">np. „Spawalnia A", „Press shop", „Utilities"</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span><span class="tag kier small">KIER</span></td>
              <td class="td-input"><input type="text" class="cell-input wydz-name-input" data-id="WYD-V2-NAZWA-W1" data-wydz-idx="1" placeholder="Press shop"></td>
              <td class="td-input"><input type="text" class="cell-input wydz-name-input" data-id="WYD-V2-NAZWA-W2" data-wydz-idx="2" placeholder="Spawalnia"></td>
              <td class="td-input"><input type="text" class="cell-input wydz-name-input" data-id="WYD-V2-NAZWA-W3" data-wydz-idx="3" placeholder="Lakiernia"></td>
              <td class="td-input"><input type="text" class="cell-input wydz-name-input" data-id="WYD-V2-NAZWA-W4" data-wydz-idx="4" placeholder="Montaż"></td>
              <td class="td-input"><input type="text" class="cell-input wydz-name-input" data-id="WYD-V2-NAZWA-W5" data-wydz-idx="5" placeholder="Utilities"></td>
            </tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Kierownik / kontakt</div>
                <div class="q-id mono">WYD-V3-KIEROWNIK</div>
                <div class="q-hint">Imię, nazwisko, tel, email</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V3-KIEROWNIK-W1"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V3-KIEROWNIK-W2"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V3-KIEROWNIK-W3"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V3-KIEROWNIK-W4"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V3-KIEROWNIK-W5"></td>
            </tr>

            <tr class="row-section-header"><td colspan="7">▼ Charakterystyka wydziału</td></tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Liczba pracowników [osób]</div>
                <div class="q-id mono">WYD-V4-ZATRUDNIENIE</div>
                <div class="q-hint">Etaty pełne. Wpływ na zyski wewnętrzne, oświetlenie.</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span><span class="tag kier small">KIER</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="WYD-V4-ZATRUDNIENIE-W1"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="WYD-V4-ZATRUDNIENIE-W2"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="WYD-V4-ZATRUDNIENIE-W3"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="WYD-V4-ZATRUDNIENIE-W4"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="WYD-V4-ZATRUDNIENIE-W5"></td>
            </tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Powierzchnia wydziału [m²]</div>
                <div class="q-id mono">WYD-V5-POW</div>
                <div class="q-hint">Suma powierzchni hal/strefy</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="WYD-V5-POW-W1"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="WYD-V5-POW-W2"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="WYD-V5-POW-W3"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="WYD-V5-POW-W4"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="WYD-V5-POW-W5"></td>
            </tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Produkcja wydziału (jednostki/rok)</div>
                <div class="q-id mono">WYD-V6-PRODUKCJA</div>
                <div class="q-hint">np. „spawalnia: 50 000 nadwozi/rok"</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span><span class="tag kier small">KIER</span></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V6-PRODUKCJA-W1"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V6-PRODUKCJA-W2"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V6-PRODUKCJA-W3"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V6-PRODUKCJA-W4"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V6-PRODUKCJA-W5"></td>
            </tr>

            <tr class="row-section-header"><td colspan="7">▼ Klasyfikacja SEU (ISO 50001)</td></tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Klasa SEU</div>
                <div class="q-id mono">WYD-V7-SEU</div>
                <div class="q-hint">PRIMARY (>20%) / SECONDARY (5-20%) / SMALL (<5%)</div>
              </td>
              <td class="td-comp"><span class="tag kon small">KON</span></td>
              <td class="td-input"><select class="cell-input" data-id="WYD-V7-SEU-W1"><option value="">—</option><option>PRIMARY (>20%)</option><option>SECONDARY (5-20%)</option><option>SMALL (<5%)</option><option>do oceny po E8</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="WYD-V7-SEU-W2"><option value="">—</option><option>PRIMARY (>20%)</option><option>SECONDARY (5-20%)</option><option>SMALL (<5%)</option><option>do oceny po E8</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="WYD-V7-SEU-W3"><option value="">—</option><option>PRIMARY (>20%)</option><option>SECONDARY (5-20%)</option><option>SMALL (<5%)</option><option>do oceny po E8</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="WYD-V7-SEU-W4"><option value="">—</option><option>PRIMARY (>20%)</option><option>SECONDARY (5-20%)</option><option>SMALL (<5%)</option><option>do oceny po E8</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="WYD-V7-SEU-W5"><option value="">—</option><option>PRIMARY (>20%)</option><option>SECONDARY (5-20%)</option><option>SMALL (<5%)</option><option>do oceny po E8</option></select></td>
            </tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Priorytet audytu (1-3)</div>
                <div class="q-id mono">WYD-V8-PRIORYTET</div>
                <div class="q-hint">1=wysoki, 2=średni, 3=niski</div>
              </td>
              <td class="td-comp"><span class="tag kon small">KON</span></td>
              <td class="td-input"><select class="cell-input" data-id="WYD-V8-PRIORYTET-W1"><option value="">—</option><option>1 (wysoki)</option><option>2 (średni)</option><option>3 (niski)</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="WYD-V8-PRIORYTET-W2"><option value="">—</option><option>1 (wysoki)</option><option>2 (średni)</option><option>3 (niski)</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="WYD-V8-PRIORYTET-W3"><option value="">—</option><option>1 (wysoki)</option><option>2 (średni)</option><option>3 (niski)</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="WYD-V8-PRIORYTET-W4"><option value="">—</option><option>1 (wysoki)</option><option>2 (średni)</option><option>3 (niski)</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="WYD-V8-PRIORYTET-W5"><option value="">—</option><option>1 (wysoki)</option><option>2 (średni)</option><option>3 (niski)</option></select></td>
            </tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Proponowany EnPI</div>
                <div class="q-id mono">WYD-V9-ENPI-PROPOZYCJA</div>
                <div class="q-hint">np. „kWh/szt nadwozia", „kWh/m² malowania"</div>
              </td>
              <td class="td-comp"><span class="tag kon small">KON</span></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V9-ENPI-PROPOZYCJA-W1"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V9-ENPI-PROPOZYCJA-W2"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V9-ENPI-PROPOZYCJA-W3"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V9-ENPI-PROPOZYCJA-W4"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V9-ENPI-PROPOZYCJA-W5"></td>
            </tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Komentarz / specyfika wydziału</div>
                <div class="q-id mono">WYD-V10-KOMENTARZ</div>
                <div class="q-hint">Kluczowe procesy, urządzenia, problemy energetyczne</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span><span class="tag kier small">KIER</span></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V10-KOMENTARZ-W1"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V10-KOMENTARZ-W2"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V10-KOMENTARZ-W3"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V10-KOMENTARZ-W4"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="WYD-V10-KOMENTARZ-W5"></td>
            </tr>
          </tbody>
        </table>
      </div>
      <button class="row-add" type="button" id="add-wydz-btn">+ Dodaj kolejny wydział (Wydział 6, 7...)</button>

    </div>
  </section>


    <!-- ============================================================ -->
  <!-- ETAP 5 · HALE — lokalizacje fizyczne (TRANSPONOWANA TABELA)  -->
  <!-- ============================================================ -->
  <section class="section" id="etap-5">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 5</div>
        <h2 class="section-title serif">Hale — lokalizacje fizyczne</h2>
        <p class="section-desc">Lista hal z parametrami fizycznymi · 10 pól × N hal · PN-EN 16247-2 (Budynki)</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-5">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Hala vs Wydział:</strong>
        <ul>
          <li><strong>Hala</strong> = lokalizacja fizyczna (budynek lub jego część). <strong>Wydział</strong> = funkcja (proces produkcyjny).</li>
          <li>Jedna hala może obsługiwać wiele wydziałów (np. „Hala G20" = 60% Spawalnia + 30% Magazyn + 10% Komunikacja).</li>
          <li>Mapowanie alokacji % robimy w <strong>E6 macierz</strong>.</li>
        </ul>
      </div>

      <div class="devices-wrap">
        <table class="devices-table" id="hale-table">
          <thead>
            <tr>
              <th class="th-question">Pytanie</th>
              <th class="th-comp">KTO</th>
              <th class="th-instance">Hala 1</th>
              <th class="th-instance">Hala 2</th>
              <th class="th-instance">Hala 3</th>
              <th class="th-instance">Hala 4</th>
              <th class="th-instance">Hala 5</th>
            </tr>
          </thead>
          <tbody>
            <tr class="row-section-header"><td colspan="7">▼ Identyfikacja hali</td></tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Numer/oznaczenie hali</div>
                <div class="q-id mono">HAL-V1-NUMER</div>
                <div class="q-hint">Numer porządkowy 1-25 (referencja w E6)</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="number" class="cell-input hal-num-input" data-id="HAL-V1-NUMER-H1" value="1" min="1"></td>
              <td class="td-input"><input type="number" class="cell-input hal-num-input" data-id="HAL-V1-NUMER-H2" value="2" min="1"></td>
              <td class="td-input"><input type="number" class="cell-input hal-num-input" data-id="HAL-V1-NUMER-H3" value="3" min="1"></td>
              <td class="td-input"><input type="number" class="cell-input hal-num-input" data-id="HAL-V1-NUMER-H4" value="4" min="1"></td>
              <td class="td-input"><input type="number" class="cell-input hal-num-input" data-id="HAL-V1-NUMER-H5" value="5" min="1"></td>
            </tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Nazwa robocza hali</div>
                <div class="q-id mono">HAL-V2-NAZWA</div>
                <div class="q-hint">np. „G20", „Hala główna", „Magazyn M1"</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="text" class="cell-input hal-name-input" data-id="HAL-V2-NAZWA-H1" placeholder="G1"></td>
              <td class="td-input"><input type="text" class="cell-input hal-name-input" data-id="HAL-V2-NAZWA-H2" placeholder="G2"></td>
              <td class="td-input"><input type="text" class="cell-input hal-name-input" data-id="HAL-V2-NAZWA-H3" placeholder="Magazyn M1"></td>
              <td class="td-input"><input type="text" class="cell-input hal-name-input" data-id="HAL-V2-NAZWA-H4" placeholder="Biurowiec"></td>
              <td class="td-input"><input type="text" class="cell-input hal-name-input" data-id="HAL-V2-NAZWA-H5" placeholder="Hala pomocnicza"></td>
            </tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Typ hali</div>
                <div class="q-id mono">HAL-V3-TYP</div>
                <div class="q-hint">Produkcja / Magazyn / Biuro / Mieszany / Inny</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><select class="cell-input" data-id="HAL-V3-TYP-H1"><option value="">—</option><option>Produkcja</option><option>Magazyn</option><option>Biuro</option><option>Mieszany</option><option>Inny</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="HAL-V3-TYP-H2"><option value="">—</option><option>Produkcja</option><option>Magazyn</option><option>Biuro</option><option>Mieszany</option><option>Inny</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="HAL-V3-TYP-H3"><option value="">—</option><option>Produkcja</option><option>Magazyn</option><option>Biuro</option><option>Mieszany</option><option>Inny</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="HAL-V3-TYP-H4"><option value="">—</option><option>Produkcja</option><option>Magazyn</option><option>Biuro</option><option>Mieszany</option><option>Inny</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="HAL-V3-TYP-H5"><option value="">—</option><option>Produkcja</option><option>Magazyn</option><option>Biuro</option><option>Mieszany</option><option>Inny</option></select></td>
            </tr>

            <tr class="row-section-header"><td colspan="7">▼ Parametry fizyczne</td></tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Powierzchnia hali [m²]</div>
                <div class="q-id mono">HAL-V4-POW</div>
                <div class="q-hint">Powierzchnia użytkowa</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V4-POW-H1"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V4-POW-H2"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V4-POW-H3"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V4-POW-H4"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V4-POW-H5"></td>
            </tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Kubatura hali [m³]</div>
                <div class="q-id mono">HAL-V5-KUB</div>
                <div class="q-hint">Pow. × wysokość średnia</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V5-KUB-H1"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V5-KUB-H2"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V5-KUB-H3"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V5-KUB-H4"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V5-KUB-H5"></td>
            </tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Wysokość hali (średnia) [m]</div>
                <div class="q-id mono">HAL-V6-WYS</div>
                <div class="q-hint">Dla hal o nierównej wysokości — uśrednić</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V6-WYS-H1" step="0.1"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V6-WYS-H2" step="0.1"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V6-WYS-H3" step="0.1"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V6-WYS-H4" step="0.1"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V6-WYS-H5" step="0.1"></td>
            </tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Liczba bram zewnętrznych</div>
                <div class="q-id mono">HAL-V7-BRAMY</div>
                <div class="q-hint">Wpływ na infiltrację i kurtyny powietrzne</div>
              </td>
              <td class="td-comp"><span class="tag ur small">UR</span><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V7-BRAMY-H1"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V7-BRAMY-H2"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V7-BRAMY-H3"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V7-BRAMY-H4"></td>
              <td class="td-input"><input type="number" class="cell-input" data-id="HAL-V7-BRAMY-H5"></td>
            </tr>

            <tr class="row-section-header"><td colspan="7">▼ Charakterystyka techniczna</td></tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Jakość izolacji</div>
                <div class="q-id mono">HAL-V8-IZOLACJA</div>
                <div class="q-hint">Krytyczne dla bilansu cieplnego</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><select class="cell-input" data-id="HAL-V8-IZOLACJA-H1"><option value="">—</option><option>dobra</option><option>średnia</option><option>słaba</option><option>brak</option><option>nie wiem</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="HAL-V8-IZOLACJA-H2"><option value="">—</option><option>dobra</option><option>średnia</option><option>słaba</option><option>brak</option><option>nie wiem</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="HAL-V8-IZOLACJA-H3"><option value="">—</option><option>dobra</option><option>średnia</option><option>słaba</option><option>brak</option><option>nie wiem</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="HAL-V8-IZOLACJA-H4"><option value="">—</option><option>dobra</option><option>średnia</option><option>słaba</option><option>brak</option><option>nie wiem</option></select></td>
              <td class="td-input"><select class="cell-input" data-id="HAL-V8-IZOLACJA-H5"><option value="">—</option><option>dobra</option><option>średnia</option><option>słaba</option><option>brak</option><option>nie wiem</option></select></td>
            </tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Rok budowy / modernizacji</div>
                <div class="q-id mono">HAL-V9-WIEK</div>
                <div class="q-hint">np. „1985 / modernizacja izolacji 2010"</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="HAL-V9-WIEK-H1"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="HAL-V9-WIEK-H2"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="HAL-V9-WIEK-H3"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="HAL-V9-WIEK-H4"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="HAL-V9-WIEK-H5"></td>
            </tr>

            <tr>
              <td class="td-question">
                <div class="q-label">Link do rzutu hali / planu</div>
                <div class="q-id mono">HAL-V10-LINK-RZUT</div>
                <div class="q-hint">URL / referencja do E12</div>
              </td>
              <td class="td-comp"><span class="tag em small">EM</span></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="HAL-V10-LINK-RZUT-H1"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="HAL-V10-LINK-RZUT-H2"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="HAL-V10-LINK-RZUT-H3"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="HAL-V10-LINK-RZUT-H4"></td>
              <td class="td-input"><input type="text" class="cell-input" data-id="HAL-V10-LINK-RZUT-H5"></td>
            </tr>
          </tbody>
        </table>
      </div>
      <button class="row-add" type="button" id="add-hal-btn">+ Dodaj kolejną halę (Hala 6, 7...)</button>

    </div>
  </section>

  <!-- ============================================================ -->
  <!-- ETAP 6 · MACIERZ Hala × Wydział (alokacja %) — DYNAMICZNA   -->
  <!-- ============================================================ -->
  <section class="section" id="etap-6">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 6</div>
        <h2 class="section-title serif">Macierz Hala × Wydział — alokacja %</h2>
        <p class="section-desc">Każda hala alokowana w 100% między wydziały · WIERSZE = hale, KOLUMNY = wydziały · NOWE — wymóg EnPI per SEU</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-6">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Jak działa macierz alokacji:</strong>
        <ul>
          <li>Każda <strong>hala</strong> (z E5) musi być alokowana w sumie <strong>100%</strong> między wydziały (z E4).</li>
          <li>Przykład: <em>Hala G20 = 60% Spawalnia + 30% Magazyn + 10% Komunikacja</em> → wpisujesz 60, 30, 10 w odpowiednich kolumnach.</li>
          <li>Hala monofunkcyjna: 100% w jednej kolumnie.</li>
          <li>Status na końcu wiersza: <span class="status-ok">OK ✓</span> gdy 100%, <span class="status-error">⚠ Brakuje X%</span> / <span class="status-error">⚠ Nadmiar X%</span> przy odchyłce.</li>
          <li>Σ kolumny pokazuje % zakładu zajęty przez wydział (informacyjny).</li>
          <li>Macierz <strong>aktualizuje się automatycznie</strong> gdy dodasz wydział (E4) lub halę (E5).</li>
        </ul>
      </div>

      <div class="devices-wrap" id="macierz-wrap">
        <table class="devices-table" id="macierz-table">
          <thead>
            <tr id="macierz-header-row">
              <th class="th-question" style="min-width: 180px">↓ Hale / Wydziały →</th>
              <!-- Kolumny wydziałów + Σ + Status będą generowane dynamicznie -->
            </tr>
          </thead>
          <tbody id="macierz-body">
            <!-- Wiersze hal generowane dynamicznie -->
          </tbody>
          <tfoot id="macierz-foot">
            <!-- Wiersz Σ kolumn generowany dynamicznie -->
          </tfoot>
        </table>
      </div>

      <div style="margin-top: 12px; font-size: 12px; color: var(--ink-mute); font-style: italic;">
        ★ Macierz odbudowuje się automatycznie po wpisaniu nazw hal (E5) i wydziałów (E4). Wartości alokacji % zachowują się przy aktualizacji.
      </div>

    </div>
  </section>


    <!-- ============================================================ -->
  <!-- ETAP 7 · NOŚNIKI energii — taryfy i ceny                     -->
  <!-- ============================================================ -->
  <section class="section" id="etap-7">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 7</div>
        <h2 class="section-title serif">Nośniki energii — taryfy i ceny</h2>
        <p class="section-desc">8 nośników × ~7 atrybutów + PV/kogeneracja · 35 pól · czas: 30-45 min · PN-EN 16247-1 § 5.5 + ISO 50001 § 6.3</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-7">0 / 35</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Źródła danych:</strong>
        <ul>
          <li><strong>Faktury klienta</strong> (12 mies.) — ceny, taryfy, opłaty stałe</li>
          <li><strong>Umowy</strong> z dostawcami — moc umowna, terminy</li>
          <li><strong>Pomijamy nośniki nieużywane</strong> (np. olej, jeśli klient nie ma) — nie wypełniamy ich pól</li>
          <li>Cena netto = bez VAT. Akcyza dla en.el. i paliw. Opłaty stałe = abonament + dystrybucja.</li>
        </ul>
      </div>

      <div class="group">
        <div class="group-title">Nośnik 1 — Energia elektryczna</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Dostawca en. elektrycznej</div>
            <div class="field-id mono">NOS-EE-DOSTAWCA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="NOS-EE-DOSTAWCA" placeholder="np. PGE Obrót, Tauron, Innogy">
            <div class="field-hint">Nazwa firmy z faktury</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Grupa taryfowa</div>
            <div class="field-id mono">NOS-EE-TARYFA</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="NOS-EE-TARYFA">
              <option value="">— wybierz —</option>
              <option>A21</option><option>A22</option><option>A23</option>
              <option>B11</option><option>B21</option><option>B22</option><option>B23</option>
              <option>C11</option><option>C12a</option><option>C12b</option><option>C21</option><option>C22a</option><option>C22b</option>
              <option>G11</option><option>G12</option><option>G12w</option><option>G13</option>
              <option>inne</option>
            </select>
            <div class="field-hint">Z umowy / faktury</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Cena netto za energię (średnia ważona)</div>
            <div class="field-id mono">NOS-EE-CENA-NETTO</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-EE-CENA-NETTO" placeholder="np. 850" step="0.01">
            <div class="field-hint">Z faktury — najnowsza cena. Jeśli kontrakt z różnymi cenami w/poza szczyt — średnia ważona.</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN/MWh</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Akcyza</div>
            <div class="field-id mono">NOS-EE-AKCYZA</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-EE-AKCYZA" placeholder="5.00" step="0.01" value="5.00">
            <div class="field-hint">Standard 5,00 PLN/MWh. Zakład produkcyjny może mieć obniżkę do 3,00 PLN/MWh.</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN/MWh</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Opłata mocowa</div>
            <div class="field-id mono">NOS-EE-OPLATA-MOCY</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-EE-OPLATA-MOCY" placeholder="np. 23.50" step="0.01">
            <div class="field-hint">Nowa od 2021. Per kW mocy umownej, miesięcznie.</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN/kW/mies</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Opłaty dystrybucyjne (zmienne)</div>
            <div class="field-id mono">NOS-EE-DYSTRYBUCJA</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-EE-DYSTRYBUCJA" placeholder="np. 180" step="0.01">
            <div class="field-hint">Z faktury — średnia ważona</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN/MWh</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Moc umowna</div>
            <div class="field-id mono">NOS-EE-MOC</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-EE-MOC" placeholder="np. 800">
            <div class="field-hint">Z umowy z OSD</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">kW</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Numer umowy + termin</div>
            <div class="field-id mono">NOS-EE-UMOWA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="NOS-EE-UMOWA" placeholder="np. UA-12345/2023, do 31.12.2026">
            <div class="field-hint">Z umowy</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Nośnik 2 — Gaz ziemny</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Dostawca gazu</div>
            <div class="field-id mono">NOS-GAZ-DOSTAWCA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="NOS-GAZ-DOSTAWCA" placeholder="np. PGNiG OD, Polenergia, Hermes">
            <div class="field-hint">Z faktury</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Grupa taryfowa</div>
            <div class="field-id mono">NOS-GAZ-TARYFA</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="NOS-GAZ-TARYFA">
              <option value="">— wybierz —</option>
              <option>W-1.1</option><option>W-1.2</option><option>W-1.12</option>
              <option>W-2.1</option><option>W-2.2</option><option>W-2.12</option>
              <option>W-3.6</option><option>W-3.9</option><option>W-4</option>
              <option>W-5.1</option><option>W-6</option>
              <option>inne</option>
            </select>
            <div class="field-hint">Z umowy</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Cena netto za gaz</div>
            <div class="field-id mono">NOS-GAZ-CENA-NETTO</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-GAZ-CENA-NETTO" placeholder="np. 2.85" step="0.01">
            <div class="field-hint">Z faktury</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN/m³</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Akcyza</div>
            <div class="field-id mono">NOS-GAZ-AKCYZA</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-GAZ-AKCYZA" placeholder="1.28" step="0.01" value="1.28">
            <div class="field-hint">Standard 1,28 PLN/m³</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN/m³</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Opłaty dystrybucyjne</div>
            <div class="field-id mono">NOS-GAZ-DYSTRYBUCJA</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-GAZ-DYSTRYBUCJA" placeholder="np. 0.45" step="0.01">
            <div class="field-hint">Z faktury</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN/m³</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Wartość opałowa gazu</div>
            <div class="field-id mono">NOS-GAZ-WARTOSC-OP</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-GAZ-WARTOSC-OP" placeholder="35" step="0.1" value="35">
            <div class="field-hint">Standardowa: 35-36 MJ/m³ dla gazu wysokometanowego E</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">MJ/m³</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Numer umowy + termin</div>
            <div class="field-id mono">NOS-GAZ-UMOWA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="NOS-GAZ-UMOWA" placeholder="—">
            <div class="field-hint">Z umowy</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Nośnik 3 — Ciepło sieciowe</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Dostawca ciepła sieciowego</div>
            <div class="field-id mono">NOS-CIEPLO-DOSTAWCA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="NOS-CIEPLO-DOSTAWCA" placeholder="np. PGE GiEK, Veolia, Fortum">
            <div class="field-hint">Z faktury</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Cena ciepła (zmienne, netto)</div>
            <div class="field-id mono">NOS-CIEPLO-CENA-NETTO</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-CIEPLO-CENA-NETTO" placeholder="np. 95" step="0.01">
            <div class="field-hint">Z faktury</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN/GJ</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Opłata stała / abonament</div>
            <div class="field-id mono">NOS-CIEPLO-OPLATA-STALA</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-CIEPLO-OPLATA-STALA" placeholder="np. 8500" step="0.01">
            <div class="field-hint">Per zamówiona moc cieplna, miesięcznie</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN/MW/mies</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Zamówiona moc cieplna</div>
            <div class="field-id mono">NOS-CIEPLO-MOC</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-CIEPLO-MOC" placeholder="np. 2.5" step="0.01">
            <div class="field-hint">Z umowy</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">MW</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Nośnik 4 — Olej opałowy</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Dostawca oleju</div>
            <div class="field-id mono">NOS-OLEJ-DOSTAWCA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="NOS-OLEJ-DOSTAWCA" placeholder="np. Orlen, Shell, BP">
            <div class="field-hint">Z faktur. Pominąć jeśli klient nie używa.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Cena netto (średnia roczna)</div>
            <div class="field-id mono">NOS-OLEJ-CENA-NETTO</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-OLEJ-CENA-NETTO" placeholder="np. 4.20" step="0.01">
            <div class="field-hint">Z faktur — średnia roczna (ceny się zmieniają)</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN/l</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Wartość opałowa</div>
            <div class="field-id mono">NOS-OLEJ-WARTOSC-OP</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-OLEJ-WARTOSC-OP" placeholder="38" step="0.1" value="38">
            <div class="field-hint">Standardowa: 36-38 MJ/l</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">MJ/l</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Nośniki 5-8 — pozostałe (opcjonalne)</div>
        <div class="group-desc">Wypełnić tylko jeśli klient używa. Pominąć puste.</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">LPG — dostawca</div>
            <div class="field-id mono">NOS-LPG-DOSTAWCA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="NOS-LPG-DOSTAWCA" placeholder="np. Gaspol">
            <div class="field-hint">Pomiń jeśli klient nie używa</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">LPG — cena netto</div>
            <div class="field-id mono">NOS-LPG-CENA-NETTO</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-LPG-CENA-NETTO" placeholder="np. 3.20" step="0.01">
            <div class="field-hint">—</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN/kg</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">LNG — dostawca</div>
            <div class="field-id mono">NOS-LNG-DOSTAWCA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="NOS-LNG-DOSTAWCA" placeholder="—">
            <div class="field-hint">Marginalny w PL — zwykle pominąć</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">LNG — cena netto</div>
            <div class="field-id mono">NOS-LNG-CENA-NETTO</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-LNG-CENA-NETTO" placeholder="—" step="0.01">
            <div class="field-hint">—</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN/kg</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Para technologiczna — dostawca</div>
            <div class="field-id mono">NOS-PARA-DOSTAWCA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="NOS-PARA-DOSTAWCA" placeholder="—">
            <div class="field-hint">Często własna kotłownia — pomiń</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Para — cena netto</div>
            <div class="field-id mono">NOS-PARA-CENA-NETTO</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-PARA-CENA-NETTO" placeholder="—" step="0.01">
            <div class="field-hint">—</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN/t</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Biomasa — rodzaj</div>
            <div class="field-id mono">NOS-BIO-RODZAJ</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="NOS-BIO-RODZAJ">
              <option value="">— wybierz lub pomiń —</option>
              <option>Pellet</option>
              <option>Zrębki</option>
              <option>RDF</option>
              <option>Inne</option>
            </select>
            <div class="field-hint">Pomiń jeśli klient nie używa</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Biomasa — cena netto</div>
            <div class="field-id mono">NOS-BIO-CENA-NETTO</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-BIO-CENA-NETTO" placeholder="—" step="0.01">
            <div class="field-hint">—</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">PLN/t</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Biomasa — wartość opałowa</div>
            <div class="field-id mono">NOS-BIO-WARTOSC-OP</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-BIO-WARTOSC-OP" placeholder="—" step="0.1">
            <div class="field-hint">Pellet: 18-20 MJ/kg, zrębki: 12-15 MJ/kg</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">MJ/kg</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Własna produkcja energii</div>
        <div class="group-desc">PV / kogeneracja / odzysk ciepła odpadowego</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Moc instalacji PV</div>
            <div class="field-id mono">NOS-PV-MOC</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-PV-MOC" placeholder="0 jeśli brak">
            <div class="field-hint">Wpisz 0 jeśli brak. W kWp.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">kWp</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Produkcja PV rocznie</div>
            <div class="field-id mono">NOS-PV-PROD-ROK</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-PV-PROD-ROK" placeholder="0 jeśli brak" step="0.1">
            <div class="field-hint">Z liczników. 0 jeśli brak.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">MWh/rok</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Moc kogeneracji</div>
            <div class="field-id mono">NOS-KOGEN-MOC</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="NOS-KOGEN-MOC" placeholder="0 jeśli brak">
            <div class="field-hint">CHP (Combined Heat &amp; Power). 0 jeśli brak.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">kW</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Odzysk ciepła odpadowego</div>
            <div class="field-id mono">NOS-ODZYSK</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="NOS-ODZYSK">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>planowany</option>
              <option>częściowo</option>
              <option>nie wiem</option>
            </select>
            <div class="field-hint">Czy klient odzyskuje ciepło ze spalin / sprężarek / chłodzenia?</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

    </div>
  </section>

  <!-- ============================================================ -->
  <!-- ETAP 8 · ZUŻYCIA roczne — tabela 36 mies × 9 nośników        -->
  <!-- ============================================================ -->
  <section class="section" id="etap-8">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 8</div>
        <h2 class="section-title serif">Zużycia roczne z faktur</h2>
        <p class="section-desc">36 miesięcy × 9 nośników z auto-konwersją na MWh · ISO 50001 § 6.3 (Historical Energy Data)</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-8">0 / 0</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Jak wypełnić:</strong>
        <ul>
          <li><strong>Wiersze</strong> = miesiące (12-36 mies.). Każdy miesiąc ma rok do wpisania w drugiej kolumnie.</li>
          <li><strong>Kolumny</strong> = nośniki energii w odpowiednich jednostkach (MWh, m³, GJ, l, kg, t).</li>
          <li><strong>Σ MWh</strong> = automatyczna konwersja zużyć na MWh dla porównań energetycznych.</li>
          <li><strong>Statystyki rocze</strong> wyliczane automatycznie (Σ rok 1/2/3, średnia, MIN, MAX).</li>
          <li>Najlepiej 24-36 mies. żeby zobaczyć trendy. Min. 12 mies. dla baseline EnPI.</li>
        </ul>
      </div>

      <div class="devices-wrap" style="max-height: 600px; overflow-y: auto;">
        <table class="devices-table" id="zuzycia-table">
          <thead>
            <tr>
              <th class="th-question" style="min-width: 110px">Miesiąc</th>
              <th class="th-instance" style="min-width: 70px">Rok</th>
              <th class="th-instance" style="min-width: 90px">Energia<br>elektryczna<br>[MWh]</th>
              <th class="th-instance" style="min-width: 80px">Gaz ziemny<br>[m³]</th>
              <th class="th-instance" style="min-width: 80px">Ciepło<br>sieciowe<br>[GJ]</th>
              <th class="th-instance" style="min-width: 80px">Olej<br>opałowy<br>[l]</th>
              <th class="th-instance" style="min-width: 70px">Gaz LPG<br>[kg]</th>
              <th class="th-instance" style="min-width: 70px">LNG<br>[kg]</th>
              <th class="th-instance" style="min-width: 80px">Para<br>technol.<br>[t]</th>
              <th class="th-instance" style="min-width: 70px">Biomasa<br>[t]</th>
              <th class="th-instance" style="min-width: 80px">Produkcja<br>PV (własna)<br>[MWh]</th>
              <th class="th-instance" style="min-width: 80px; background: var(--gold)">Σ [MWh]</th>
            </tr>
          </thead>
          <tbody id="zuzycia-body">
            <!-- 36 wierszy generowanych dynamicznie -->
          </tbody>
          <tfoot id="zuzycia-foot">
            <!-- Statystyki: Σ rok 1, 2, 3 + średnia + MIN + MAX -->
          </tfoot>
        </table>
      </div>

      <div style="margin-top: 12px; font-size: 11px; color: var(--ink-mute); font-style: italic;">
        ★ Konwersje na MWh: EE×1 · Gaz×0.0097 (35 MJ/m³) · Ciepło×0.278 (1 GJ=0.278 MWh) · Olej×0.0105 (38 MJ/l) · LPG×0.0128 · LNG×0.014 · Para×0.7 (2.5 GJ/t) · Biomasa×4.5 (16 GJ/t) · PV×1
      </div>

    </div>
  </section>


    <!-- ============================================================ -->
  <!-- ETAP 9 · ZMIENNE ISTOTNE (Relevant Variables)                -->
  <!-- ============================================================ -->
  <section class="section" id="etap-9">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 9</div>
        <h2 class="section-title serif">Zmienne istotne</h2>
        <p class="section-desc">HDD/CDD, produkcja, zatrudnienie, korelacja R² · 10 pól · ISO 50001 § 6.4 (Relevant Variables) · mianownik dla EnPI</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-9">0 / 10</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Co to są "zmienne istotne":</strong>
        <ul>
          <li>Czynniki które <strong>wpływają na zużycie energii</strong> (produkcja, klimat, zatrudnienie, mix asortymentowy).</li>
          <li>Bez nich EnPI nie ma sensu (np. „kWh/szt nadwozia" nie pokaże postępu jeśli zmienia się asortyment).</li>
          <li>ISO 50001 § 6.4 wymaga <strong>identyfikacji i monitorowania</strong>.</li>
        </ul>
      </div>

      <div class="group">
        <div class="group-title">Zmienne klimatyczne</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Stopniodni grzewcze (HDD) — historia 36 mies.</div>
            <div class="field-id mono">ZMI-V1-HDD</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="ZMI-V1-HDD" placeholder='np. "2022: 3450, 2023: 3380, 2024: 3520 K·dni" lub link do CSV w E12'></textarea>
            <div class="field-hint">Tabela miesięczna z stacji meteo. Dla LH/AHU baseline.</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">K·dni</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Stopniodni chłodzenia (CDD) — historia 36 mies.</div>
            <div class="field-id mono">ZMI-V2-CDD</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="ZMI-V2-CDD" placeholder='np. "2022: 220, 2023: 280, 2024: 250 K·dni"'></textarea>
            <div class="field-hint">Dla AHU/chłodnictwa baseline.</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">K·dni</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Średnia temperatura miesięczna</div>
            <div class="field-id mono">ZMI-V3-TEMP-AVG</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="ZMI-V3-TEMP-AVG" placeholder="z stacji meteo najbliższej">
            <div class="field-hint">Z stacji meteo najbliższej zakładu</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">°C</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Zmienne produkcyjne</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Wielkość produkcji miesięczna (12-36 mies.)</div>
            <div class="field-id mono">ZMI-V4-PRODUKCJA-MIES</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="ZMI-V4-PRODUKCJA-MIES" placeholder='Tabela miesięczna lub link do CSV w E12'></textarea>
            <div class="field-hint">KLUCZOWE dla EnPI baseline. Najlepiej 36 mies.</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">jedn./mies</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Mix asortymentowy (% udział głównych SKU)</div>
            <div class="field-id mono">ZMI-V5-ASORTYMENT-MIX</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="ZMI-V5-ASORTYMENT-MIX" placeholder='np. "Produkt A 60%, B 30%, C 10%"'></textarea>
            <div class="field-hint">Czy zmienność produktów wpływa na zużycie? (np. produkt A — energooszczędny, B — energochłonny)</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span><span class="tag kier">KIER</span></div>
          <div class="field-unit">%</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Wydajność / efektywność procesu</div>
            <div class="field-id mono">ZMI-V6-WYDAJNOSC</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="ZMI-V6-WYDAJNOSC" placeholder='np. "OEE 78%, braki 2.3%"'>
            <div class="field-hint">np. „liczba braków %", „OEE %" — wpływ na energię/jednostkę</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span><span class="tag kier">KIER</span></div>
          <div class="field-unit">%</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Zmienne operacyjne</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Zatrudnienie (etaty) — średnia miesięczna</div>
            <div class="field-id mono">ZMI-V7-ZATRUDNIENIE</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="ZMI-V7-ZATRUDNIENIE" placeholder="np. 850">
            <div class="field-hint">Wpływ na zyski wewnętrzne, oświetlenie</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">osób</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Godziny pracy zakładu (/mies)</div>
            <div class="field-id mono">ZMI-V8-GODZINY-PRACY</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="ZMI-V8-GODZINY-PRACY" placeholder="np. 480">
            <div class="field-hint">Σ godzin pracy zmianowej × liczba zmian × dni roboczych</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span><span class="tag kier">KIER</span></div>
          <div class="field-unit">h/mies</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Walidacja statystyczna (KON wylicza)</div>
        <div class="group-desc">Konsultant ENESA wypełnia po analizie zużyć z E8 i zmiennych z V1-V8</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Korelacja zużycia ze zmienną — R²</div>
            <div class="field-id mono">ZMI-V9-KORELACJA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="ZMI-V9-KORELACJA" placeholder='np. "EE-Produkcja: R²=0.84, EE-HDD: R²=0.32"'>
            <div class="field-hint">Współczynnik R² regresji liniowej. R² &gt; 0.7 = mocna korelacja.</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Model EnPI baseline</div>
            <div class="field-id mono">ZMI-V10-MODEL</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="ZMI-V10-MODEL" placeholder='np. "Zużycie EE [MWh] = 0.12 × Produkcja + 0.045 × HDD + 250"'></textarea>
            <div class="field-hint">Funkcja regresji wielokrotnej. ISO 50001 § 6.4.</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

    </div>
  </section>

  <!-- ============================================================ -->
  <!-- ETAP 10 · STATUS EnMS                                        -->
  <!-- ============================================================ -->
  <section class="section" id="etap-10">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 10</div>
        <h2 class="section-title serif">Status systemu zarządzania energią (EnMS)</h2>
        <p class="section-desc">ISO 50001 — czy klient ma EnMS · 8 pól · czas: 10 min</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-10">0 / 8</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group">
        <div class="group-title">Status certyfikacji</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Czy klient ma certyfikat ISO 50001?</div>
            <div class="field-id mono">ENMS-V1-CERTYFIKAT</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="ENMS-V1-CERTYFIKAT">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w trakcie wdrażania</option>
              <option>planowane</option>
              <option>nie wiem</option>
            </select>
            <div class="field-hint">Status certyfikacji ISO 50001:2018</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Data certyfikacji</div>
            <div class="field-id mono">ENMS-V2-DATA-CERT</div>
          </div>
          <div class="field-input-wrap">
            <input type="date" class="field-input" data-id="ENMS-V2-DATA-CERT">
            <div class="field-hint">Tylko jeśli V1=TAK</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">data</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Jednostka certyfikująca</div>
            <div class="field-id mono">ENMS-V3-CERT-AUDYTOR</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="ENMS-V3-CERT-AUDYTOR">
              <option value="">— wybierz —</option>
              <option>TÜV</option>
              <option>BSI</option>
              <option>DEKRA</option>
              <option>DNV</option>
              <option>LRQA</option>
              <option>Bureau Veritas</option>
              <option>inna</option>
            </select>
            <div class="field-hint">Akredytowana jednostka certyfikująca</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Termin następnej recertyfikacji</div>
            <div class="field-id mono">ENMS-V4-CERT-TERMIN</div>
          </div>
          <div class="field-input-wrap">
            <input type="date" class="field-input" data-id="ENMS-V4-CERT-TERMIN">
            <div class="field-hint">Co 3 lata</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">data</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Baseline i cele (jeśli istnieją)</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Rok bazowy (Energy Baseline)</div>
            <div class="field-id mono">ENMS-V5-BASELINE-ROK</div>
          </div>
          <div class="field-input-wrap">
            <input type="number" class="field-input" data-id="ENMS-V5-BASELINE-ROK" placeholder="np. 2022" min="2000" max="2050">
            <div class="field-hint">Jeśli klient ma już zdefiniowany baseline</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">rok</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Lista istniejących EnPI</div>
            <div class="field-id mono">ENMS-V6-ENPI-LIST</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="ENMS-V6-ENPI-LIST" placeholder='np. "kWh/szt nadwozia, kWh/m², GJ/tonę produktu"'></textarea>
            <div class="field-hint">Lista wskaźników już używanych przez klienta</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Cele redukcji energii (% w okresie)</div>
            <div class="field-id mono">ENMS-V7-CELE</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="ENMS-V7-CELE" placeholder='np. "-15% energii / produkcja do 2027 vs 2024"'>
            <div class="field-hint">Cele zarządu / korporacji-matki</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span><span class="tag top">TOP</span></div>
          <div class="field-unit">%</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Cele osiągnięte do tej pory</div>
            <div class="field-id mono">ENMS-V8-OSIAGNIETE</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="ENMS-V8-OSIAGNIETE" placeholder='np. "-8% w 2024 vs 2022"'>
            <div class="field-hint">Postęp w realizacji celów</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span><span class="tag top">TOP</span></div>
          <div class="field-unit">%</div>
        </div>
      </div>

    </div>
  </section>

  <!-- ============================================================ -->
  <!-- ETAP 11 · KONTEKST i Liderstwo (NOWE - § 4-5 ISO 50001)      -->
  <!-- ============================================================ -->
  <section class="section" id="etap-11">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 11</div>
        <h2 class="section-title serif">Kontekst i liderstwo</h2>
        <p class="section-desc">ISO 50001 § 4 (Context) + § 5 (Leadership) · 12 pól · czas: 30-45 min · sesja otwierająca</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-11">0 / 12</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Sekcje § 4-5 ISO 50001:2018:</strong>
        <ul>
          <li><strong>§ 4.1</strong> — Czynniki wewnętrzne i zewnętrzne wpływające na EnMS</li>
          <li><strong>§ 4.2</strong> — Interested parties (strony zainteresowane EnMS)</li>
          <li><strong>§ 4.3</strong> — Wymogi prawne i inne</li>
          <li><strong>§ 5.2</strong> — Polityka energetyczna (przeglądana min. 1×/rok)</li>
          <li><strong>§ 5.3</strong> — Energy Manager — kompetencje, dedykacja</li>
        </ul>
      </div>

      <div class="group">
        <div class="group-title">Czynniki wewnętrzne (§ 4.1)</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Czynniki wewnętrzne TECHNOLOGICZNE</div>
            <div class="field-id mono">KON-V1-WEWN-TECH</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="KON-V1-WEWN-TECH" placeholder='np. "starzejące się urządzenia, nowa linia produkcyjna 2025, brak BMS w hali B"'></textarea>
            <div class="field-hint">Stan techniczny zakładu, planowane modernizacje</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span><span class="tag top">TOP</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Czynniki wewnętrzne ORGANIZACYJNE</div>
            <div class="field-id mono">KON-V2-WEWN-ORG</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="KON-V2-WEWN-ORG" placeholder='np. "brak Energy Managera dedykowanego, kultura nie nastawiona na oszczędność, silne UR"'></textarea>
            <div class="field-hint">Struktura organizacyjna, kultura, zasoby ludzkie</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span><span class="tag top">TOP</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Czynniki wewnętrzne FINANSOWE</div>
            <div class="field-id mono">KON-V3-WEWN-FIN</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="KON-V3-WEWN-FIN" placeholder='np. "CAPEX ograniczony do 5 mln PLN/rok, brak osobnego budżetu energii, presja zarządu na koszty"'></textarea>
            <div class="field-hint">Budżety, dostępność CAPEX, presje finansowe</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span><span class="tag top">TOP</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Czynniki zewnętrzne (§ 4.1)</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Czynniki zewnętrzne REGULACYJNE</div>
            <div class="field-id mono">KON-V4-ZEWN-REG</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="KON-V4-ZEWN-REG" placeholder='np. "Dyrektywa EED 2023 — audyt obowiązkowy, CSRD raportowanie, CBAM dla eksportu, BAT/BREF"'></textarea>
            <div class="field-hint">Przepisy nakładające obowiązki energetyczne</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Czynniki zewnętrzne RYNKOWE</div>
            <div class="field-id mono">KON-V5-ZEWN-RYNEK</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="KON-V5-ZEWN-RYNEK" placeholder='np. "wzrost cen energii 2022-2025, presja klientów na ślad węglowy, konkurencja energooszczędna"'></textarea>
            <div class="field-hint">Trendy cenowe, oczekiwania klientów, konkurencja</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span><span class="tag top">TOP</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Czynniki zewnętrzne TECHNOLOGICZNE</div>
            <div class="field-id mono">KON-V6-ZEWN-TECH</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="KON-V6-ZEWN-TECH" placeholder='np. "dotacje FENG na PV i pompy ciepła, rozwój wodoru, elektryfikacja procesów"'></textarea>
            <div class="field-hint">Dotacje, nowe technologie, kierunki rozwoju</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Interested parties (§ 4.2)</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Strony wewnętrzne zainteresowane EnMS</div>
            <div class="field-id mono">KON-V7-INTERES-WEWN</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="KON-V7-INTERES-WEWN" placeholder='Tabela: rola (Zarząd / Pracownicy / Związki / R&D) × interes × wpływ na decyzje'></textarea>
            <div class="field-hint">Kto wewnątrz firmy ma wpływ / interes w EnMS</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span><span class="tag top">TOP</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Strony zewnętrzne zainteresowane</div>
            <div class="field-id mono">KON-V8-INTERES-ZEWN</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="KON-V8-INTERES-ZEWN" placeholder='Tabela: rola (Korporacja-matka / Bank / Klienci / Sąsiedzi / Regulator / NGO) × interes × wpływ'></textarea>
            <div class="field-hint">Kto poza firmą ma wpływ / interes (klienci wymagający certyfikatów, banki wymagające ESG, korporacja-matka, regulator)</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span><span class="tag top">TOP</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Wymogi prawne i inne mające wpływ</div>
            <div class="field-id mono">KON-V9-WYMOGI-EXT</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="KON-V9-WYMOGI-EXT" placeholder='Lista: Ustawa o EE 2016 / Dyrektywa EED 2023 / Ustawa Prawo energetyczne / przepisy lokalne'></textarea>
            <div class="field-hint">Lista przepisów których trzeba przestrzegać. Wymóg ISO 50001 § 4.3.</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Polityka energetyczna (§ 5.2)</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Czy klient ma politykę energetyczną?</div>
            <div class="field-id mono">KON-V10-POLITYKA-EXIST</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="KON-V10-POLITYKA-EXIST">
              <option value="">— wybierz —</option>
              <option>TAK (mam dokument)</option>
              <option>NIE</option>
              <option>w opracowaniu</option>
              <option>nie wiem</option>
            </select>
            <div class="field-hint">Polityka jest podstawą EnMS — wymóg § 5.2</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Data zatwierdzenia polityki</div>
            <div class="field-id mono">KON-V11-POLITYKA-DATA</div>
          </div>
          <div class="field-input-wrap">
            <input type="date" class="field-input" data-id="KON-V11-POLITYKA-DATA">
            <div class="field-hint">Polityka powinna być przeglądana min. 1×/rok</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">data</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Energy Manager — kto, dedykacja %</div>
            <div class="field-id mono">KON-V12-EM-DEDIKACJA</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="KON-V12-EM-DEDIKACJA" placeholder='np. "Jan Kowalski, Kierownik UR, 30% czasu poświęca na EnMS, kompetencje ISO 50001 — szkolenie 2023"'></textarea>
            <div class="field-hint">Imię, stanowisko, % czasu, czy ma odpowiednie kompetencje (ISO 50001 § 7.2)</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span><span class="tag top">TOP</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

    </div>
  </section>

  <!-- ============================================================ -->
  <!-- ETAP 12 · HISTORIA + dokumenty źródłowe                      -->
  <!-- ============================================================ -->
  <section class="section" id="etap-12">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 12</div>
        <h2 class="section-title serif">Historia działań + dokumenty źródłowe</h2>
        <p class="section-desc">Modernizacje, audyty, dofinansowania, rejestr dokumentów · 10 pól · PN-EN 16247-1 § 5.5</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-12">0 / 10</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">postęp etapu</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group">
        <div class="group-title">Modernizacje energetyczne ostatnich 5 lat</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Lista modernizacji (data, opis, oszczędność)</div>
            <div class="field-id mono">HIS-V1-MODERN-LIST</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="HIS-V1-MODERN-LIST" placeholder='Tabela:
2023 — wymiana opraw na LED, 350 tys PLN, -120 MWh/rok
2022 — modernizacja sprężarkowni, 800 tys PLN, -180 MWh/rok
2021 — instalacja PV 200 kWp, 950 tys PLN, -180 MWh/rok' style="min-height: 100px"></textarea>
            <div class="field-hint">Wszystkie projekty energetyczne za ostatnie 5 lat</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Wcześniejsze audyty energetyczne</div>
        <div class="group-info">
          Wg Ustawy o EE 2016 audyt jest <strong>obowiązkowy co 4 lata</strong> dla dużych przedsiębiorstw.
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Lista poprzednich audytów</div>
            <div class="field-id mono">HIS-V2-AUDYTY-LIST</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="HIS-V2-AUDYTY-LIST" placeholder='Tabela:
2020 — Energotest, rekomendacje: LED, modernizacja CA, ZREALIZOWANE 60%
2016 — KAPE, rekomendacje: BMS, izolacja, ZREALIZOWANE 30%' style="min-height: 80px"></textarea>
            <div class="field-hint">Rok × Audytor × Główne rekomendacje × Status realizacji</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Dofinansowania / środki pomocowe</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Białe certyfikaty</div>
            <div class="field-id mono">HIS-V3-BC</div>
          </div>
          <div class="field-input-wrap">
            <select class="field-select" data-id="HIS-V3-BC">
              <option value="">— wybierz —</option>
              <option>otrzymane</option>
              <option>aplikowane</option>
              <option>planowane</option>
              <option>nie korzystaliśmy</option>
              <option>nie wiem</option>
            </select>
            <div class="field-hint">Status korzystania z systemu Białych certyfikatów (URE)</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">FENG / FE / inne fundusze EU</div>
            <div class="field-id mono">HIS-V4-FENG</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="HIS-V4-FENG" placeholder='np. "Kredyt ekologiczny FENG na PV — w trakcie wniosku"'>
            <div class="field-hint">Programy wsparcia europejskiego</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">NFOŚiGW / WFOŚiGW</div>
            <div class="field-id mono">HIS-V5-NFOS</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="HIS-V5-NFOS" placeholder="—">
            <div class="field-hint">Programy pomocowe — czy klient korzystał</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Europejski Bank Inwestycyjny / inne</div>
            <div class="field-id mono">HIS-V6-EBI</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="HIS-V6-EBI" placeholder="—">
            <div class="field-hint">EBI, EBOR, kredyty preferencyjne</div>
          </div>
          <div class="kto-cell"><span class="tag em">EM</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Rejestr dokumentów źródłowych</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Tabela dokumentów źródłowych</div>
            <div class="field-id mono">HIS-V7-DOK-LIST</div>
          </div>
          <div class="field-input-wrap">
            <textarea class="field-textarea" data-id="HIS-V7-DOK-LIST" placeholder='Tabela: Nazwa × Typ (faktura/schemat/DTR/raport/certyfikat/umowa) × Rok × Źródło × Kto przekazał × Data otrzymania × Wykorzystanie w audycie' style="min-height: 100px"></textarea>
            <div class="field-hint">Wszystkie dokumenty pobrane od klienta podczas audytu — wymóg traceability</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <div class="group">
        <div class="group-title">Kontrola wersji Master Form</div>
        <div class="group-desc">Metadane wewnętrzne — Konsultant aktualizuje przy każdej modyfikacji</div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Wersja Master Form</div>
            <div class="field-id mono">HIS-V8-WERSJA</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="HIS-V8-WERSJA" placeholder='np. "v0.1 (szkielet)", "v1.0 (po sesji otwierającej)"'>
            <div class="field-hint">Wersjonowanie pliku — np. v0.1, v1.0, v1.1</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Data ostatniej edycji</div>
            <div class="field-id mono">HIS-V9-DATA-EDIT</div>
          </div>
          <div class="field-input-wrap">
            <input type="date" class="field-input" data-id="HIS-V9-DATA-EDIT">
            <div class="field-hint">Data ostatniej zmiany pliku</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">data</div>
        </div>

        <div class="field">
          <div class="field-label">
            <div class="field-q">Autor ostatniej edycji</div>
            <div class="field-id mono">HIS-V10-AUTOR-EDIT</div>
          </div>
          <div class="field-input-wrap">
            <input type="text" class="field-input" data-id="HIS-V10-AUTOR-EDIT" placeholder="np. inż. S. Kowalski">
            <div class="field-hint">Imię + nazwisko Konsultanta ENESA</div>
          </div>
          <div class="kto-cell"><span class="tag kon">KON</span></div>
          <div class="field-unit">—</div>
        </div>
      </div>

      <!-- Końcowy banner — koniec formularza -->
      <div class="group" style="background: linear-gradient(135deg, var(--green-deep) 0%, var(--green-primary) 100%); color: white; margin-top: 32px; padding: 32px;">
        <div style="font-family: var(--serif); font-size: 22px; font-weight: 600; margin-bottom: 8px;">
          ▮ Koniec formularza Master
        </div>
        <div style="font-size: 14px; line-height: 1.6; opacity: 0.95;">
          Po wypełnieniu wszystkich 13 etapów Master Form jest gotowy do <strong>delegacji scope formularzy</strong>:
          <ul style="margin: 12px 0 12px 24px; line-height: 1.8;">
            <li><strong>LH</strong> (Local Heating) — kurtyny, AGW, promienniki, nagrzewnice</li>
            <li><strong>AHU</strong> (Wentylacja) — centrale wentylacyjne, BMS, harmonogramy</li>
            <li><strong>CA</strong> (Compressed Air) — sprężarki, sieć, odbiorcy + alokacja % per wydział</li>
            <li><strong>Inne scope</strong> — oświetlenie, chłodnictwo, kotłownia, etc.</li>
          </ul>
          Każdy scope formularz <strong>czyta dane globalne z Master</strong> przez external links — Single Source of Truth.
        </div>
      </div>

    </div>
  </section>

  <section class="section" id="etap-13">
    <div class="section-head">
      <div>
        <div class="section-eyebrow">ETAP 13 ★</div>
        <h2 class="section-title serif">Zakres audytu — instalacje i systemy</h2>
        <p class="section-desc">Macierz instalacji występujących w zakładzie + zaznaczenie które są <strong>audytowane w tym przeglądzie</strong>. 18 pozycji w 5 kategoriach + pole "Inne". Wybór tutaj steruje <strong>dashboardem scope</strong> (pokazującym status każdego formularza scope) i <strong>linkami do plików HTML</strong>. Zalecenie PN-EN 16247-1: identyfikacja i opis SEU (Significant Energy Users).</p>
      </div>
      <div class="section-meta">
        <div class="section-progress" data-etap="etap-13">0 / 18</div>
        <div style="font-size: 11px; color: var(--ink-mute); margin-top: 4px;">wybranych instalacji</div>
      </div>
    </div>

    <div class="section-body">

      <div class="group-info">
        <strong>Jak wypełnić macierz E13:</strong>
        <ul>
          <li><strong>"Występuje?"</strong> — czy ta instalacja istnieje w zakładzie (niezależnie od audytu)</li>
          <li><strong>"Audytowana?"</strong> — czy ta instalacja jest objęta <strong>tym audytem energetycznym</strong></li>
          <li>Można mieć instalację która <strong>występuje, ale nie jest audytowana</strong> (np. CHP planowane, ale poza zakresem tego audytu)</li>
          <li>Dla instalacji audytowanych — w dashboardzie poniżej będą widoczne <strong>linki do formularzy scope</strong> z postępem</li>
          <li>Pole "Inne" — wolny tekst dla niestandardowych instalacji (np. spalarnia odpadów, biogazownia, elektroliza wodoru)</li>
        </ul>
      </div>

      <div class="scope-matrix-wrapper">
        <table class="scope-matrix">
          <thead>
            <tr>
              <th class="th-scope-kod" style="width: 80px;">Kod</th>
              <th class="th-scope-name">Instalacja / system</th>
              <th class="th-scope-exist" style="width: 130px;">Występuje?</th>
              <th class="th-scope-audit" style="width: 130px;">Audytowana?</th>
            </tr>
          </thead>
          <tbody>
            
        <tr class="row-cat-header">
          <td colspan="4">
            <strong>KATEGORIA A — WYTWARZANIE / produkcja energii i ciepła</strong>
          </td>
        </tr>
        <tr data-scope-kod="BO">
          <td class="td-scope-kod"><strong class="mono">BO</strong></td>
          <td class="td-scope-name">
            <strong>Kotłownia centralna</strong>
            <div class="scope-desc">Gaz / olej / węgiel / biomasa · MCP/LCP</div>
            <span class="scope-status-badge status-ready">✓ gotowy</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-BO-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-BO-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr data-scope-kod="LH">
          <td class="td-scope-kod"><strong class="mono">LH</strong></td>
          <td class="td-scope-name">
            <strong>Lokalne ogrzewanie</strong>
            <div class="scope-desc">Promienniki, aerotermy, podłogowe — bilans cieplny hal</div>
            <span class="scope-status-badge status-ready">✓ gotowy</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-LH-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-LH-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr data-scope-kod="CHP">
          <td class="td-scope-kod"><strong class="mono">CHP</strong></td>
          <td class="td-scope-name">
            <strong>Kogeneracja CHP</strong>
            <div class="scope-desc">Silniki gazowe, turbiny parowe, ORC, mikroturbiny</div>
            <span class="scope-status-badge status-pending">⏳ planowane</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-CHP-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-CHP-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr data-scope-kod="HP">
          <td class="td-scope-kod"><strong class="mono">HP</strong></td>
          <td class="td-scope-name">
            <strong>Pompy ciepła</strong>
            <div class="scope-desc">Komercyjne, przemysłowe wysokoT do pary 150°C</div>
            <span class="scope-status-badge status-pending">⏳ planowane</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-HP-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-HP-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr data-scope-kod="PV">
          <td class="td-scope-kod"><strong class="mono">PV</strong></td>
          <td class="td-scope-name">
            <strong>Fotowoltaika + magazyny EE</strong>
            <div class="scope-desc">PV + BESS, autokonsumpcja, sprzedaż nadwyżek</div>
            <span class="scope-status-badge status-pending">⏳ planowane</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-PV-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-PV-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr data-scope-kod="ST">
          <td class="td-scope-kod"><strong class="mono">ST</strong></td>
          <td class="td-scope-name">
            <strong>Solar thermal</strong>
            <div class="scope-desc">Kolektory słoneczne — CWU/proces</div>
            <span class="scope-status-badge status-pending">⏳ planowane</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-ST-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-ST-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr data-scope-kod="DH">
          <td class="td-scope-kod"><strong class="mono">DH</strong></td>
          <td class="td-scope-name">
            <strong>Ciepło sieciowe</strong>
            <div class="scope-desc">Odbiorca / dostawca z miejskiej sieci ciepłowniczej</div>
            <span class="scope-status-badge status-pending">⏳ planowane</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-DH-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-DH-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr data-scope-kod="TES">
          <td class="td-scope-kod"><strong class="mono">TES</strong></td>
          <td class="td-scope-name">
            <strong>Magazyny ciepła (TES)</strong>
            <div class="scope-desc">Bufory, akumulatory parowe (Ruths), BTES — bufor dla CHP/PV</div>
            <span class="scope-status-badge status-pending">⏳ planowane</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-TES-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-TES-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr class="row-cat-header">
          <td colspan="4">
            <strong>KATEGORIA B — DYSTRYBUCJA / odbiorniki energii</strong>
          </td>
        </tr>
        <tr data-scope-kod="CA">
          <td class="td-scope-kod"><strong class="mono">CA</strong></td>
          <td class="td-scope-name">
            <strong>Sprężone powietrze</strong>
            <div class="scope-desc">Sprężarki, sieć CA, osuszacze, zbiorniki</div>
            <span class="scope-status-badge status-ready">✓ gotowy</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-CA-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-CA-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr data-scope-kod="AHU">
          <td class="td-scope-kod"><strong class="mono">AHU</strong></td>
          <td class="td-scope-name">
            <strong>Wentylacja</strong>
            <div class="scope-desc">Centrale AHU, klimatyzacja procesowa</div>
            <span class="scope-status-badge status-ready">✓ gotowy</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-AHU-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-AHU-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr data-scope-kod="RC">
          <td class="td-scope-kod"><strong class="mono">RC</strong></td>
          <td class="td-scope-name">
            <strong>Chłodnictwo + AC komfortu</strong>
            <div class="scope-desc">Chillery, NH3/CO2, komory chłodnicze, klimatyzacja komfortu</div>
            <span class="scope-status-badge status-ready">✓ gotowy</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-RC-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-RC-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr data-scope-kod="LIGHT">
          <td class="td-scope-kod"><strong class="mono">LIGHT</strong></td>
          <td class="td-scope-name">
            <strong>Oświetlenie ⭐</strong>
            <div class="scope-desc">Wewnętrzne (hale, biura) + zewnętrzne (parkingi, place)</div>
            <span class="scope-status-badge status-pending">⏳ planowane</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-LIGHT-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-LIGHT-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr data-scope-kod="EE">
          <td class="td-scope-kod"><strong class="mono">EE</strong></td>
          <td class="td-scope-name">
            <strong>Stacje EE / Trafostacje</strong>
            <div class="scope-desc">SN/nN, kompensacja mocy biernej, jakość EE</div>
            <span class="scope-status-badge status-pending">⏳ planowane</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-EE-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-EE-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr data-scope-kod="STEAM">
          <td class="td-scope-kod"><strong class="mono">STEAM</strong></td>
          <td class="td-scope-name">
            <strong>Sieci pary procesowej</strong>
            <div class="scope-desc">Rozbudowane sieci międzywydziałowe (warunkowo dla dużych zakładów)</div>
            <span class="scope-status-badge status-pending">⏳ planowane</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-STEAM-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-STEAM-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr class="row-cat-header">
          <td colspan="4">
            <strong>KATEGORIA C — TECHNOLOGIA / procesy produkcyjne</strong>
          </td>
        </tr>
        <tr data-scope-kod="TECH">
          <td class="td-scope-kod"><strong class="mono">TECH</strong></td>
          <td class="td-scope-name">
            <strong>Procesy technologiczne ★</strong>
            <div class="scope-desc">Silniki, pompy, piece, suszarnie, młyny, prasy, reaktory, walcownie (decyzja arch. po LIGHT+CHP)</div>
            <span class="scope-status-badge status-pending">⏳ planowane</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-TECH-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-TECH-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr class="row-cat-header">
          <td colspan="4">
            <strong>KATEGORIA D — TRANSPORT</strong>
          </td>
        </tr>
        <tr data-scope-kod="TRANS">
          <td class="td-scope-kod"><strong class="mono">TRANS</strong></td>
          <td class="td-scope-name">
            <strong>Transport wewnątrzzakładowy</strong>
            <div class="scope-desc">Wózki widłowe, AGV, suwnice, podajniki</div>
            <span class="scope-status-badge status-pending">⏳ planowane</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-TRANS-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-TRANS-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr data-scope-kod="FLEET">
          <td class="td-scope-kod"><strong class="mono">FLEET</strong></td>
          <td class="td-scope-name">
            <strong>Flota pojazdów + EV</strong>
            <div class="scope-desc">Osobowe, dostawcze, ciężarowe + stacje ładowania EV</div>
            <span class="scope-status-badge status-pending">⏳ planowane</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-FLEET-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-FLEET-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
        <tr class="row-cat-header">
          <td colspan="4">
            <strong>KATEGORIA E — BUDYNKI</strong>
          </td>
        </tr>
        <tr data-scope-kod="TIB">
          <td class="td-scope-kod"><strong class="mono">TIB</strong></td>
          <td class="td-scope-name">
            <strong>Termoizolacja budynków</strong>
            <div class="scope-desc">Przegrody, dach, okna, infiltracja powietrza</div>
            <span class="scope-status-badge status-pending">⏳ planowane</span>
          </td>
          <td class="td-scope-exist">
            <select class="scope-select" data-id="SCOPE-V1-TIB-EXIST" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>nie wiem</option>
            </select>
          </td>
          <td class="td-scope-audit">
            <select class="scope-select" data-id="SCOPE-V1-TIB-AUDIT" data-source="" data-confidence="" data-phase="client" data-iso="6.3.d" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="true" data-audit-profile="eed,white-cert,iso50001,full-map" data-ai-prompt="EXIST = czy instalacja istnieje w zakładzie. AUDIT = czy klient chce ją audytować w tym projekcie. Razem definiują zakres audytu.">
              <option value="">— wybierz —</option>
              <option>TAK</option>
              <option>NIE</option>
              <option>w planach</option>
            </select>
          </td>
        </tr>
            
            <!-- Kategoria F — INNE (wolne pole) -->
            <tr class="row-cat-header">
              <td colspan="4">
                <strong>KATEGORIA F — INNE / niestandardowe</strong>
              </td>
            </tr>
            <tr>
              <td class="td-scope-kod"><strong class="mono">OTHER</strong></td>
              <td class="td-scope-name" colspan="3">
                <strong>Inne instalacje — opisz w polu poniżej</strong>
                <div class="scope-desc">np. spalarnia odpadów, biogazownia, elektroliza wodoru, magazyny H2, instalacje membranowe</div>
                <textarea class="field-input" data-id="SCOPE-V2-OTHER" placeholder="Wpisz każdą niestandardową instalację w osobnej linii (Enter)" style="margin-top: 8px; min-height: 60px; width: 100%;" data-source="" data-confidence="" data-phase="client" data-iso="" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="false" data-audit-profile="eed,iso50001,full-map" data-ai-prompt="Dodatkowe scope poza listą + cel audytu + program dotacji."></textarea>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Podsumowanie wyboru -->
      <div class="group" style="background: var(--green-bg, #e6efe2); border-left: 3px solid var(--green-primary, #1a4d3a); margin-top: 24px;">
        <div class="group-title" style="color: var(--green-deep, #0a2c20);">📊 Podsumowanie wyboru</div>
        <div id="e13-summary" style="font-size: 13px; line-height: 1.7;">
          <p>Wpisz wybory powyżej — podsumowanie pojawi się automatycznie.</p>
        </div>
      </div>

      <!-- DASHBOARD SCOPE — placeholder iter 2 -->
            <!-- DASHBOARD SCOPE — iter 2 -->
      <div class="group" id="scope-dashboard-wrapper" style="background: var(--paper, white); border-left: 3px solid var(--green-primary, #1a4d3a); margin-top: 24px;">
        <div class="group-title" style="display: flex; align-items: center; justify-content: space-between;">
          <span style="color: var(--green-deep, #0a2c20);">📊 Dashboard Scope — status formularzy</span>
          <button type="button" id="dashboard-refresh-btn" style="background: var(--green-primary, #1a4d3a); color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; cursor: pointer; font-family: var(--sans);">↻ Odśwież</button>
        </div>
        <div class="group-desc" style="margin-bottom: 16px;">
          Karty pokazują postęp wypełniania scope wybranych jako "Audytowana? = TAK". Status jest czytany na żywo z localStorage przeglądarki.
        </div>

        <!-- Główny dashboard summary -->
        <div id="dashboard-overall" style="background: var(--green-soft, #c8d5c2); padding: 14px 18px; border-radius: 4px; margin-bottom: 16px; font-size: 13px;">
          <p>Ładowanie...</p>
        </div>

        <!-- Siatka kart 18 scope -->
        <div id="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px;">
          <!-- karty generowane dynamicznie -->
        </div>

      </div>

      <!-- Cel / uzasadnienie zakresu -->
      <div class="group" style="margin-top: 24px;">
        <div class="group-title">Cel i uzasadnienie zakresu audytu</div>
        <div class="field-list">
          <div class="field">
            <div class="field-head">
              <span class="field-id mono">SCOPE-V3-CEL</span>
              <span class="field-label">Powód wyboru zakresu audytu</span>
            </div>
            <textarea class="field-input field-textarea" data-id="SCOPE-V3-CEL" placeholder="np. EED — obowiązkowy audyt co 4 lata zgodnie z ustawą o efektywności energetycznej · Przygotowanie do certyfikacji ISO 50001 · Wniosek o Białe Certyfikaty · Wymóg Banku przed inwestycją · CSRD/ESRS raportowanie · Strategia dekarbonizacji · ..." style="min-height: 80px;" data-source="" data-confidence="" data-phase="client" data-iso="" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="false" data-audit-profile="eed,iso50001,full-map" data-ai-prompt="Dodatkowe scope poza listą + cel audytu + program dotacji."></textarea>
            <span class="field-hint">★ SCOPE-V3-CEL · Główne uzasadnienie audytu wpływa na zakres rekomendacji końcowych</span>
          </div>

          <div class="field">
            <div class="field-head">
              <span class="field-id mono">SCOPE-V4-PROG</span>
              <span class="field-label">Planowany termin zakończenia audytu</span>
            </div>
            <input type="date" class="field-input" data-id="SCOPE-V4-PROG" data-source="" data-confidence="" data-phase="client" data-iso="" data-iso50002="50002-1:6.2" data-eed="zal.II.1" data-required="false" data-audit-profile="eed,iso50001,full-map" data-ai-prompt="Dodatkowe scope poza listą + cel audytu + program dotacji.">
            <span class="field-hint">★ SCOPE-V4-PROG · Deadline na ukończenie wszystkich scope (PN-EN 16247-1: 2-12 miesięcy typowo)</span>
          </div>
        </div>
      </div>

    </div>
  </section>


</main>


<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>


</main>


</div>

<script>
const SAVE_URL   = '{{ isset($company) && $company ? route("client.energy-audit-master.save") : "" }}';
const CSRF       = '{{ csrf_token() }}';
const FORM_DATA  = @json($formData ?? []);
const COMPANY_ID = {{ isset($company) && $company ? $company->id : 'null' }};
const PREVIEW_MODE = {{ (!empty($previewMode) && $previewMode) ? 'true' : 'false' }};
const COMPANY_DATA = @json($_companyData);
const TEAM_MEMBERS = @json($_teamMembers);
@php
$_auditorsList = [];
if (isset($auditors)) {
    foreach ($auditors as $_a) {
        $_auditorsList[] = ['id' => $_a->id, 'name' => $_a->name, 'email' => $_a->email, 'phone' => $_a->phone ?? ''];
    }
}
@endphp
const AUDITORS_LIST = @json($_auditorsList);


// ============================================================
// ENESA Master Form - JavaScript (v0.1)
// - Persistent storage przez window.storage
// - Auto-save z debounce
// - Postęp per etap + globalny
// - Sidenav navigation (smooth scroll)
// ============================================================

const STORAGE_PREFIX = 'master:';
let saveTimer = null;

// === Storage abstraction ===
// Używamy localStorage (działa w każdej przeglądarce gdy plik otwarty z dysku)
// Z fallbackiem do in-memory jeśli localStorage zablokowany (tryb prywatny etc.)

let storageMode = 'localStorage';
let memoryStore = {}; // fallback in-memory

// Test czy localStorage dostępny
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

// Storage API (synchroniczne)
const enesaStorage = {
  set(key, value) {
    if (storageMode === 'localStorage') {
      try { localStorage.setItem(key, value); return true; }
      catch (e) {
        // Quota exceeded etc. - przełącz na memory
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
  listKeys() {
    if (storageMode === 'localStorage') {
      const keys = [];
      try {
        for (let i = 0; i < localStorage.length; i++) {
          const k = localStorage.key(i);
          if (k && k.startsWith(STORAGE_PREFIX)) keys.push(k);
        }
      } catch (e) {}
      return keys;
    } else {
      return Object.keys(memoryStore).filter(k => k.startsWith(STORAGE_PREFIX));
    }
  }
};

// Helper: pobierz wszystkie pola formularza
function getAllFields() {
  return Array.from(document.querySelectorAll('[data-id]')).filter(el =>
    el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'TEXTAREA'
  );
}

// === Wczytaj zapisane dane ===
function loadSavedData() {
  detectStorage();
  
  const keys = enesaStorage.listKeys();
  let loadedCount = 0;
  
  for (const key of keys) {
    const fieldId = key.replace(STORAGE_PREFIX, '');
    const value = enesaStorage.get(key);
    if (value !== null && value !== undefined && value !== '') {
      const el = document.querySelector(`[data-id="${fieldId}"]`);
      if (el) {
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
  
  updateAllProgress();
}

// === Zapisz pojedyncze pole ===
function saveField(fieldId, value) {
  if (value === '' || value === null || value === undefined) {
    enesaStorage.delete(STORAGE_PREFIX + fieldId);
  } else {
    enesaStorage.set(STORAGE_PREFIX + fieldId, String(value));
  }
}

// === Auto-save z debounce ===
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

// === Save indicator ===
function showSaveIndicator(text) {
  const el = document.getElementById('save-indicator');
  if (!el) return;
  el.textContent = text;
  el.classList.add('show');
  setTimeout(() => el.classList.remove('show'), 2000);
}

// === Update progress (per etap + globalny) ===
function updateAllProgress() {
  const sections = document.querySelectorAll('.section');
  let totalFilled = 0;
  let totalAll = 0;

  sections.forEach(sec => {
    const id = sec.id;
    if (!id || !id.startsWith('etap-')) return;
    
    const fields = sec.querySelectorAll('[data-id]');
    let filled = 0;
    fields.forEach(f => {
      const v = (f.value || '').trim();
      if (v !== '' && v !== '— wybierz —') filled++;
    });
    const total = fields.length;
    
    totalFilled += filled;
    totalAll += total;

    // Section progress badge
    const badge = sec.querySelector('.section-progress');
    if (badge) badge.textContent = `${filled} / ${total}`;
    
    // Sidenav count
    const sideCount = document.querySelector(`[data-count-for="${id}"]`);
    if (sideCount) sideCount.textContent = `${filled}/${total}`;
  });
  
  // Global
  const globalEl = document.getElementById('overall-progress');
  if (globalEl) {
    const pct = totalAll > 0 ? Math.round(totalFilled / totalAll * 100) : 0;
    globalEl.textContent = `${totalFilled} / ${totalAll} pól (${pct}%)`;
  }
}

// === Field input handler ===
function onFieldInput(e) {
  const f = e.target;
  if (!f.dataset.id) return;
  
  // Visual feedback (filled)
  if ((f.value || '').trim()) {
    f.classList.add('filled');
  } else {
    f.classList.remove('filled');
  }
  
  scheduleAutoSave();
  updateAllProgress();
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

// === Detect active section on scroll ===
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

// === ADD TEAM MEMBER (E0) ===
const addTeamBtn = document.getElementById('add-team-btn');
if (addTeamBtn) {
  addTeamBtn.addEventListener('click', () => {
    const table = document.getElementById('team-table');
    const headerRow = table.querySelector('thead tr');
    const instanceHeaders = headerRow.querySelectorAll('.th-instance');
    const newIdx = instanceHeaders.length + 1;
    
    // Add header
    const newTh = document.createElement('th');
    newTh.className = 'th-instance';
    newTh.textContent = `Osoba ${newIdx}`;
    headerRow.appendChild(newTh);
    
    // Add cell to each row
    const bodyRows = table.querySelectorAll('tbody tr');
    bodyRows.forEach(tr => {
      const tds = tr.querySelectorAll('td.td-input');
      if (tds.length === 0) return;
      const lastTd = tds[tds.length - 1];
      const newTd = lastTd.cloneNode(true);
      
      // Update data-id
      const oldSuffix = `-U${instanceHeaders.length}`;
      const newSuffix = `-U${newIdx}`;
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
    
    // Bind new inputs
    table.querySelectorAll('input, select').forEach(el => {
      el.removeEventListener('input', onFieldInput);
      el.addEventListener('input', onFieldInput);
      el.removeEventListener('change', onFieldInput);
      el.addEventListener('change', onFieldInput);
    });
    
    updateAllProgress();
  });
}

// === Persistent state - liczba wydziałów i hal ===
const STATE_KEY_WYDZ = STORAGE_PREFIX + '_meta_n_wydz';
const STATE_KEY_HAL = STORAGE_PREFIX + '_meta_n_hal';
const STATE_KEY_TEAM = STORAGE_PREFIX + '_meta_n_team';

function getNWydz() {
  const v = enesaStorage.get(STATE_KEY_WYDZ);
  return v ? parseInt(v, 10) : 5;
}
function setNWydz(n) {
  enesaStorage.set(STATE_KEY_WYDZ, String(n));
}
function getNHal() {
  const v = enesaStorage.get(STATE_KEY_HAL);
  return v ? parseInt(v, 10) : 5;
}
function setNHal(n) {
  enesaStorage.set(STATE_KEY_HAL, String(n));
}
function getNTeam() {
  const v = enesaStorage.get(STATE_KEY_TEAM);
  return v ? parseInt(v, 10) : 5;
}
function setNTeam(n) {
  enesaStorage.set(STATE_KEY_TEAM, String(n));
}

// === MACIERZ — dynamiczna budowa i walidacja ===
function rebuildMacierz() {
  const headerRow = document.getElementById('macierz-header-row');
  const body = document.getElementById('macierz-body');
  const foot = document.getElementById('macierz-foot');
  if (!headerRow || !body || !foot) return;
  
  const nWydz = getNWydz();
  const nHal = getNHal();
  
  // Pobierz nazwy wydziałów (z E4 inputów)
  const wydzNames = [];
  for (let w = 1; w <= nWydz; w++) {
    const el = document.querySelector(`[data-id="WYD-V2-NAZWA-W${w}"]`);
    const num = document.querySelector(`[data-id="WYD-V1-NUMER-W${w}"]`);
    const name = el && el.value ? el.value : `Wydz. ${num && num.value ? num.value : w}`;
    wydzNames.push(name);
  }
  
  // Pobierz nazwy hal (z E5 inputów)
  const halNames = [];
  for (let h = 1; h <= nHal; h++) {
    const el = document.querySelector(`[data-id="HAL-V2-NAZWA-H${h}"]`);
    const num = document.querySelector(`[data-id="HAL-V1-NUMER-H${h}"]`);
    const name = el && el.value ? el.value : `Hala ${num && num.value ? num.value : h}`;
    halNames.push(name);
  }
  
  // Zachowaj istniejące wartości macierzy
  const existing = {};
  body.querySelectorAll('input[data-mac-cell]').forEach(inp => {
    existing[inp.dataset.id] = inp.value;
  });
  
  // === Header: pytanie + wydziały + Σ + Status ===
  // Zostaw pierwszą th (↓ Hale / Wydziały →), usuń resztę
  while (headerRow.children.length > 1) headerRow.removeChild(headerRow.lastChild);
  
  for (let w = 1; w <= nWydz; w++) {
    const th = document.createElement('th');
    th.className = 'th-instance';
    th.style.minWidth = '90px';
    th.textContent = wydzNames[w-1];
    headerRow.appendChild(th);
  }
  // Σ wiersza
  const thSum = document.createElement('th');
  thSum.className = 'th-instance';
  thSum.style.minWidth = '70px';
  thSum.style.background = 'var(--gold)';
  thSum.textContent = 'Σ wiersza';
  headerRow.appendChild(thSum);
  // Status
  const thStat = document.createElement('th');
  thStat.className = 'th-instance';
  thStat.style.minWidth = '120px';
  thStat.style.background = 'var(--gold)';
  thStat.textContent = 'Status';
  headerRow.appendChild(thStat);
  
  // === Body: wiersze hal ===
  body.innerHTML = '';
  for (let h = 1; h <= nHal; h++) {
    const tr = document.createElement('tr');
    
    // Td: nazwa hali
    const tdHal = document.createElement('td');
    tdHal.className = 'td-question';
    tdHal.innerHTML = `<div class="q-label">${halNames[h-1]}</div><div class="q-id mono">HAL-${h}</div>`;
    tr.appendChild(tdHal);
    
    // Td: kolumny wydziałów (% input)
    for (let w = 1; w <= nWydz; w++) {
      const td = document.createElement('td');
      td.className = 'td-input';
      const inp = document.createElement('input');
      inp.type = 'number';
      inp.className = 'cell-input mac-cell-input';
      inp.dataset.id = `MAC-H${h}-W${w}`;
      inp.dataset.macCell = '1';
      inp.dataset.row = String(h);
      inp.dataset.col = String(w);
      inp.min = '0';
      inp.max = '100';
      inp.step = '0.1';
      inp.placeholder = '%';
      // Restore value
      if (existing[inp.dataset.id] !== undefined) {
        inp.value = existing[inp.dataset.id];
      } else {
        // Try load from storage
        const stored = enesaStorage.get(STORAGE_PREFIX + inp.dataset.id);
        if (stored !== null) inp.value = stored;
      }
      if (inp.value) inp.classList.add('filled');
      td.appendChild(inp);
      tr.appendChild(td);
    }
    
    // Td: Σ wiersza
    const tdSum = document.createElement('td');
    tdSum.className = 'td-input';
    tdSum.style.fontWeight = '700';
    tdSum.style.background = 'var(--paper-paper)';
    tdSum.dataset.sumFor = String(h);
    tdSum.textContent = '0';
    tr.appendChild(tdSum);
    
    // Td: Status
    const tdStat = document.createElement('td');
    tdStat.className = 'td-input';
    tdStat.style.fontWeight = '700';
    tdStat.style.fontSize = '11px';
    tdStat.dataset.statusFor = String(h);
    tdStat.textContent = '(brak)';
    tr.appendChild(tdStat);
    
    body.appendChild(tr);
  }
  
  // === Foot: Σ kolumn (informacyjny) ===
  foot.innerHTML = '';
  const trFoot = document.createElement('tr');
  
  const tdLabel = document.createElement('td');
  tdLabel.className = 'td-question';
  tdLabel.style.fontWeight = '700';
  tdLabel.style.background = 'var(--paper-deep)';
  tdLabel.textContent = 'Σ kolumn (informacyjny)';
  trFoot.appendChild(tdLabel);
  
  for (let w = 1; w <= nWydz; w++) {
    const td = document.createElement('td');
    td.className = 'td-input';
    td.style.fontWeight = '700';
    td.style.background = 'var(--paper-deep)';
    td.dataset.colSumFor = String(w);
    td.textContent = '0';
    trFoot.appendChild(td);
  }
  // Σ Σ
  const tdTotal = document.createElement('td');
  tdTotal.className = 'td-input';
  tdTotal.style.fontWeight = '700';
  tdTotal.style.background = 'var(--paper-deep)';
  tdTotal.id = 'mac-grand-total';
  tdTotal.textContent = '0';
  trFoot.appendChild(tdTotal);
  
  const tdInfo = document.createElement('td');
  tdInfo.className = 'td-input';
  tdInfo.style.fontWeight = '700';
  tdInfo.style.background = 'var(--paper-deep)';
  tdInfo.style.fontSize = '11px';
  tdInfo.id = 'mac-info';
  tdInfo.textContent = `max: ${nHal * 100}%`;
  trFoot.appendChild(tdInfo);
  
  foot.appendChild(trFoot);
  
  // Zbinduj nowe inputy
  body.querySelectorAll('input.mac-cell-input').forEach(inp => {
    inp.addEventListener('input', onMacInput);
  });
  
  // Aktualizuj sumy
  updateMacSums();
}

// === Walidacja sum macierzy ===
function onMacInput(e) {
  const inp = e.target;
  if (inp.value) inp.classList.add('filled'); else inp.classList.remove('filled');
  
  // Zapisz do storage
  saveField(inp.dataset.id, inp.value);
  scheduleAutoSave();
  updateMacSums();
  updateAllProgress();
}

function updateMacSums() {
  const nWydz = getNWydz();
  const nHal = getNHal();
  
  // Σ wiersza i status per wiersz
  for (let h = 1; h <= nHal; h++) {
    let sum = 0;
    for (let w = 1; w <= nWydz; w++) {
      const inp = document.querySelector(`input[data-id="MAC-H${h}-W${w}"]`);
      if (inp && inp.value) sum += parseFloat(inp.value) || 0;
    }
    
    // Σ wiersza
    const tdSum = document.querySelector(`td[data-sum-for="${h}"]`);
    if (tdSum) {
      tdSum.textContent = sum.toFixed(1);
      if (sum > 0) {
        if (Math.abs(sum - 100) < 0.1) {
          tdSum.style.background = 'var(--ok-light)';
          tdSum.style.color = 'var(--green-deep)';
        } else {
          tdSum.style.background = 'var(--rose-light)';
          tdSum.style.color = 'var(--rose)';
        }
      } else {
        tdSum.style.background = 'var(--paper-paper)';
        tdSum.style.color = 'var(--ink-mute)';
      }
    }
    
    // Status
    const tdStat = document.querySelector(`td[data-status-for="${h}"]`);
    if (tdStat) {
      if (sum === 0) {
        tdStat.textContent = '(brak)';
        tdStat.style.color = 'var(--ink-mute)';
      } else if (Math.abs(sum - 100) < 0.1) {
        tdStat.textContent = 'OK ✓';
        tdStat.style.color = 'var(--ok)';
      } else if (sum < 100) {
        tdStat.textContent = `⚠ Brakuje ${(100 - sum).toFixed(1)}%`;
        tdStat.style.color = 'var(--rose)';
      } else {
        tdStat.textContent = `⚠ Nadmiar ${(sum - 100).toFixed(1)}%`;
        tdStat.style.color = 'var(--rose)';
      }
    }
  }
  
  // Σ kolumny
  let grand = 0;
  for (let w = 1; w <= nWydz; w++) {
    let colSum = 0;
    for (let h = 1; h <= nHal; h++) {
      const inp = document.querySelector(`input[data-id="MAC-H${h}-W${w}"]`);
      if (inp && inp.value) colSum += parseFloat(inp.value) || 0;
    }
    const td = document.querySelector(`td[data-col-sum-for="${w}"]`);
    if (td) td.textContent = colSum.toFixed(1);
    grand += colSum;
  }
  const grandEl = document.getElementById('mac-grand-total');
  if (grandEl) grandEl.textContent = grand.toFixed(1);
}

// Reagowanie na zmianę nazw wydziałów / hal → odbuduj macierz nagłówków
function onWydzHalNameChange() {
  rebuildMacierz();
}

// === ADD HAL (E5) — dynamiczne dodawanie kolumn hal ===
const addHalBtn = document.getElementById('add-hal-btn');
if (addHalBtn) {
  addHalBtn.addEventListener('click', () => {
    const table = document.getElementById('hale-table');
    const headerRow = table.querySelector('thead tr');
    const instanceHeaders = headerRow.querySelectorAll('.th-instance');
    const newIdx = instanceHeaders.length + 1;
    const oldSuffix = `-H${instanceHeaders.length}`;
    const newSuffix = `-H${newIdx}`;
    
    // Header
    const newTh = document.createElement('th');
    newTh.className = 'th-instance';
    newTh.textContent = `Hala ${newIdx}`;
    headerRow.appendChild(newTh);
    
    // Komórki w wierszach (pomijając section-header)
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
    
    setNHal(newIdx);
    bindAllFields();
    rebuildMacierz();
    updateAllProgress();
    showSaveIndicator(`Dodano Hala ${newIdx}`);
  });
}

// === E8 ZUZYCIA — dynamiczna tabela 36 mies × 9 nośników ===

// Konwersje energetyczne na MWh
const NOS_FACTORS = {
  EE: 1,           // MWh × 1 = MWh
  GAZ: 0.0097,     // m³ × 0.0097 ≈ 35 MJ/m³ konwersja
  CIEPLO: 0.2778,  // GJ × 0.2778 = MWh (1 GJ = 0.2778 MWh)
  OLEJ: 0.0105,    // l × 0.0105 ≈ 38 MJ/l
  LPG: 0.0128,     // kg × 0.0128 ≈ 46 MJ/kg
  LNG: 0.014,      // kg × 0.014 ≈ 50 MJ/kg
  PARA: 0.7,       // t × 0.7 ≈ 2.5 GJ/t
  BIO: 4.5,        // t × 4.5 ≈ 16 GJ/t
  PV: 1            // MWh × 1
};

const MIESIACE = ['styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec',
                  'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień'];
const NOS_KEYS = ['EE', 'GAZ', 'CIEPLO', 'OLEJ', 'LPG', 'LNG', 'PARA', 'BIO', 'PV'];
const N_MIES = 36; // 3 lata

function buildZuzyciaTable() {
  const body = document.getElementById('zuzycia-body');
  const foot = document.getElementById('zuzycia-foot');
  if (!body || !foot) return;
  
  // Zachowaj istniejące wartości
  const existing = {};
  body.querySelectorAll('input[data-id]').forEach(inp => {
    existing[inp.dataset.id] = inp.value;
  });
  
  body.innerHTML = '';
  for (let m = 1; m <= N_MIES; m++) {
    const tr = document.createElement('tr');
    const miesIdx = (m - 1) % 12;
    
    // Td: miesiąc
    const tdM = document.createElement('td');
    tdM.className = 'td-question';
    tdM.style.fontWeight = '600';
    tdM.style.background = 'var(--paper-deep)';
    tdM.textContent = MIESIACE[miesIdx];
    tr.appendChild(tdM);
    
    // Td: rok (input)
    const tdRok = document.createElement('td');
    tdRok.className = 'td-input';
    const inpRok = document.createElement('input');
    inpRok.type = 'number';
    inpRok.className = 'cell-input zuz-rok-input';
    inpRok.dataset.id = `ZUZ-M${m}-ROK`;
    inpRok.placeholder = '2024';
    inpRok.min = '2000';
    inpRok.max = '2050';
    if (existing[inpRok.dataset.id] !== undefined) inpRok.value = existing[inpRok.dataset.id];
    else {
      const stored = enesaStorage.get(STORAGE_PREFIX + inpRok.dataset.id);
      if (stored !== null) inpRok.value = stored;
    }
    if (inpRok.value) inpRok.classList.add('filled');
    tdRok.appendChild(inpRok);
    tr.appendChild(tdRok);
    
    // 9 kolumn nośników
    for (const nos of NOS_KEYS) {
      const td = document.createElement('td');
      td.className = 'td-input';
      const inp = document.createElement('input');
      inp.type = 'number';
      inp.className = 'cell-input zuz-cell-input';
      inp.dataset.id = `ZUZ-M${m}-${nos}`;
      inp.dataset.month = String(m);
      inp.dataset.nos = nos;
      inp.step = '0.1';
      // Restore
      if (existing[inp.dataset.id] !== undefined) inp.value = existing[inp.dataset.id];
      else {
        const stored = enesaStorage.get(STORAGE_PREFIX + inp.dataset.id);
        if (stored !== null) inp.value = stored;
      }
      if (inp.value) inp.classList.add('filled');
      td.appendChild(inp);
      tr.appendChild(td);
    }
    
    // Σ MWh dla wiersza
    const tdSum = document.createElement('td');
    tdSum.className = 'td-input';
    tdSum.style.fontWeight = '700';
    tdSum.style.background = 'var(--paper-paper)';
    tdSum.dataset.zuzSumFor = String(m);
    tdSum.textContent = '0.0';
    tr.appendChild(tdSum);
    
    body.appendChild(tr);
  }
  
  // === Foot: statystyki ===
  foot.innerHTML = '';
  const statRows = [
    {label: 'Σ rok 1 (mies. 1-12)', from: 1, to: 12, fn: 'sum'},
    {label: 'Σ rok 2 (mies. 13-24)', from: 13, to: 24, fn: 'sum'},
    {label: 'Σ rok 3 (mies. 25-36)', from: 25, to: 36, fn: 'sum'},
    {label: 'Średnia roczna (3-letnia)', from: 1, to: 36, fn: 'avg12'},
    {label: 'Σ MIN miesięczne', from: 1, to: 36, fn: 'min'},
    {label: 'Σ MAX miesięczne', from: 1, to: 36, fn: 'max'},
  ];
  
  statRows.forEach(stat => {
    const tr = document.createElement('tr');
    
    const tdL = document.createElement('td');
    tdL.className = 'td-question';
    tdL.style.fontWeight = '700';
    tdL.style.background = 'var(--paper-deep)';
    tdL.style.fontSize = '11px';
    tdL.textContent = stat.label;
    tr.appendChild(tdL);
    
    // Pusty rok
    const tdR = document.createElement('td');
    tdR.className = 'td-input';
    tdR.style.background = 'var(--paper-deep)';
    tr.appendChild(tdR);
    
    // 9 kolumn nośników + Σ MWh
    for (const nos of NOS_KEYS) {
      const td = document.createElement('td');
      td.className = 'td-input';
      td.style.fontWeight = '600';
      td.style.background = 'var(--paper-deep)';
      td.style.fontSize = '11px';
      td.dataset.statRow = `${stat.from}-${stat.to}-${stat.fn}`;
      td.dataset.statNos = nos;
      td.textContent = '0.0';
      tr.appendChild(td);
    }
    // Σ MWh
    const tdMWh = document.createElement('td');
    tdMWh.className = 'td-input';
    tdMWh.style.fontWeight = '700';
    tdMWh.style.background = 'var(--gold)';
    tdMWh.style.color = 'white';
    tdMWh.style.fontSize = '11px';
    tdMWh.dataset.statRow = `${stat.from}-${stat.to}-${stat.fn}`;
    tdMWh.dataset.statNos = 'TOTAL';
    tdMWh.textContent = '0.0';
    tr.appendChild(tdMWh);
    
    foot.appendChild(tr);
  });
  
  // Bind nowe inputy
  body.querySelectorAll('input.zuz-cell-input, input.zuz-rok-input').forEach(inp => {
    inp.addEventListener('input', onZuzInput);
  });
  
  updateZuzyciaSums();
}

function onZuzInput(e) {
  const inp = e.target;
  if (inp.value) inp.classList.add('filled'); else inp.classList.remove('filled');
  saveField(inp.dataset.id, inp.value);
  scheduleAutoSave();
  updateZuzyciaSums();
  updateAllProgress();
}

function updateZuzyciaSums() {
  // Σ MWh per wiersz
  for (let m = 1; m <= N_MIES; m++) {
    let totalMWh = 0;
    for (const nos of NOS_KEYS) {
      const inp = document.querySelector(`input[data-id="ZUZ-M${m}-${nos}"]`);
      if (inp && inp.value) {
        totalMWh += (parseFloat(inp.value) || 0) * NOS_FACTORS[nos];
      }
    }
    const td = document.querySelector(`td[data-zuz-sum-for="${m}"]`);
    if (td) td.textContent = totalMWh.toFixed(1);
  }
  
  // Statystyki w foot
  document.querySelectorAll('td[data-stat-row]').forEach(td => {
    const [fromStr, toStr, fn] = td.dataset.statRow.split('-');
    const from = parseInt(fromStr, 10), to = parseInt(toStr, 10);
    const nos = td.dataset.statNos;
    
    let values = [];
    for (let m = from; m <= to; m++) {
      let v;
      if (nos === 'TOTAL') {
        // Suma MWh dla miesiąca
        v = 0;
        for (const k of NOS_KEYS) {
          const inp = document.querySelector(`input[data-id="ZUZ-M${m}-${k}"]`);
          if (inp && inp.value) v += (parseFloat(inp.value) || 0) * NOS_FACTORS[k];
        }
      } else {
        const inp = document.querySelector(`input[data-id="ZUZ-M${m}-${nos}"]`);
        v = inp && inp.value ? (parseFloat(inp.value) || 0) : 0;
      }
      values.push(v);
    }
    
    let result = 0;
    if (fn === 'sum') {
      result = values.reduce((a, b) => a + b, 0);
    } else if (fn === 'avg12') {
      const sum = values.reduce((a, b) => a + b, 0);
      result = sum / values.length * 12; // średnia roczna
    } else if (fn === 'min') {
      const nonZero = values.filter(v => v > 0);
      result = nonZero.length > 0 ? Math.min(...nonZero) : 0;
    } else if (fn === 'max') {
      result = values.length > 0 ? Math.max(...values) : 0;
    }
    
    td.textContent = result.toFixed(1);
  });
}

// === Restore dynamic columns/rows on load ===
function restoreDynamicCols() {
  // Restore wydziały (kolumny w E4)
  const targetWydz = getNWydz();
  const wydzTable = document.getElementById('wydzialy-table');
  if (wydzTable) {
    const currentWydz = wydzTable.querySelectorAll('thead tr .th-instance').length;
    for (let i = currentWydz; i < targetWydz; i++) {
      // Symuluj klik
      document.getElementById('add-wydz-btn')?.click();
    }
  }
  
  // Restore hale (kolumny w E5)
  const targetHal = getNHal();
  const halTable = document.getElementById('hale-table');
  if (halTable) {
    const currentHal = halTable.querySelectorAll('thead tr .th-instance').length;
    for (let i = currentHal; i < targetHal; i++) {
      document.getElementById('add-hal-btn')?.click();
    }
  }
  
  // Restore team (kolumny w E0)
  const targetTeam = getNTeam();
  const teamTable = document.getElementById('team-table');
  if (teamTable) {
    const currentTeam = teamTable.querySelectorAll('thead tr .th-instance').length;
    for (let i = currentTeam; i < targetTeam; i++) {
      document.getElementById('add-team-btn')?.click();
    }
  }
}

// === ADD WYDZIAL (E4) — dynamiczne dodawanie kolumn wydziałów ===
const addWydzBtn = document.getElementById('add-wydz-btn');
if (addWydzBtn) {
  addWydzBtn.addEventListener('click', () => {
    const table = document.getElementById('wydzialy-table');
    const headerRow = table.querySelector('thead tr');
    const instanceHeaders = headerRow.querySelectorAll('.th-instance');
    const newIdx = instanceHeaders.length + 1;
    const oldSuffix = `-W${instanceHeaders.length}`;
    const newSuffix = `-W${newIdx}`;
    
    // Dodaj header
    const newTh = document.createElement('th');
    newTh.className = 'th-instance';
    newTh.textContent = `Wydział ${newIdx}`;
    headerRow.appendChild(newTh);
    
    // Dodaj komórkę do każdego wiersza danych
    const bodyRows = table.querySelectorAll('tbody tr');
    bodyRows.forEach(tr => {
      // Pomiń wiersze sekcji (row-section-header)
      if (tr.classList.contains('row-section-header')) {
        // Aktualizuj colspan headera sekcji
        const td = tr.querySelector('td[colspan]');
        if (td) td.setAttribute('colspan', String(newIdx + 2));
        return;
      }
      
      const tds = tr.querySelectorAll('td.td-input');
      if (tds.length === 0) return;
      
      const lastTd = tds[tds.length - 1];
      const newTd = lastTd.cloneNode(true);
      
      // Aktualizuj data-id na nowych elementach
      newTd.querySelectorAll('[data-id]').forEach(el => {
        const oldId = el.getAttribute('data-id');
        if (oldId && oldId.endsWith(oldSuffix)) {
          el.setAttribute('data-id', oldId.slice(0, -oldSuffix.length) + newSuffix);
        }
        // Wyczyść wartość, usuń klasę filled
        el.value = '';
        el.classList.remove('filled');
        // Aktualizuj data-wydz-idx jeśli jest
        if (el.hasAttribute('data-wydz-idx')) {
          el.setAttribute('data-wydz-idx', String(newIdx));
        }
      });
      
      tr.appendChild(newTd);
    });
    
    // Zbinduj handler na nowych inputach
    setNWydz(newIdx);
    bindAllFields();
    rebuildMacierz();
    updateAllProgress();
    showSaveIndicator(`Dodano Wydział ${newIdx}`);
  });
}

// === Aktualizacja istniejącego add-team-btn: zapis liczby uczestników ===
const addTeamBtnExtra = document.getElementById('add-team-btn');
if (addTeamBtnExtra) {
  addTeamBtnExtra.addEventListener('click', () => {
    const table = document.getElementById('team-table');
    const n = table.querySelectorAll('thead tr .th-instance').length;
    setNTeam(n);
  });
}

// === Bind all inputs ===
function bindAllFields() {
  document.querySelectorAll('[data-id]').forEach(el => {
    if (el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'TEXTAREA') {
      el.removeEventListener('input', onFieldInput);
      el.addEventListener('input', onFieldInput);
      el.removeEventListener('change', onFieldInput);
      el.addEventListener('change', onFieldInput);
    }
  });
  // Specjalny listener: zmiana nazwy wydziału / hali → rebuildMacierz nagłówków
  document.querySelectorAll('.wydz-name-input, .wydz-num-input, .hal-name-input, .hal-num-input').forEach(el => {
    el.removeEventListener('input', onWydzHalNameChange);
    el.addEventListener('input', onWydzHalNameChange);
  });
}

// === INIT ===
restoreDynamicCols();  // odbuduj dynamiczne kolumny (wydz/hale/zespół) z localStorage
buildZuzyciaTable();   // zbuduj tabelę E8 (36 mies × 9 nośników)
bindAllFields();
loadSavedData();
rebuildMacierz();      // odbuduj macierz E6 z aktualnym stanem nazw
updateZuzyciaSums();   // przeliczy statystyki E8 po załadowaniu



// === E13 / PROFILE / DASHBOARD NEW FUNCTIONS ===
const SCOPE_KODY_E13 = ['BO','LH','CHP','HP','PV','ST','DH','TES',
                        'CA','AHU','RC','LIGHT','EE','STEAM',
                        'TECH','TRANS','FLEET','TIB'];

function updateE13Summary() {
  const summaryEl = document.getElementById('e13-summary');
  if (!summaryEl) return;

  let nExist = 0, nAudit = 0, nNotAudit = 0;
  const auditList = [];
  const existList = [];

  SCOPE_KODY_E13.forEach(kod => {
    const exist = (document.querySelector(`[data-id="SCOPE-V1-${kod}-EXIST"]`) || {}).value || '';
    const audit = (document.querySelector(`[data-id="SCOPE-V1-${kod}-AUDIT"]`) || {}).value || '';
    if (exist === 'TAK') {
      nExist++;
      existList.push(kod);
    }
    if (audit === 'TAK') {
      nAudit++;
      auditList.push(kod);
    } else if (exist === 'TAK' && audit && audit !== 'TAK') {
      nNotAudit++;
    }
  });

  let html = '';
  if (nExist === 0 && nAudit === 0) {
    html = '<p style="color: var(--ink-mute);">Wpisz wybory w macierzy powyżej — podsumowanie pojawi się automatycznie.</p>';
  } else {
    html = `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
        <div>
          <div style="font-size: 11px; color: var(--ink-mute); text-transform: uppercase; letter-spacing: 0.5px;">Występuje w zakładzie</div>
          <div style="font-size: 22px; font-weight: 700; color: var(--green-deep, #1a4d3a); font-family: var(--serif);">${nExist} <span style="font-size: 13px; font-weight: 400; color: var(--ink-mute);">/ 18 instalacji</span></div>
          ${existList.length > 0 ? `<div style="font-size: 11px; color: var(--ink-soft); margin-top: 4px;">${existList.join(', ')}</div>` : ''}
        </div>
        <div>
          <div style="font-size: 11px; color: var(--ink-mute); text-transform: uppercase; letter-spacing: 0.5px;">Objęte audytem</div>
          <div style="font-size: 22px; font-weight: 700; color: var(--gold, #c8a951); font-family: var(--serif);">${nAudit} <span style="font-size: 13px; font-weight: 400; color: var(--ink-mute);">/ ${nExist} występujących</span></div>
          ${auditList.length > 0 ? `<div style="font-size: 11px; color: var(--ink-soft); margin-top: 4px;">${auditList.join(', ')}</div>` : ''}
        </div>
        <div>
          <div style="font-size: 11px; color: var(--ink-mute); text-transform: uppercase; letter-spacing: 0.5px;">Występuje, ale poza audytem</div>
          <div style="font-size: 22px; font-weight: 700; color: var(--rose, #c87a5e); font-family: var(--serif);">${nNotAudit} <span style="font-size: 13px; font-weight: 400; color: var(--ink-mute);">instalacji</span></div>
          <div style="font-size: 11px; color: var(--ink-soft); margin-top: 4px; font-style: italic;">np. planowane lub poza zakresem</div>
        </div>
      </div>
      <p style="margin-top: 16px; font-size: 12px; color: var(--ink-soft);">
        💡 <strong>Następny krok:</strong> Dashboard scope (iter 2) pokaże status każdego z ${nAudit} audytowanych formularzy z linkami do plików HTML.
      </p>
    `;
  }
  summaryEl.innerHTML = html;
}

// Bind handler dla SCOPE-V1-* fields
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    document.querySelectorAll('[data-id^="SCOPE-V1-"]').forEach(el => {
      el.addEventListener('change', () => {
        updateE13Summary();
        // Iter 2: też odśwież dashboard
        if (typeof buildDashboard === 'function') buildDashboard();
      });
    });
    updateE13Summary();
  }, 200);
});




// ============================================================
// === E13 DASHBOARD SCOPE — iter 2 ============================
// ============================================================

// Konfiguracja każdego scope: kod → {prefix, name, file, expectedFields, icon, status}
const SCOPE_CONFIG = {
  // Kategoria A — Wytwarzanie
  'BO':    { prefix: 'boilers:', name: 'Kotłownia centralna',     file: 'ENESA_Formularz_HTML_Kotly_v1_0.html',  expected: 320, icon: '🔥', cat: 'A', status: 'ready' },
  'LH':    { prefix: 'lh:',      name: 'Lokalne ogrzewanie',      file: 'ENESA_Formularz_HTML_LH_v2_0-6.html',   expected: 180, icon: '🌡', cat: 'A', status: 'ready' },
  'CHP':   { prefix: 'chp:',     name: 'Kogeneracja CHP',         file: '',                                        expected: 250, icon: '⚡', cat: 'A', status: 'pending' },
  'HP':    { prefix: 'hp:',      name: 'Pompy ciepła',            file: '',                                        expected: 220, icon: '♨',  cat: 'A', status: 'pending' },
  'PV':    { prefix: 'pv:',      name: 'Fotowoltaika + BESS',     file: '',                                        expected: 280, icon: '☀',  cat: 'A', status: 'pending' },
  'ST':    { prefix: 'st:',      name: 'Solar thermal',           file: '',                                        expected: 150, icon: '🌞', cat: 'A', status: 'pending' },
  'DH':    { prefix: 'dh:',      name: 'Ciepło sieciowe',         file: '',                                        expected: 100, icon: '🔗', cat: 'A', status: 'pending' },
  'TES':   { prefix: 'tes:',     name: 'Magazyny ciepła',         file: '',                                        expected: 130, icon: '📦', cat: 'A', status: 'pending' },
  // Kategoria B — Dystrybucja
  'CA':    { prefix: 'ca:',      name: 'Sprężone powietrze',      file: 'ENESA_Formularz_HTML_CA_v1_0.html',     expected: 200, icon: '💨', cat: 'B', status: 'ready' },
  'AHU':   { prefix: 'ahu:',     name: 'Wentylacja',              file: 'ENESA_Formularz_HTML_AHU_v1_0-4.html',  expected: 220, icon: '🌬', cat: 'B', status: 'ready' },
  'RC':    { prefix: 'rc:',      name: 'Chłodnictwo + AC',        file: 'ENESA_Formularz_HTML_RC_v1_0.html',     expected: 330, icon: '❄',  cat: 'B', status: 'ready' },
  'LIGHT': { prefix: 'light:',   name: 'Oświetlenie',             file: '',                                        expected: 200, icon: '💡', cat: 'B', status: 'pending' },
  'EE':    { prefix: 'ee:',      name: 'Stacje EE / Trafostacje', file: '',                                        expected: 200, icon: '⚡', cat: 'B', status: 'pending' },
  'STEAM': { prefix: 'steam:',   name: 'Sieci pary procesowej',   file: '',                                        expected: 180, icon: '♨',  cat: 'B', status: 'pending' },
  // Kategoria C — Technologia
  'TECH':  { prefix: 'tech:',    name: 'Procesy technologiczne',  file: '',                                        expected: 250, icon: '⚙',  cat: 'C', status: 'pending' },
  // Kategoria D — Transport
  'TRANS': { prefix: 'trans:',   name: 'Transport wewn.',         file: '',                                        expected: 150, icon: '🚜', cat: 'D', status: 'pending' },
  'FLEET': { prefix: 'fleet:',   name: 'Flota + EV',              file: '',                                        expected: 200, icon: '🚛', cat: 'D', status: 'pending' },
  // Kategoria E — Budynki
  'TIB':   { prefix: 'tib:',     name: 'Termoizolacja',           file: '',                                        expected: 180, icon: '🏢', cat: 'E', status: 'pending' },
};

// Średni czas pracy audytora na pole (minuty)
const MIN_PER_FIELD = 0.4;  // ~25s średnio

function calcScopeProgress(prefix, expected) {
  // Liczy ile pól w localStorage zaczyna się od prefix:
  // i ma niepustą wartość (≠ "" i ≠ null)
  let filled = 0;
  let totalKeys = 0;
  try {
    for (let i = 0; i < localStorage.length; i++) {
      const key = localStorage.key(i);
      if (key && key.startsWith(prefix)) {
        totalKeys++;
        const val = localStorage.getItem(key);
        if (val !== null && val !== '' && val !== '0' && val !== 'undefined') {
          // Pomijamy klucze meta (zaczynające się od _meta lub _n_)
          const subkey = key.substring(prefix.length);
          if (subkey.startsWith('_')) continue;
          filled++;
        }
      }
    }
  } catch (e) {
    return { filled: 0, total: expected, pct: 0, totalKeys: 0 };
  }
  const pct = expected > 0 ? Math.min(100, Math.round((filled / expected) * 100)) : 0;
  return { filled, total: expected, pct, totalKeys };
}

function calcScopeFlagCount(prefix) {
  // Heurystycznie: szukamy kluczy zawierających "FLAG" lub "F1", "F2"... itd.
  // Realnie: scope ma własną logikę flag, więc czytamy zapisane wyniki
  let count = 0;
  try {
    for (let i = 0; i < localStorage.length; i++) {
      const key = localStorage.key(i);
      if (key && key.startsWith(prefix) && key.includes('FLAG')) {
        const val = localStorage.getItem(key);
        if (val === 'true' || val === 'TAK' || val === 'AKTYWNA') count++;
      }
    }
  } catch (e) {}
  return count;
}

function getScopeAuditState(kod) {
  // Czy scope jest "audytowany" w E13?
  const exist = (document.querySelector(`[data-id="SCOPE-V1-${kod}-EXIST"]`) || {}).value || '';
  const audit = (document.querySelector(`[data-id="SCOPE-V1-${kod}-AUDIT"]`) || {}).value || '';
  return {
    exist: exist === 'TAK',
    audit: audit === 'TAK',
    raw: { exist, audit }
  };
}


function buildScopeCard(kod, cfg, state, progress, flagCount) {
  // Określamy stan karty
  let cardClass = 'scope-card';
  let statusLabel = '';
  let statusClass = '';
  let progressBarClass = '';
  let timeEstHtml = '';
  let actionsHtml = '';

  // Logika stanu
  if (!state.audit) {
    if (state.exist) {
      cardClass += ' is-not-audited';
      statusLabel = '⊘ Występuje, ale poza audytem';
      statusClass = 'pending';
    } else if (state.raw.exist === 'NIE') {
      cardClass += ' is-not-audited';
      statusLabel = '— Nie występuje w zakładzie';
      statusClass = '';
    } else {
      statusLabel = '◌ Nie wybrano w E13';
      statusClass = '';
    }
  } else {
    // Audytowane — sprawdzamy postęp
    cardClass += ' is-active';
    if (cfg.status === 'pending' && !cfg.file) {
      statusLabel = '⏳ Scope w przygotowaniu';
      statusClass = 'pending';
    } else if (progress.pct >= 95) {
      cardClass += ' is-completed';
      statusLabel = '✓ Ukończony';
      statusClass = 'ready';
      progressBarClass = 'complete';
    } else if (progress.pct >= 50) {
      statusLabel = '◐ W trakcie';
      progressBarClass = 'warn';
    } else if (progress.pct > 0) {
      statusLabel = '◔ Rozpoczęty';
      progressBarClass = 'low';
    } else {
      statusLabel = '○ Nierozpoczęty';
      progressBarClass = 'low';
    }
    if (flagCount > 0) {
      cardClass += ' has-flags';
    }
  }

  // Czas oczekiwany (min × pola pozostałe)
  if (state.audit && cfg.file) {
    const remaining = Math.max(0, cfg.expected - progress.filled);
    const minRemain = Math.round(remaining * 0.4);
    if (minRemain >= 60) {
      const hours = Math.floor(minRemain / 60);
      const mins = minRemain % 60;
      timeEstHtml = `~${hours}h ${mins}min do końca`;
    } else if (minRemain > 0) {
      timeEstHtml = `~${minRemain}min do końca`;
    } else if (progress.pct >= 95) {
      timeEstHtml = '✓ Wszystkie pola wypełnione';
    }
  }

  // Akcje
  if (state.audit && cfg.file) {
    actionsHtml = `<a class="scope-btn primary" href="${cfg.file}" target="_blank">▶ Otwórz</a>`;
  } else if (state.audit && cfg.status === 'pending') {
    actionsHtml = `<button class="scope-btn disabled" disabled>⏳ Buduj scope</button>`;
  } else if (state.exist && !state.audit) {
    actionsHtml = `<button class="scope-btn secondary disabled" disabled>Poza audytem</button>`;
  } else {
    actionsHtml = `<button class="scope-btn secondary disabled" disabled>Wybierz w E13</button>`;
  }

  // Flagi
  let flagHtml = '';
  if (flagCount > 0) {
    flagHtml = `<span class="scope-flag-count danger">🔴 ${flagCount} flag</span>`;
  } else if (state.audit && progress.pct >= 50) {
    flagHtml = `<span class="scope-flag-count ok">🟢 OK</span>`;
  }

  return `
    <div class="${cardClass}" data-scope-kod="${kod}">
      <div class="scope-card-head">
        <span class="scope-card-icon">${cfg.icon}</span>
        <div class="scope-card-name">${cfg.name}</div>
        <span class="scope-card-kod">${kod}</span>
      </div>
      <div class="scope-status-line ${statusClass}">${statusLabel}</div>
      ${state.audit ? `
        <div class="scope-progress-wrap">
          <div class="scope-progress-bar ${progressBarClass}" style="width: ${progress.pct}%"></div>
        </div>
        <div class="scope-stats-row">
          <span><strong>${progress.filled}</strong>/${cfg.expected} pól (${progress.pct}%)</span>
          ${flagHtml}
        </div>
        ${timeEstHtml ? `<div class="scope-time-est">${timeEstHtml}</div>` : ''}
      ` : ''}
      <div class="scope-card-actions">
        ${actionsHtml}
      </div>
    </div>
  `;
}

function buildDashboardOverall(cards, state) {
  const overallEl = document.getElementById('dashboard-overall');
  if (!overallEl) return;

  const nAudit = cards.filter(c => c.audit).length;
  const nReady = cards.filter(c => c.audit && c.cfg.status === 'ready').length;
  const nCompleted = cards.filter(c => c.audit && c.cfg.status === 'ready' && c.progress.pct >= 95).length;
  const nInProgress = cards.filter(c => c.audit && c.cfg.status === 'ready' && c.progress.pct > 0 && c.progress.pct < 95).length;
  const nNotStarted = cards.filter(c => c.audit && c.cfg.status === 'ready' && c.progress.pct === 0).length;
  const nPending = cards.filter(c => c.audit && c.cfg.status === 'pending').length;

  // Σ czasu pozostałego
  let totalRemainMin = 0;
  cards.forEach(c => {
    if (c.audit && c.cfg.file) {
      const remaining = Math.max(0, c.cfg.expected - c.progress.filled);
      totalRemainMin += remaining * 0.4;
    }
  });
  const totalH = Math.floor(totalRemainMin / 60);
  const totalM = Math.round(totalRemainMin % 60);

  // Σ flag
  const totalFlags = cards.reduce((sum, c) => sum + c.flagCount, 0);

  if (nAudit === 0) {
    overallEl.innerHTML = '<p style="color: var(--ink-mute, #8b7355); margin: 0;">Wybierz instalacje "Audytowana? = TAK" w macierzy powyżej, aby zobaczyć dashboard.</p>';
    return;
  }

  overallEl.innerHTML = `
    <div class="ds-row">
      <div class="ds-stat">
        <span class="ds-stat-label">Audytowanych</span>
        <span class="ds-stat-value">${nAudit}</span>
      </div>
      <div class="ds-stat">
        <span class="ds-stat-label">✓ Ukończone</span>
        <span class="ds-stat-value" style="color: var(--ok, #4a8a5e);">${nCompleted}</span>
      </div>
      <div class="ds-stat">
        <span class="ds-stat-label">◐ W trakcie</span>
        <span class="ds-stat-value" style="color: var(--gold, #c8a951);">${nInProgress}</span>
      </div>
      <div class="ds-stat">
        <span class="ds-stat-label">○ Nierozpoczęte</span>
        <span class="ds-stat-value" style="color: var(--rose, #c87a5e);">${nNotStarted}</span>
      </div>
      ${nPending > 0 ? `
        <div class="ds-stat">
          <span class="ds-stat-label">⏳ Scope nieukończony</span>
          <span class="ds-stat-value" style="color: var(--ink-mute, #8b7355);">${nPending}</span>
        </div>
      ` : ''}
      <div class="ds-stat">
        <span class="ds-stat-label">⏱ Czas do końca</span>
        <span class="ds-stat-value">${totalH > 0 ? totalH + 'h ' : ''}${totalM}min</span>
      </div>
      ${totalFlags > 0 ? `
        <div class="ds-stat">
          <span class="ds-stat-label">🔴 Łącznie flag</span>
          <span class="ds-stat-value" style="color: var(--rose, #c87a5e);">${totalFlags}</span>
        </div>
      ` : ''}
    </div>
  `;
}

function buildDashboard() {
  const grid = document.getElementById('dashboard-grid');
  if (!grid) return;

  grid.innerHTML = '';

  const cards = [];
  // Iterujemy po SCOPE_KODY_E13 (kolejność z iter 1)
  SCOPE_KODY_E13.forEach(kod => {
    const cfg = SCOPE_CONFIG[kod];
    if (!cfg) return;
    const state = getScopeAuditState(kod);
    const progress = calcScopeProgress(cfg.prefix, cfg.expected);
    const flagCount = calcScopeFlagCount(cfg.prefix);
    cards.push({ kod, cfg, state, progress, flagCount, audit: state.audit });
    grid.innerHTML += buildScopeCard(kod, cfg, state, progress, flagCount);
  });

  buildDashboardOverall(cards, null);
}

// Reactive: zmiana SCOPE-V1-* odświeża dashboard
function bindDashboardReactive() {
  document.querySelectorAll('[data-id^="SCOPE-V1-"]').forEach(el => {
    el.addEventListener('change', () => {
      buildDashboard();
    });
  });
  // Refresh button
  const btn = document.getElementById('dashboard-refresh-btn');
  if (btn) {
    btn.addEventListener('click', () => {
      buildDashboard();
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    if (typeof buildDashboard === 'function') {
      buildDashboard();
      bindDashboardReactive();
    }
  }, 300);
});




// ============================================================
// === E13 ITER 3 — Excel Export, Import, Print, Validation ===
// ============================================================

// Lista scope w SCOPE_CONFIG (już zdefiniowana w iter 2)
// Funkcja showSaveIndicator już istnieje w Master (oryginalna)


// ============================================================
// EKSPORT DO EXCEL
// ============================================================
async function exportFormToExcel() {
  if (typeof ExcelJS === 'undefined') {
    alert('⚠ Biblioteka ExcelJS nie jest załadowana. Sprawdź połączenie internetowe.');
    return;
  }

  showSaveIndicator('📊 Generuję plik Excel...');

  try {
    // === Paleta ENESA ===
    const COLOR_GREEN_DEEP = 'FF1A4D3A';
    const COLOR_GREEN_LIGHT = 'FFC8D5C2';
    const COLOR_PAPER = 'FFF5EFE0';
    const COLOR_PAPER_DEEP = 'FFEBE3D0';
    const COLOR_GOLD = 'FFA87F2A';
    const COLOR_WHITE = 'FFFFFFFF';
    const COLOR_INK = 'FF1A1612';

    const wb = new ExcelJS.Workbook();
    wb.creator = 'ENESA';
    wb.created = new Date();

    // Mapa nazw zakładek dla etapów Master (wzorzec ENESA_Formularz_AUDYT_GLOBAL_v1_0.xlsx)
    const ETAP_TABS = {
      'etap-0':  'E0_Audyt',
      'etap-1':  'E1_Zakres',
      'etap-2':  'E2_Zaklad',
      'etap-3':  'E3_Procesy',
      'etap-4':  'E4_Wydzialy',
      'etap-5':  'E5_Hale',
      'etap-6':  'E6_Macierz',
      'etap-7':  'E7_Nosniki',
      'etap-8':  'E8_Zuzycia',
      'etap-9':  'E9_Zmienne',
      'etap-10': 'E10_EnMS',
      'etap-11': 'E11_Kontekst',
      'etap-12': 'E12_Historia',
      'etap-13': 'E13_Zakres_audytu',
    };

    const klient = enesaStorage.get(STORAGE_PREFIX + 'AUD-V1-NAZWA') || '(brak)';

    // Helper: aplikuje style do całego rzędu
    function styleRow(row, opts) {
      opts = opts || {};
      if (opts.height) row.height = opts.height;
      row.eachCell({ includeEmpty: true }, (cell) => {
        if (opts.font) cell.font = opts.font;
        if (opts.fill) cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: opts.fill } };
        if (opts.alignment) cell.alignment = opts.alignment;
        if (opts.border) cell.border = opts.border;
      });
    }

    // Helper: thin border dla całego zakresu
    const thinBorder = {
      top: { style: 'thin', color: { argb: 'FFD6CDB6' } },
      left: { style: 'thin', color: { argb: 'FFD6CDB6' } },
      bottom: { style: 'thin', color: { argb: 'FFD6CDB6' } },
      right: { style: 'thin', color: { argb: 'FFD6CDB6' } },
    };

    // Helper: render header zakładki etapu (wiersze 1-5)
    function renderEtapHeader(ws, eyebrow, title, desc, headerColumns) {
      const cols = headerColumns.length;
      const lastCol = String.fromCharCode(64 + cols);

      // Wiersz 1: POUFNE
      ws.getCell('A1').value = 'POUFNE — WEWNĘTRZNY DOKUMENT PROJEKTOWY ENESA · Nie udostępniać poza zespołem projektu';
      ws.mergeCells('A1:' + lastCol + '1');
      ws.getRow(1).height = 18;
      ws.getCell('A1').font = { name: 'Calibri', size: 8, color: { argb: COLOR_GOLD } };
      ws.getCell('A1').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLOR_PAPER_DEEP } };
      ws.getCell('A1').alignment = { horizontal: 'center', vertical: 'middle' };

      // Wiersz 2: ETAP nagłówek
      ws.getCell('A2').value = eyebrow + ' · ' + title.toUpperCase();
      ws.mergeCells('A2:' + lastCol + '2');
      ws.getRow(2).height = 32;
      ws.getCell('A2').font = { name: 'Calibri', size: 14, bold: true, color: { argb: COLOR_WHITE } };
      ws.getCell('A2').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLOR_GREEN_DEEP } };
      ws.getCell('A2').alignment = { horizontal: 'center', vertical: 'middle' };

      // Wiersz 3: opis
      ws.getCell('A3').value = desc;
      ws.mergeCells('A3:' + lastCol + '3');
      ws.getRow(3).height = 22;
      ws.getCell('A3').font = { name: 'Calibri', size: 10, color: { argb: COLOR_GREEN_DEEP } };
      ws.getCell('A3').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLOR_PAPER } };
      ws.getCell('A3').alignment = { horizontal: 'center', vertical: 'middle' };

      // Wiersz 4: pusty (separator)

      // Wiersz 5: header kolumn
      headerColumns.forEach((h, i) => {
        const cell = ws.getCell(5, i + 1);
        cell.value = h;
        cell.font = { name: 'Calibri', size: 11, bold: true, color: { argb: COLOR_WHITE } };
        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLOR_GREEN_DEEP } };
        cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
        cell.border = thinBorder;
      });
      ws.getRow(5).height = 28;
    }

    // Helper: render wiersz grupy (▼ separator)
    function renderGroupRow(ws, rowIdx, title, lastCol) {
      ws.getCell('A' + rowIdx).value = '  ▼  ' + title.toUpperCase();
      ws.mergeCells('A' + rowIdx + ':' + lastCol + rowIdx);
      ws.getCell('A' + rowIdx).font = { name: 'Calibri', size: 11, bold: true, color: { argb: COLOR_GREEN_DEEP } };
      ws.getCell('A' + rowIdx).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLOR_PAPER_DEEP } };
      ws.getCell('A' + rowIdx).alignment = { horizontal: 'left', vertical: 'middle', indent: 1 };
      ws.getRow(rowIdx).height = 24;
    }

    // Helper: render wiersz pola
    function renderFieldRow(ws, rowIdx, fid, question, kto, hint, val, unit) {
      ws.getCell('A' + rowIdx).value = fid;
      ws.getCell('A' + rowIdx).font = { name: 'Consolas', size: 9, color: { argb: COLOR_INK } };
      ws.getCell('A' + rowIdx).alignment = { vertical: 'middle' };

      ws.getCell('B' + rowIdx).value = question;
      ws.getCell('B' + rowIdx).font = { name: 'Calibri', size: 10 };
      ws.getCell('B' + rowIdx).alignment = { vertical: 'middle', wrapText: true };

      ws.getCell('C' + rowIdx).value = kto;
      ws.getCell('C' + rowIdx).font = { name: 'Calibri', size: 9, bold: true, color: { argb: COLOR_GREEN_DEEP } };
      ws.getCell('C' + rowIdx).alignment = { horizontal: 'center', vertical: 'middle' };

      ws.getCell('D' + rowIdx).value = hint;
      ws.getCell('D' + rowIdx).font = { name: 'Calibri', size: 9, color: { argb: COLOR_INK } };
      ws.getCell('D' + rowIdx).alignment = { vertical: 'middle', wrapText: true };

      ws.getCell('E' + rowIdx).value = val;
      ws.getCell('E' + rowIdx).font = { name: 'Calibri', size: 11, bold: !!val, color: { argb: COLOR_GREEN_DEEP } };
      ws.getCell('E' + rowIdx).alignment = { vertical: 'middle' };
      ws.getCell('E' + rowIdx).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFFFBED' } };  // very light gold

      ws.getCell('F' + rowIdx).value = unit || '—';
      ws.getCell('F' + rowIdx).font = { name: 'Calibri', size: 9, color: { argb: COLOR_INK } };
      ws.getCell('F' + rowIdx).alignment = { horizontal: 'center', vertical: 'middle' };

      // Border
      for (let c = 1; c <= 6; c++) {
        ws.getCell(rowIdx, c).border = thinBorder;
      }

      // Row height auto - dłuższe pytania/hinty zwiększają wysokość
      const lenMax = Math.max((question || '').length, (hint || '').length);
      if (lenMax > 50) {
        ws.getRow(rowIdx).height = Math.min(60, 18 + Math.floor(lenMax / 50) * 14);
      } else {
        ws.getRow(rowIdx).height = 22;
      }
    }

    // ============================================================
    // === Zakładka 1: Spis treści
    // ============================================================
    const wsToc = wb.addWorksheet('Spis treści');
    wsToc.columns = [
      { width: 8 }, { width: 40 }, { width: 80 }, { width: 22 },
    ];

    // Wiersz 1: POUFNE
    wsToc.getCell('A1').value = 'POUFNE — WEWNĘTRZNY DOKUMENT PROJEKTOWY ENESA';
    wsToc.mergeCells('A1:C1');
    wsToc.getRow(1).height = 18;
    wsToc.getCell('A1').font = { name: 'Calibri', size: 8, color: { argb: COLOR_GOLD } };
    wsToc.getCell('A1').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLOR_PAPER_DEEP } };
    wsToc.getCell('A1').alignment = { horizontal: 'center', vertical: 'middle' };

    // Wiersz 2: tytuł
    wsToc.getCell('A2').value = 'ENESA Audyt Energetyczny — FORMULARZ GLOBALNY (Master) · v1.1';
    wsToc.mergeCells('A2:C2');
    wsToc.getRow(2).height = 40;
    wsToc.getCell('A2').font = { name: 'Calibri', size: 16, bold: true, color: { argb: COLOR_WHITE } };
    wsToc.getCell('A2').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLOR_GREEN_DEEP } };
    wsToc.getCell('A2').alignment = { horizontal: 'center', vertical: 'middle' };

    // Wiersz 3: subtytuł
    wsToc.getCell('A3').value = 'Audyt energetyczny + EnMS Foundation (ISO 50001 § 4-6) · 14 zakładek (E0-E13) · Eksport: ' + new Date().toLocaleString('pl-PL');
    wsToc.mergeCells('A3:C3');
    wsToc.getRow(3).height = 26;
    wsToc.getCell('A3').font = { name: 'Calibri', size: 11, color: { argb: COLOR_GREEN_DEEP } };
    wsToc.getCell('A3').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLOR_PAPER } };
    wsToc.getCell('A3').alignment = { horizontal: 'center', vertical: 'middle' };

    // Wiersz 5: Klient
    wsToc.getCell('A5').value = 'Klient:';
    wsToc.getCell('A5').font = { bold: true, color: { argb: COLOR_GREEN_DEEP } };
    wsToc.getCell('B5').value = klient;
    wsToc.getCell('B5').font = { bold: true, size: 12 };

    // Wiersz 7: Workflow
    wsToc.getCell('A7').value = 'WORKFLOW WYPEŁNIANIA:';
    wsToc.getCell('A7').font = { bold: true, color: { argb: COLOR_GREEN_DEEP } };
    const workflow = [
      '1. PRE-WORK (klient): EM zbiera dokumenty, wypełnia E0/E1 (dane obiektywne)',
      '2. SESJA OTWIERAJĄCA (Konsultant + EM, 4h): wypełnienie E2-E12 (kontekst, procesy, wydziały)',
      '3. POST-WORK (Konsultant): konsolidacja, gap analysis ISO 50001, plan scope (E13)',
      '4. SESJA SCOPE (Konsultant + KIER UR): delegacja wypełniania scope formularzy',
      '5. WERYFIKACJA (Konsultant): kontrola spójności, raport audytu',
    ];
    workflow.forEach((line, i) => {
      wsToc.getCell('A' + (8 + i)).value = line;
      wsToc.mergeCells('A' + (8 + i) + ':C' + (8 + i));
    });

    // Wiersz 14: Tabela zakładek
    wsToc.getCell('A14').value = 'ZAKŁADKI:';
    wsToc.getCell('A14').font = { bold: true, color: { argb: COLOR_GREEN_DEEP } };

    const tocHeaderRow = 15;
    ['#', 'NAZWA ETAPU', 'OPIS', 'POSTĘP'].forEach((h, i) => {
      const cell = wsToc.getCell(tocHeaderRow, i + 1);
      cell.value = h;
      cell.font = { name: 'Calibri', size: 11, bold: true, color: { argb: COLOR_WHITE } };
      cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLOR_GREEN_DEEP } };
      cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
      cell.border = thinBorder;
    });
    wsToc.getRow(tocHeaderRow).height = 24;

    // Zbieramy info o sekcjach z DOM
    const sections = [];
    document.querySelectorAll('section.section[id^="etap-"]').forEach(sec => {
      const id = sec.id;
      const eyebrow = (sec.querySelector('.section-eyebrow') || {}).textContent || '';
      const title = (sec.querySelector('.section-title') || {}).textContent || '';
      const desc = (sec.querySelector('.section-desc') || {}).textContent || '';
      const progressEl = sec.querySelector('.section-progress');
      const progress = progressEl ? progressEl.textContent.trim() : '';
      sections.push({ id, eyebrow, title, desc, progress, element: sec });
    });

    let r = tocHeaderRow + 1;
    sections.forEach(s => {
      const tabName = ETAP_TABS[s.id] || s.id;
      wsToc.getCell(r, 1).value = s.eyebrow.replace('ETAP ', 'E').trim();
      wsToc.getCell(r, 2).value = tabName + ' · ' + s.title.trim();
      wsToc.getCell(r, 3).value = s.desc.trim().substring(0, 150);
      wsToc.getCell(r, 4).value = s.progress;
      for (let c = 1; c <= 4; c++) wsToc.getCell(r, c).border = thinBorder;
      wsToc.getCell(r, 1).font = { bold: true, name: 'Consolas', size: 10 };
      wsToc.getCell(r, 4).alignment = { horizontal: 'center' };
      wsToc.getRow(r).height = 22;
      r++;
    });

    // Sekcja zakładek scope
    r += 1;
    wsToc.getCell(r, 1).value = 'AUDYTOWANE SCOPE (z E13):';
    wsToc.getCell(r, 1).font = { bold: true, color: { argb: COLOR_GREEN_DEEP } };
    r++;

    const auditedScopes = [];
    SCOPE_KODY_E13.forEach(kod => {
      const audit = (enesaStorage.get(STORAGE_PREFIX + 'SCOPE-V1-' + kod + '-AUDIT') || '');
      const cfg = SCOPE_CONFIG[kod];
      if (audit === 'TAK') {
        auditedScopes.push(kod);
        const sheetName = (kod + '_' + cfg.name).substring(0, 31).replace(/[\\\/\?\*\[\]]/g, '_');
        wsToc.getCell(r, 1).value = cfg.icon;
        wsToc.getCell(r, 1).alignment = { horizontal: 'center' };
        wsToc.getCell(r, 2).value = sheetName;
        wsToc.getCell(r, 3).value = cfg.name;
        for (let c = 1; c <= 4; c++) wsToc.getCell(r, c).border = thinBorder;
        r++;
      }
    });
    if (auditedScopes.length === 0) {
      wsToc.getCell(r, 1).value = '(brak audytowanych scope — zaznacz w E13)';
      wsToc.mergeCells('A' + r + ':C' + r);
      wsToc.getCell(r, 1).font = { italic: true, color: { argb: COLOR_GOLD } };
    }

    // ============================================================
    // === Per etap (E0-E13) ===
    // ============================================================
    const headerCols = ['ID pola', 'PYTANIE', 'KTO', 'HINT / objaśnienie', 'WARTOŚĆ', 'JEDNOSTKA'];

    sections.forEach(s => {
      const tabName = ETAP_TABS[s.id] || s.id;
      const safeTabName = tabName.substring(0, 31).replace(/[\\\/\?\*\[\]]/g, '_');
      const ws = wb.addWorksheet(safeTabName);

      // Szerokości kolumn
      ws.columns = [
        { width: 22 },  // ID
        { width: 50 },  // PYTANIE
        { width: 8 },   // KTO
        { width: 60 },  // HINT
        { width: 25 },  // WARTOŚĆ
        { width: 12 },  // JEDNOSTKA
      ];

      // Header (wiersze 1-5)
      renderEtapHeader(ws, s.eyebrow.trim(), s.title.trim(), s.desc.trim(), headerCols);

      // Wiersze danych zaczynają się od 6
      let rowIdx = 6;
      const seenIds = new Set();

      // Iterujemy po grupach
      const groups = s.element.querySelectorAll('.group');
      groups.forEach(grp => {
        const grpTitle = (grp.querySelector('.group-title') || {}).textContent || '';
        if (grpTitle.trim()) {
          renderGroupRow(ws, rowIdx, grpTitle.trim(), 'F');
          rowIdx++;
        }

        // Pola w grupie
        grp.querySelectorAll('.field').forEach(f => {
          const inputEl = f.querySelector('[data-id]');
          if (!inputEl) return;
          const fid = inputEl.dataset.id;
          if (seenIds.has(fid)) return;
          seenIds.add(fid);

          const qEl = f.querySelector('.field-q');
          const ktoEl = f.querySelector('.kto-cell .tag, .tag');
          const unitEl = f.querySelector('.field-unit');
          const hintEl = f.querySelector('.field-hint');

          const question = qEl ? qEl.textContent.trim() : '';
          const kto = ktoEl ? ktoEl.textContent.trim() : '';
          const hint = hintEl ? hintEl.textContent.trim() : '';
          const unit = unitEl ? unitEl.textContent.trim() : '';
          const val = enesaStorage.get(STORAGE_PREFIX + fid) || '';

          renderFieldRow(ws, rowIdx, fid, question, kto, hint, val, unit);
          rowIdx++;
        });
      });

      // Pola spoza grup (np. macierz E13 SCOPE)
      const allFields = s.element.querySelectorAll('[data-id]');
      const ungrouped = Array.from(allFields).filter(el => !seenIds.has(el.dataset.id));
      if (ungrouped.length > 0 && groups.length > 0) {
        renderGroupRow(ws, rowIdx, 'POZOSTAŁE POLA', 'F');
        rowIdx++;
      }

      ungrouped.forEach(el => {
        const fid = el.dataset.id;
        if (seenIds.has(fid)) return;
        seenIds.add(fid);

        let question = '';
        let hint = '';
        let kto = '';
        let unit = '';

        const closestRow = el.closest('tr');
        const closestField = el.closest('.field');

        if (closestRow) {
          // Pole w tabeli (np. macierz E13 lub E4 wydziały)
          const cells = closestRow.cells;
          if (cells && cells.length > 0) {
            const firstCell = cells[0].textContent.trim();
            const secondCell = cells[1] ? cells[1].textContent.trim() : '';
            question = firstCell + (secondCell && secondCell !== firstCell ? ' — ' + secondCell.substring(0, 60) : '');
          }
        } else if (closestField) {
          const qEl = closestField.querySelector('.field-q');
          const hintEl = closestField.querySelector('.field-hint');
          const ktoEl = closestField.querySelector('.tag');
          const unitEl = closestField.querySelector('.field-unit');
          question = qEl ? qEl.textContent.trim() : '';
          hint = hintEl ? hintEl.textContent.trim() : '';
          kto = ktoEl ? ktoEl.textContent.trim() : '';
          unit = unitEl ? unitEl.textContent.trim() : '';
        }

        const val = enesaStorage.get(STORAGE_PREFIX + fid) || '';
        renderFieldRow(ws, rowIdx, fid, question, kto, hint, val, unit);
        rowIdx++;
      });
    });

    // ============================================================
    // === Zakładki per audytowany scope (uproszczona struktura)
    // ============================================================
    auditedScopes.forEach(kod => {
      const cfg = SCOPE_CONFIG[kod];
      const sheetName = (kod + '_' + cfg.name).substring(0, 31).replace(/[\\\/\?\*\[\]]/g, '_');
      const ws = wb.addWorksheet(sheetName);
      ws.columns = [{ width: 30 }, { width: 50 }];

      // Header
      ws.getCell('A1').value = 'POUFNE — Scope ' + kod + ' (' + cfg.name + ')';
      ws.mergeCells('A1:B1');
      ws.getRow(1).height = 18;
      ws.getCell('A1').font = { name: 'Calibri', size: 8, color: { argb: COLOR_GOLD } };
      ws.getCell('A1').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLOR_PAPER_DEEP } };
      ws.getCell('A1').alignment = { horizontal: 'center', vertical: 'middle' };

      ws.getCell('A2').value = 'SCOPE ' + kod + ' · ' + cfg.name;
      ws.mergeCells('A2:B2');
      ws.getRow(2).height = 32;
      ws.getCell('A2').font = { name: 'Calibri', size: 14, bold: true, color: { argb: COLOR_WHITE } };
      ws.getCell('A2').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLOR_GREEN_DEEP } };
      ws.getCell('A2').alignment = { horizontal: 'center', vertical: 'middle' };

      ws.getCell('A3').value = 'Plik: ' + (cfg.file || '(scope w przygotowaniu)') + ' · Storage prefix: ' + cfg.prefix;
      ws.mergeCells('A3:B3');
      ws.getRow(3).height = 22;
      ws.getCell('A3').font = { name: 'Calibri', size: 10, color: { argb: COLOR_GREEN_DEEP } };
      ws.getCell('A3').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLOR_PAPER } };
      ws.getCell('A3').alignment = { horizontal: 'center', vertical: 'middle' };

      // Header tabeli (wiersz 5)
      ['ID pola', 'WARTOŚĆ'].forEach((h, i) => {
        const cell = ws.getCell(5, i + 1);
        cell.value = h;
        cell.font = { bold: true, color: { argb: COLOR_WHITE } };
        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: COLOR_GREEN_DEEP } };
        cell.alignment = { horizontal: 'center', vertical: 'middle' };
        cell.border = thinBorder;
      });
      ws.getRow(5).height = 24;

      // Dane
      const keys = [];
      try {
        for (let i = 0; i < localStorage.length; i++) {
          const key = localStorage.key(i);
          if (key && key.startsWith(cfg.prefix)) keys.push(key);
        }
      } catch (e) {}
      keys.sort();

      let r2 = 6;
      let dataCount = 0;
      keys.forEach(k => {
        const fid = k.substring(cfg.prefix.length);
        if (fid.startsWith('_')) return;
        const val = localStorage.getItem(k) || '';
        ws.getCell('A' + r2).value = fid;
        ws.getCell('A' + r2).font = { name: 'Consolas', size: 9 };
        ws.getCell('A' + r2).border = thinBorder;
        ws.getCell('B' + r2).value = val;
        ws.getCell('B' + r2).font = { name: 'Calibri', size: 11, bold: !!val, color: { argb: COLOR_GREEN_DEEP } };
        ws.getCell('B' + r2).border = thinBorder;
        ws.getCell('B' + r2).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFFFBED' } };
        r2++;
        dataCount++;
      });

      if (dataCount === 0) {
        ws.getCell('A' + r2).value = '(brak danych — scope nie był wypełniany w tej przeglądarce)';
        ws.mergeCells('A' + r2 + ':B' + r2);
        ws.getCell('A' + r2).font = { italic: true, color: { argb: COLOR_GOLD } };
        ws.getCell('A' + r2).alignment = { horizontal: 'center' };
      }
    });

    // === Zapis pliku ===
    const buf = await wb.xlsx.writeBuffer();
    const blob = new Blob([buf], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const fileName = 'ENESA_Audyt_' + (klient || 'zaklad').replace(/[^a-zA-Z0-9_-]/g, '_') + '_' + new Date().toISOString().slice(0,10) + '.xlsx';

    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = fileName;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(a.href);

    showSaveIndicator('✓ Wyeksportowano: ' + fileName);

  } catch (err) {
    console.error('Excel export error:', err);
    alert('⚠ Błąd eksportu: ' + err.message);
  }
}


// ============================================================
// IMPORT Z EXCEL
// ============================================================
function importFromExcel(file) {
  if (typeof XLSX === 'undefined') {
    alert('⚠ Biblioteka SheetJS nie jest załadowana.');
    return;
  }
  if (!file) return;

  if (!confirm(
    '⚠ Import nadpisze obecne dane w przeglądarce!\n\n' +
    'Plik: ' + file.name + '\n' +
    'Wczyta dane do localStorage prefixów: master:, ' +
    'oraz wszystkich scope (boilers:, rc:, ca:, ahu:, lh:, ...)\n\n' +
    'Czy kontynuować?'
  )) return;

  // Mapa zakładek na storage
  const ETAP_TABS = {
    'E0_Audyt': true,    'E1_Zakres': true,    'E2_Zaklad': true,
    'E3_Procesy': true,  'E4_Wydzialy': true,  'E5_Hale': true,
    'E6_Macierz': true,  'E7_Nosniki': true,   'E8_Zuzycia': true,
    'E9_Zmienne': true,  'E10_EnMS': true,     'E11_Kontekst': true,
    'E12_Historia': true,'E13_Zakres_audytu': true,
  };

  const reader = new FileReader();
  reader.onload = (e) => {
    try {
      const data = new Uint8Array(e.target.result);
      const wb = XLSX.read(data, { type: 'array' });

      let totalImported = 0;
      let scopesImported = [];
      let etapowImported = 0;

      // === Import zakładek E0_..E13_ — wszystkie do master: ===
      wb.SheetNames.forEach(sheetName => {
        if (ETAP_TABS[sheetName]) {
          const rows = XLSX.utils.sheet_to_json(wb.Sheets[sheetName], { header: 1, defval: '' });
          // Header tabeli jest w wierszu 5 (index 4): ['ID pola', 'PYTANIE', 'KTO', 'HINT', 'WARTOŚĆ', 'JEDNOSTKA']
          // Dane od wiersza 6 (index 5)
          let imported = 0;
          for (let i = 5; i < rows.length; i++) {
            const row = rows[i];
            if (!row[0]) continue;
            const fid = String(row[0]).trim();
            // Pomijamy nagłówki grup (zaczynają się od ▼)
            if (fid.startsWith('▼') || fid.startsWith('  ▼')) continue;
            // WARTOŚĆ jest w kolumnie 5 (index 4)
            const val = row[4] !== undefined ? String(row[4]) : '';
            if (fid && val !== '') {
              enesaStorage.set(STORAGE_PREFIX + fid, val);
              imported++;
            }
          }
          if (imported > 0) etapowImported++;
          totalImported += imported;
        }
      });

      // === Import zakładek scope (BO_, RC_, CA_, ...) ===
      Object.keys(SCOPE_CONFIG).forEach(kod => {
        const cfg = SCOPE_CONFIG[kod];
        const sheetNames = wb.SheetNames.filter(n => n.startsWith(kod + '_'));
        sheetNames.forEach(sheetName => {
          const rows = XLSX.utils.sheet_to_json(wb.Sheets[sheetName], { header: 1, defval: '' });
          let imported = 0;
          // Pomijamy 5 pierwszych wierszy (tytuł, plik, prefix, pusty, header)
          for (let i = 5; i < rows.length; i++) {
            const row = rows[i];
            if (!row[0]) continue;
            const fid = String(row[0]).trim();
            const val = row[1] !== undefined ? String(row[1]) : '';
            if (fid && !fid.startsWith('(')) {
              localStorage.setItem(cfg.prefix + fid, val);
              imported++;
            }
          }
          if (imported > 0) {
            scopesImported.push(kod + ' (' + imported + ' pól)');
            totalImported += imported;
          }
        });
      });

      const msg = '✓ Wczytano ' + totalImported + ' pól z pliku.\n\n' +
                  'Etapy Master z danymi: ' + etapowImported + '\n' +
                  (scopesImported.length > 0 ? 'Scope: ' + scopesImported.join(', ') + '\n' : '') +
                  '\nOdświeżam stronę aby zobaczyć dane...';
      alert(msg);
      setTimeout(() => location.reload(), 500);

    } catch (err) {
      console.error('Excel import error:', err);
      alert('⚠ Błąd importu: ' + err.message + '\n\nSprawdź czy plik to zgodny eksport ENESA.');
    }
  };
  reader.readAsArrayBuffer(file);
}


// ============================================================
// PRINT E13 — używa CSS @media print
// ============================================================
function printE13() {
  // Przewiń do E13 przed drukiem
  const e13 = document.getElementById('etap-13');
  if (e13) e13.scrollIntoView({ behavior: 'smooth', block: 'start' });
  setTimeout(() => window.print(), 300);
}


// ============================================================
// WALIDACJA ZAKRESU AUDYTU
// ============================================================
function validateAuditScope() {
  const issues = [];
  const warnings = [];
  const successes = [];

  // 1. Sprawdź SCOPE-V3-CEL
  const cel = (document.querySelector('[data-id="SCOPE-V3-CEL"]') || {}).value || '';
  if (!cel.trim()) {
    issues.push('Pole <strong>SCOPE-V3-CEL</strong> (Cel audytu) jest puste — wymagane uzasadnienie zakresu (EED / ISO 50001 / Białe Certyfikaty / Bank / CSRD)');
  } else if (cel.length < 20) {
    warnings.push('Pole <strong>SCOPE-V3-CEL</strong> jest bardzo krótkie (' + cel.length + ' znaków) — zalecam pełniejsze uzasadnienie');
  } else {
    successes.push('Cel audytu wpisany (' + cel.length + ' znaków)');
  }

  // 2. Sprawdź SCOPE-V4-PROG (data w przyszłości)
  const termin = (document.querySelector('[data-id="SCOPE-V4-PROG"]') || {}).value || '';
  if (!termin) {
    warnings.push('Pole <strong>SCOPE-V4-PROG</strong> (Planowany termin) jest puste — zalecane wpisanie deadline');
  } else {
    const terminDate = new Date(termin);
    const now = new Date();
    if (terminDate < now) {
      warnings.push('Termin <strong>' + termin + '</strong> jest w przeszłości — sprawdź czy poprawny');
    } else {
      const diffDays = Math.round((terminDate - now) / (1000 * 60 * 60 * 24));
      if (diffDays < 30) {
        warnings.push('Termin <strong>' + termin + '</strong> za ' + diffDays + ' dni — krótki czas (audyt PN-EN 16247 typowo 2-12 mies.)');
      } else {
        successes.push('Termin: ' + termin + ' (za ' + diffDays + ' dni)');
      }
    }
  }

  // 3. Sprawdź czy ≥1 instalacja audytowana
  let nAudit = 0;
  const auditList = [];
  SCOPE_KODY_E13.forEach(kod => {
    const audit = (document.querySelector('[data-id="SCOPE-V1-' + kod + '-AUDIT"]') || {}).value || '';
    if (audit === 'TAK') {
      nAudit++;
      auditList.push(kod);
    }
  });
  if (nAudit === 0) {
    issues.push('Żadna instalacja nie jest oznaczona jako "Audytowana? = TAK" — wybierz w macierzy E13');
  } else {
    successes.push('Audytowanych instalacji: <strong>' + nAudit + '</strong> (' + auditList.join(', ') + ')');
  }

  // 4. Sprawdź czy każdy audytowany scope ma EXIST=TAK
  SCOPE_KODY_E13.forEach(kod => {
    const exist = (document.querySelector('[data-id="SCOPE-V1-' + kod + '-EXIST"]') || {}).value || '';
    const audit = (document.querySelector('[data-id="SCOPE-V1-' + kod + '-AUDIT"]') || {}).value || '';
    if (audit === 'TAK' && exist !== 'TAK') {
      warnings.push('Scope <strong>' + kod + '</strong> jest oznaczony jako audytowany, ale "Występuje?" = "' + (exist || 'puste') + '" — sprawdź spójność');
    }
  });

  // 5. Sprawdź obecność scope dla których nie ma jeszcze formularza
  const auditedPending = [];
  SCOPE_KODY_E13.forEach(kod => {
    const audit = (document.querySelector('[data-id="SCOPE-V1-' + kod + '-AUDIT"]') || {}).value || '';
    if (audit === 'TAK' && SCOPE_CONFIG[kod] && SCOPE_CONFIG[kod].status === 'pending') {
      auditedPending.push(kod);
    }
  });
  if (auditedPending.length > 0) {
    warnings.push('Audytowane scope, których formularze są jeszcze w przygotowaniu: <strong>' + auditedPending.join(', ') + '</strong>');
  }

  // 6. Sprawdź podstawowe pola Master (Klient + Audytor)
  const klient = enesaStorage.get(STORAGE_PREFIX + 'AUD-V1-NAZWA') || '';
  if (!klient.trim()) {
    issues.push('Pole <strong>AUD-V1-NAZWA</strong> (Nazwa zakładu) puste — uzupełnij w E0');
  } else {
    successes.push('Nazwa zakładu: <strong>' + klient + '</strong>');
  }

  // === Render wyników ===
  // Wyniki walidacji wyświetlamy wewnątrz sekcji E13 (a nie obok globalnego toolbar)
  const e13Section = document.getElementById('etap-13');
  const e13Body = e13Section ? e13Section.querySelector('.section-body') : null;
  let resultEl = document.getElementById('validation-result');
  if (!resultEl) {
    resultEl = document.createElement('div');
    resultEl.id = 'validation-result';
    if (e13Body) {
      // Wstawiamy jako pierwszy element w E13 body
      e13Body.insertBefore(resultEl, e13Body.firstChild);
    }
  }

  if (issues.length === 0 && warnings.length === 0) {
    resultEl.className = 'validation-result ok';
    resultEl.innerHTML = '<h4>✓ Walidacja zakresu — OK</h4>' +
                        '<ul>' + successes.map(s => '<li>' + s + '</li>').join('') + '</ul>';
  } else if (issues.length === 0) {
    resultEl.className = 'validation-result warn';
    resultEl.innerHTML = '<h4>⚠ Walidacja — uwagi (' + warnings.length + ')</h4>' +
                        '<p><strong>Sukces:</strong></p><ul>' + successes.map(s => '<li>' + s + '</li>').join('') + '</ul>' +
                        '<p style="margin-top:10px"><strong>Uwagi:</strong></p><ul>' + warnings.map(w => '<li>' + w + '</li>').join('') + '</ul>';
  } else {
    resultEl.className = 'validation-result error';
    resultEl.innerHTML = '<h4>✗ Walidacja — błędy (' + issues.length + ')</h4>' +
                        '<p><strong>Krytyczne:</strong></p><ul>' + issues.map(i => '<li>' + i + '</li>').join('') + '</ul>' +
                        (warnings.length > 0 ? '<p style="margin-top:10px"><strong>Uwagi:</strong></p><ul>' + warnings.map(w => '<li>' + w + '</li>').join('') + '</ul>' : '') +
                        (successes.length > 0 ? '<p style="margin-top:10px"><strong>Pozytywne:</strong></p><ul>' + successes.map(s => '<li>' + s + '</li>').join('') + '</ul>' : '');
  }

  resultEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
}


// ============================================================
// Bind handlers
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    const btnExport = document.getElementById('btn-export-excel');
    const btnPrint = document.getElementById('btn-print-e13');
    const btnValidate = document.getElementById('btn-validate');
    const fileImport = document.getElementById('file-import-excel');

    if (btnExport) btnExport.addEventListener('click', exportFormToExcel);
    if (btnPrint) btnPrint.addEventListener('click', printE13);
    if (btnValidate) btnValidate.addEventListener('click', validateAuditScope);
    if (fileImport) fileImport.addEventListener('change', (e) => {
      const f = e.target.files[0];
      if (f) importFromExcel(f);
      e.target.value = '';  // reset
    });
  }, 400);
});



// ============================================================
// === KRS Lookup — auto-pobieranie danych firmy z Open API MS ===
// ============================================================

const KRS_API_URL = 'https://api-krs.ms.gov.pl/api/krs/OdpisAktualny/';

function showKrsStatus(type, html) {
  const el = document.getElementById('krs-status');
  if (!el) return;
  el.className = 'show ' + type;
  el.innerHTML = html;
}

function clearKrsStatus() {
  const el = document.getElementById('krs-status');
  if (!el) return;
  el.className = '';
  el.innerHTML = '';
}

function highlightKrsFilled(fieldId) {
  const el = document.querySelector('[data-id="' + fieldId + '"]');
  if (!el) return;
  el.classList.add('krs-filled');
  setTimeout(() => el.classList.remove('krs-filled'), 6500);
}

function setMasterField(fieldId, value) {
  if (value === null || value === undefined || value === '') return false;
  const el = document.querySelector('[data-id="' + fieldId + '"]');
  if (!el) return false;
  el.value = value;
  el.dispatchEvent(new Event('input', { bubbles: true }));
  el.dispatchEvent(new Event('change', { bubbles: true }));
  if (typeof enesaStorage !== 'undefined') {
    enesaStorage.set(STORAGE_PREFIX + fieldId, String(value));
  }
  highlightKrsFilled(fieldId);
  return true;
}

function buildAdresFromKRS(adresObj) {
  if (!adresObj) return '';
  const parts = [];
  if (adresObj.ulica) {
    let ulica = adresObj.ulica;
    if (adresObj.nrDomu) {
      ulica += ' ' + adresObj.nrDomu;
      if (adresObj.nrLokalu) ulica += '/' + adresObj.nrLokalu;
    }
    parts.push(ulica);
  }
  const miasto = (adresObj.kodPocztowy ? adresObj.kodPocztowy + ' ' : '') + (adresObj.miejscowosc || '');
  if (miasto.trim()) parts.push(miasto.trim());
  return parts.join(', ');
}

function validateNIP(nip) {
  if (!/^\d{10}$/.test(nip)) return false;
  const weights = [6, 5, 7, 2, 3, 4, 5, 6, 7];
  let sum = 0;
  for (let i = 0; i < 9; i++) sum += parseInt(nip[i], 10) * weights[i];
  const checksum = sum % 11;
  return checksum === parseInt(nip[9], 10);
}

async function fetchKRSData() {
  const input = document.querySelector('[data-id="AUD-V0-KRS"]');
  const btn = document.getElementById('btn-krs-fetch');
  if (!input || !btn) return;

  let krs = (input.value || '').trim().replace(/[\s-]/g, '');

  if (!krs) {
    showKrsStatus('error', '⚠ Wpisz numer KRS w polu powyżej');
    return;
  }
  if (!/^\d{10}$/.test(krs)) {
    showKrsStatus('error', '⚠ KRS musi być <strong>10-cyfrowym numerem</strong> (np. 0000752649). Wpisałeś: ' + krs.length + ' znaków.');
    return;
  }

  // Wykrycie NIP
  if (krs[0] !== '0') {
    let msg = '⚠ To wygląda na <strong>NIP, nie KRS</strong> (KRS zaczyna się od zer).<br>';
    if (validateNIP(krs)) {
      msg += '✅ Numer NIP poprawny — wpisałem go do pola NIP poniżej.<br>';
      setMasterField('AUD-V2-NIP', krs);
    } else {
      msg += '⚠ Numer nie jest też poprawnym NIP-em (zła suma kontrolna).<br>';
    }
    msg += '📋 Aby pobrać dane firmy, znajdź jej KRS: <a href="https://wyszukiwarka-krs.ms.gov.pl/" target="_blank" rel="noopener" style="color: var(--green-deep);">otwórz wyszukiwarkę MS ↗</a>';
    showKrsStatus('error', msg);
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '⏳ Pobieram...';
  showKrsStatus('loading',
    '<span class="krs-spinner"></span> <strong>Pobieranie z Open API KRS Ministerstwa Sprawiedliwości...</strong>'
  );

  const url = KRS_API_URL + krs + '?rejestr=P&format=json';
  const startTime = performance.now();

  try {
    const response = await fetch(url, {
      method: 'GET',
      mode: 'cors',
      headers: { 'Accept': 'application/json' }
    });
    const elapsed = (performance.now() - startTime).toFixed(0);

    if (!response.ok) {
      let msg = '';
      if (response.status === 404) {
        msg = '❌ <strong>KRS ' + krs + ' nie istnieje</strong> w rejestrze przedsiębiorców (rejestr=P).<br>' +
              'Sprawdź numer lub wyszukaj firmę: <a href="https://wyszukiwarka-krs.ms.gov.pl/" target="_blank" rel="noopener">wyszukiwarka MS ↗</a>';
      } else if (response.status === 429) {
        msg = '⏱ <strong>Limit zapytań</strong> — odczekaj kilka minut i spróbuj ponownie.';
      } else if (response.status >= 500) {
        msg = '🔧 <strong>Serwer KRS chwilowo niedostępny</strong> (HTTP ' + response.status + '). Spróbuj za chwilę.';
      } else {
        msg = '❌ Błąd HTTP <strong>' + response.status + '</strong> — ' + response.statusText;
      }
      showKrsStatus('error', msg);
      return;
    }

    const data = await response.json();
    const odpis = data.odpis || data;
    const dane = odpis.dane || odpis;
    const dzial1 = dane.dzial1 || {};
    const dzial3 = dane.dzial3 || {};

    const podmiot = dzial1.danePodmiotu || {};
    const identyfikatory = podmiot.identyfikatory || {};
    const siedziba = dzial1.siedzibaIAdres || {};
    const adresObj = siedziba.adres || {};
    const pkdGlowny = ((dzial3.przedmiotDzialalnosci || {}).przedmiotPrzewazajacejDzialalnosci || [])[0] || {};

    const filled = [];
    if (setMasterField('AUD-V1-NAZWA', podmiot.nazwa)) filled.push('Nazwa');
    if (setMasterField('AUD-V2-NIP', identyfikatory.nip)) filled.push('NIP');
    if (setMasterField('AUD-V3-REGON', identyfikatory.regon)) filled.push('REGON');
    const adresStr = buildAdresFromKRS(adresObj);
    if (adresStr && setMasterField('AUD-V4-ADRES', adresStr)) filled.push('Adres');
    if (pkdGlowny.kodPKD) {
      const pkdStr = pkdGlowny.kodPKD + (pkdGlowny.opis ? ' (' + pkdGlowny.opis + ')' : '');
      if (setMasterField('AUD-V5-PKD', pkdStr)) filled.push('PKD');
    }

    if (filled.length === 0) {
      showKrsStatus('error',
        '⚠ KRS znaleziony, ale odpowiedź nie zawiera spodziewanych pól. <br>' +
        'Sprawdź ręcznie na <a href="https://wyszukiwarka-krs.ms.gov.pl/" target="_blank" rel="noopener">wyszukiwarce MS ↗</a>'
      );
      return;
    }

    const formaPrawna = podmiot.formaPrawna ? ' · ' + podmiot.formaPrawna : '';
    showKrsStatus('ok',
      '✅ <strong>Pobrano dane z KRS w ' + elapsed + 'ms</strong>' + formaPrawna + '<br>' +
      'Wypełniono: <strong>' + filled.join(', ') + '</strong>. Sprawdź wypełnione pola (lekko żółte tło, pulsują przez 6s).'
    );

  } catch (err) {
    const elapsed = (performance.now() - startTime).toFixed(0);
    const isCors = err.message && (err.message.includes('Failed to fetch') || err.message.includes('NetworkError'));
    showKrsStatus('error',
      '❌ <strong>' + (isCors ? 'Brak połączenia z API KRS' : 'Błąd zapytania') + '</strong> (po ' + elapsed + 'ms)<br>' +
      (isCors ?
        'Sprawdź połączenie internetowe. Możesz wpisać dane ręcznie poniżej.' :
        err.message)
    );
  } finally {
    btn.disabled = false;
    btn.innerHTML = '🔍 Pobierz dane';
  }
}

// Bind handlers
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    const btn = document.getElementById('btn-krs-fetch');
    const input = document.querySelector('[data-id="AUD-V0-KRS"]');
    if (btn) btn.addEventListener('click', fetchKRSData);
    if (input) {
      input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          fetchKRSData();
        }
      });
      input.addEventListener('input', (e) => {
        const cleaned = e.target.value.replace(/\D/g, '').substring(0, 10);
        if (cleaned !== e.target.value) e.target.value = cleaned;
        if (cleaned.length === 10 && cleaned[0] === '0') {
          clearTimeout(window._krsAutoFetchTimer);
          window._krsAutoFetchTimer = setTimeout(() => fetchKRSData(), 400);
        } else if (cleaned.length < 10) {
          clearKrsStatus();
        }
      });
    }
  }, 400);
});




// ============================================================
// === Meta-warstwa + Profile audytu (refaktor B) ============
// ============================================================

const AUDIT_PROFILES = {
  'eed': {
    name: 'EED minimum',
    description: 'Audyt obowiązkowy co 4 lata (Art. 36 Ustawy o efekt. energet., PN-EN 16247)',
    fields_estimate: '~600',
    duration_estimate: '~3 dni',
    price_range: '30-80 tys. PLN',
    enabled: true
  },
  'white-cert': {
    name: 'Białe Certyfikaty',
    description: 'Audyt dla zwrotu URE (Świadectwa Efektywności Energetycznej)',
    fields_estimate: '~400',
    duration_estimate: '~2 dni',
    price_range: '15-40 tys. PLN',
    enabled: false  // TODO później
  },
  'iso50001': {
    name: 'ISO 50001:2018 § 6.3',
    description: 'Pełny przegląd energetyczny dla certyfikacji ISO 50001 (EnPI + EnB + opportunities)',
    fields_estimate: '~1500',
    duration_estimate: '~7 dni',
    price_range: '80-180 tys. PLN',
    enabled: true
  },
  'full-map': {
    name: 'Pełna Mapa Energetyczna',
    description: 'ISO 50001 + ISO 50002 Level 3 + CSRD/ESRS, pełna dekarbonizacja',
    fields_estimate: '~3500',
    duration_estimate: '~14 dni',
    price_range: '250-500 tys. PLN',
    enabled: false
  },
  'custom': {
    name: 'Custom (audytor wybiera)',
    description: 'Audytor ENESA wybiera scope ad-hoc pod indywidualne potrzeby',
    fields_estimate: 'wariant',
    duration_estimate: 'wariant',
    price_range: 'wariant',
    enabled: false
  }
};

const PROFILE_STATE_KEY = 'enesa_audit_profile';

window.enesaSetAuditProfile = function(profile) {
  if (!AUDIT_PROFILES[profile]) {
    console.error('Unknown profile:', profile);
    return false;
  }
  if (!AUDIT_PROFILES[profile].enabled) {
    console.warn('Profile not yet enabled:', profile);
    return false;
  }
  
  // Zapis do localStorage
  try { localStorage.setItem(PROFILE_STATE_KEY, profile); } catch(e) {}
  
  // Pokoloruj pola
  document.querySelectorAll('[data-audit-profile]').forEach(el => {
    el.classList.remove('field-must', 'field-optional');
    const profiles = (el.dataset.auditProfile || '').split(',').filter(p => p);
    if (profiles.includes(profile)) {
      el.classList.add('field-must');
    } else if (profiles.length > 0) {
      el.classList.add('field-optional');
    }
  });
  
  document.body.classList.add('profile-active');
  document.body.dataset.activeProfile = profile;
  
  // Update statystyki
  enesaUpdateProfileStats();
  
  // Update UI selector (jeśli jest)
  document.querySelectorAll('.profile-option').forEach(opt => {
    opt.classList.toggle('selected', opt.dataset.profile === profile);
    const radio = opt.querySelector('input[type="radio"]');
    if (radio) radio.checked = (opt.dataset.profile === profile);
  });
  
  return true;
};

window.enesaClearAuditProfile = function() {
  try { localStorage.removeItem(PROFILE_STATE_KEY); } catch(e) {}
  document.body.classList.remove('profile-active');
  delete document.body.dataset.activeProfile;
  document.querySelectorAll('[data-audit-profile]').forEach(el => {
    el.classList.remove('field-must', 'field-optional');
  });
};

window.enesaUpdateProfileStats = function() {
  const profile = document.body.dataset.activeProfile;
  if (!profile) return;
  
  const allFields = document.querySelectorAll('[data-audit-profile]');
  let mustCount = 0, mustFilled = 0;
  
  allFields.forEach(el => {
    const profiles = (el.dataset.auditProfile || '').split(',').filter(p => p);
    if (profiles.includes(profile)) {
      mustCount++;
      if (el.value) mustFilled++;
    }
  });
  
  const pct = mustCount > 0 ? Math.round(mustFilled / mustCount * 100) : 0;
  const statsEl = document.getElementById('profile-stats-display');
  if (statsEl) {
    const profInfo = AUDIT_PROFILES[profile];
    statsEl.innerHTML = `
      <strong>📊 Profil: ${profInfo.name}</strong> · 
      Wypełnione MUST: <strong>${mustFilled}/${mustCount} (${pct}%)</strong> · 
      Szac. czas: ${profInfo.duration_estimate} · 
      Zakres cenowy: ${profInfo.price_range}
    `;
  }
};

window.enesaSetFieldMeta = function(fieldId, source, confidence) {
  const el = document.querySelector('[data-id="' + fieldId + '"]');
  if (!el) return false;
  if (source !== undefined) el.dataset.source = source;
  if (confidence !== undefined) el.dataset.confidence = confidence;
  return true;
};

window.enesaGetFieldsByPhase = function(phase) {
  return Array.from(document.querySelectorAll('[data-phase="' + phase + '"]'))
    .map(el => ({
      id: el.dataset.id,
      value: el.value,
      filled: !!el.value,
      source: el.dataset.source,
      confidence: el.dataset.confidence,
      iso: el.dataset.iso,
      iso50002: el.dataset.iso50002,
      eed: el.dataset.eed,
      required: el.dataset.required === 'true',
      profiles: (el.dataset.auditProfile || '').split(',').filter(p => p),
      aiPrompt: el.dataset.aiPrompt
    }));
};

window.enesaGetProgressStats = function() {
  const all = document.querySelectorAll('[data-phase]');
  const stats = {
    total: all.length,
    by_phase: { client: { total: 0, filled: 0 }, agent: { total: 0, filled: 0 }, consultant: { total: 0, filled: 0 } },
    by_source: { '': 0, client: 0, agent: 0, consultant: 0, measured: 0 },
    by_profile: { eed: 0, 'white-cert': 0, iso50001: 0, 'full-map': 0 },
    iso50001_required_filled: 0,
    iso50001_required_total: 0,
    eed_required_filled: 0,
    eed_required_total: 0
  };
  all.forEach(el => {
    const phase = el.dataset.phase;
    const filled = !!el.value;
    if (stats.by_phase[phase]) {
      stats.by_phase[phase].total++;
      if (filled) stats.by_phase[phase].filled++;
    }
    const src = el.dataset.source || '';
    if (stats.by_source.hasOwnProperty(src)) stats.by_source[src]++;
    
    const profiles = (el.dataset.auditProfile || '').split(',').filter(p => p);
    profiles.forEach(p => { if (stats.by_profile[p] !== undefined) stats.by_profile[p]++; });
    
    if (el.dataset.required === 'true' && el.dataset.iso) {
      stats.iso50001_required_total++;
      if (filled) stats.iso50001_required_filled++;
    }
    if (el.dataset.required === 'true' && el.dataset.eed) {
      stats.eed_required_total++;
      if (filled) stats.eed_required_filled++;
    }
  });
  return stats;
};

window.enesaShowConfidence = function(show) {
  if (show) document.body.classList.add('show-confidence');
  else document.body.classList.remove('show-confidence');
};

window.enesaShowPhases = function(show) {
  if (show) document.body.classList.add('show-phases');
  else document.body.classList.remove('show-phases');
};

// Restore profile na starcie
document.addEventListener('DOMContentLoaded', function() {
  setTimeout(function() {
    let saved = null;
    try { saved = localStorage.getItem(PROFILE_STATE_KEY); } catch(e) {}
    if (saved && AUDIT_PROFILES[saved] && AUDIT_PROFILES[saved].enabled) {
      enesaSetAuditProfile(saved);
    }
    
    // Update stats co 2s
    setInterval(enesaUpdateProfileStats, 2000);
  }, 500);
});

console.log('[ENESA Meta+Profile] Layer ready. Helpers:');
console.log('  enesaSetAuditProfile("eed"|"iso50001")  — wybierz profil');
console.log('  enesaClearAuditProfile()                — wyczyść profil');
console.log('  enesaGetProgressStats()                 — statystyki');
console.log('  enesaGetFieldsByPhase("client")         — pola fazy');
console.log('  enesaShowConfidence(true)               — pokaż 🟢🟡🔴');
console.log('  enesaShowPhases(true)                   — pokaż kolorowe ramki');



// ============================================================
// === Helper dla dynamicznie tworzonych pól ==================
// Patchuje funkcje createElement i pozwala stosować meta na lotnie
// ============================================================

// Tabela reguł dla dynamicznych pól (uproszczona — tylko ZUZ i MAC)
const ENESA_DYNAMIC_RULES = [
  {
    test: function(id) { return /^ZUZ-M\d+-(EE|GAZ|CIEPLO|OLEJ|LPG|LNG|PARA|BIO|PV)$/.test(id); },
    meta: {
      phase: 'client', iso: '6.3.a', iso50002: '50002-1:6.4', eed: 'zal.II.2',
      required: 'true',
      profiles: 'eed,white-cert,iso50001,full-map',
      aiPrompt: 'Zużycie nośnika w danym miesiącu — z faktur. Bilans 12-36 mies. daje Energy Baseline (EnB).'
    }
  },
  {
    test: function(id) { return /^ZUZ-M\d+-ROK$/.test(id); },
    meta: {
      phase: 'client', iso: '6.3.a', iso50002: '50002-1:6.4', eed: 'zal.II.2',
      required: 'true',
      profiles: 'eed,white-cert,iso50001,full-map',
      aiPrompt: 'Rok dla danego miesiąca bilansu (np. 2022, 2023, 2024).'
    }
  },
  {
    test: function(id) { return /^MAC-H\d+-W\d+$/.test(id); },
    meta: {
      phase: 'client', iso: '6.3.d', iso50002: '50002-1:6.4', eed: 'zal.II.3',
      required: 'false',
      profiles: 'iso50001,full-map',
      aiPrompt: 'Macierz przypisania hala-wydział: który wydział działa w której hali.'
    }
  }
];

// Funkcja stosująca meta na pojedynczy input
window.enesaApplyMetaToInput = function(input, fieldId) {
  if (!input || !fieldId) return;
  fieldId = fieldId || (input.dataset && input.dataset.id);
  if (!fieldId) return;
  
  // Już ma meta?
  if (input.dataset.phase) return;
  
  // Szukaj reguły
  for (const rule of ENESA_DYNAMIC_RULES) {
    if (rule.test(fieldId)) {
      const m = rule.meta;
      input.dataset.source = '';
      input.dataset.confidence = '';
      input.dataset.phase = m.phase;
      input.dataset.iso = m.iso;
      input.dataset.iso50002 = m.iso50002;
      input.dataset.eed = m.eed;
      input.dataset.required = m.required;
      input.dataset.auditProfile = m.profiles;
      input.dataset.aiPrompt = m.aiPrompt;
      
      // Jeśli jest aktywny profil — pokoloruj
      const activeProfile = document.body.dataset.activeProfile;
      if (activeProfile) {
        const profiles = m.profiles.split(',').filter(p => p);
        if (profiles.includes(activeProfile)) {
          input.classList.add('field-must');
        } else if (profiles.length > 0) {
          input.classList.add('field-optional');
        }
      }
      return;
    }
  }
  
  // Default fallback
  input.dataset.source = '';
  input.dataset.confidence = '';
  input.dataset.phase = 'client';
  input.dataset.iso = '';
  input.dataset.iso50002 = '50002-1:6.4';
  input.dataset.eed = '';
  input.dataset.required = 'false';
  input.dataset.auditProfile = 'full-map';
  input.dataset.aiPrompt = 'Wypełnij na podstawie własnej wiedzy.';
};

// MutationObserver: gdy w DOM pojawią się nowe inputy z data-id — automatycznie stosuj meta
(function() {
  const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mut) {
      mut.addedNodes.forEach(function(node) {
        if (node.nodeType === 1) {  // Element
          // Sam node?
          if (node.dataset && node.dataset.id && !node.dataset.phase) {
            enesaApplyMetaToInput(node, node.dataset.id);
          }
          // Dzieci
          if (node.querySelectorAll) {
            node.querySelectorAll('[data-id]').forEach(function(el) {
              if (!el.dataset.phase) {
                enesaApplyMetaToInput(el, el.dataset.id);
              }
            });
          }
        }
      });
    });
  });
  
  // Start gdy DOM gotowy
  if (document.body) {
    observer.observe(document.body, { childList: true, subtree: true });
  } else {
    document.addEventListener('DOMContentLoaded', function() {
      observer.observe(document.body, { childList: true, subtree: true });
    });
  }
  
  // Także po DOMContentLoaded — przejdź przez istniejące pola
  document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
      document.querySelectorAll('[data-id]').forEach(function(el) {
        if (!el.dataset.phase && el.dataset.id) {
          enesaApplyMetaToInput(el, el.dataset.id);
        }
      });
    }, 1000);
    
    // I po 3 sekundach (na wypadek jak buildZuzyciaTable jest opóźnione)
    setTimeout(function() {
      let added = 0;
      document.querySelectorAll('[data-id]').forEach(function(el) {
        if (!el.dataset.phase && el.dataset.id) {
          enesaApplyMetaToInput(el, el.dataset.id);
          added++;
        }
      });
      if (added > 0) console.log('[ENESA Meta] Late-applied meta to ' + added + ' dynamic fields');
    }, 3000);
  });
})();

console.log('[ENESA Dynamic Meta] MutationObserver active');



// ============================================================
// === Walidator zgodności ISO 50001 / EED / ISO 50002 ========
// ============================================================

window.enesaValidateCompliance = function(profile) {
  profile = profile || document.body.dataset.activeProfile || 'iso50001';
  
  const allFields = document.querySelectorAll('[data-audit-profile]');
  const stats = {
    profile: profile,
    total_must: 0,
    filled_must: 0,
    iso50001: { total: 0, filled: 0, missing: [] },
    eed: { total: 0, filled: 0, missing: [] },
    iso50002: { total: 0, filled: 0, missing: [] }
  };
  
  allFields.forEach(function(el) {
    const profiles = (el.dataset.auditProfile || '').split(',').filter(function(p) { return p; });
    if (!profiles.includes(profile)) return;
    
    stats.total_must++;
    const filled = !!el.value;
    if (filled) stats.filled_must++;
    
    if (el.dataset.iso) {
      stats.iso50001.total++;
      if (filled) stats.iso50001.filled++;
      else stats.iso50001.missing.push({ id: el.dataset.id, klauzula: el.dataset.iso });
    }
    if (el.dataset.eed) {
      stats.eed.total++;
      if (filled) stats.eed.filled++;
      else stats.eed.missing.push({ id: el.dataset.id, klauzula: el.dataset.eed });
    }
    if (el.dataset.iso50002) {
      stats.iso50002.total++;
      if (filled) stats.iso50002.filled++;
      else stats.iso50002.missing.push({ id: el.dataset.id, klauzula: el.dataset.iso50002 });
    }
  });
  
  // Zaokrąglenia
  stats.percent_must = stats.total_must > 0 ? Math.round(stats.filled_must / stats.total_must * 100) : 0;
  stats.iso50001.percent = stats.iso50001.total > 0 ? Math.round(stats.iso50001.filled / stats.iso50001.total * 100) : 0;
  stats.eed.percent = stats.eed.total > 0 ? Math.round(stats.eed.filled / stats.eed.total * 100) : 0;
  stats.iso50002.percent = stats.iso50002.total > 0 ? Math.round(stats.iso50002.filled / stats.iso50002.total * 100) : 0;
  
  return stats;
};

window.enesaShowComplianceReport = function(profile) {
  const stats = enesaValidateCompliance(profile);
  
  let html = '<div class="compliance-report">';
  html += '<h3>📋 Raport zgodności — Profil: <strong>' + (AUDIT_PROFILES[stats.profile] ? AUDIT_PROFILES[stats.profile].name : stats.profile) + '</strong></h3>';
  
  // Łącznie
  const overallColor = stats.percent_must >= 90 ? '#4a8a5e' : (stats.percent_must >= 50 ? '#c8a951' : '#c87a5e');
  html += '<div class="comp-summary" style="border-left: 4px solid ' + overallColor + '; padding: 12px; background: rgba(245,239,224,0.5); margin: 12px 0;">';
  html += '<strong>Łącznie pól MUST:</strong> ' + stats.filled_must + ' / ' + stats.total_must + ' (' + stats.percent_must + '%)';
  if (stats.percent_must >= 90) {
    html += ' <span style="color: #4a8a5e;">✓ Audyt formalnie kompletny</span>';
  } else if (stats.percent_must >= 50) {
    html += ' <span style="color: #c8a951;">⚠ Wymaga uzupełnień</span>';
  } else {
    html += ' <span style="color: #c87a5e;">✗ Audyt formalnie niekompletny</span>';
  }
  html += '</div>';
  
  // Per norma
  html += '<table style="width: 100%; border-collapse: collapse; margin: 12px 0; font-size: 13px;">';
  html += '<thead><tr style="background: var(--paper-deep);">';
  html += '<th style="padding: 8px; text-align: left; border-bottom: 2px solid var(--gold);">Norma</th>';
  html += '<th style="padding: 8px; text-align: center; border-bottom: 2px solid var(--gold);">Wypełnione</th>';
  html += '<th style="padding: 8px; text-align: center; border-bottom: 2px solid var(--gold);">%</th>';
  html += '<th style="padding: 8px; text-align: left; border-bottom: 2px solid var(--gold);">Status</th>';
  html += '</tr></thead><tbody>';
  
  function row(name, st) {
    const pct = st.percent;
    const status = pct >= 90 ? '<span style="color:#4a8a5e">✓ Zgodne</span>' : 
                   (pct >= 50 ? '<span style="color:#c8a951">⚠ Częściowe</span>' : 
                    '<span style="color:#c87a5e">✗ Brakuje</span>');
    return '<tr>' +
      '<td style="padding:8px;"><strong>' + name + '</strong></td>' +
      '<td style="padding:8px; text-align:center;">' + st.filled + ' / ' + st.total + '</td>' +
      '<td style="padding:8px; text-align:center;">' + pct + '%</td>' +
      '<td style="padding:8px;">' + status + '</td></tr>';
  }
  
  html += row('ISO 50001:2018 § 6.3', stats.iso50001);
  html += row('Polska Ustawa o Efekt. Energet.', stats.eed);
  html += row('ISO 50002:2025', stats.iso50002);
  html += '</tbody></table>';
  
  // Lista brakujących pól (top 20)
  const missing50001 = stats.iso50001.missing.slice(0, 20);
  if (missing50001.length > 0) {
    html += '<details style="margin: 12px 0;"><summary style="cursor:pointer; font-weight:600;">Brakujące pola ISO 50001 (' + stats.iso50001.missing.length + ' total)</summary>';
    html += '<ul style="font-size: 11px; margin: 8px 0; max-height: 200px; overflow-y: auto;">';
    missing50001.forEach(function(m) {
      html += '<li><code style="background: var(--paper-deep); padding: 1px 4px;">' + m.id + '</code> — <em>§ ' + m.klauzula + '</em></li>';
    });
    if (stats.iso50001.missing.length > 20) {
      html += '<li>... i ' + (stats.iso50001.missing.length - 20) + ' więcej</li>';
    }
    html += '</ul></details>';
  }
  
  const missingEED = stats.eed.missing.slice(0, 20);
  if (missingEED.length > 0) {
    html += '<details style="margin: 12px 0;"><summary style="cursor:pointer; font-weight:600;">Brakujące pola EED (' + stats.eed.missing.length + ' total)</summary>';
    html += '<ul style="font-size: 11px; margin: 8px 0; max-height: 200px; overflow-y: auto;">';
    missingEED.forEach(function(m) {
      html += '<li><code style="background: var(--paper-deep); padding: 1px 4px;">' + m.id + '</code> — <em>' + m.klauzula + '</em></li>';
    });
    if (stats.eed.missing.length > 20) {
      html += '<li>... i ' + (stats.eed.missing.length - 20) + ' więcej</li>';
    }
    html += '</ul></details>';
  }
  
  html += '</div>';
  
  // Pokaż w modal lub przekaż do containera
  let container = document.getElementById('compliance-report-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'compliance-report-container';
    container.style.cssText = 'position:fixed; top:50px; right:20px; width:500px; max-height:80vh; overflow-y:auto; background:white; border:2px solid var(--gold); border-radius:8px; padding:16px; box-shadow:0 4px 20px rgba(0,0,0,0.2); z-index:9999;';
    container.innerHTML = '<button onclick="document.getElementById(\'compliance-report-container\').remove()" style="position:absolute; top:8px; right:8px; background:transparent; border:none; cursor:pointer; font-size:20px;">×</button>';
    document.body.appendChild(container);
  }
  container.innerHTML = '<button onclick="document.getElementById(\'compliance-report-container\').remove()" style="position:absolute; top:8px; right:8px; background:transparent; border:none; cursor:pointer; font-size:20px;">×</button>' + html;
  
  return stats;
};

console.log('[ENESA Validator] Ready. Use: enesaValidateCompliance("eed"|"iso50001"), enesaShowComplianceReport()');


// ============================================================
// === ARCHITEKTURA C v1.6 — funkcje dynamiczne ===============
// ============================================================

/**
 * Renderuje karty wyboru typu audytu na podstawie AUDIT_PROFILES.
 * Wywoływana przy starcie strony.
 */
function renderAuditCards() {
  const container = document.getElementById('audit-cards-container');
  if (!container) return;
  
  const currentProfile = enesaStorage.get('enesa_audit_profile');
  let html = '';
  
  // Kolejność kart — od najtańszych do najdroższych
  const order = ['eed', 'white-cert', 'iso50001', 'full-map', 'custom'];
  
  order.forEach(function(profileKey) {
    const profile = AUDIT_PROFILES[profileKey];
    if (!profile) return;
    
    const isSelected = currentProfile === profileKey;
    const isDisabled = !profile.enabled;
    const cssClasses = ['audit-card'];
    if (isSelected) cssClasses.push('selected');
    if (isDisabled) cssClasses.push('disabled');
    
    // Liczymy realne pola dla tego profilu (z całego DOMu)
    const fieldsCount = countFieldsForProfile(profileKey);
    
    // Badge dla rekomendowanych
    let badge = '';
    if (profileKey === 'eed') badge = '<div class="audit-card-badge">Obowiązkowy</div>';
    else if (profileKey === 'iso50001') badge = '<div class="audit-card-badge">Rekomendowany</div>';
    
    html += '<div class="' + cssClasses.join(' ') + '" data-profile="' + profileKey + '" onclick="enesaSelectAuditFromCard(\'' + profileKey + '\')">';
    html += badge;
    html += '<div class="audit-card-name">' + profile.name + '</div>';
    html += '<div class="audit-card-desc">' + profile.description + '</div>';
    html += '<div class="audit-card-stats">';
    html += '<div class="audit-card-stat"><span class="audit-card-stat-label">Pól</span><span class="audit-card-stat-value field-count">' + (fieldsCount > 0 ? fieldsCount : profile.fields_estimate) + '</span></div>';
    html += '<div class="audit-card-stat"><span class="audit-card-stat-label">Czas</span><span class="audit-card-stat-value">' + profile.duration_estimate + '</span></div>';
    html += '<div class="audit-card-stat"><span class="audit-card-stat-label">Cena</span><span class="audit-card-stat-value">' + profile.price_range + '</span></div>';
    html += '<div class="audit-card-stat"><span class="audit-card-stat-label">Status</span><span class="audit-card-stat-value">' + (isDisabled ? 'Wkrótce' : 'Dostępny') + '</span></div>';
    html += '</div>';
    html += '<button type="button" class="audit-card-button">' + (isSelected ? 'Wybrano' : (isDisabled ? 'Niedostępny' : 'Wybierz')) + '</button>';
    html += '</div>';
  });
  
  container.innerHTML = html;
}

/**
 * Wybór profilu z karty — wywołuje istniejącą enesaSetAuditProfile i odświeża karty.
 */
function enesaSelectAuditFromCard(profileKey) {
  const profile = AUDIT_PROFILES[profileKey];
  if (!profile || !profile.enabled) return;
  
  // Wywołaj istniejącą funkcję
  enesaSetAuditProfile(profileKey);
  
  // Odśwież karty (oznaczenie wybranego)
  renderAuditCards();
  
  // Pokaż info dla użytkownika
  setTimeout(function() {
    updateProgressInfo();
    applyProfileSectionVisibility(profileKey);
  }, 100);
}

/**
 * Liczy liczbę pól w DOM dla danego profilu.
 */
function countFieldsForProfile(profileKey) {
  const allFields = document.querySelectorAll('[data-audit-profile]');
  let count = 0;
  allFields.forEach(function(el) {
    const profiles = (el.dataset.auditProfile || '').split(',').filter(function(p) { return p.trim(); });
    if (profiles.includes(profileKey)) count++;
  });
  return count;
}

/**
 * Aplikuje widoczność sekcji (etap-N) — sekcje BEZ pól dla profilu są chowane.
 */
function applyProfileSectionVisibility(profileKey) {
  // Tryb audytora — nie chowamy nic
  if (document.body.classList.contains('mode-enesa')) {
    document.querySelectorAll('section.section').forEach(function(s) {
      s.classList.remove('section-hidden-by-profile');
    });
    document.querySelectorAll('nav .nav-link, nav a').forEach(function(a) {
      a.classList.remove('nav-hidden-by-profile');
    });
    return;
  }
  
  // Bez aktywnego profilu — nie chowamy
  if (!profileKey) {
    document.querySelectorAll('section.section').forEach(function(s) {
      s.classList.remove('section-hidden-by-profile');
    });
    return;
  }
  
  // Dla każdej sekcji etap-N sprawdź czy ma jakiekolwiek pole dla tego profilu
  document.querySelectorAll('section.section[id^="etap-"]').forEach(function(section) {
    const sectionId = section.id;  // np. "etap-5"
    
    // Sekcja E0 (audit-type-selector) zawsze widoczna
    if (sectionId === 'etap-0') {
      section.classList.remove('section-hidden-by-profile');
      return;
    }
    
    const fieldsInSection = section.querySelectorAll('[data-audit-profile]');
    let hasMatchingField = false;
    fieldsInSection.forEach(function(f) {
      const profs = (f.dataset.auditProfile || '').split(',').filter(function(p) { return p.trim(); });
      if (profs.includes(profileKey)) hasMatchingField = true;
    });
    
    if (hasMatchingField) {
      section.classList.remove('section-hidden-by-profile');
    } else {
      section.classList.add('section-hidden-by-profile');
    }
    
    // Aktualizuj odpowiedni link w menu bocznym
    const navLinks = document.querySelectorAll('nav a[href="#' + sectionId + '"], nav .nav-link[data-target="' + sectionId + '"]');
    navLinks.forEach(function(link) {
      if (hasMatchingField) {
        link.classList.remove('nav-hidden-by-profile');
      } else {
        link.classList.add('nav-hidden-by-profile');
      }
    });
  });
}

/**
 * Przełącza widoczność tabeli porównawczej profili.
 */
function enesaToggleComparisonTable() {
  const table = document.getElementById('audit-comparison-table');
  if (!table) return;
  
  if (table.classList.contains('visible')) {
    table.classList.remove('visible');
    return;
  }
  
  // Wygeneruj zawartość
  let html = '<table><thead><tr><th>Cecha</th>';
  const profileKeys = Object.keys(AUDIT_PROFILES);
  profileKeys.forEach(function(k) {
    html += '<th>' + AUDIT_PROFILES[k].name + '</th>';
  });
  html += '</tr></thead><tbody>';
  
  // Wiersz: liczba pól
  html += '<tr><td>Liczba pól (orientacyjnie)</td>';
  profileKeys.forEach(function(k) {
    const real = countFieldsForProfile(k);
    html += '<td>' + (real > 0 ? real : AUDIT_PROFILES[k].fields_estimate) + '</td>';
  });
  html += '</tr>';
  
  // Wiersz: czas
  html += '<tr><td>Czas trwania audytu</td>';
  profileKeys.forEach(function(k) {
    html += '<td>' + AUDIT_PROFILES[k].duration_estimate + '</td>';
  });
  html += '</tr>';
  
  // Wiersz: cena
  html += '<tr><td>Orientacyjna cena</td>';
  profileKeys.forEach(function(k) {
    html += '<td>' + AUDIT_PROFILES[k].price_range + '</td>';
  });
  html += '</tr>';
  
  // Wiersz: dostępność
  html += '<tr><td>Dostępność</td>';
  profileKeys.forEach(function(k) {
    html += '<td class="' + (AUDIT_PROFILES[k].enabled ? 'check' : 'cross') + '">' + 
            (AUDIT_PROFILES[k].enabled ? '✓ Dostępny' : '— Wkrótce') + '</td>';
  });
  html += '</tr>';
  
  // Wiersz: opis
  html += '<tr><td>Opis</td>';
  profileKeys.forEach(function(k) {
    html += '<td style="font-size:11px">' + AUDIT_PROFILES[k].description + '</td>';
  });
  html += '</tr>';
  
  html += '</tbody></table>';
  
  table.innerHTML = html;
  table.classList.add('visible');
}

/**
 * Przełącza tryb "pokaż wszystko" — odsłania sekcje spoza profilu.
 */
function enesaShowAllSections() {
  const body = document.body;
  if (body.classList.contains('show-all-sections')) {
    body.classList.remove('show-all-sections');
    const toggle = document.querySelector('.show-all-toggle');
    if (toggle) toggle.classList.remove('active');
  } else {
    body.classList.add('show-all-sections');
    const toggle = document.querySelector('.show-all-toggle');
    if (toggle) toggle.classList.add('active');
  }
}

/**
 * Aktualizuje progress info w menu bocznym.
 */
function updateProgressInfo() {
  const container = document.getElementById('audit-progress-info');
  if (!container) return;
  
  const profile = enesaStorage.get('enesa_audit_profile');
  if (!profile) {
    container.innerHTML = '<div style="font-size:11px;color:var(--ink-mute)">Wybierz typ audytu aby zobaczyć postęp</div>';
    return;
  }
  
  const profileName = AUDIT_PROFILES[profile] ? AUDIT_PROFILES[profile].name : profile;
  const totalFields = countFieldsForProfile(profile);
  
  // Liczymy wypełnione pola dla tego profilu
  let filledFields = 0;
  document.querySelectorAll('[data-audit-profile]').forEach(function(el) {
    const profs = (el.dataset.auditProfile || '').split(',').filter(function(p) { return p.trim(); });
    if (profs.includes(profile) && el.value && el.value.toString().trim()) {
      filledFields++;
    }
  });
  
  const pct = totalFields > 0 ? Math.round(filledFields / totalFields * 100) : 0;
  
  container.innerHTML = 
    '<div style="font-size:10px;color:var(--ink-mute);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px">Postęp · ' + profileName + '</div>' +
    '<div class="progress-line">' + filledFields + ' / ' + totalFields + ' pól (' + pct + '%)</div>' +
    '<div class="progress-bar-bg"><div class="progress-bar-fill" style="width:' + pct + '%"></div></div>' +
    '<div style="font-size:10px;color:var(--ink-mute)">Aktualizuje się w trakcie wypełniania</div>';
}

/**
 * Inicjalizacja architektury C — wywoływana po DOMContentLoaded.
 */
function initArchitectureC() {
  // 1. Sprawdź URL — czy tryb audytora?
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('mode') === 'enesa') {
    document.body.classList.add('mode-enesa');
    console.log('[ENESA Arch C] Tryb audytora WŁĄCZONY');
  }
  
  // 2. Renderuj karty wyboru audytu
  renderAuditCards();
  
  // 3. Aplikuj widoczność sekcji dla aktualnie wybranego profilu
  const currentProfile = enesaStorage.get('enesa_audit_profile');
  if (currentProfile) {
    applyProfileSectionVisibility(currentProfile);
  }
  
  // 4. Wstaw progress info w menu bocznym (przed pierwszym linkiem)
  const nav = document.querySelector('nav');
  if (nav && !document.getElementById('audit-progress-info')) {
    const progressDiv = document.createElement('div');
    progressDiv.id = 'audit-progress-info';
    progressDiv.className = 'audit-progress-info';
    nav.insertBefore(progressDiv, nav.firstChild);
    updateProgressInfo();
  }
  
  // 5. Dodaj przycisk "Pokaż wszystko" w menu bocznym
  if (nav && !document.querySelector('.show-all-toggle')) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'show-all-toggle';
    btn.textContent = '👁️ Pokaż wszystkie sekcje';
    btn.onclick = enesaShowAllSections;
    nav.insertBefore(btn, nav.firstChild);
  }
  
  // 6. Aktualizuj progress przy każdej zmianie pola
  document.body.addEventListener('input', function(e) {
    if (e.target.dataset && e.target.dataset.auditProfile) {
      // Debounce
      clearTimeout(window._progressUpdateTimer);
      window._progressUpdateTimer = setTimeout(updateProgressInfo, 500);
    }
  });
  
  console.log('[ENESA Arch C] Inicjalizacja zakończona');
}

// Uruchom po załadowaniu DOMa
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initArchitectureC);
} else {
  setTimeout(initArchitectureC, 100);
}

// Eksport
window.enesaSelectAuditFromCard = enesaSelectAuditFromCard;
window.enesaToggleComparisonTable = enesaToggleComparisonTable;
window.enesaShowAllSections = enesaShowAllSections;
window.renderAuditCards = renderAuditCards;
window.applyProfileSectionVisibility = applyProfileSectionVisibility;
window.updateProgressInfo = updateProgressInfo;
window.countFieldsForProfile = countFieldsForProfile;

console.log('[ENESA Arch C v1.6] Załadowano: 7 nowych funkcji + tryb audytora + dynamiczne ukrywanie');



// Wrapper dla enesaSetAuditProfile aby automatycznie aplikował widoczność sekcji
(function() {
  if (typeof window.enesaSetAuditProfile === 'function' && !window._enesaSetAuditProfileWrapped) {
    const original = window.enesaSetAuditProfile;
    window.enesaSetAuditProfile = function(profileKey) {
      original.call(this, profileKey);
      setTimeout(function() {
        if (typeof applyProfileSectionVisibility === 'function') {
          applyProfileSectionVisibility(profileKey);
        }
        if (typeof updateProgressInfo === 'function') {
          updateProgressInfo();
        }
        if (typeof renderAuditCards === 'function') {
          renderAuditCards();
        }
      }, 50);
    };
    window._enesaSetAuditProfileWrapped = true;
    console.log('[ENESA Arch C] enesaSetAuditProfile wrapped');
  }
  if (typeof window.enesaClearAuditProfile === 'function' && !window._enesaClearWrapped) {
    const orig = window.enesaClearAuditProfile;
    window.enesaClearAuditProfile = function() {
      orig.call(this);
      setTimeout(function() {
        document.querySelectorAll('section.section').forEach(function(s) {
          s.classList.remove('section-hidden-by-profile');
        });
        document.querySelectorAll('.nav-hidden-by-profile').forEach(function(n) {
          n.classList.remove('nav-hidden-by-profile');
        });
        if (typeof renderAuditCards === 'function') renderAuditCards();
        if (typeof updateProgressInfo === 'function') updateProgressInfo();
      }, 50);
    };
    window._enesaClearWrapped = true;
  }
})();



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

// 2. Załaduj FORM_DATA do pól DOM (loadSavedData uruchomił się przed override)
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
      showSaveIndicator('✓ Zapisano');
    }).catch(err => {
      showSaveIndicator('Błąd zapisu!');
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
  setIfEmpty('AUD-V1-NAZWA',         COMPANY_DATA.name);
  setIfEmpty('AUD-V2-NIP',           COMPANY_DATA.nip);
  setIfEmpty('AUD-V3-REGON',         COMPANY_DATA.regon);
  // Adres siedziby — dwa osobne pola
  setIfEmpty('AUD-V4-ULICA',         COMPANY_DATA.street);
  const cityFull = [COMPANY_DATA.postalCode, COMPANY_DATA.city].filter(Boolean).join(' ');
  setIfEmpty('AUD-V4-MIASTO',        cityFull);
  // Domyślna lokalizacja audytowana = miejscowość siedziby
  setIfEmpty('ZAK-V2-LOK-ADRES',     COMPANY_DATA.city || cityFull);
  // Audytor — zawsze z systemu, nadpisuje stare wartości z FORM_DATA
  _forceSetField('AUD-V10-AUDYTOR',      COMPANY_DATA.auditorName  || '');
  _forceSetField('AUD-V11-AUDYTOR-MAIL', COMPANY_DATA.auditorEmail || '');
  _forceSetField('AUD-V12-AUDYTOR-TEL',  COMPANY_DATA.auditorPhone || '');
  // Zleceniodawca — domyślnie = firma klienta (użytkownik może zmienić)
  setIfEmpty('AUD-V8-ZLEC', COMPANY_DATA.name);
  // Kontakt zleceniodawcy — z danych rejestracyjnych firmy (pierwszy kontakt)
  setIfEmpty('AUD-V9-ZLEC-IMIE',  COMPANY_DATA.contactName);
  setIfEmpty('AUD-V9-ZLEC-MAIL',  COMPANY_DATA.contactEmail);
  setIfEmpty('AUD-V9-ZLEC-TEL',   COMPANY_DATA.contactPhone);
  // Numer umowy — z oferty powiązanej z audytem (zawsze aktualizuj gdy jest wartość)
  if (COMPANY_DATA.offerNumber) _forceSetField('AUD-V19-UMOWA', COMPANY_DATA.offerNumber);
}

// Force-fill helpers (przyciski "Uzupełnij automatycznie" — nadpisują istniejące wartości)
function _forceSetField(id, v) {
  const el = document.querySelector('[data-id="' + id + '"]');
  if (el) { el.value = v ?? ''; el.dispatchEvent(new Event('input', {bubbles: true})); }
}
function fillZleceniodawca() {
  if (!COMPANY_DATA) return;
  _forceSetField('AUD-V8-ZLEC',      COMPANY_DATA.name);
  _forceSetField('AUD-V9-ZLEC-IMIE', COMPANY_DATA.contactName);
  _forceSetField('AUD-V9-ZLEC-MAIL', COMPANY_DATA.contactEmail);
  _forceSetField('AUD-V9-ZLEC-TEL',  COMPANY_DATA.contactPhone);
}
function fillAudytor() {
  if (!COMPANY_DATA) return;
  _forceSetField('AUD-V10-AUDYTOR',      COMPANY_DATA.auditorName  || '');
  _forceSetField('AUD-V11-AUDYTOR-MAIL', COMPANY_DATA.auditorEmail || '');
  _forceSetField('AUD-V12-AUDYTOR-TEL',  COMPANY_DATA.auditorPhone || '');
  const btn = document.querySelector('button[onclick="fillAudytor()"]');
  if (!COMPANY_DATA.auditorName && !COMPANY_DATA.auditorEmail && btn) {
    const orig = btn.innerHTML;
    btn.textContent = '\u26a0 Brak audytora \u2014 przypisz w dashboardzie firmy';
    btn.style.cssText += ';background:#fef3c7;color:#92400e;border-color:#fbbf24;';
    setTimeout(() => { btn.innerHTML = orig; btn.style.background = ''; btn.style.color = ''; btn.style.borderColor = ''; }, 4000);
  }
}
function showAuditorPicker() { fillAudytor(); }

// 7. Klimat — auto-uzupełnienie
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
  if (statusEl) { statusEl.innerHTML = 'Szukam warunków klimatycznych dla ' + q + '...'; statusEl.style.display='block'; }
  try {
    const url = 'https://nominatim.openstreetmap.org/search?q='+encodeURIComponent(q)+'&countrycodes=pl&addressdetails=1&format=json&limit=5';
    const resp = await fetch(url, {headers:{'Accept-Language':'pl'}});
    const results = await resp.json();
    if (!results||!results.length) { if(statusEl) statusEl.innerHTML='Nie znaleziono lokalizacji — wpisz ręcznie.'; return; }
    const place = results.find(r=>['city','town','village','hamlet','suburb','municipality'].includes(r.type)||['city','town','village','hamlet'].includes(r.addresstype))||results[0];
    const addr = place.address||{};
    const cityName = addr.city||addr.town||addr.village||addr.hamlet||addr.suburb||place.display_name.split(',')[0];
    masterLocSelectPlace(place.lat, place.lon, cityName, addr.state||'', place.display_name);
  } catch(e) { if(statusEl){statusEl.innerHTML='Błąd połączenia z geolokalizacją.';statusEl.style.display='block';} }
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
  }catch(e){box.innerHTML='<div style="padding:9px 14px;">Błąd połączenia.</div>';}
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
  if(statusEl){statusEl.innerHTML='Klimat uzupelniony: stacja '+station.name+' ('+dist+' km) Â· Strefa '+station.zone+' Â· HDD='+station.hdd+' Â· CDD='+station.cdd;statusEl.style.display='block';}
}
document.addEventListener('click',function(e){
  const box=document.getElementById('master-loc-suggestions');
  const inp=document.getElementById('master-loc-adres-input');
  if(box&&!box.contains(e.target)&&e.target!==inp)box.style.display='none';
});

// 8a. GUS / Biala Lista MF - auto-uzupelnienie REGON, PKD, adresu
async function fetchGUSData() {
  const nipEl = document.querySelector('[data-id="AUD-V2-NIP"]');
  if (!nipEl || !nipEl.value.trim()) { showGUSStatus('⚠ Wpisz NIP firmy', 'error'); return; }
  const nip = nipEl.value.replace(/[^0-9]/g, '');
  if (nip.length !== 10) { showGUSStatus('⚠ NIP musi mieć 10 cyfr', 'error'); return; }
  showGUSStatus('⏳ Pobieranie danych z Białej Listy MF...', 'loading');
  const today = new Date().toISOString().slice(0, 10);
  try {
    const resp = await fetch('https://wl-api.mf.gov.pl/api/search/nip/' + nip + '?date=' + today, { headers: { 'Accept': 'application/json' } });
    if (!resp.ok) { showGUSStatus('⚠ Nie znaleziono NIP ' + nip + ' w Białej Liście MF', 'error'); return; }
    const data = await resp.json();
    const s = data.result && data.result.subject;
    if (!s) { showGUSStatus('⚠ Brak danych dla NIP ' + nip, 'error'); return; }
    const filled = [];
    if (s.regon) {
      const el = document.querySelector('[data-id="AUD-V3-REGON"]');
      if (el) { el.value = s.regon; el.dispatchEvent(new Event('input', {bubbles:true})); filled.push('REGON: ' + s.regon); }
    }
    if (s.residenceAddress || s.workingAddress) {
      const parsed = parsePolishAddress(s.residenceAddress || s.workingAddress);
      if (parsed.street) {
        const el = document.querySelector('[data-id="AUD-V4-ULICA"]');
        if (el && !el.value) { el.value = parsed.street; el.dispatchEvent(new Event('input', {bubbles:true})); filled.push('ulica'); }
      }
      if (parsed.city) {
        const el = document.querySelector('[data-id="AUD-V4-MIASTO"]');
        if (el && !el.value) { el.value = parsed.city; el.dispatchEvent(new Event('input', {bubbles:true})); filled.push('miejscowość'); }
      }
    }
    let statusMsg = 'Pobrano: ' + (filled.length ? filled.join(', ') : 'brak nowych danych');
    if (s.krs) {
      statusMsg += ' \u00b7 Pobieranie PKD z KRS ' + s.krs + '...';
      showGUSStatus(statusMsg, 'loading');
      try {
        const krsNum = String(s.krs).replace(/\D/g,'').padStart(10,'0');
        const kr = await fetch('/api/krs/pkd/' + krsNum, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        if (kr.ok) {
          const kd = await kr.json();
          if (kd.pkd) {
            const el = document.querySelector('[data-id="AUD-V5-PKD"]');
            if (el && !el.value) { el.value = kd.pkd; el.dispatchEvent(new Event('input', {bubbles:true})); filled.push('PKD'); }
            statusMsg = '\u2713 Pobrano: ' + filled.join(', ');
          } else { statusMsg += ' \u00b7 PKD nie znaleziono w KRS'; }
        } else { statusMsg += ' \u00b7 b\u0142\u0105d KRS API (' + kr.status + ')'; }
      } catch { statusMsg += ' \u00b7 PKD: b\u0142\u0105d po\u0142\u0105czenia z KRS'; }
    } else {
      statusMsg += ' \u00b7 PKD: brak nr KRS (sp\u00f3\u0142ki os. / CEIDG)';
    }
    showGUSStatus(statusMsg, 'ok');
  } catch { showGUSStatus('\u26a0 B\u0142\u0105d po\u0142\u0105czenia z API Bia\u0142ej Listy MF', 'error'); }
}
function parsePolishAddress(addr) {
  if (!addr) return { street: '', city: '' };
  const m = addr.match(/^(.*?),\s*(\d{2}-\d{3}[\s\S]*)$/);
  if (m) return { street: m[1].trim(), city: m[2].trim() };
  return { street: addr.trim(), city: '' };
}
function extractMainPKD(krsData) {
  try {
    const odpis = krsData && (krsData.odpis || krsData.OdpisAktualny || krsData);
    const dane = odpis && (odpis.dane || odpis.Dane);
    const d3 = dane && (dane.dzial3 || dane.Dzial3);
    const pred = d3 && (d3.przedmiotDzialalnosci || d3.PrzedmiotDzialalnosci);
    if (!pred) return null;
    // Nowa struktura KRS MS API: przedmiotPrzewazajacejDzialalnosci
    const main = pred.przedmiotPrzewazajacejDzialalnosci || pred.PrzedmiotPrzewazajacejDzialalnosci;
    const item = Array.isArray(main) ? main[0] : main;
    if (item && (item.kodDzial || item.KodDzial)) {
      const dzial = item.kodDzial || item.KodDzial || '';
      const klasa = item.kodKlasa || item.KodKlasa || '';
      const podklasa = item.kodPodklasa || item.KodPodklasa || '';
      const opis = item.opis || item.Opis || '';
      let kod = dzial;
      if (klasa) kod += '.' + klasa;
      if (podklasa) kod += '.' + podklasa;
      return kod + (opis ? ' ' + opis : '');
    }
    // Fallback: stara struktura z pozycja/kodDzialalnosci
    const poz = pred.pozycja || pred.Pozycja;
    if (!poz) return null;
    const items = Array.isArray(poz) ? poz : [poz];
    const fallback = items.find(p => {
      const g = String(p.glownoscDzialalnosci || p.GlownoscDzialalnosci || '').toLowerCase();
      return g === 'true' || g === 't' || g === 'tak' || g === '1';
    }) || items[0];
    if (!fallback) return null;
    const kod = fallback.kodDzialalnosci || fallback.KodDzialalnosci || '';
    const nazwa = fallback.nazwyPkd || fallback.NazwyPkd || '';
    return kod ? kod + (nazwa ? ' ' + nazwa : '') : null;
  } catch { return null; }
}
function showGUSStatus(msg, type) {
  const el = document.getElementById('gus-status');
  if (!el) return;
  el.style.display = 'block';
  el.innerHTML = msg;
  el.style.background = type==='error' ? '#fef2f2' : type==='ok' ? '#eef8f0' : '#fffbeb';
  el.style.borderColor = type==='error' ? '#fca5a5' : type==='ok' ? '#a8ddb8' : '#fcd34d';
  el.style.color = type==='error' ? '#c0392b' : type==='ok' ? '#1a5c3a' : '#78350f';
}

// 8b. DOMContentLoaded - prefill + watcher adresu + klimat + GUS auto-trigger
document.addEventListener('DOMContentLoaded', () => {
  prefillFromCompanyData();
  if (TEAM_MEMBERS && TEAM_MEMBERS.length > 0) {
    TEAM_MEMBERS.forEach((m, i) => {
      const n = i + 1;
      setIfEmpty('AUD-V13-IMIE-U' + n, m.name || '');
      setIfEmpty('AUD-V13-MAIL-U' + n, m.email || '');
      setIfEmpty('AUD-V13-ROLA-U' + n, m.role || '');
    });
  }
  const siedzibaEl = document.querySelector('[data-id="AUD-V4-MIASTO"]');
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
  const nipVal = (document.querySelector('[data-id="AUD-V2-NIP"]') || {}).value || '';
  const regonVal = (document.querySelector('[data-id="AUD-V3-REGON"]') || {}).value || '';
  if (nipVal.replace(/[^0-9]/g,'').length === 10 && !regonVal) {
    setTimeout(fetchGUSData, 600);
  }
  setTimeout(masterLocAutoFillIfNeeded, 200);
});
window.addEventListener('load', function() { setTimeout(masterLocAutoFillIfNeeded, 300); });

// === END LARAVEL BLADE OVERRIDES ===
</script>

<x-client-chat-float :chatMessages="$chatMessages" :companyId="isset($company) && $company ? $company->id : null" />
<x-ai-chat-float contextType="general" :agentNumber="1" />

</x-layouts.app>
