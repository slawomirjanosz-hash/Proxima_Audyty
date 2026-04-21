<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ENESA | Zarejestruj firmę</title>
    <link rel="icon" type="image/png" href="/logo.png">
    <link rel="shortcut icon" type="image/png" href="/logo.png">
    <link rel="apple-touch-icon" href="/logo.png">
    <style>
        :root {
            --bg-1: #eff8f0;
            --bg-2: #e7f1fb;
            --ink: #0f2330;
            --muted: #4c6373;
            --line: #d5e0ea;
            --menu-grad: linear-gradient(130deg, #1ba84a 0%, #0e89d8 100%);
            --panel: #ffffff;
            --green: #1ba84a;
            --blue: #0e89d8;
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

        .logo img { width: 100%; height: 100%; object-fit: contain; }
        .logo span { font-size: 20px; font-weight: 800; color: #fff; }

        .brand h1 { margin: 0; font-size: 19px; letter-spacing: .2px; }
        .brand p  { margin: 2px 0 0; font-size: 12px; opacity: .9; text-transform: uppercase; letter-spacing: .5px; }

        .menu { margin: 0; padding: 0; list-style: none; display: grid; gap: 8px; }

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
            background: var(--menu-grad);
            color: #fff;
            border-radius: 14px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 12px 24px rgba(9, 70, 104, 0.16);
        }

        .topbar strong { font-size: 15px; }

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

        /* ── Registration card ── */
        .reg-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 32px 36px 40px;
            box-shadow: 0 18px 40px rgba(14,55,85,.08);
            max-width: 760px;
        }

        .reg-heading {
            margin: 0 0 6px;
            font-size: 26px;
            font-weight: 800;
            background: linear-gradient(90deg, #1ba84a 0%, #0e89d8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
        }

        .reg-sub { margin: 0 0 28px; font-size: 14px; color: var(--muted); }

        /* NIP lookup row */
        .nip-row {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            margin-bottom: 24px;
        }

        .nip-row .field { flex: 1; }

        /* Form layout */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px 20px;
        }

        .field { display: flex; flex-direction: column; gap: 5px; }
        .field.full { grid-column: 1 / -1; }

        .field label {
            font-size: 13px;
            font-weight: 700;
            color: #1d3a50;
        }

        .field label .req { color: #e53e3e; margin-left: 2px; }

        .field input {
            padding: 10px 12px;
            border: 1px solid #c8d8e6;
            border-radius: 9px;
            font-size: 14px;
            font-family: inherit;
            color: var(--ink);
            background: #f8fbfd;
            transition: border-color .15s, box-shadow .15s;
        }

        .field input:focus {
            outline: none;
            border-color: #0e89d8;
            box-shadow: 0 0 0 3px rgba(14,137,216,.12);
            background: #fff;
        }

        .field input.autofilled {
            background: #f0fef4;
            border-color: #86efac;
        }

        .field-error {
            font-size: 12px;
            color: #c53030;
            margin-top: 2px;
        }

        .nip-btn {
            padding: 10px 18px;
            border: none;
            border-radius: 9px;
            background: linear-gradient(130deg, #1ba84a, #0e89d8);
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            white-space: nowrap;
            transition: opacity .15s;
        }

        .nip-btn:hover { opacity: .88; }
        .nip-btn:disabled { opacity: .55; cursor: default; }

        .nip-status {
            font-size: 13px;
            margin-top: 6px;
            padding: 8px 12px;
            border-radius: 8px;
            display: none;
        }

        .nip-status.ok  { display: block; background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
        .nip-status.err { display: block; background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }

        .divider {
            grid-column: 1 / -1;
            border: none;
            border-top: 1px solid #e8f0f7;
            margin: 4px 0;
        }

        .section-label {
            grid-column: 1 / -1;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #4c8ab0;
            margin-bottom: -4px;
        }

        .submit-row {
            margin-top: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-submit {
            padding: 12px 28px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(130deg, #1ba84a, #0e89d8);
            color: #fff;
            font-weight: 800;
            font-size: 15px;
            cursor: pointer;
            transition: opacity .15s, transform .1s;
        }

        .btn-submit:hover { opacity: .88; transform: translateY(-1px); }

        .hint-text {
            font-size: 12px;
            color: var(--muted);
            max-width: 380px;
        }

        .global-error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #991b1b;
            font-size: 14px;
        }

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

        .built-by__logo { height: 30px; width: auto; display: block; flex-shrink: 0; }
        .built-by__text { display: flex; flex-direction: column; gap: 1px; }
        .built-by__label { font-size: 9px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: rgba(50,80,100,0.45); line-height: 1; }
        .built-by__name  { font-size: 12px; font-weight: 600; color: rgba(50,80,100,0.65); letter-spacing: .3px; line-height: 1.3; }

        @media (max-width: 960px) {
            .shell { grid-template-columns: 1fr; }
            .content { padding: 12px; }
            .form-grid { grid-template-columns: 1fr; }
            .reg-card { padding: 24px 20px 32px; }
            .nip-row { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>
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

        <ul class="menu">
            <li><a href="{{ route('home') }}">🏠 Strona główna</a></li>
            @if(\App\Models\SystemSetting::get('informacje_public', '1'))
                <li><a href="{{ route('information.index') }}">{{ __('ui.menu.info') }}</a></li>
            @endif
            <li><a href="{{ route('register.form') }}" class="menu-active">📝 Zarejestruj firmę</a></li>
        </ul>
    </aside>

    <main class="content">
        <header class="topbar">
            <strong>Rejestracja firmy</strong>
            <div style="display:flex; align-items:center; gap:18px;">
                <form method="GET" action="{{ route('locale.switch', [], false) }}">
                    <select name="locale" onchange="this.form.submit()" style="height:36px; border-radius:9px; border:1px solid rgba(255,255,255,.2); background:rgba(255,255,255,.12); color:#fff; padding:0 10px; font-weight:600;">
                        @foreach(config('localization.supported_locales', ['pl' => 'Polski', 'en' => 'English']) as $localeCode => $localeLabel)
                            <option value="{{ $localeCode }}" @selected(app()->getLocale() === $localeCode) style="color:#0f2330;">{{ $localeLabel }}</option>
                        @endforeach
                    </select>
                </form>
                @guest
                    <a class="login-btn" href="{{ route('home', ['login' => 1]) }}">Zaloguj się</a>
                @endguest
            </div>
        </header>

        <div class="reg-card">
            <h1 class="reg-heading">Zarejestruj swoją firmę</h1>
            <p class="reg-sub">Wypełnij formularz, aby zgłosić firmę do systemu ENESA. Wniosek zostanie rozpatrzony przez administratora.</p>

            @if ($errors->any())
                <div class="global-error">
                    @foreach ($errors->all() as $error)
                        <div>⚠ {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            {{-- NIP Lookup --}}
            <div class="nip-row">
                <div class="field">
                    <label for="nip-search">NIP firmy <span class="req">*</span></label>
                    <input type="text" id="nip-search" placeholder="np. 1234567890" maxlength="13"
                           value="{{ old('nip') }}" inputmode="numeric">
                </div>
                <button type="button" class="nip-btn" id="btn-lookup" onclick="lookupNip()">
                    🔍 Pobierz dane z GUS
                </button>
            </div>
            <div class="nip-status" id="nip-status"></div>

            <form method="POST" action="{{ route('register.store') }}" id="reg-form">
                @csrf
                <input type="hidden" name="nip" id="nip-hidden" value="{{ old('nip') }}">

                <div class="form-grid">
                    <div class="section-label">Dane firmy</div>

                    <div class="field full">
                        <label for="name">Pełna nazwa firmy <span class="req">*</span></label>
                        <input type="text" id="name" name="name" required
                               value="{{ old('name') }}" placeholder="np. Kowalski sp. z o. o.">
                        @error('name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="short_name">Nazwa skrócona</label>
                        <input type="text" id="short_name" name="short_name"
                               value="{{ old('short_name') }}" placeholder="np. Kowalski">
                        @error('short_name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="city">Miasto</label>
                        <input type="text" id="city" name="city"
                               value="{{ old('city') }}" placeholder="np. Warszawa">
                        @error('city')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="street">Ulica i numer</label>
                        <input type="text" id="street" name="street"
                               value="{{ old('street') }}" placeholder="np. ul. Kwiatowa 5">
                        @error('street')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="postal_code">Kod pocztowy</label>
                        <input type="text" id="postal_code" name="postal_code"
                               value="{{ old('postal_code') }}" placeholder="np. 00-001" maxlength="10">
                        @error('postal_code')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <hr class="divider">
                    <div class="section-label">Dane kontaktowe</div>

                    <div class="field">
                        <label for="phone">Telefon kontaktowy <span class="req">*</span></label>
                        <input type="tel" id="phone" name="phone" required
                               value="{{ old('phone') }}" placeholder="np. +48 123 456 789">
                        @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="email">Adres e-mail <span class="req">*</span></label>
                        <input type="email" id="email" name="email" required
                               value="{{ old('email') }}" placeholder="np. biuro@firma.pl">
                        @error('email')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="submit-row">
                    <button type="submit" class="btn-submit">Złóż wniosek rejestracyjny →</button>
                    <p class="hint-text">Po złożeniu wniosku administrator systemu ENESA zaakceptuje lub odrzuci rejestrację. Otrzymasz informację e-mailem.</p>
                </div>
            </form>
        </div>
    </main>
</div>

<div class="built-by" title="Built by ProximaLumine">
    <img class="built-by__logo" src="/Proxima_Lumine5.png" alt="ProximaLumine">
    <div class="built-by__text">
        <span class="built-by__label">Built by</span>
        <span class="built-by__name">ProximaLumine</span>
    </div>
</div>

<script>
    async function lookupNip() {
        const nipRaw  = document.getElementById('nip-search').value;
        const nip     = nipRaw.replace(/\D/g, '');
        const statusEl = document.getElementById('nip-status');
        const btn     = document.getElementById('btn-lookup');

        if (nip.length !== 10) {
            statusEl.className = 'nip-status err';
            statusEl.textContent = '⚠ Wpisz poprawny 10-cyfrowy NIP przed pobraniem danych.';
            return;
        }

        btn.disabled = true;
        btn.textContent = '⏳ Pobieranie…';
        statusEl.className = 'nip-status';
        statusEl.textContent = '';

        try {
            const url  = '{{ route('register.nip-lookup') }}?nip=' + encodeURIComponent(nip);
            const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const data = await resp.json();

            if (!resp.ok) {
                statusEl.className = 'nip-status err';
                statusEl.textContent = '⚠ ' + (data.error ?? 'Nie udało się pobrać danych.');
            } else {
                // Fill form fields
                setField('name',        data.name        ?? '');
                setField('city',        data.city        ?? '');
                setField('street',      data.street      ?? '');
                setField('postal_code', data.postal_code ?? '');
                document.getElementById('nip-hidden').value = data.nip;

                statusEl.className = 'nip-status ok';
                statusEl.textContent = '✅ Dane pobrane z rejestru podatników VAT. Możesz je zmodyfikować przed wysłaniem.';
            }
        } catch (e) {
            statusEl.className = 'nip-status err';
            statusEl.textContent = '⚠ Błąd połączenia. Proszę wypełnić dane ręcznie.';
        } finally {
            btn.disabled = false;
            btn.textContent = '🔍 Pobierz dane z GUS';
        }
    }

    function setField(id, value) {
        const el = document.getElementById(id);
        if (!el) return;
        el.value = value;
        if (value) {
            el.classList.add('autofilled');
            setTimeout(() => el.classList.remove('autofilled'), 3000);
        }
    }

    // Sync NIP search field → hidden field on form submit
    document.getElementById('reg-form').addEventListener('submit', function () {
        const nip = document.getElementById('nip-search').value.replace(/\D/g, '');
        if (nip.length === 10 && !document.getElementById('nip-hidden').value) {
            document.getElementById('nip-hidden').value = nip;
        }
    });

    // Allow Enter key on NIP field to trigger lookup
    document.getElementById('nip-search').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); lookupNip(); }
    });
</script>
</body>
</html>
