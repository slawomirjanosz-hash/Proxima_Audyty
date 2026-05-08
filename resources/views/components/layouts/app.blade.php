<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ENESA Panel</title>
    <link rel="icon" type="image/png" href="/logo.png">
    <link rel="shortcut icon" type="image/png" href="/logo.png">
    <link rel="apple-touch-icon" href="/logo.png">
    <!-- ENESA Design System — Fraunces + Manrope + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Manrope:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        /* ── ENESA Palette ── */
        :root {
            --green-deep:    #1A4D3A;
            --green-primary: #2E7D5C;
            --green-light:   #A4C2A8;
            --green-bg:      #E7EEE5;
            --paper:         #F5EFE0;
            --paper-deep:    #EBE3D0;
            --paper-soft:    #FAF5E8;
            --gold:          #A87F2A;
            --gold-light:    #D4A84B;
            --rose:          #B8485A;
            --ink:           #1A1612;
            --ink-soft:      #3D352C;
            --ink-mute:      #76695A;
            --serif:         'Fraunces', Georgia, 'Times New Roman', serif;
            --sans:          'Manrope', -apple-system, 'Segoe UI', sans-serif;
            --mono:          'JetBrains Mono', 'Consolas', monospace;
            /* legacy compat */
            --bg-1: var(--paper);
            --bg-2: var(--paper-deep);
            --panel: var(--paper-soft);
            --muted: var(--ink-mute);
            --line: var(--paper-deep);
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }
        body {
            font-family: var(--sans);
            background: var(--paper);
            color: var(--ink);
            font-size: 14px;
            line-height: 1.5;
        }

        /* ── Layout ── */
        .layout { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar {
            background: var(--green-deep);
            color: var(--paper);
            padding: 0;
            box-shadow: 2px 0 12px rgba(0,0,0,0.10);
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        /* ── Brand ── */
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.10);
            margin-bottom: 8px;
        }
        .brand-logo {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            background: rgba(255,255,255,0.12);
            display: grid;
            place-items: center;
            overflow: hidden;
            flex-shrink: 0;
        }
        .brand-logo img { width: 100%; height: 100%; object-fit: contain; }
        .brand-logo span { font-family: var(--serif); font-weight: 700; font-size: 20px; color: var(--paper); }
        .brand h1 {
            margin: 0;
            font-family: var(--serif);
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 0.3px;
            color: var(--paper);
        }
        .brand p {
            margin: 2px 0 0;
            font-size: 10.5px;
            color: var(--green-light);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ── Menu ── */
        .menu { list-style: none; margin: 0; padding: 8px 12px 24px; display: flex; flex-direction: column; gap: 3px; }
        .menu a {
            display: block;
            text-decoration: none;
            color: rgba(245,239,224,0.88);
            font-weight: 500;
            font-size: 13.5px;
            padding: 9px 12px;
            border-radius: 6px;
            border-left: 3px solid transparent;
            transition: all 0.15s ease;
        }
        .menu a:hover {
            background: rgba(255,255,255,0.08);
            border-left-color: var(--gold);
            color: var(--paper);
        }
        .menu a.menu-active {
            background: rgba(255,255,255,0.12);
            border-left-color: var(--gold);
            color: var(--paper);
            font-weight: 600;
        }
        .menu a.menu-alert { color: #F5D88E; }
        .menu-alert-dot {
            display: inline-block;
            width: 7px; height: 7px;
            background: var(--gold-light);
            border-radius: 50%;
            margin-left: 6px;
            vertical-align: middle;
            box-shadow: 0 0 6px rgba(168,127,42,0.8);
            animation: pulse-dot 1.6s ease-in-out infinite;
        }
        @keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.3)} }

        /* ── Sub-menu (tree) ── */
        .menu-tree { list-style:none; margin: 3px 0 0; padding: 0 0 0 12px; display: flex; flex-direction: column; gap: 2px; }
        .menu-tree a {
            display: block;
            text-decoration: none;
            color: rgba(164,194,168,0.9);
            font-weight: 500;
            font-size: 12.5px;
            padding: 7px 10px;
            border-radius: 5px;
            border-left: 2px solid transparent;
            transition: all 0.15s ease;
        }
        .menu-tree a:hover, .menu-tree a.menu-active {
            background: rgba(255,255,255,0.10);
            border-left-color: var(--gold);
            color: var(--paper);
        }
        .menu-tree-toggle {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 6px;
            color: rgba(245,239,224,0.88);
            font-weight: 500;
            font-size: 13.5px;
            padding: 9px 12px;
            border-radius: 6px;
            border-left: 3px solid transparent;
            background: transparent;
            border-top: none; border-right: none; border-bottom: none;
            cursor: pointer;
            font-family: var(--sans);
            text-align: left;
            transition: all 0.15s ease;
        }
        .menu-tree-toggle:hover, .menu-tree-toggle.menu-active {
            background: rgba(255,255,255,0.08);
            border-left-color: var(--gold);
            color: var(--paper);
        }
        .menu-tree-toggle.open { border-left-color: var(--gold); color: var(--paper); }
        .menu-tree-arrow { font-size: 10px; opacity: 0.7; transition: transform 0.2s; }
        .menu-tree-toggle.open .menu-tree-arrow { transform: rotate(180deg); }

        /* ── Topbar ── */
        .content { padding: 0; display: flex; flex-direction: column; min-height: 100vh; }
        .topbar {
            background: var(--green-deep);
            color: var(--paper);
            padding: 11px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid var(--gold);
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .topbar strong {
            font-family: var(--serif);
            font-size: 15px;
            font-weight: 600;
            color: var(--paper);
            letter-spacing: 0.2px;
        }
        .login-btn {
            color: var(--paper);
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid rgba(245,239,224,0.25);
            background: rgba(245,239,224,0.10);
            padding: 7px 14px;
            border-radius: 5px;
            transition: background 0.15s;
        }
        .login-btn:hover { background: rgba(245,239,224,0.18); }

        /* ── Main content area ── */
        .content > .topbar ~ * { padding: 0; }
        .content-body { padding: 24px 28px 40px; flex: 1; }

        /* ── Panel / card ── */
        .panel {
            background: var(--paper-soft);
            border: 1px solid var(--paper-deep);
            border-radius: 8px;
            padding: 24px;
            margin-top: 14px;
            box-shadow: 0 2px 8px rgba(26,77,58,0.06);
        }

        /* ── Status flash ── */
        .status {
            margin: 14px 28px 0;
            padding: 12px 16px;
            border-radius: 6px;
            background: var(--green-bg);
            color: var(--green-deep);
            border: 1px solid var(--green-light);
            font-size: 13.5px;
            font-weight: 500;
        }

        /* ── Table defaults ── */
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid var(--paper-deep); font-size: 13.5px; }
        th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--ink-mute);
            font-family: var(--mono);
            background: var(--paper-deep);
        }

        /* ── Form inputs (global defaults) ── */
        input:not([type=checkbox]):not([type=radio]):not([type=range]),
        select,
        textarea {
            font-family: var(--sans);
            font-size: 13.5px;
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid var(--paper-deep);
            background: white;
            color: var(--ink);
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        input:not([type=checkbox]):not([type=radio]):focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--green-primary);
            box-shadow: 0 0 0 3px rgba(46,125,92,0.12);
        }
        button:not(.menu-tree-toggle) {
            cursor: pointer;
            font-family: var(--sans);
            font-size: 13.5px;
            padding: 8px 16px;
            border-radius: 5px;
            border: none;
            background: var(--green-primary);
            color: var(--paper);
            font-weight: 600;
            transition: background 0.15s;
        }
        button:not(.menu-tree-toggle):hover { background: var(--green-deep); }

        .muted, .text-muted { color: var(--ink-mute); }
        .inline-form { display: flex; gap: 8px; align-items: center; }

        /* ── Responsive ── */
        @media (max-width: 960px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { position: static; height: auto; }
        }

        /* ── Built-by badge ── */
        .built-by {
            position: fixed; bottom: 16px; left: 12px;
            display: flex; align-items: center; gap: 8px;
            z-index: 200; text-decoration: none; cursor: default; user-select: none;
            opacity: 0.65; transition: opacity 0.2s;
        }
        .built-by:hover { opacity: 1; }
        .built-by__logo { height: 26px; width: auto; display: block; flex-shrink: 0; }
        .built-by__text { display: flex; flex-direction: column; gap: 1px; }
        .built-by__label { font-size: 8.5px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--ink-mute); line-height: 1; }
        .built-by__name { font-size: 11px; font-weight: 600; color: var(--ink-soft); letter-spacing: 0.3px; line-height: 1.3; }
        /* ── Global ENESA overrides for legacy view inline colors ── */
        /* These catch hardcoded old-palette values used in per-view <style> blocks */
        .company-tile, .audit-row, .inquiry-row, .user-row, .offer-row-firm,
        .dash-section, .pending-section, .co-tbl, .panel {
            border-color: var(--paper-deep) !important;
        }
        .company-tile, .dash-section, .pending-section { background: var(--paper-soft) !important; }
        .company-tile:hover, .audit-row:hover, .user-row:hover, .co-tbl tbody tr:hover { background: var(--green-bg) !important; }
        .tile-name, .audit-row-title, .inquiry-type, .dash-section-header h2,
        .section-box-header h2, h1, h2, h3 { color: var(--green-deep) !important; }
        h1, h2 { font-family: var(--serif) !important; }
        .tile-meta, .audit-row-meta, .inquiry-meta, .inquiry-msg, p.muted { color: var(--ink-mute) !important; }
        .co-view-btn.active, .btn-accept, .add-user-tab.active, .btn-primary-sm {
            background: var(--green-primary) !important; color: var(--paper) !important; border-color: transparent !important;
        }
        .co-tbl th { background: var(--paper-deep) !important; color: var(--ink-mute) !important; font-family: var(--mono) !important; }
        .co-tbl th.sortable:hover, .co-tbl th.sort-asc::after, .co-tbl th.sort-desc::after { color: var(--gold) !important; background: var(--green-bg) !important; }
        .co-tbl td { border-color: var(--paper-deep) !important; }
        .co-row-link, a[style*="0e89d8"], a[style*="1d4f73"] { color: var(--green-primary) !important; }
        .user-avatar { background: var(--green-deep) !important; font-family: var(--serif) !important; }
        .ai-summary-bar { background: var(--green-bg) !important; border-color: var(--green-light) !important; }
        .ai-stat-val { color: var(--green-deep) !important; }
        .co-search-wrap input:focus { border-color: var(--green-primary) !important; }
        .pending-section { border-color: var(--gold) !important; }
        .pending-header h2 { color: var(--gold) !important; font-family: var(--serif) !important; }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo">
                <img src="/logo.png" alt="ENESA logo" onerror="this.remove(); this.parentElement.innerHTML='<span>E</span>';">
            </div>
            <div>
                <h1>ENESA</h1>
                <p>Energy Audit Systems</p>
            </div>
        </div>

        @php
            $menuUser = auth()->user();
            $dashboardAlertCount = 0;
            if ($menuUser && !$menuUser->isClient()) {
                try {
                    $dashboardAlertCount =
                        \App\Models\ClientInquiry::where('status', 'new')->count()
                        + \App\Models\ClientRegistration::where('status', 'pending')->count()
                        + \App\Models\ClientChatMessage::where('is_from_admin', false)->where('read_at', null)->count()
                        + \App\Models\EnergyAudit::where('questionnaire_completed', true)->whereNull('questionnaire_reviewed_at')->count();
                } catch (\Throwable $e) {
                    $dashboardAlertCount = 0;
                }
            }
        @endphp
        <ul class="menu">
            @if(!$menuUser)
                <li><a href="{{ route('home') }}" @class(['menu-active' => request()->routeIs('home')])>{{ __('ui.menu.home') }}</a></li>
                @if(\App\Models\SystemSetting::get('informacje_public', '1'))
                    <li><a href="{{ route('information.index') }}" @class(['menu-active' => request()->routeIs('information.*')])>{{ __('ui.menu.info') }}</a></li>
                @endif
                <li><a href="{{ route('register.form') }}" @class(['menu-active' => request()->routeIs('register.*')])>📝 Zarejestruj firmę</a></li>
            @else
                @if($menuUser->isClient() || $menuUser->canAccessTab(\App\Models\User::TAB_HOME))
                    <li><a href="{{ route('home') }}" @class(['menu-active' => request()->routeIs('home')])>{{ __('ui.menu.home') }}</a></li>
                @endif

                @if($menuUser->canAccessTab(\App\Models\User::TAB_INFO))
                    <li><a href="{{ route('information.index') }}" @class(['menu-active' => request()->routeIs('information.*')])>{{ __('ui.menu.info') }}</a></li>
                @endif

                @if(!$menuUser->isClient() && $menuUser->canAccessTab(\App\Models\User::TAB_AUDITS))
                    <li><a href="{{ route('dashboard') }}" @class(['menu-active' => request()->routeIs('dashboard'), 'menu-alert' => $dashboardAlertCount > 0])>{{ __('ui.menu.dashboard') }}@if($dashboardAlertCount > 0)<span class="menu-alert-dot" title="{{ $dashboardAlertCount }} nowych powiadomień"></span>@endif</a></li>
                    @php($inAuditsTypes = request()->routeIs('audits.types'))
                    @php($currentTab = request()->route('tab'))
                    <li>
                        <button type="button" class="menu-tree-toggle {{ $inAuditsTypes ? 'open' : '' }}"
                            onclick="this.classList.toggle('open'); this.nextElementSibling.style.display = this.classList.contains('open') ? '' : 'none';">
                            <span>Rodzaje audytów</span>
                            <span class="menu-tree-arrow">&#9660;</span>
                        </button>
                        <ul class="menu-tree" style="{{ $inAuditsTypes ? '' : 'display:none' }}">
                            <li><a href="{{ route('audits.types', ['tab' => 'energetyczne']) }}" @class(['menu-active' => $inAuditsTypes && ($currentTab === 'energetyczne' || $currentTab === null)])>Audyty energetyczne</a></li>
                            <li><a href="{{ route('audits.types', ['tab' => 'iso50001']) }}" @class(['menu-active' => $inAuditsTypes && $currentTab === 'iso50001'])>ISO 50001</a></li>
                            <li><a href="{{ route('audits.types', ['tab' => 'biale-certyfikaty']) }}" @class(['menu-active' => $inAuditsTypes && $currentTab === 'biale-certyfikaty'])>Białe certyfikaty</a></li>
                            <li><a href="{{ route('audits.types', ['tab' => 'ustawienia']) }}" @class(['menu-active' => $inAuditsTypes && $currentTab === 'ustawienia'])>Ustawienia</a></li>
                        </ul>
                    </li>
                    <li><a href="{{ route('crm.index') }}" @class(['menu-active' => request()->routeIs('crm.*')])>{{ __('ui.menu.crm') }}</a></li>
                @endif

                @if(!$menuUser->isClient() && $menuUser->canAccessTab(\App\Models\User::TAB_OFFER))
                    <li><a href="{{ route('offers.index') }}" @class(['menu-active' => request()->routeIs('offers.*')])>{{ __('ui.menu.offer') }}</a></li>
                @endif

                @if($menuUser->isClient() || $menuUser->canAccessTab(\App\Models\User::TAB_CLIENT_ZONE))
                    <li><a href="{{ route('strefa-klienta') }}" @class(['menu-active' => request()->routeIs('strefa-klienta')])>{{ __('ui.menu.client_zone') }}</a></li>
                @endif

                @if(!$menuUser->isClient() && $menuUser->canAccessTab(\App\Models\User::TAB_SETTINGS))
                    <li><a href="{{ route('settings.index') }}" @class(['menu-active' => request()->routeIs('settings.*')])>{{ __('ui.menu.settings') }}</a></li>
                @endif
            @endif
        </ul>
    </aside>

    <main class="content">
        <header class="topbar">
            <strong>{{ __('ui.company') }}</strong>
            <div style="display:flex; align-items:center; gap:18px;">
                <span id="topbar-clock" style="font-size:12px; font-weight:500; color:var(--green-light); letter-spacing:.5px; min-width:140px; font-family:var(--mono);"></span>
                @auth
                    <x-online-users-info />
                @endauth
                <form method="GET" action="{{ route('locale.switch', [], false) }}">
                    <select name="locale" onchange="this.form.submit()" style="height:32px; border-radius:5px; border:1px solid rgba(245,239,224,0.25); background:rgba(245,239,224,0.10); color:var(--paper); padding:0 8px; font-weight:600; font-size:12px; font-family:var(--sans);">
                        @foreach(config('localization.supported_locales', ['pl' => 'Polski', 'en' => 'English']) as $localeCode => $localeLabel)
                            <option value="{{ $localeCode }}" @selected(app()->getLocale() === $localeCode) style="color:#0f2330; background:#fff;">{{ $localeLabel }}</option>
                        @endforeach
                    </select>
                </form>
                @auth
                    {{-- logout moved to user avatar dropdown --}}
                @else
                    <a class="login-btn" href="{{ route('home', ['login' => 1]) }}">{{ __('ui.actions.login') }}</a>
                @endauth
            </div>
        </header>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <div class="content-body">
        {{ $slot }}
        </div>
    </main>
</div>
<div class="built-by" title="BUILT BY Proxima Lumine">
    <img class="built-by__logo" src="/Proxima_Lumine5.png" alt="Proxima Lumine">
    <div class="built-by__text">
        <span class="built-by__label">BUILT BY</span>
        <span class="built-by__name">Proxima Lumine</span>
    </div>
</div>
</body>
<script>
(function() {
    const el = document.getElementById('topbar-clock');
    if (!el) return;
    const days = ['niedziela','poniedziałek','wtorek','środa','czwartek','piątek','sobota'];
    function pad(n) { return String(n).padStart(2, '0'); }
    function tick() {
        const now = new Date();
        const d   = pad(now.getDate());
        const m   = pad(now.getMonth() + 1);
        const y   = now.getFullYear();
        const H   = pad(now.getHours());
        const M   = pad(now.getMinutes());
        const S   = pad(now.getSeconds());
        el.textContent = d + '.' + m + '.' + y + '  ' + H + ':' + M + ':' + S;
    }
    tick();
    setInterval(tick, 1000);
})();
</script>
</html>
