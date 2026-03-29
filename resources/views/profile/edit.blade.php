<x-layouts.app>
    <section class="panel">
        <style>
            .profile-card { border: 1px solid #d2e3f1; border-radius: 14px; background: #fff; margin-top: 14px; overflow: hidden; box-shadow: 0 4px 14px rgba(18,72,110,.06); max-width: 640px; }
            .profile-card-header { padding: 16px 18px; border-bottom: 1px solid #e8f1f8; background: #fafdff; }
            .profile-card-header h2 { margin: 0; font-size: 18px; font-weight: 800; color: #10344c; }
            .profile-card-header p { margin: 4px 0 0; font-size: 13px; color: #355c77; }
            .profile-card-body { padding: 18px; }
            .form-group { margin-bottom: 14px; }
            .form-group label { display: block; font-size: 13px; font-weight: 700; color: #1d4f73; margin-bottom: 5px; }
            .form-group input { width: 100%; box-sizing: border-box; }
            .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
            .form-error { font-size: 12px; color: #b42318; margin-top: 3px; }
            .alert-success { background: #d9f6e3; color: #0c5f28; border: 1px solid #a3e4b8; border-radius: 8px; padding: 10px 14px; margin-bottom: 14px; font-size: 13px; font-weight: 600; }
            @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
        </style>

        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
            <a href="{{ url()->previous() }}" class="btn btn-secondary" style="font-size:13px; padding:6px 12px;">← Wstecz</a>
            <h1 style="margin:0; font-size:22px; font-weight:800; color:#10344c;">Ustawienia profilu</h1>
        </div>

        <div class="profile-card">
            <div class="profile-card-header">
                <h2>Dane osobowe</h2>
                <p>Zaktualizuj swoje imię, nazwisko i dane kontaktowe.</p>
            </div>
            <div class="profile-card-body">
                @if(session('status'))
                    <div class="alert-success">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">Imię</label>
                            <input type="text" id="first_name" name="first_name"
                                value="{{ old('first_name', $user->first_name) }}" autocomplete="given-name">
                            @error('first_name')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="last_name">Nazwisko</label>
                            <input type="text" id="last_name" name="last_name"
                                value="{{ old('last_name', $user->last_name) }}" autocomplete="family-name">
                            @error('last_name')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Pełna nazwa (wyświetlana) <span style="color:#b42318">*</span></label>
                            <input type="text" id="name" name="name"
                                value="{{ old('name', $user->name) }}" required autocomplete="name">
                            @error('name')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="short_name">Skrót (inicjały / nick)</label>
                            <input type="text" id="short_name" name="short_name"
                                value="{{ old('short_name', $user->short_name) }}" maxlength="20" autocomplete="off">
                            @error('short_name')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Adres e-mail <span style="color:#b42318">*</span></label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email', $user->email) }}" required autocomplete="email">
                            @error('email')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefon</label>
                            <input type="tel" id="phone" name="phone"
                                value="{{ old('phone', $user->phone) }}" autocomplete="tel">
                            @error('phone')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div style="display:flex; gap:10px; margin-top:6px;">
                        <button type="submit" class="btn">Zapisz zmiany</button>
                        <a href="{{ route('profile.password') }}" class="btn btn-secondary">Zmień hasło →</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
