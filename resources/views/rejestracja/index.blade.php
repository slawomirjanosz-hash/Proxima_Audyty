<x-layouts.app>
    <style>
        .reg-card {
            background: var(--paper-soft);
            border: 1px solid var(--paper-deep);
            border-radius: 16px;
            padding: 32px 36px 40px;
            box-shadow: 0 4px 18px rgba(26,77,58,.07);
            max-width: 760px;
        }
        .reg-heading { margin: 0 0 6px; font-size: 26px; font-weight: 700; font-family: var(--serif); color: var(--green-deep); }
        .reg-sub { margin: 0 0 28px; font-size: 14px; color: var(--ink-mute); }
        .nip-row { display: flex; gap: 10px; align-items: flex-end; margin-bottom: 24px; }
        .nip-row .field { flex: 1; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 20px; }
        .field { display: flex; flex-direction: column; gap: 5px; }
        .field.full { grid-column: 1 / -1; }
        .field label { font-size: 13px; font-weight: 700; color: var(--ink-soft); }
        .field label .req { color: #e53e3e; margin-left: 2px; }
        .field input {
            padding: 10px 12px; border: 1px solid var(--paper-deep); border-radius: 8px;
            font-size: 14px; font-family: inherit; color: var(--ink); background: #fff;
            transition: border-color .15s, box-shadow .15s;
        }
        .field input:focus { outline: none; border-color: var(--green-primary); box-shadow: 0 0 0 3px rgba(46,125,92,.10); }
        .field input.autofilled { background: var(--green-bg); border-color: var(--green-light); }
        .field-error { font-size: 12px; color: #c53030; margin-top: 2px; }
        .nip-btn {
            padding: 10px 18px; border: none; border-radius: 8px;
            background: var(--green-primary); color: var(--paper);
            font-weight: 700; font-size: 14px; cursor: pointer; white-space: nowrap; transition: background .15s;
        }
        .nip-btn:hover { background: var(--green-deep); }
        .nip-btn:disabled { opacity: .55; cursor: default; }
        .nip-status { font-size: 13px; margin-top: 6px; padding: 8px 12px; border-radius: 8px; display: none; }
        .nip-status.ok  { display: block; background: var(--green-bg); border: 1px solid var(--green-light); color: var(--green-deep); }
        .nip-status.err { display: block; background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }
        .divider { grid-column: 1 / -1; border: none; border-top: 1px solid var(--paper-deep); margin: 4px 0; }
        .section-label { grid-column: 1 / -1; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .7px; color: var(--green-primary); margin-bottom: -4px; font-family: var(--mono); }
        .submit-row { margin-top: 24px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
        .btn-submit { padding: 12px 28px; border: none; border-radius: 8px; background: var(--green-primary); color: var(--paper); font-weight: 800; font-size: 15px; cursor: pointer; transition: background .15s; }
        .btn-submit:hover { background: var(--green-deep); }
        .hint-text { font-size: 12px; color: var(--ink-mute); max-width: 380px; line-height: 1.55; }
        .global-error { background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; color: #991b1b; font-size: 14px; }
        .consent-block { margin-top: 20px; padding: 16px 18px; background: #f0f7f4; border: 1px solid var(--green-light); border-radius: 10px; }
        .consent-block label { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: var(--ink-soft); line-height: 1.55; cursor: pointer; }
        .consent-block input[type=checkbox] { flex-shrink: 0; width: 17px; height: 17px; margin-top: 2px; accent-color: var(--green-primary); cursor: pointer; }
        .consent-block a { color: var(--green-primary); font-weight: 700; text-decoration: underline; }
        .consent-block a:hover { color: var(--green-deep); }
        .consent-block .field-error { margin-top: 6px; }
        @media (max-width: 800px) { .form-grid { grid-template-columns: 1fr; } .nip-row { flex-direction: column; align-items: stretch; } .reg-card { padding: 24px 20px 32px; } }
    </style>

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
                    <label for="first_name">Imię osoby kontaktowej <span class="req">*</span></label>
                    <input type="text" id="first_name" name="first_name" required
                           value="{{ old('first_name') }}" placeholder="np. Jan">
                    @error('first_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="field">
                    <label for="last_name">Nazwisko osoby kontaktowej <span class="req">*</span></label>
                    <input type="text" id="last_name" name="last_name" required
                           value="{{ old('last_name') }}" placeholder="np. Kowalski">
                    @error('last_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>

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

            <div class="consent-block">
                <label for="accepted_terms">
                    <input type="checkbox" id="accepted_terms" name="accepted_terms" value="1"
                           {{ old('accepted_terms') ? 'checked' : '' }}>
                    <span>
                        Zapoznałem/-am się i akceptuję
                        <a href="{{ route('legal.regulamin') }}" target="_blank">Warunki korzystania z platformy ENESA</a>
                        oraz
                        <a href="{{ route('legal.rodo') }}" target="_blank">Politykę prywatności i klauzulę RODO</a>.
                        Wyrażam zgodę na przetwarzanie danych osobowych w celach związanych z obsługą rejestracji
                        i świadczeniem usług audytu energetycznego przez ENESA Energy Audit &amp; Solutions sp. z o.o.
                    </span>
                </label>
                @error('accepted_terms')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="submit-row">
                <button type="submit" class="btn-submit">Złóż wniosek rejestracyjny →</button>
                <p class="hint-text">Po złożeniu wniosku administrator systemu ENESA zaakceptuje lub odrzuci rejestrację. Otrzymasz informację e-mailem.</p>
            </div>
        </form>
    </div>

    <script>
    async function lookupNip() {
        const nipRaw   = document.getElementById('nip-search').value;
        const nip      = nipRaw.replace(/\D/g, '');
        const statusEl = document.getElementById('nip-status');
        const btn      = document.getElementById('btn-lookup');

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
        if (value) { el.classList.add('autofilled'); setTimeout(() => el.classList.remove('autofilled'), 3000); }
    }

    document.getElementById('reg-form').addEventListener('submit', function () {
        const nip = document.getElementById('nip-search').value.replace(/\D/g, '');
        if (nip.length === 10 && !document.getElementById('nip-hidden').value) {
            document.getElementById('nip-hidden').value = nip;
        }
    });

    document.getElementById('nip-search').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); lookupNip(); }
    });
    </script>
</x-layouts.app>
