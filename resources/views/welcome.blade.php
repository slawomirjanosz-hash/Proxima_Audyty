<x-layouts.app>
    @php($showLoginModal = request()->boolean('login') || $errors->any())

    <style>
        .hero-logo { width: min(190px, 32vw); max-width: 100%; margin-bottom: 4px; }
        .hero-logo img { width: 100%; height: auto; display: block; }
        .hero-heading { display: grid; gap: 4px; }
        .hero-heading-main {
            margin: 0; font-size: clamp(22px, 3vw, 36px); font-weight: 700;
            font-family: var(--serif); color: var(--green-primary);
        }
        .hero-heading-sub {
            margin: 0; font-size: clamp(17px, 2.2vw, 26px); font-weight: 600;
            font-family: var(--serif); color: var(--green-deep);
        }
        .hero-badge {
            width: fit-content; border-radius: 999px; padding: 6px 14px;
            font-size: 11px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase;
            background: var(--green-bg); border: 1px solid var(--green-light); color: var(--green-deep);
        }
        .login-overlay {
            position: fixed; inset: 0;
            background: rgba(8, 24, 36, 0.45);
            display: grid; place-items: center;
            z-index: 400; padding: 20px;
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
        }
        .login-card {
            width: min(430px, 92vw);
            background: var(--paper-soft);
            border: 1px solid var(--paper-deep);
            border-radius: 16px; padding: 26px;
            box-shadow: 0 18px 36px rgba(13,58,90,.18);
        }
        .login-card .brand-login { display:flex; align-items:center; gap:10px; margin-bottom:16px; }
        .login-card .logo-login { width:42px; height:42px; border-radius:12px; overflow:hidden; display:grid; place-items:center; background:var(--green-deep); }
        .login-card h3 { margin:0 0 14px; font-size:22px; font-family:var(--serif); color:var(--green-deep); }
        .login-card label { display:block; margin:12px 0 5px; font-weight:600; font-size:13.5px; }
        .login-card .row { display:flex; justify-content:flex-start; align-items:center; margin-top:10px; font-size:13.5px; }
        .login-card .submit-btn { margin-top:16px; width:100%; border:0; border-radius:8px; padding:11px; color:var(--paper); font-weight:700; background:var(--green-primary); cursor:pointer; font-size:14px; }
        .login-card .submit-btn:hover { background: var(--green-deep); }
        .login-card .err { margin-top:10px; padding:10px; background:#ffe6e6; color:#9f1f1f; border:1px solid #ffc9c9; border-radius:8px; font-size:13px; }
        .login-close { margin-top:12px; display:block; text-align:center; color:var(--ink-mute); text-decoration:none; font-size:13px; }
        .login-close:hover { color: var(--ink); }
    </style>

    <section class="panel" style="min-height: calc(100vh - 180px); display:grid; align-content:center; gap:16px; max-width:800px;">
        <div class="hero-logo">
            <img src="/Logo2.png" alt="ENESA Logo" onerror="this.style.display='none'">
        </div>

        <span class="hero-badge">Industrial Audits</span>

        <div class="hero-heading">
            <p class="hero-heading-main">ENESA sp. z o. o.</p>
            <p class="hero-heading-sub">Audyty energetyczne</p>
        </div>

        <p class="muted" style="margin:0; max-width:700px; font-size:clamp(14px,1.6vw,17px); line-height:1.65;">
            Zaawansowane audyty energetyczne, analiza efektywności oraz profesjonalne raportowanie
            dla sektora komercyjnego i przemysłowego. Precyzja danych, techniczne podejście i
            standard wykonania gotowy dla rynku międzynarodowego.
        </p>
    </section>

    @guest
        @if($showLoginModal)
            <div class="login-overlay">
                <form class="login-card" method="POST" action="{{ route('login.store', [], false) }}">
                    @csrf

                    <div class="brand-login">
                        <div class="logo-login">
                            <img src="/logo.png" alt="ENESA" style="width:42px;height:42px;object-fit:contain;" onerror="this.remove();">
                        </div>
                        <div>
                            <strong style="font-family:var(--serif); font-size:15px; color:var(--green-deep);">ENESA</strong>
                            <div style="font-size:12px;color:var(--ink-mute);">{{ __('ui.auth.access_panel') }}</div>
                        </div>
                    </div>

                    <h3>{{ __('ui.auth.title') }}</h3>

                    @if ($errors->any())
                        <div class="err">{{ $errors->first() }}</div>
                    @endif

                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required style="width:100%;">

                    <label for="password">{{ __('ui.auth.password') }}</label>
                    <input id="password" name="password" type="password" required style="width:100%;">

                    <div class="row">
                        <label style="margin:0; display:flex; align-items:center; gap:6px; white-space:nowrap; cursor:pointer;">
                            <input type="checkbox" name="remember"> {{ __('ui.actions.remember_me') }}
                        </label>
                    </div>

                    <button class="submit-btn" type="submit">{{ __('ui.actions.sign_in') }}</button>

                    <a class="login-close" href="{{ route('home') }}">{{ __('ui.actions.close') }}</a>
                </form>
            </div>
        @endif
    @endguest
</x-layouts.app>
