<x-layouts.app>
    <section class="panel">
        <style>
            .profile-card { border: 1px solid #d2e3f1; border-radius: 14px; background: #fff; margin-top: 14px; overflow: hidden; box-shadow: 0 4px 14px rgba(18,72,110,.06); max-width: 480px; }
            .profile-card-header { padding: 16px 18px; border-bottom: 1px solid #e8f1f8; background: #fafdff; }
            .profile-card-header h2 { margin: 0; font-size: 18px; font-weight: 800; color: #10344c; }
            .profile-card-header p { margin: 4px 0 0; font-size: 13px; color: #355c77; }
            .profile-card-body { padding: 18px; }
            .form-group { margin-bottom: 14px; }
            .form-group label { display: block; font-size: 13px; font-weight: 700; color: #1d4f73; margin-bottom: 5px; }
            .form-group input { width: 100%; box-sizing: border-box; }
            .form-error { font-size: 12px; color: #b42318; margin-top: 3px; }
            .alert-success { background: #d9f6e3; color: #0c5f28; border: 1px solid #a3e4b8; border-radius: 8px; padding: 10px 14px; margin-bottom: 14px; font-size: 13px; font-weight: 600; }
            .password-hint { font-size: 12px; color: #6b8294; margin-top: 3px; }
        </style>

        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
            <a href="{{ route('profile.edit') }}" class="btn btn-secondary" style="font-size:13px; padding:6px 12px;">← Profil</a>
            <h1 style="margin:0; font-size:22px; font-weight:800; color:#10344c;">Zmiana hasła</h1>
        </div>

        <div class="profile-card">
            <div class="profile-card-header">
                <h2>Nowe hasło</h2>
                <p>Upewnij się, że hasło ma co najmniej 8 znaków.</p>
            </div>
            <div class="profile-card-body">
                @if(session('status'))
                    <div class="alert-success">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf

                    <div class="form-group">
                        <label for="current_password">Aktualne hasło <span style="color:#b42318">*</span></label>
                        <input type="password" id="current_password" name="current_password"
                            required autocomplete="current-password">
                        @error('current_password')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Nowe hasło <span style="color:#b42318">*</span></label>
                        <input type="password" id="password" name="password"
                            required autocomplete="new-password">
                        <div class="password-hint">Minimum 8 znaków.</div>
                        @error('password')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Powtórz nowe hasło <span style="color:#b42318">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            required autocomplete="new-password">
                        @error('password_confirmation')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn">Zapisz nowe hasło</button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
