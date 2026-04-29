<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Diagnostyka — ENESA</title>
<style>
    * { box-sizing: border-box; }
    body { font-family: "Segoe UI", Arial, sans-serif; background: #f2f6fb; color: #0f2330; margin: 0; padding: 20px; }
    h1  { margin: 0 0 4px; font-size: 22px; }
    h2  { font-size: 15px; font-weight: 800; margin: 20px 0 8px; color: #1d4f73; border-bottom: 2px solid #d2e3f1; padding-bottom: 4px; }
    .card { background: #fff; border: 1px solid #d5e0ea; border-radius: 12px; padding: 16px 18px; margin-bottom: 14px; }
    .badge { display: inline-block; border-radius: 6px; padding: 2px 9px; font-size: 12px; font-weight: 700; }
    .ok   { background: #d4edda; color: #155724; }
    .fail { background: #f8d7da; color: #721c24; }
    .warn { background: #fff3cd; color: #856404; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th, td { padding: 7px 10px; text-align: left; border-bottom: 1px solid #e8f0f7; }
    th { font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: #6b8294; }
    tr:last-child td { border-bottom: none; }
    pre { background: #1a2634; color: #a8d8a8; border-radius: 8px; padding: 12px 14px; font-size: 12px; overflow-x: auto; max-height: 400px; overflow-y: auto; white-space: pre-wrap; word-break: break-all; }
    .btn { display: inline-block; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-size: 13px; font-weight: 700; margin-right: 8px; text-decoration: none; }
    .btn-primary { background: #0e89d8; color: #fff; }
    .btn-danger  { background: #dc3545; color: #fff; }
    .btn-secondary { background: #e0eaf5; color: #1d4f73; }
    .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .info-row { display: flex; justify-content: space-between; align-items: baseline; border-bottom: 1px solid #edf3f8; padding: 5px 0; font-size: 13px; }
    .info-row:last-child { border-bottom: none; }
    .info-row .key  { color: #4c6373; font-weight: 600; }
    .info-row .val  { font-weight: 700; font-family: monospace; }
    .status-msg { padding: 10px 14px; border-radius: 8px; margin-bottom: 12px; font-size: 13px; font-weight: 600; }
    .status-msg.ok   { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .status-msg.fail { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    @media (max-width: 640px) { .grid2 { grid-template-columns: 1fr; } }
</style>
</head>
<body>

<div style="max-width:1000px; margin:0 auto;">

<a href="/" style="font-size:13px; color:#0e89d8; text-decoration:none;">← Strona główna</a>
@auth
    <a href="{{ route('settings.index') }}" style="font-size:13px; color:#0e89d8; text-decoration:none; margin-left:12px;">⚙️ Ustawienia</a>
@endauth
<h1 style="margin-top:14px;">🔍 Diagnostyka serwera ENESA</h1>
<p style="color:#4c6373; font-size:13px; margin:0 0 18px;">Strona dostępna bez logowania. Uruchomiona: <strong>{{ now()->format('Y-m-d H:i:s') }}</strong></p>

@if(session('migrate_output'))
    <div class="status-msg ok">
        <strong>Migracja wykonana:</strong><br>
        <pre style="margin:8px 0 0; background:#1a2634; color:#a8d8a8;">{{ session('migrate_output') }}</pre>
    </div>
@endif

@if(session('cache_output'))
    <div class="status-msg ok">
        <strong>Cache wyczyszczony:</strong><br>
        <pre style="margin:8px 0 0; background:#1a2634; color:#a8d8a8;">{{ session('cache_output') }}</pre>
    </div>
@endif

{{-- ═══ SYSTEM INFO ═══ --}}
<div class="card">
    <h2 style="margin-top:0;">⚙️ Informacje o systemie</h2>
    <div class="grid2">
        <div>
            @foreach($sysInfo as $k => $v)
                <div class="info-row">
                    <span class="key">{{ $k }}</span>
                    <span class="val">{{ $v }}</span>
                </div>
            @endforeach
        </div>
        <div>
            <div class="info-row"><span class="key">Czas serwera</span><span class="val">{{ now()->toIso8601String() }}</span></div>
            <div class="info-row"><span class="key">Strefa czasowa</span><span class="val">{{ config('app.timezone') }}</span></div>
            <div class="info-row"><span class="key">APP_URL</span><span class="val">{{ config('app.url') }}</span></div>
            <div class="info-row"><span class="key">Debug mode</span>
                <span class="val"><span class="badge {{ config('app.debug') ? 'fail' : 'ok' }}">{{ config('app.debug') ? 'ON (niebezpieczne!)' : 'OFF (produkcja)' }}</span></span>
            </div>
        </div>
    </div>
</div>

{{-- ═══ DB + TABLES ═══ --}}
<div class="card">
    <h2 style="margin-top:0;">🗄️ Baza danych</h2>

    <div class="info-row" style="margin-bottom:10px;">
        <span class="key">Połączenie z DB</span>
        <span>
            @if($dbOk)
                <span class="badge ok">✓ OK ({{ $dbDriver }})</span>
            @else
                <span class="badge fail">✗ BŁĄD: {{ $dbError }}</span>
            @endif
        </span>
    </div>

    @php
        $missing = array_filter($tableStatus, fn($s) => !$s['exists']);
        $present = array_filter($tableStatus, fn($s) => $s['exists']);
    @endphp

    @if(count($missing) > 0)
        <div style="background:#fff3cd; border:1px solid #ffc107; border-radius:8px; padding:10px 14px; margin-bottom:12px; font-size:13px; color:#856404;">
            <strong>⚠️ Brakujące tabele ({{ count($missing) }}):</strong>
            {{ implode(', ', array_keys($missing)) }}<br>
            <span style="font-size:12px;">Prawdopodobnie migracje nie zostały uruchomione na tym środowisku.</span>
        </div>
    @endif

    <table>
        <thead>
            <tr><th>Tabela</th><th>Status</th></tr>
        </thead>
        <tbody>
            @foreach($tableStatus as $table => $status)
            <tr>
                <td><code>{{ $table }}</code></td>
                <td>
                    @if($status['exists'])
                        <span class="badge ok">✓ istnieje</span>
                    @elseif($status['error'])
                        <span class="badge fail">✗ błąd: {{ $status['error'] }}</span>
                    @else
                        <span class="badge fail">✗ brak tabeli</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($pendingCount > 0)
        <div style="margin-top:12px; background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; padding:10px 14px; color:#721c24; font-size:13px;">
            <strong>❌ {{ $pendingCount }} migracja(i) oczekujących na wykonanie!</strong>
        </div>
    @else
        <div style="margin-top:12px; background:#d4edda; border:1px solid #c3e6cb; border-radius:8px; padding:10px 14px; color:#155724; font-size:13px;">
            <strong>✓ Wszystkie migracje wykonane.</strong>
        </div>
    @endif

    @auth
        @if(auth()->user()->canManageEverything())
            <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
                <form method="POST" action="{{ route('diagnostics.migrate') }}" onsubmit="return confirm('Uruchomić php artisan migrate --force?');">
                    @csrf
                    <button type="submit" class="btn btn-primary">▶ Uruchom migrate --force</button>
                </form>
                <form method="POST" action="{{ route('diagnostics.cache-clear') }}" onsubmit="return confirm('Wyczyścić wszystkie cache?');">
                    @csrf
                    <button type="submit" class="btn btn-secondary">🗑 Wyczyść cache</button>
                </form>
            </div>
        @endif
    @endauth

    @guest
        <p style="font-size:12px; color:#6b8294; margin-top:10px;">Zaloguj się jako admin, aby uruchomić migracje i czyścić cache z tego panelu.</p>
    @endguest
</div>

{{-- ═══ MIGRATION STATUS ═══ --}}
<div class="card">
    <h2 style="margin-top:0;">📋 Status migracji</h2>
    @if($migrationError)
        <div class="status-msg fail">Błąd pobierania statusu migracji: {{ $migrationError }}</div>
    @else
        <pre>{{ $migrationOutput }}</pre>
    @endif
</div>

{{-- ═══ RECENT ERRORS ═══ --}}
<div class="card">
    <h2 style="margin-top:0;">🚨 Ostatnie błędy w logach</h2>
    @if(empty($recentErrors))
        <div class="status-msg ok">Brak błędów ERROR/CRITICAL w ostatnich logach.</div>
    @else
        <p style="font-size:12px; color:#4c6373; margin:0 0 8px;">Ostatnie {{ count($recentErrors) }} wpisów ERROR/CRITICAL (najnowsze pierwsze):</p>
        @foreach($recentErrors as $err)
            <div style="font-size:11px; font-family:monospace; background:#fefce8; border-left:3px solid #f59e0b; padding:5px 10px; margin-bottom:4px; border-radius:4px; word-break:break-all;">{{ $err }}</div>
        @endforeach
    @endif
</div>

{{-- ═══ AUDITS PROBE ═══ --}}
<div class="card" style="{{ ($auditsProbeError) ? 'border-color:#f5c6cb;' : '' }}">
    <h2 style="margin-top:0;">📋 Test Audytów – wykonanie zapytań</h2>
    @if(!$dbOk)
        <div class="status-msg fail">Pominięto – brak połączenia z bazą danych.</div>
    @elseif(empty($auditsProbe))
        <div class="status-msg warn">Brak danych testu audytów.</div>
    @else
        @if($auditsProbeError)
            <div style="background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; padding:12px 14px; margin-bottom:12px; color:#721c24;">
                <strong>❌ Błąd Audyty na kroku: {{ $auditsProbeError['step'] }}</strong><br>
                <span style="font-size:13px; font-weight:700;">{{ $auditsProbeError['class'] }}</span><br>
                <span style="font-size:13px;">{{ $auditsProbeError['message'] }}</span><br>
                <span style="font-size:12px; color:#a94442;">{{ $auditsProbeError['file'] }}:{{ $auditsProbeError['line'] }}</span>
                <pre style="margin-top:8px; font-size:11px; max-height:200px; background:#2d1a1a; color:#f8a;">{{ $auditsProbeError['trace'] }}</pre>
            </div>
        @else
            <div class="status-msg ok" style="margin-bottom:10px;">✓ Wszystkie kroki Audytów przeszły pomyślnie.</div>
        @endif
        <table>
            <thead><tr><th>Test</th><th>Status</th><th>Wynik</th></tr></thead>
            <tbody>
                @foreach($auditsProbe as $probe)
                <tr>
                    <td style="font-family:monospace; font-size:12px;">{{ $probe['label'] }}</td>
                    <td><span class="badge {{ $probe['ok'] ? 'ok' : 'fail' }}">{{ $probe['ok'] ? '✓ OK' : '✗ BŁĄD' }}</span></td>
                    <td style="font-size:12px; {{ !$probe['ok'] ? 'color:#721c24; font-weight:700;' : '' }}">{{ $probe['detail'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ═══ CRM PROBE ═══ --}}
<div class="card" style="{{ $crmProbeError ? 'border-color:#f5c6cb;' : '' }}">
    <h2 style="margin-top:0;">🔬 Test CRM – wykonanie zapytań</h2>
    @if(!$dbOk)
        <div class="status-msg fail">Pominięto – brak połączenia z bazą danych.</div>
    @else
        @if($crmProbeError)
            <div style="background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; padding:12px 14px; margin-bottom:12px; color:#721c24;">
                <strong>❌ Błąd CRM na kroku: {{ $crmProbeError['step'] }}</strong><br>
                <span style="font-size:13px; font-weight:700;">{{ $crmProbeError['class'] }}</span><br>
                <span style="font-size:13px;">{{ $crmProbeError['message'] }}</span><br>
                <span style="font-size:12px; color:#a94442;">{{ $crmProbeError['file'] }}:{{ $crmProbeError['line'] }}</span>
                <pre style="margin-top:8px; font-size:11px; max-height:200px; background:#2d1a1a; color:#f8a;">{{ $crmProbeError['trace'] }}</pre>
            </div>
        @else
            <div class="status-msg ok" style="margin-bottom:10px;">✓ Wszystkie kroki CRM przeszły pomyślnie.</div>
        @endif
        <table>
            <thead><tr><th>Test</th><th>Status</th><th>Wynik</th></tr></thead>
            <tbody>
                @foreach($crmProbe as $probe)
                <tr>
                    <td style="font-family:monospace; font-size:12px;">{{ $probe['label'] }}</td>
                    <td><span class="badge {{ $probe['ok'] ? 'ok' : 'fail' }}">{{ $probe['ok'] ? '✓ OK' : '✗ BŁĄD' }}</span></td>
                    <td style="font-size:12px; {{ !$probe['ok'] ? 'color:#721c24; font-weight:700;' : '' }}">{{ $probe['detail'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ═══ CRITICAL COLUMNS ═══ --}}
@if(!empty($columnChecks))
<div class="card" style="border-color:#f5c6cb;">
    <h2 style="margin-top:0;">🗂️ Brakujące kolumny w tabelach</h2>
    <div style="background:#f8d7da; border-radius:8px; padding:10px 14px; margin-bottom:10px; font-size:13px; color:#721c24;">
        <strong>❌ Wykryto {{ count($columnChecks) }} brakujących kolumn!</strong>
        Prawdopodobna przyczyna błędu 500.
    </div>
    <table>
        <thead><tr><th>Tabela</th><th>Kolumna</th><th>Błąd</th></tr></thead>
        <tbody>
            @foreach($columnChecks as $cc)
            <tr>
                <td><code>{{ $cc['table'] }}</code></td>
                <td><code>{{ $cc['column'] }}</code></td>
                <td style="color:#721c24; font-size:12px;">{{ $cc['error'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- ═══ MAIL DIAGNOSTICS ═══ --}}
<div class="card" style="{{ $mailConfig['mailer'] === 'log' ? 'border-color:#f59e0b; background:#fffbeb;' : '' }}">
    <h2 style="margin-top:0;">📧 Konfiguracja i test e-mail</h2>

    @if($mailConfig['mailer'] === 'log')
    <div style="background:#fef3c7; border:2px solid #f59e0b; border-radius:8px; padding:12px 16px; margin-bottom:14px; color:#92400e; font-weight:700; font-size:13px;">
        ⚠ MAIL_MAILER = <code style="background:#fbbf24; padding:1px 6px; border-radius:4px;">log</code> — maile <strong>NIE są wysyłane</strong>! Trafiają tylko do pliku logów. Zmień zmienną na <code>smtp</code>.
    </div>
    @endif

    <table style="margin-bottom:16px;">
        <thead><tr><th>Zmienna</th><th>Wartość</th><th>Status</th></tr></thead>
        <tbody>
            <tr>
                <td><code>MAIL_MAILER</code></td>
                <td><code>{{ $mailConfig['mailer'] }}</code></td>
                <td>{!! $mailConfig['mailer'] === 'smtp' ? '<span style="color:#16a34a;">✅ OK</span>' : '<span style="color:#dc2626;">❌ Powinno być smtp</span>' !!}</td>
            </tr>
            <tr>
                <td><code>MAIL_HOST</code></td>
                <td><code>{{ $mailConfig['host'] }}</code></td>
                <td>{!! $mailConfig['host'] !== 'n/a' && $mailConfig['host'] !== '' ? '<span style="color:#16a34a;">✅ OK</span>' : '<span style="color:#dc2626;">❌ Brak</span>' !!}</td>
            </tr>
            <tr>
                <td><code>MAIL_PORT</code></td>
                <td><code>{{ $mailConfig['port'] }}</code></td>
                <td>{!! in_array((string)$mailConfig['port'], ['465','587','25']) ? '<span style="color:#16a34a;">✅ OK</span>' : '<span style="color:#dc2626;">❌ Nieznany port</span>' !!}</td>
            </tr>
            <tr>
                <td><code>MAIL_SCHEME</code></td>
                <td><code>{{ $mailConfig['scheme'] }}</code></td>
                <td><span style="color:#4c6373;">ℹ {{ $mailConfig['port'] == 465 ? 'port 465 wymaga smtps/ssl' : 'port 587 = tls' }}</span></td>
            </tr>
            <tr>
                <td><code>MAIL_USERNAME</code></td>
                <td><code>{{ $mailConfig['username'] }}</code></td>
                <td>{!! $mailConfig['username'] !== 'n/a' ? '<span style="color:#16a34a;">✅ Ustawiony</span>' : '<span style="color:#dc2626;">❌ Brak</span>' !!}</td>
            </tr>
            <tr>
                <td><code>MAIL_PASSWORD</code></td>
                <td><code>{{ $mailConfig['password_set'] ? '••••••••' : '(brak)' }}</code></td>
                <td>{!! $mailConfig['password_set'] ? '<span style="color:#16a34a;">✅ Ustawione</span>' : '<span style="color:#dc2626;">❌ Brak hasła</span>' !!}</td>
            </tr>
            <tr>
                <td><code>MAIL_FROM_ADDRESS</code></td>
                <td><code>{{ $mailConfig['from_address'] }}</code></td>
                <td>{!! filter_var($mailConfig['from_address'], FILTER_VALIDATE_EMAIL) ? '<span style="color:#16a34a;">✅ OK</span>' : '<span style="color:#dc2626;">❌ Nieprawidłowy e-mail</span>' !!}</td>
            </tr>
        </tbody>
    </table>

    {{-- Live test form --}}
    <div style="background:#f3f8fc; border:1px solid #c9dcea; border-radius:10px; padding:16px 18px;">
        <h3 style="margin:0 0 10px; font-size:15px; color:#0f2330;">🔬 Test połączenia SMTP + wysyłka</h3>
        <p style="font-size:12px; color:#4c6373; margin:0 0 12px;">Wpisz adres e-mail aby wykonać pełny test: sprawdzenie konfiguracji → połączenie TCP → wysyłka wiadomości testowej.</p>
        <div style="display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap;">
            <div>
                <label style="font-size:12px; font-weight:700; color:#1d3a50; display:block; margin-bottom:4px;">Adres docelowy testu</label>
                <input type="email" id="mail-test-to" placeholder="twoj@email.com"
                    style="padding:8px 12px; border:1px solid #c8d8e6; border-radius:8px; font-size:13px; min-width:240px;">
            </div>
            <button onclick="runMailTest()" id="mail-test-btn"
                style="padding:9px 18px; background:#0e89d8; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer;">
                ▶ Uruchom test
            </button>
        </div>
        <div id="mail-test-results" style="margin-top:14px; display:none;">
            <div id="mail-test-steps"></div>
        </div>
    </div>
</div>

<script>
function runMailTest() {
    const to = document.getElementById('mail-test-to').value.trim();
    const btn = document.getElementById('mail-test-btn');
    const results = document.getElementById('mail-test-results');
    const steps = document.getElementById('mail-test-steps');

    btn.disabled = true;
    btn.textContent = '⏳ Testuję…';
    results.style.display = 'none';
    steps.innerHTML = '';

    fetch('{{ route('diagnostics.test-mail') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '{{ csrf_token() }}'
        },
        body: JSON.stringify({ to_email: to })
    })
    .then(r => r.json())
    .then(data => {
        steps.innerHTML = data.steps.map(s => `
            <div style="display:flex; gap:10px; align-items:flex-start; padding:8px 10px; border-radius:8px; background:${s.ok ? '#f0fdf4' : '#fef2f2'}; border:1px solid ${s.ok ? '#bbf7d0' : '#fca5a5'}; margin-bottom:6px;">
                <span style="font-size:16px; flex-shrink:0;">${s.ok ? '✅' : '❌'}</span>
                <div>
                    <div style="font-size:13px; font-weight:700; color:#0f2330;">${s.label}</div>
                    <div style="font-size:12px; color:${s.ok ? '#15803d' : '#dc2626'}; margin-top:2px;">${s.detail}</div>
                </div>
            </div>
        `).join('');
        results.style.display = 'block';
        btn.disabled = false;
        btn.textContent = '▶ Uruchom test';
    })
    .catch(err => {
        steps.innerHTML = `<div style="color:#dc2626; font-size:13px;">❌ Błąd żądania: ${err.message}</div>`;
        results.style.display = 'block';
        btn.disabled = false;
        btn.textContent = '▶ Uruchom test';
    });
}
</script>

{{-- ═══ QUICK LINKS ═══ --}}
<div class="card">
    <h2 style="margin-top:0;">🔗 Szybkie linki testowe</h2>
    <div style="display:flex; flex-wrap:wrap; gap:8px;">
        <a href="{{ route('home') }}" class="btn btn-secondary">Strona główna</a>
        <a href="{{ route('information.index') }}" class="btn btn-secondary">Informacje</a>
        @auth
            <a href="{{ route('strefa-klienta') }}" class="btn btn-secondary">Strefa klienta</a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Dashboard</a>
            @if(auth()->user()->canManageEverything())
                <a href="{{ route('settings.index') }}" class="btn btn-secondary">Ustawienia</a>
            @endif
        @else
            <a href="{{ route('home', ['login' => 1]) }}" class="btn btn-primary">Zaloguj się</a>
        @endauth
    </div>
</div>

</div>
</body>
</html>
