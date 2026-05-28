<x-layouts.app>
@php
$catConfig = [
    'global'            => ['label' => 'Szablony globalne',   'icon' => "\u{1F4C4}", 'color' => '#1e3a5f', 'gradient' => 'linear-gradient(135deg,#1e3a5f,#2d5f8f)', 'badge' => '#dbeafe', 'badgeText' => '#1e3a5f', 'backRoute' => 'offers.index', 'backLabel' => "\u{2190} Oferty"],
    'energetyczny'      => ['label' => 'Audyt Energetyczny',  'icon' => "\u{26A1}",  'color' => '#1A4D3A', 'gradient' => 'linear-gradient(135deg,#1A4D3A,#2d7a5f)', 'badge' => '#d1fae5', 'badgeText' => '#065f46',  'backRoute' => 'audits.index', 'backLabel' => "\u{2190} System Audyt\u{00F3}w"],
    'iso50001'          => ['label' => 'Audyt ISO 50001',      'icon' => "\u{1F3ED}", 'color' => '#1e40af', 'gradient' => 'linear-gradient(135deg,#1e40af,#3b82f6)', 'badge' => '#dbeafe', 'badgeText' => '#1e40af',  'backRoute' => 'audits.index', 'backLabel' => "\u{2190} System Audyt\u{00F3}w"],
    'biale_certyfikaty' => ['label' => "Bia\u{0142}e Certyfikaty", 'icon' => "\u{1F4DC}", 'color' => '#7c3aed', 'gradient' => 'linear-gradient(135deg,#7c3aed,#a855f7)', 'badge' => '#ede9fe', 'badgeText' => '#5b21b6', 'backRoute' => 'audits.index', 'backLabel' => "\u{2190} System Audyt\u{00F3}w"],
];
$cfg = $catConfig[$category] ?? $catConfig['global'];
$isGlobal = ($category === 'global');
@endphp
<style>
.ot-header { background: {{ $cfg['gradient'] }}; border-radius: 16px; padding: 24px 28px; color: #fff; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.ot-header-title { display: flex; align-items: center; gap: 14px; }
.ot-header-icon { width: 52px; height: 52px; border-radius: 14px; background: rgba(255,255,255,.18); display: flex; align-items: center; justify-content: center; font-size: 26px; flex-shrink: 0; }
.ot-header h1 { font-size: 22px; font-weight: 800; margin: 0 0 3px; }
.ot-header p { font-size: 13px; opacity: .75; margin: 0; }
.ot-btn-new { background: rgba(255,255,255,.22); border: 1px solid rgba(255,255,255,.4); color: #fff; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 14px; white-space: nowrap; transition: background .15s; }
.ot-btn-new:hover { background: rgba(255,255,255,.35); }
.ot-btn-back { background: rgba(0,0,0,.15); border: 1px solid rgba(255,255,255,.2); color: rgba(255,255,255,.85); padding: 8px 14px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; white-space: nowrap; }
.ot-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 16px; }
.ot-card { background: #fff; border: 1px solid var(--paper-deep); border-radius: 14px; overflow: hidden; }
.ot-card-top { padding: 18px 20px; }
.ot-card-top h3 { font-size: 17px; font-weight: 700; margin: 0 0 4px; color: #111; }
.ot-card-top .ot-code { font-family: monospace; font-size: 11px; color: var(--ink-mute); }
.ot-stats { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; padding: 0 20px 14px; }
.ot-stat { background: #f8faf8; border-radius: 8px; padding: 9px 10px; text-align: center; }
.ot-stat-label { font-size: 10px; text-transform: uppercase; letter-spacing: .5px; color: var(--ink-mute); margin-bottom: 3px; }
.ot-stat-val { font-size: 14px; font-weight: 700; }
.ot-card-footer { padding: 12px 20px; border-top: 1px solid var(--paper-deep); display: flex; gap: 8px; align-items: center; }
.ot-empty { background: #f8faf8; border: 2px dashed var(--paper-deep); border-radius: 16px; padding: 52px; text-align: center; }
</style>

<div class="panel">
    <div class="ot-header">
        <div class="ot-header-title">
            <div class="ot-header-icon">{{ $cfg['icon'] }}</div>
            <div>
                <h1>Szablony &mdash; {{ $cfg['label'] }}</h1>
                <p>Definiuj HTML oferty, domyslne stawki i godziny audytorow.</p>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route($cfg['backRoute']) }}" class="ot-btn-back">{!! $cfg['backLabel'] !!}</a>
            <a href="{{ route('offer-templates.create', ['category' => $category]) }}" class="ot-btn-new">+ Nowy szablon</a>
        </div>
    </div>

    @if(session('status'))
        <div style="background:{{ $cfg['badge'] }};border-radius:10px;padding:12px 16px;margin-bottom:16px;color:{{ $cfg['badgeText'] }};font-weight:600;font-size:13px;">
            {{ session('status') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fee2e2;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#991b1b;font-weight:600;font-size:13px;">
            {{ session('error') }}
        </div>
    @endif

    @if($templates->isEmpty())
        <div class="ot-empty">
            <div style="font-size:44px;margin-bottom:14px;">{{ $cfg['icon'] }}</div>
            <div style="font-size:16px;font-weight:700;margin-bottom:8px;color:#111;">Brak szablonow dla {{ $cfg['label'] }}</div>
            <div style="font-size:13px;color:var(--ink-mute);margin-bottom:22px;">Utworz pierwszy szablon oferty dla tego rodzaju audytu.</div>
            <a href="{{ route('offer-templates.create', ['category' => $category]) }}"
               style="display:inline-block;background:{{ $cfg['color'] }};color:#fff;padding:10px 24px;border-radius:10px;text-decoration:none;font-weight:700;font-size:14px;">
                + Utworz pierwszy szablon
            </a>
        </div>
    @else
        <div class="ot-grid">
            @foreach($templates as $tpl)
            <div class="ot-card">
                <div class="ot-card-top" style="border-left: 4px solid {{ $cfg['color'] }};">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
                        <div>
                            <h3>{{ $tpl->name }}</h3>
                            <div class="ot-code">{{ $tpl->type_code }}</div>
                        </div>
                        @if($tpl->is_active)
                            <span style="background:{{ $cfg['badge'] }};color:{{ $cfg['badgeText'] }};font-size:11px;padding:3px 10px;border-radius:20px;font-weight:700;white-space:nowrap;">Aktywny</span>
                        @else
                            <span style="background:#f3f4f6;color:#6b7280;font-size:11px;padding:3px 10px;border-radius:20px;">Nieaktywny</span>
                        @endif
                    </div>
                    @if($tpl->description)
                        <p style="margin:10px 0 0;font-size:12.5px;color:var(--ink-mute);line-height:1.5;">{{ Str::limit($tpl->description, 90) }}</p>
                    @endif
                </div>
                <div class="ot-stats">
                    <div class="ot-stat">
                        <div class="ot-stat-label">Stawka km</div>
                        <div class="ot-stat-val" style="color:{{ $cfg['color'] }};">{{ number_format($tpl->default_km_rate, 2, ',', ' ') }} zl</div>
                    </div>
                    <div class="ot-stat">
                        <div class="ot-stat-label">Stawka h</div>
                        <div class="ot-stat-val" style="color:{{ $cfg['color'] }};">{{ number_format($tpl->default_hour_rate, 2, ',', ' ') }} zl</div>
                    </div>
                    <div class="ot-stat">
                        <div class="ot-stat-label">Godziny</div>
                        <div class="ot-stat-val" style="color:{{ $cfg['color'] }};">{{ number_format($tpl->default_auditor_hours, 1, ',', ' ') }} h</div>
                    </div>
                </div>
                <div class="ot-card-footer">
                    <a href="{{ route('offer-templates.edit', $tpl) }}" style="flex:1;text-align:center;padding:8px;border-radius:8px;background:#f3f8f7;color:{{ $cfg['color'] }};text-decoration:none;font-weight:700;font-size:13px;">Edytuj</a>
                    <a href="{{ route('offer-templates.preview', $tpl) }}" target="_blank" style="padding:8px 12px;border-radius:8px;background:#eff6ff;color:#1d4ed8;text-decoration:none;font-weight:600;font-size:13px;">Podgląd</a>
                    @if($isGlobal)
                    <div style="position:relative;display:inline-block;">
                        <button type="button" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='block'?'none':'block'" style="padding:8px 12px;border-radius:8px;background:#1e3a5f;color:#fff;border:0;cursor:pointer;font-weight:700;font-size:13px;">
                            Kopiuj do ▾
                        </button>
                        <div style="display:none;position:absolute;right:0;top:calc(100% + 4px);background:#fff;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.12);min-width:200px;z-index:50;padding:6px;">
                            <form method="POST" action="{{ route('offer-templates.duplicate', $tpl) }}">
                                @csrf
                                <input type="hidden" name="target_category" value="energetyczny">
                                <button type="submit" style="width:100%;text-align:left;padding:9px 14px;background:none;border:0;cursor:pointer;font-size:13px;border-radius:6px;color:#1A4D3A;font-weight:600;" onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='none'">⚡ Audyt Energetyczny</button>
                            </form>
                            <form method="POST" action="{{ route('offer-templates.duplicate', $tpl) }}">
                                @csrf
                                <input type="hidden" name="target_category" value="iso50001">
                                <button type="submit" style="width:100%;text-align:left;padding:9px 14px;background:none;border:0;cursor:pointer;font-size:13px;border-radius:6px;color:#1e40af;font-weight:600;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='none'">🏭 Audyt ISO 50001</button>
                            </form>
                            <form method="POST" action="{{ route('offer-templates.duplicate', $tpl) }}">
                                @csrf
                                <input type="hidden" name="target_category" value="biale_certyfikaty">
                                <button type="submit" style="width:100%;text-align:left;padding:9px 14px;background:none;border:0;cursor:pointer;font-size:13px;border-radius:6px;color:#7c3aed;font-weight:600;" onmouseover="this.style.background='#ede9fe'" onmouseout="this.style.background='none'">📜 Białe Certyfikaty</button>
                            </form>
                        </div>
                    </div>
                    @endif
                    @if($audits->isNotEmpty())
                    <div style="position:relative;display:inline-block;">
                        <button type="button" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='block'?'none':'block'" style="padding:8px 12px;border-radius:8px;background:#059669;color:#fff;border:0;cursor:pointer;font-weight:700;font-size:13px;">
                            Zastosuj do audytu ▾
                        </button>
                        <div style="display:none;position:absolute;right:0;top:calc(100% + 4px);background:#fff;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.12);min-width:260px;z-index:50;padding:6px;max-height:320px;overflow-y:auto;">
                            @foreach($audits as $audit)
                            <a href="{{ route('offers.create', ['from_company' => $audit->company_id, 'template_id' => $tpl->id]) }}"
                               style="display:block;padding:9px 14px;font-size:13px;border-radius:6px;color:#065f46;text-decoration:none;font-weight:600;"
                               onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='none'">
                                {{ $audit->title }}
                                @if($audit->company)<span style="font-weight:400;color:var(--ink-mute);"> — {{ $audit->company->name }}</span>@endif
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @if(!$tpl->offers_count && !$isGlobal)
                    <form method="POST" action="{{ route('offer-templates.destroy', $tpl) }}" onsubmit="return confirm('Usunąć szablon?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="padding:8px 12px;border-radius:8px;background:#fee2e2;color:#991b1b;border:0;cursor:pointer;font-weight:600;font-size:13px;">🗑</button>
                    </form>
                    @elseif(!$isGlobal)
                    @endif
                    @if($isGlobal && !$tpl->offers_count)
                    <form method="POST" action="{{ route('offer-templates.destroy', $tpl) }}" onsubmit="return confirm('Usunąć szablon?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="padding:8px 12px;border-radius:8px;background:#fee2e2;color:#991b1b;border:0;cursor:pointer;font-weight:600;font-size:13px;">🗑</button>
                    </form>
                    @endif
                    <span style="font-size:11px;color:var(--ink-mute);white-space:nowrap;">{{ $tpl->offers_count }} {{ $tpl->offers_count === 1 ? 'oferta' : ($tpl->offers_count < 5 ? 'oferty' : 'ofert') }}</span>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
</x-layouts.app>