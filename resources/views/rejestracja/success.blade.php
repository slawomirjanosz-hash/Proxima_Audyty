<x-layouts.app>
    <div style="display:grid; place-items:center; min-height:calc(100vh - 120px);">
        <div style="background:var(--paper-soft); border:1px solid var(--paper-deep); border-radius:16px; padding:48px 52px; max-width:540px; width:100%; box-shadow:0 4px 18px rgba(26,77,58,.07); text-align:center;">
            <div style="font-size:56px; margin-bottom:12px;">✅</div>
            <h1 style="margin:0 0 10px; font-family:var(--serif); color:var(--green-deep);">Wniosek wysłany!</h1>
            <p style="color:var(--ink-mute); font-size:15px; line-height:1.6; margin:0 0 24px;">
                Wniosek rejestracyjny firmy<br>
                <strong style="color:var(--ink);">{{ $name }}</strong><br>
                został pomyślnie złożony. Administrator systemu ENESA rozpatrzy go i skontaktuje się z Tobą.
            </p>
            <a href="{{ route('home') }}" style="display:inline-flex; align-items:center; gap:8px; padding:12px 24px; background:var(--green-primary); color:var(--paper); border-radius:8px; text-decoration:none; font-weight:700; font-size:15px;">
                ← Wróć na stronę główną
            </a>
        </div>
    </div>
</x-layouts.app>
