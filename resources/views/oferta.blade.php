<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ENESA | Oferta</title>
    <style>
        :root {
            --bg-1: #eff8f0;
            --bg-2: #e7f1fb;
            --ink: #0f2330;
            --muted: #4c6373;
            --line: #d5e0ea;
            --menu-grad: linear-gradient(130deg, #1ba84a 0%, #0e89d8 100%);
            --menu-grad-soft: linear-gradient(130deg, #1ba84a 0%, #1997d8 100%);
            --panel: #ffffff;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 86% 12%, rgba(14, 137, 216, 0.16), transparent 30%),
                radial-gradient(circle at 12% 84%, rgba(27, 168, 74, 0.14), transparent 34%),
                linear-gradient(165deg, var(--bg-1) 0%, var(--bg-2) 100%);
        }

        .shell {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: var(--menu-grad);
            color: #fff;
            padding: 24px 18px;
            box-shadow: 10px 0 30px rgba(10, 53, 83, 0.15);
        }

        .brand {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 26px;
        }

        .logo {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(255,255,255,.16);
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo span {
            font-size: 20px;
            font-weight: 800;
            color: #fff;
        }

        .brand h1 {
            margin: 0;
            font-size: 19px;
            letter-spacing: .2px;
        }

        .brand p {
            margin: 2px 0 0;
            font-size: 12px;
            opacity: .9;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .menu {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 8px;
        }

        .menu a {
            display: block;
            text-decoration: none;
            color: #fff;
            font-weight: 600;
            padding: 11px 12px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,.15);
            background: rgba(255,255,255,.1);
        }

        .menu a:hover, .menu a.menu-active {
            background: rgba(255,255,255,.28);
            border-color: rgba(255,255,255,.4);
        }

        .content {
            padding: 16px 22px 22px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .topbar {
            background: var(--menu-grad-soft);
            color: #fff;
            border-radius: 14px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 12px 24px rgba(9, 70, 104, 0.16);
        }

        .topbar strong {
            font-size: 15px;
        }

        .login-btn {
            text-decoration: none;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            padding: 9px 14px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,.24);
            background: rgba(255,255,255,.16);
        }

        /* ── Oferta section ── */
        .section-oferta {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 32px 36px 40px;
            box-shadow: 0 18px 40px rgba(14,55,85,.08);
        }

        .section-oferta h2 {
            margin: 0 0 24px;
            font-size: clamp(20px, 2.4vw, 28px);
            font-weight: 800;
            background: linear-gradient(90deg, #1ba84a 0%, #0e89d8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
        }

        .offer-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .offer-grid-bottom {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-top: 16px;
        }

        .offer-card {
            background: #0f1e2e;
            border-radius: 14px;
            padding: 22px 22px 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: transform .18s, box-shadow .18s;
        }

        .offer-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(0,0,0,.28);
        }

        .offer-tag {
            width: fit-content;
            border-radius: 6px;
            padding: 3px 9px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .7px;
            text-transform: uppercase;
        }

        .tag-pro     { background: #1ba84a; color: #fff; }
        .tag-express { background: #1585d0; color: #fff; }
        .tag-iso     { background: #d97706; color: #fff; }
        .tag-micro   { background: #6d28d9; color: #fff; }
        .tag-impl    { background: #ea580c; color: #fff; }

        .offer-card h3 {
            margin: 0;
            font-size: 17px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
        }

        .offer-card p {
            margin: 0;
            font-size: 13px;
            line-height: 1.55;
            color: #8da5b7;
            flex: 1;
        }

        .offer-price {
            margin-top: 6px;
            font-size: 14px;
            font-weight: 700;
        }

        .price-green  { color: #22c55e; }
        .price-orange { color: #f59e0b; }
        .price-purple { color: #a78bfa; }
        .price-red    { color: #f97316; }

        @media (max-width: 960px) {
            .shell { grid-template-columns: 1fr; }
            .content { padding: 12px; }
            .section-oferta { padding: 24px 18px 28px; }
            .offer-grid { grid-template-columns: 1fr 1fr; }
            .offer-grid-bottom { grid-template-columns: 1fr; }
        }

        @media (max-width: 600px) {
            .offer-grid { grid-template-columns: 1fr; }
        }

        /* ── Built-by badge ── */
        .built-by {
            position: fixed;
            bottom: 20px;
            left: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 200;
            text-decoration: none;
            cursor: default;
            user-select: none;
        }

        .built-by__logo {
            height: 30px;
            width: auto;
            display: block;
            flex-shrink: 0;
        }

        .built-by__text {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .built-by__label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(50,80,100,0.45);
            line-height: 1;
        }

        .built-by__name {
            font-size: 12px;
            font-weight: 600;
            color: rgba(50,80,100,0.65);
            letter-spacing: .3px;
            line-height: 1.3;
        }

        .login-overlay {
            position: fixed;
            inset: 0;
            background: rgba(8, 24, 36, 0.42);
            display: grid;
            place-items: center;
            z-index: 400;
            padding: 20px;
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
        }

        .login-card {
            width: min(430px, 92vw);
            background: #fff;
            border: 1px solid #d7e6f0;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 18px 36px rgba(13,58,90,.18);
        }

        .login-card .brand-login { display:flex; align-items:center; gap:10px; margin-bottom:16px; }
        .login-card .logo-login { width:42px; height:42px; border-radius:12px; background:linear-gradient(130deg, #1ba84a, #0e89d8); display:grid; place-items:center; color:#fff; font-weight:800; }
        .login-card h3 { margin:0 0 12px; font-size:24px; }
        .login-card label { display:block; margin:12px 0 6px; font-weight:600; font-size:14px; }
        .login-card input { width:100%; box-sizing:border-box; padding:10px; border-radius:9px; border:1px solid #c9d7e3; }
        .login-card .row { display:flex; justify-content:space-between; align-items:center; margin-top:10px; font-size:14px; }
        .login-card .submit-btn { margin-top:14px; width:100%; border:0; border-radius:10px; padding:11px; color:#fff; font-weight:700; background:linear-gradient(130deg, #1ba84a, #0e89d8); cursor:pointer; }
        .login-card .err { margin-top:10px; padding:9px; background:#ffe6e6; color:#9f1f1f; border:1px solid #ffc9c9; border-radius:8px; }
        .login-card .hint { margin-top:10px; font-size:12px; color:#4f6675; }
        .login-close { margin-top:10px; display:block; text-align:center; color:#4f6675; text-decoration:none; font-size:13px; }
    </style>
</head>
<body>
    @php($showLoginModal = request()->boolean('login') || $errors->any())
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="logo">
                    <img src="/Logo.png" alt="ENESA logo" onerror="this.remove(); this.parentElement.innerHTML='<span>E</span>';">
                </div>
                <div>
                    <h1>ENESA</h1>
                    <p>Energy Audit Systems</p>
                </div>
            </div>

            @php($menuUser = auth()->user())
            <ul class="menu">
                @if(!$menuUser)
                    <li><a href="{{ route('home') }}">{{ __('ui.menu.home') }}</a></li>
                    <li><a href="{{ route('information.index') }}">{{ __('ui.menu.info') }}</a></li>
                    <li><a href="{{ route('oferta') }}" class="menu-active">{{ __('ui.menu.offer') }}</a></li>
                @else
                    @if($menuUser->isClient() || $menuUser->canAccessTab(\App\Models\User::TAB_HOME))
                        <li><a href="{{ route('home') }}">{{ __('ui.menu.home') }}</a></li>
                    @endif

                    @if(!$menuUser->isClient() && $menuUser->canAccessTab(\App\Models\User::TAB_INFO))
                        <li><a href="{{ route('information.index') }}">{{ __('ui.menu.info') }}</a></li>
                    @endif

                    @if(!$menuUser->isClient() && $menuUser->canAccessTab(\App\Models\User::TAB_AUDITS))
                        <li><a href="{{ route('dashboard') }}">{{ __('ui.menu.dashboard') }}</a></li>
                        <li><a href="{{ route('audits.index') }}">{{ __('ui.menu.audits') }}</a></li>
                        <li><a href="{{ route('crm.index') }}">{{ __('ui.menu.crm') }}</a></li>
                    @endif

                    @if(!$menuUser->isClient() && $menuUser->canAccessTab(\App\Models\User::TAB_OFFER))
                        <li><a href="{{ route('oferta') }}" class="menu-active">{{ __('ui.menu.offer') }}</a></li>
                    @endif

                    @if($menuUser->isClient() || $menuUser->canAccessTab(\App\Models\User::TAB_CLIENT_ZONE))
                        <li><a href="{{ route('strefa-klienta') }}">{{ __('ui.menu.client_zone') }}</a></li>
                    @endif

                    @if(!$menuUser->isClient() && $menuUser->canAccessTab(\App\Models\User::TAB_SETTINGS))
                        <li><a href="{{ route('settings.index') }}">{{ __('ui.menu.settings') }}</a></li>
                    @endif
                @endif
            </ul>
        </aside>

        <main class="content">
            <header class="topbar">
                <strong>{{ __('ui.company') }}</strong>
                <div style="display:flex; align-items:center; gap:8px;">
                    <form method="GET" action="{{ route('locale.switch', [], false) }}">
                        <select name="locale" onchange="this.form.submit()" style="height:36px; border-radius:9px; border:1px solid rgba(255,255,255,.2); background:rgba(255,255,255,.12); color:#fff; padding:0 10px; font-weight:600;">
                            @foreach(config('localization.supported_locales', ['pl' => 'Polski', 'en' => 'English']) as $localeCode => $localeLabel)
                                <option value="{{ $localeCode }}" @selected(app()->getLocale() === $localeCode) style="color:#0f2330;">{{ $localeLabel }}</option>
                            @endforeach
                        </select>
                    </form>
                    @auth
                        <form method="POST" action="{{ route('logout', [], false) }}">
                            @csrf
                            <button class="login-btn" type="submit">{{ __('ui.actions.logout') }}</button>
                        </form>
                    @else
                        <a class="login-btn" href="{{ route('oferta', ['login' => 1]) }}">{{ __('ui.actions.login') }}</a>
                    @endauth
                </div>
            </header>

            <section class="section-oferta">
                <h2>Nasza Oferta</h2>

                <div class="offer-grid">
                    <div class="offer-card">
                        <span class="offer-tag tag-pro">PRO</span>
                        <h3>Audyt Pełny</h3>
                        <p>Pełna inwentaryzacja dużych zakładów. Pomiary, bilanse, 15–20 kart przedsięwzięć, analiza autogeneracji, odzyski ciepła, SCADA.</p>
                        <div class="offer-price price-green">od 55 000 PLN</div>
                    </div>

                    <div class="offer-card">
                        <span class="offer-tag tag-express">EXPRESS</span>
                        <h3>Audyt Compliance+</h3>
                        <p>Szybki audyt z aplikacją self-assessment, wizyta audytora i 3–4 business case'ami. Raport PN-EN 16247.</p>
                        <div class="offer-price price-green">od 12 000 PLN</div>
                    </div>

                    <div class="offer-card">
                        <span class="offer-tag tag-iso">ISO 50001</span>
                        <h3>Zarządzanie Energią</h3>
                        <p>Szkolenia, procedury, przegląd energetyczny, dokumentacja, audyt wewnętrzny, wsparcie certyfikacji. Zwalnia z audytu co 4 lata.</p>
                        <div class="offer-price price-orange">od 30 000 PLN</div>
                    </div>
                </div>

                <div class="offer-grid-bottom">
                    <div class="offer-card">
                        <span class="offer-tag tag-micro">MICROGRID</span>
                        <h3>Microgrid Przemysłowy</h3>
                        <p>Dobór elementów: PV + BESS + CHP + pompy ciepła + magazyny ciepła. Projekt EMS z arbitrażem cenowym. Optymalizacja kosztowa vs sieć + kontrakty PPA.</p>
                        <div class="offer-price price-purple">wycena indywidualna</div>
                    </div>

                    <div class="offer-card">
                        <span class="offer-tag tag-impl">IMPLEMENT</span>
                        <h3>Inżynier Kontraktu</h3>
                        <p>SiWZ/SWZ, wsparcie przetargowe, nadzór nad wykonawcą, odbiór, monitoring efektów (M&amp;V). Niezależny nadzór w imieniu Zamawiającego. Idealny dla samorządów i PZP.</p>
                        <div class="offer-price price-red">3–8% wart. kontraktu</div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <div class="built-by" title="Website built by ProximaLumine">
        <img class="built-by__logo" src="/Proxima_Lumine5.png" alt="ProximaLumine">
        <div class="built-by__text">
            <span class="built-by__label">Built by</span>
            <span class="built-by__name">ProximaLumine</span>
        </div>
    </div>

    @guest
        @if($showLoginModal)
            <div class="login-overlay">
                <form class="login-card" method="POST" action="{{ route('login.store', [], false) }}">
                    @csrf

                    <div class="brand-login">
                        <div class="logo-login">E</div>
                        <div>
                            <strong>ENESA</strong>
                            <div style="font-size:12px;color:#4f6675;">{{ __('ui.auth.access_panel') }}</div>
                        </div>
                    </div>

                    <h3>{{ __('ui.auth.title') }}</h3>

                    @if ($errors->any())
                        <div class="err">{{ $errors->first() }}</div>
                    @endif

                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required>

                    <label for="password">{{ __('ui.auth.password') }}</label>
                    <input id="password" name="password" type="password" required>

                    <div class="row">
                        <label style="margin:0;"><input type="checkbox" name="remember"> {{ __('ui.actions.remember_me') }}</label>
                    </div>

                    <button class="submit-btn" type="submit">{{ __('ui.actions.sign_in') }}</button>

                    <div class="hint">{{ __('ui.auth.test_accounts') }}</div>
                    <a class="login-close" href="{{ route('oferta') }}">{{ __('ui.actions.close') }}</a>
                </form>
            </div>
        @endif
    @endguest
</body>
</html>
