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
        body { margin:0; font-family: "Segoe UI", Tahoma, Arial, sans-serif; min-height:100vh; display:grid; place-items:center; background: linear-gradient(150deg, #e9f8ee 0%, #e8f2fb 100%); }
        .card { width:min(430px, 92vw); background:#fff; border:1px solid #d7e6f0; border-radius:16px; padding:22px; box-shadow:0 14px 28px rgba(13,58,90,.08); }
        .brand { display:flex; align-items:center; gap:10px; margin-bottom:16px; }
        .logo { width:42px; height:42px; border-radius:12px; overflow:hidden; display:grid; place-items:center; }
        h1 { margin:0 0 14px; font-size:24px; }
        label { display:block; margin:12px 0 6px; font-weight:600; font-size:14px; }
        input { width:100%; box-sizing:border-box; padding:10px; border-radius:9px; border:1px solid #c9d7e3; }
        .row { display:flex; justify-content:flex-start; align-items:center; margin-top:10px; font-size:14px; }
        button { margin-top:14px; width:100%; border:0; border-radius:10px; padding:11px; color:#fff; font-weight:700; background:linear-gradient(130deg, #1ba84a, #0e89d8); cursor:pointer; }
        .err { margin-top:10px; padding:9px; background:#ffe6e6; color:#9f1f1f; border:1px solid #ffc9c9; border-radius:8px; }
        .hint { margin-top:10px; font-size:12px; color:#4f6675; }
    </style>
</head>
<body>
    <form class="card" method="POST" action="{{ route('login.store', [], false) }}">
        @csrf
        <div class="brand">
            <div class="logo"><img src="/logo.png" alt="ENESA" style="width:42px;height:42px;object-fit:cover;"></div>
            <div>
                <strong>ENESA</strong>
                <div style="font-size:12px;color:#4f6675;">Panel dostepu</div>
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
