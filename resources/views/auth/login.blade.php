<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logowanie | ENESA</title>
    <link rel="icon" type="image/png" href="/logo.png">
    <link rel="shortcut icon" type="image/png" href="/logo.png">
    <link rel="apple-touch-icon" href="/logo.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@400;600;700&family=Manrope:wght@400;500;600;700&display=swap');
        :root { --green-deep:#1A4D3A; --green-primary:#2E7D5C; --green-light:#A4C2A8; --green-bg:#E7EEE5; --paper:#F5EFE0; --paper-deep:#EBE3D0; --paper-soft:#FAF5E8; --gold:#A87F2A; --ink:#1A1612; --ink-mute:#76695A; }
        body { margin:0; font-family:'Manrope',sans-serif; min-height:100vh; display:grid; place-items:center; background:var(--green-bg); }
        .card { width:min(430px, 92vw); background:var(--paper-soft); border:1px solid var(--paper-deep); border-radius:8px; padding:22px; box-shadow:0 8px 24px rgba(26,77,58,.12); }
        .brand { display:flex; align-items:center; gap:10px; margin-bottom:16px; }
        .logo { width:42px; height:42px; border-radius:12px; overflow:hidden; display:grid; place-items:center; }
        h1 { margin:0 0 14px; font-size:24px; font-family:'Fraunces',serif; color:var(--green-deep); font-weight:600; }
        label { display:block; margin:12px 0 6px; font-weight:600; font-size:14px; color:var(--ink); }
        input { width:100%; box-sizing:border-box; padding:10px; border-radius:5px; border:1px solid var(--paper-deep); font-family:'Manrope',sans-serif; background:white; }
        input:focus { outline:none; border-color:var(--green-primary); box-shadow:0 0 0 2px rgba(46,125,92,.12); }
        .row { display:flex; justify-content:flex-start; align-items:center; margin-top:10px; font-size:14px; }
        button { margin-top:14px; width:100%; border:0; border-radius:5px; padding:11px; color:var(--paper); font-weight:700; background:var(--green-primary); cursor:pointer; font-family:'Manrope',sans-serif; font-size:15px; }
        button:hover { background:var(--green-deep); }
        .err { margin-top:10px; padding:9px; background:#ffe6e6; color:#9f1f1f; border:1px solid #ffc9c9; border-radius:5px; }
        .hint { margin-top:10px; font-size:12px; color:var(--ink-mute); }
    </style>
</head>
<body>
    <form class="card" method="POST" action="{{ route('login.store', [], false) }}">
        @csrf
        <div class="brand">
            <div class="logo"><img src="/logo.png" alt="ENESA" style="width:42px;height:42px;object-fit:cover;"></div>
            <div>
                <strong>ENESA</strong>
                <div style="font-size:12px;color:var(--ink-mute);">Panel dostępu</div>
            </div>
        </div>

        <h1>Logowanie</h1>

        @if ($errors->any())
            <div class="err">{{ $errors->first() }}</div>
        @endif

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>

        <label for="password">Haslo</label>
        <input id="password" name="password" type="password" required>

        <div class="row">
            <label style="margin:0; display:flex; align-items:center; gap:6px; white-space:nowrap;"><input type="checkbox" name="remember"> Zapamietaj mnie</label>
        </div>

        <button type="submit">Zaloguj</button>

    </form>
</body>
</html>
