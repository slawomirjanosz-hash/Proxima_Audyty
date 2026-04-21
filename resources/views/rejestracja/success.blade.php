<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ENESA | Wniosek przyjęty</title>
    <link rel="icon" type="image/png" href="/logo.png">
    <style>
        :root { --menu-grad: linear-gradient(130deg, #1ba84a 0%, #0e89d8 100%); }
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            background:
                radial-gradient(circle at 86% 12%, rgba(14, 137, 216, 0.16), transparent 30%),
                radial-gradient(circle at 12% 84%, rgba(27, 168, 74, 0.14), transparent 34%),
                linear-gradient(165deg, #eff8f0 0%, #e7f1fb 100%);
            display: grid; place-items: center; padding: 20px;
        }
        .card {
            background: #fff;
            border: 1px solid #d5e0ea;
            border-radius: 20px;
            padding: 48px 52px;
            max-width: 540px;
            width: 100%;
            box-shadow: 0 20px 50px rgba(14,55,85,.1);
            text-align: center;
        }
        .icon { font-size: 56px; margin-bottom: 12px; }
        h1 {
            margin: 0 0 10px;
            font-size: 26px;
            font-weight: 800;
            background: linear-gradient(90deg, #1ba84a, #0e89d8);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; color: transparent;
        }
        p { color: #4c6373; font-size: 15px; line-height: 1.6; margin: 0 0 24px; }
        .company-name { font-weight: 700; color: #0f2330; }
        .back-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 24px;
            background: var(--menu-grad);
            color: #fff;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
        }
        .back-btn:hover { opacity: .88; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✅</div>
        <h1>Wniosek wysłany!</h1>
        <p>
            Wniosek rejestracyjny firmy<br>
            <span class="company-name">{{ $name }}</span><br>
            został pomyślnie złożony. Administrator systemu ENESA rozpatrzy go i skontaktuje się z Tobą.
        </p>
        <a href="{{ route('home') }}" class="back-btn">← Wróć na stronę główną</a>
    </div>
</body>
</html>
