<x-layouts.app>
    <section class="panel" style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 4px; font-size:20px; font-weight:800; color:#0f2330;">Diagnostyka audytów (Railway)</h2>
            <p class="muted" style="margin:0; font-size:13px;">
                Raport sprawdza środowisko, bazę danych, migracje, cache, storage i symulację zapisu rodzaju audytu.
            </p>
        </div>
        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <a href="{{ route('audits.settings') }}" class="btn" style="text-decoration:none;">Powrót do ustawień audytów</a>
            <a href="{{ route('audits.diagnostics') }}" class="btn" style="text-decoration:none; background:#0e89d8;">Odśwież testy</a>
        </div>
    </section>

    <section class="panel" style="margin-top:12px; display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
        <span style="font-size:13px; color:#4c6373;">Wygenerowano: <strong>{{ $generatedAt->format('Y-m-d H:i:s') }}</strong></span>
        <span style="font-size:13px; color:#0b7a30; background:#e9f9ef; border:1px solid #b6ebc8; border-radius:999px; padding:4px 10px;">
            OK: {{ $okCount }}
        </span>
        <span style="font-size:13px; color:#9f1f1f; background:#fff0f0; border:1px solid #ffc9c9; border-radius:999px; padding:4px 10px;">
            Błędy: {{ $failedCount }}
        </span>
    </section>

    @foreach($groupedChecks as $group => $groupItems)
        <section class="panel" style="margin-top:12px;">
            <h3 style="margin:0 0 10px; font-size:16px; font-weight:700; color:#0f2330;">{{ $group }}</h3>

            <table>
                <thead>
                <tr>
                    <th style="width:220px;">Test</th>
                    <th style="width:90px;">Status</th>
                    <th>Szczegóły</th>
                </tr>
                </thead>
                <tbody>
                @foreach($groupItems as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>
                            @if($item['ok'])
                                <span style="display:inline-block; font-size:11px; font-weight:700; letter-spacing:.4px; text-transform:uppercase; color:#0b7a30; background:#e9f9ef; border:1px solid #b6ebc8; border-radius:999px; padding:2px 8px;">OK</span>
                            @else
                                <span style="display:inline-block; font-size:11px; font-weight:700; letter-spacing:.4px; text-transform:uppercase; color:#9f1f1f; background:#fff0f0; border:1px solid #ffc9c9; border-radius:999px; padding:2px 8px;">FAIL</span>
                            @endif
                        </td>
                        <td style="font-family:ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size:12px; color:#355468;">
                            {{ $item['details'] !== '' ? $item['details'] : '—' }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>
    @endforeach

    @if($failedCount > 0)
        <section class="panel" style="margin-top:12px; border-color:#ffc9c9; background:#fff7f7;">
            <strong style="display:block; margin-bottom:6px; color:#9f1f1f;">Wykryto problemy, które mogą powodować błąd 500.</strong>
            <p style="margin:0; color:#5f2830; font-size:13px;">
                Najpierw napraw wszystkie pozycje z oznaczeniem FAIL, potem ponownie uruchom testy przyciskiem „Odśwież testy”.
            </p>
        </section>
    @endif
</x-layouts.app>
