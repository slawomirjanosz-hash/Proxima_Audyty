<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ENESA Panel</title>
    <link rel="icon" type="image/png" href="/logo.png">
    <link rel="shortcut icon" type="image/png" href="/logo.png">
    <link rel="apple-touch-icon" href="/logo.png">
    <style>
        :root {
            --bg-1: #f3f8f7;
            --bg-2: #e8f1fb;
            --panel: #ffffff;
            --ink: #0f2330;
            --muted: #4c6373;
            --line: #d5e0ea;
            --menu-grad: linear-gradient(130deg, #1ba84a 0%, #0e89d8 100%);
            --menu-grad-soft: linear-gradient(130deg, #1ba84a 0%, #1997d8 100%);
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; font-family: "Segoe UI", Tahoma, Arial, sans-serif; color: var(--ink); }
        body {
            background:
                radial-gradient(circle at 88% 12%, rgba(14, 137, 216, 0.15), transparent 32%),
                radial-gradient(circle at 18% 78%, rgba(27, 168, 74, 0.14), transparent 36%),
                linear-gradient(165deg, var(--bg-1) 0%, var(--bg-2) 100%);
        }
        .layout { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
        .sidebar { background: var(--menu-grad); color: #fff; padding: 24px 18px; box-shadow: 10px 0 30px rgba(10,53,83,.15); }
        .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 26px; }
        .brand-logo { width: 48px; height: 48px; border-radius: 14px; background: rgba(255,255,255,.16); display: grid; place-items: center; overflow: hidden; }
        .brand-logo img { width: 100%; height: 100%; object-fit: contain; }
        .brand-logo span { font-weight: 800; font-size: 20px; }
        .brand h1 { margin: 0; font-size: 19px; letter-spacing: .2px; }
        .brand p { margin: 2px 0 0; font-size: 12px; opacity: .9; text-transform: uppercase; letter-spacing: .5px; }
        .menu { list-style: none; margin: 0; padding: 0; display: grid; gap: 8px; }
        .menu a { display: block; text-decoration: none; color: #fff; font-weight: 600; padding: 11px 12px; border-radius: 10px; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15); }
        .menu a:hover, .menu a.menu-active { background: rgba(255,255,255,.28); border-color: rgba(255,255,255,.4); }
        .menu a.menu-alert { color: #fde047; border-color: rgba(253,224,71,.5); }
        .menu a.menu-alert:hover, .menu a.menu-alert.menu-active { color: #fde047; }
        .menu-alert-dot { display:inline-block; width:8px; height:8px; background:#fde047; border-radius:50%; margin-left:6px; vertical-align:middle; box-shadow:0 0 6px rgba(253,224,71,.8); animation:pulse-dot 1.6s ease-in-out infinite; }
        @keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.3)} }
        .content { padding: 16px 22px 22px; display: flex; flex-direction: column; gap: 14px; }
        .topbar { background: var(--menu-grad-soft); color: #fff; border-radius: 14px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 12px 24px rgba(9, 70, 104, 0.16); }
        .login-btn { color: #fff; text-decoration: none; font-weight: 600; border: 1px solid rgba(255,255,255,.2); background: rgba(255,255,255,.12); padding: 8px 12px; border-radius: 9px; }
        .login-btn { cursor: pointer; }
        .panel { background: var(--panel); border: 1px solid var(--line); border-radius: 16px; padding: 20px; margin-top: 14px; box-shadow: 0 14px 30px rgba(14,55,85,.07); }
        .status { margin-top: 12px; padding: 10px 12px; border-radius: 10px; background: #d9f6e3; color: #0c5f28; border: 1px solid #bcecca; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid #e4edf3; font-size: 14px; }
        th { font-size: 12px; text-transform: uppercase; letter-spacing: .5px; color: var(--muted); }
        .inline-form { display: flex; gap: 8px; align-items: center; }
        input, select, button { padding: 8px 10px; border-radius: 9px; border: 1px solid #c9d7e3; font-size: 14px; }
        button { cursor: pointer; background: #0e89d8; color: #fff; border: 0; }
        .muted { color: var(--muted); }
        @media (max-width: 960px) {
            .layout { grid-template-columns: 1fr; }
            .main { padding: 12px; }
        }
        /* ── Built-by badge ── */
        .menu-tree { list-style:none; margin:4px 0 0; padding:0 0 0 14px; display:grid; gap:3px; }
        .menu-tree a { display:block; text-decoration:none; color:rgba(255,255,255,.82); font-weight:600; font-size:13px; padding:6px 10px; border-radius:8px; background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.1); }
        .menu-tree a:hover, .menu-tree a.menu-active { background:rgba(255,255,255,.24); border-color:rgba(255,255,255,.35); color:#fff; }
        .menu-tree-toggle { width:100%; display:flex; justify-content:space-between; align-items:center; gap:6px; color:#fff; font-weight:600; padding:11px 12px; border-radius:10px; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15); cursor:pointer; font-size:inherit; font-family:inherit; text-align:left; }
        .menu-tree-toggle:hover, .menu-tree-toggle.menu-active { background:rgba(255,255,255,.28); border-color:rgba(255,255,255,.4); }
        .menu-tree-arrow { font-size:11px; opacity:.8; transition:transform .2s; }
        .menu-tree-toggle.open .menu-tree-arrow { transform:rotate(180deg); }
        .built-by { position: fixed; bottom: 20px; left: 20px; display: flex; align-items: center; gap: 10px; z-index: 200; text-decoration: none; cursor: default; user-select: none; }
        .built-by__logo { height: 30px; width: auto; display: block; flex-shrink: 0; }
        .built-by__text { display: flex; flex-direction: column; gap: 1px; }
        .built-by__label { font-size: 9px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: rgba(255,255,255,0.4); line-height: 1; }
        .built-by__name { font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.75); letter-spacing: .3px; line-height: 1.3; }
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
                        + \App\Models\ClientChatMessage::where('is_from_admin', false)->where('read_at', null)->count();
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
                <span id="topbar-clock" style="font-size:13px; font-weight:600; color:rgba(255,255,255,.85); letter-spacing:.3px; min-width:140px;"></span>
                @auth
                    <x-online-users-info />
                @endauth
                <form method="GET" action="{{ route('locale.switch', [], false) }}">
                    <select name="locale" onchange="this.form.submit()" style="height:36px; border-radius:9px; border:1px solid rgba(255,255,255,.2); background:rgba(255,255,255,.12); color:#fff; padding:0 10px; font-weight:600;">
                        @foreach(config('localization.supported_locales', ['pl' => 'Polski', 'en' => 'English']) as $localeCode => $localeLabel)
                            <option value="{{ $localeCode }}" @selected(app()->getLocale() === $localeCode) style="color:#0f2330;">{{ $localeLabel }}</option>
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

        {{ $slot }}
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
