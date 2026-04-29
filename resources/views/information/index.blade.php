<x-layouts.app>
    <section class="panel">
        <style>
            .info-structure-box { margin-top:12px; border:1px solid #d7e5f0; border-radius:12px; background:#f8fbff; padding:14px; }
            .info-share-header { display:grid; grid-template-columns:1fr auto 1fr; gap:10px; align-items:center; font-size:13px; color:#2c4e67; }
            .info-share-bar { height:20px; border-radius:999px; overflow:hidden; background:#e6eef5; margin-top:8px; display:flex; }
            .info-share-left { background:#0c9a45; color:#fff; font-weight:700; display:flex; align-items:center; padding:0 8px; white-space:nowrap; }
            .info-share-right { background:#9c1811; color:#fff; font-weight:700; display:flex; align-items:center; justify-content:flex-end; padding:0 8px; white-space:nowrap; }
            .info-sources-bars { margin-top:14px; display:grid; gap:10px; }
            .info-source-row { display:grid; grid-template-columns:220px 1fr 90px 80px; gap:10px; align-items:center; }
            .info-source-name { font-size:13px; font-weight:700; color:#2c4e67; }
            .info-source-track { height:16px; border-radius:999px; background:#e6eef5; overflow:hidden; }
            .info-source-fill { height:100%; border-radius:999px; background:#0e89d8; min-width:2px; }
            .info-source-share, .info-source-mwh { font-size:13px; color:#2c4e67; text-align:right; }
            @media (max-width: 900px) {
                .info-share-header { grid-template-columns:1fr; }
                .info-source-row { grid-template-columns:1fr; }
                .info-source-share, .info-source-mwh { text-align:left; }
            }
        </style>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:10px;">
            <div>
                <h1 style="margin:0;">Informacje</h1>
                <p class="muted" style="margin:4px 0 0;">Aktualna struktura generacji mocy i kalkulator energetyczny.</p>
            </div>
            <a href="{{ route('information.index') }}" class="btn-secondary" style="text-decoration:none; padding:8px 10px; border-radius:9px; background:#dbe9f5; color:#1d4f73;">Odśwież teraz</a>
        </div>

        <div class="ec-acc" id="acc-generation" style="border:1px solid #d7e5f0; border-radius:12px; overflow:hidden; margin-top:4px;">
            <button class="ec-acc-trigger" onclick="ecAccToggle('acc-generation')" style="background:#f4f8fd;">
                <div class="ec-acc-icon">⚡</div>
                <div class="ec-acc-text">
                    <strong>Aktualna struktura generacji mocy (KSE)</strong>
                    <span>Energetyczny Kompas · dane odświeżane automatycznie co 60 s</span>
                </div>
                <div class="ec-acc-chevron">▾</div>
            </button>
            <div class="ec-acc-body" style="padding:14px 16px 16px;">

        <div id="generation-status" class="muted" style="font-size:13px; margin-bottom:8px;">
            @if(!($generationData['ok'] ?? false))
                {{ $generationData['message'] ?? 'Brak danych.' }}
            @else
                Dane zaktualizowano: {{ $generationData['fetchedAt'] ?? '—' }}
            @endif
        </div>

        <div class="info-structure-box" id="generation-structure-box" style="margin-top:0;">
            <h2 style="margin:0 0 10px; color:#10344c;">{{ $generationData['title'] ?? 'Aktualna struktura generacji mocy' }}</h2>

            <div class="info-share-header">
                <div><strong>Źródła odnawialne</strong>: <span id="share-renewables">{{ $generationData['renewablesShare'] ?? '—' }}</span></div>
                <div style="text-align:center; font-weight:700;">vs</div>
                <div style="text-align:right;"><strong>Źródła konwencjonalne</strong>: <span id="share-conventional">{{ $generationData['conventionalShare'] ?? '—' }}</span></div>
            </div>

            <div class="info-share-bar">
                <div id="share-left" class="info-share-left" style="width: {{ $generationData['renewablesShare'] ?? '0%' }};">{{ $generationData['renewablesShare'] ?? '—' }}</div>
                <div id="share-right" class="info-share-right" style="width: {{ $generationData['conventionalShare'] ?? '0%' }};">{{ $generationData['conventionalShare'] ?? '—' }}</div>
            </div>

            <div id="sources-bars" class="info-sources-bars">
                @forelse(($generationData['sources'] ?? []) as $source)
                    @php($shareText = (string) ($source['share'] ?? '0%'))
                    @php($shareValue = is_numeric(str_replace('%', '', $shareText)) ? (float) str_replace('%', '', $shareText) : 0)
                    @php($sourceColor = $source['color'] ?? '#0e89d8')
                    <div class="info-source-row">
                        <div class="info-source-name">{{ $source['name'] ?? '—' }}</div>
                        <div class="info-source-track"><div class="info-source-fill" style="width: {{ $shareValue }}%; background: {{ $sourceColor }};"></div></div>
                        <div class="info-source-share">{{ $source['share'] ?? '—' }}</div>
                        <div class="info-source-mwh">{{ $source['mwh'] ?? '—' }} MWh</div>
                    </div>
                @empty
                    <div class="muted">Brak danych źródeł.</div>
                @endforelse
            </div>

            <div style="font-size:12px; color:#4c6373; margin-top:10px;">
                Aktualizacja źródła: <span id="published-at">{{ $generationData['publishedAt'] ?? '—' }}</span>
                •
                Źródło: <a id="source-link" href="{{ $generationData['sourceUrl'] ?? 'https://www.energetycznykompas.pl' }}" target="_blank" rel="noopener">energetycznykompas.pl</a>
            </div>
        </div>

            </div>{{-- /ec-acc-body (generation) --}}
        </div>{{-- /ec-acc#acc-generation --}}

        <script>
            (function () {
                const endpoint = '{{ route('information.pse-kse') }}';
                const status = document.getElementById('generation-status');
                const renewablesNode = document.getElementById('share-renewables');
                const conventionalNode = document.getElementById('share-conventional');
                const leftBar = document.getElementById('share-left');
                const rightBar = document.getElementById('share-right');
                const sourcesBars = document.getElementById('sources-bars');
                const sourceLink = document.getElementById('source-link');
                const publishedAt = document.getElementById('published-at');

                const updateView = (payload) => {
                    renewablesNode.textContent = payload?.renewablesShare ?? '—';
                    conventionalNode.textContent = payload?.conventionalShare ?? '—';

                    leftBar.style.width = payload?.renewablesShare ?? '0%';
                    leftBar.textContent = payload?.renewablesShare ?? '—';
                    rightBar.style.width = payload?.conventionalShare ?? '0%';
                    rightBar.textContent = payload?.conventionalShare ?? '—';

                    const sources = Array.isArray(payload?.sources) ? payload.sources : [];
                    const toShareNumber = (shareText) => {
                        const numeric = String(shareText ?? '0').replace('%', '').trim();
                        const parsed = Number(numeric);
                        return Number.isFinite(parsed) ? parsed : 0;
                    };

                    if (sources.length > 0 && sourcesBars) {
                        const sorted = [...sources].sort((a, b) => toShareNumber(b?.share) - toShareNumber(a?.share));

                        sourcesBars.innerHTML = sorted.map((source) => {
                            const shareText = source.share ?? '—';
                            const shareValue = toShareNumber(shareText);
                            const fillWidth = Math.max(0, Math.min(100, shareValue));
                            const color = source.color ?? '#0e89d8';

                            return '<div class="info-source-row">' +
                                '<div class="info-source-name">' + (source.name ?? '—') + '</div>' +
                                '<div class="info-source-track"><div class="info-source-fill" style="width:' + fillWidth + '%;background:' + color + ';"></div></div>' +
                                '<div class="info-source-share">' + shareText + '</div>' +
                                '<div class="info-source-mwh">' + (source.mwh ?? '—') + ' MWh</div>' +
                                '</div>';
                        }).join('');
                    }

                    if (payload?.sourceUrl) {
                        sourceLink.href = payload.sourceUrl;
                    }

                    if (publishedAt) {
                        publishedAt.textContent = payload?.publishedAt ?? '—';
                    }

                    if (payload?.ok) {
                        status.textContent = 'Dane zaktualizowano: ' + (payload.fetchedAt ?? '—');
                        return;
                    }

                    status.textContent = payload?.message ?? 'Nie udało się odświeżyć danych.';
                };

                const fetchSnapshot = async () => {
                    try {
                        const response = await fetch(endpoint, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('HTTP ' + response.status);
                        }

                        const payload = await response.json();
                        updateView(payload);
                    } catch (error) {
                        status.textContent = 'Błąd odświeżania danych Energetycznego Kompasu.';
                    }
                };

                setInterval(fetchSnapshot, 60 * 1000);
            })();
        </script>
    </section>

    {{-- ═══════════════════════════════════════════════
         KALKULATOR ENERGETYCZNY
    ═══════════════════════════════════════════════ --}}
    <section class="panel" id="energy-calc" style="padding-bottom:10px;">
        <style>
            /* ── Kalkulator – ogólne ── */
            .ec-page-header { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
            .ec-page-header .ec-bar { width:4px; height:30px; background:linear-gradient(180deg,#1ba84a,#0e89d8); border-radius:4px; flex-shrink:0; }
            .ec-page-header h2 { margin:0; font-size:21px; font-weight:800; color:#10344c; }
            .ec-page-header p { margin:3px 0 0; font-size:13px; color:#4c6373; }

            /* ── Accordion ── */
            .ec-acc { border:1px solid #d2e3f1; border-radius:14px; overflow:hidden; margin-top:12px; }
            .ec-acc-trigger { width:100%; background:#f4f8fd; border:none; border-radius:0; text-align:left; padding:13px 18px; cursor:pointer; display:flex; align-items:center; gap:12px; transition:background .15s; }
            .ec-acc-trigger:hover { background:#e8f0f9; }
            .ec-acc-icon { font-size:20px; flex-shrink:0; }
            .ec-acc-text { flex:1; }
            .ec-acc-text strong { font-size:15px; font-weight:800; color:#0f2330; display:block; }
            .ec-acc-text span { font-size:12px; color:#4c6373; }
            .ec-acc-chevron { font-size:16px; color:#6b8294; transition:transform .25s; flex-shrink:0; }
            .ec-acc.open .ec-acc-chevron { transform:rotate(180deg); }
            .ec-acc-body { display:none; padding:18px 18px 20px; border-top:1px solid #d2e3f1; }
            .ec-acc.open .ec-acc-body { display:block; }

            /* ── Przelicznik jednostek - dark panel ── */
            .ec-converter { background:linear-gradient(135deg,#0f1e30 0%,#163854 100%); border-radius:13px; padding:20px; box-shadow:0 6px 24px rgba(10,40,70,.18); }
            .ec-converter h3 { margin:0 0 14px; color:#fff; font-size:15px; font-weight:700; }
            .ec-units-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; }
            .ec-unit-card { background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.15); border-radius:12px; padding:14px 12px 12px; }
            .ec-unit-label { font-size:10px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:rgba(255,255,255,.5); margin-bottom:4px; }
            .ec-unit-sym { font-size:22px; font-weight:900; margin-bottom:8px; }
            .ec-unit-input { width:100%; box-sizing:border-box; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.18); border-radius:8px; color:#fff; padding:8px 10px; font-size:14px; font-weight:600; outline:none; transition:border-color .15s; }
            .ec-unit-input:focus { border-color:rgba(255,255,255,.45); background:rgba(255,255,255,.15); }
            .ec-unit-hint { font-size:10px; color:rgba(255,255,255,.35); margin-top:4px; }
            .ec-pln-box { margin-top:12px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:12px; padding:12px 16px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
            .ec-pln-label { font-size:12px; color:rgba(255,255,255,.5); margin-bottom:3px; }
            .ec-pln-value { font-size:24px; font-weight:800; color:#fbbf24; }
            .ec-price-meta { font-size:11px; color:rgba(255,255,255,.4); line-height:1.7; text-align:right; }
            .ec-price-meta span { color:rgba(255,255,255,.65); }

            /* ── Stałe energetyczne ── */
            .ec-const-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
            .ec-const-card { border-radius:13px; padding:14px 14px 12px; border:1px solid; }
            .ec-const-card h4 { margin:0 0 10px; font-size:13px; font-weight:800; letter-spacing:.4px; text-transform:uppercase; display:flex; align-items:center; gap:6px; }
            .ec-const-row { display:flex; justify-content:space-between; align-items:baseline; gap:8px; padding:4px 0; border-bottom:1px solid rgba(0,0,0,.05); font-size:13px; }
            .ec-const-row:last-child { border-bottom:none; padding-bottom:0; }
            .ec-const-row .name { color:#355468; font-weight:500; flex:1; }
            .ec-const-row .val { font-weight:800; font-size:13px; white-space:nowrap; }
            .ec-const-row .unit { font-size:11px; color:#6b8294; margin-left:3px; white-space:nowrap; }
            .ec-card-gas { background:#fffde7; border-color:#fdd835; }
            .ec-card-gas h4 { color:#b7780a; } .ec-card-gas .val { color:#c47c00; }
            .ec-card-coal { background:#f5f5f5; border-color:#9e9e9e; }
            .ec-card-coal h4 { color:#424242; } .ec-card-coal .val { color:#212121; }
            .ec-card-oil { background:#fce4ec; border-color:#f06292; }
            .ec-card-oil h4 { color:#880e4f; } .ec-card-oil .val { color:#ad1457; }
            .ec-card-bio { background:#e8f5e9; border-color:#66bb6a; }
            .ec-card-bio h4 { color:#1b5e20; } .ec-card-bio .val { color:#2e7d32; }
            .ec-card-elec { background:#e3f2fd; border-color:#42a5f5; }
            .ec-card-elec h4 { color:#0d47a1; } .ec-card-elec .val { color:#1565c0; }
            .ec-card-conv { background:#f3e5f5; border-color:#ab47bc; }
            .ec-card-conv h4 { color:#4a148c; } .ec-card-conv .val { color:#7b1fa2; }

            /* ── Kalkulator spalin / CO₂ – wspólne ── */
            .ec-flue-controls { display:grid; grid-template-columns:220px 220px 1fr; gap:14px; align-items:end; }
            .ec-flue-label { font-size:12px; font-weight:700; color:#1d4f73; margin-bottom:5px; }
            .ec-flue-select { width:100%; padding:9px 12px; border-radius:9px; border:1px solid #c9d7e3; font-size:14px; font-weight:600; color:#0f2330; background:#fff; }
            .ec-flue-input { width:100%; box-sizing:border-box; padding:9px 12px; border-radius:9px; border:1px solid #c9d7e3; font-size:14px; font-weight:600; color:#0f2330; }
            .ec-flue-results { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-top:14px; }
            .ec-flue-result-card { background:#fff; border:1px solid #d2e3f1; border-radius:10px; padding:12px 14px; text-align:center; }
            .ec-flue-result-label { font-size:10px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#6b8294; margin-bottom:6px; }
            .ec-flue-result-val { font-size:20px; font-weight:800; color:#10344c; }
            .ec-flue-result-unit { font-size:11px; color:#6b8294; margin-top:2px; }
            .ec-flue-note { font-size:12px; color:#4c6373; margin-top:12px; padding:10px 14px; background:#edf3f8; border-radius:8px; border-left:3px solid #0e89d8; }
            .ec-sub-title { font-size:13px; font-weight:700; color:#1d4f73; margin:18px 0 10px; padding-top:16px; border-top:2px dashed #c9d7e3; display:flex; align-items:center; gap:6px; }
            /* O2 scale bar */
            .ec-o2-bar { margin-top:10px; height:8px; border-radius:4px; background:linear-gradient(90deg,#22c55e 0%,#eab308 45%,#f97316 75%,#ef4444 100%); position:relative; }
            .ec-o2-thumb { position:absolute; top:-4px; width:16px; height:16px; border-radius:50%; background:#fff; border:2px solid #0e89d8; transform:translateX(-50%); transition:left .2s; }

            /* ── CO₂ badges ── */
            .ec-co2el-badge { display:inline-flex; align-items:center; gap:6px; background:#e6f4ea; border:1px solid #a8d5b5; border-radius:8px; padding:7px 12px; font-size:12px; color:#1a5c2e; }
            .ec-co2el-badge strong { font-size:17px; color:#1a5c2e; }

            @media(max-width:960px) {
                .ec-units-grid { grid-template-columns:1fr 1fr; }
                .ec-const-grid { grid-template-columns:1fr 1fr; }
                .ec-flue-controls { grid-template-columns:1fr 1fr; }
                .ec-flue-results { grid-template-columns:1fr 1fr; }
            }
            @media(max-width:600px) {
                .ec-units-grid { grid-template-columns:1fr; }
                .ec-const-grid { grid-template-columns:1fr; }
                .ec-flue-controls { grid-template-columns:1fr; }
            }
        </style>

        <div class="ec-page-header">
            <div class="ec-bar"></div>
            <div>
                <h2>⚡ Kalkulator energetyczny</h2>
                <p>Przeliczniki jednostek, stałe energetyczne, kalkulator spalin i emisji CO₂</p>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             SEKCJA 1 – PRZELICZNIK JEDNOSTEK ENERGII
        ══════════════════════════════════════════════════════ --}}
        <div class="ec-acc" id="acc-units">
            <button class="ec-acc-trigger" onclick="ecAccToggle('acc-units')">
                <div class="ec-acc-icon">🔄</div>
                <div class="ec-acc-text">
                    <strong>Przelicznik jednostek energii</strong>
                    <span>GJ · kWh · MWh · TOE z szacowaniem wartości w PLN</span>
                </div>
                <div class="ec-acc-chevron">▾</div>
            </button>
            <div class="ec-acc-body">
                <div class="ec-converter">
                    <h3>🔄 Przelicznik jednostek energii</h3>
                    <div class="ec-units-grid">
                        <div class="ec-unit-card">
                            <div class="ec-unit-label">Gigadżule</div>
                            <div class="ec-unit-sym" style="color:#22d3ee;">GJ</div>
                            <input type="number" id="ec-gj" class="ec-unit-input" placeholder="wpisz wartość" step="any" min="0" oninput="ecConvert('gj',this.value)">
                            <div class="ec-unit-hint">1 GJ = 277.778 kWh = 0.2778 MWh</div>
                        </div>
                        <div class="ec-unit-card">
                            <div class="ec-unit-label">Kilowatogodziny</div>
                            <div class="ec-unit-sym" style="color:#4ade80;">kWh</div>
                            <input type="number" id="ec-kwh" class="ec-unit-input" placeholder="wpisz wartość" step="any" min="0" oninput="ecConvert('kwh',this.value)">
                            <div class="ec-unit-hint">1 kWh = 0.0036 GJ = 0.001 MWh</div>
                        </div>
                        <div class="ec-unit-card">
                            <div class="ec-unit-label">Megawatogodziny</div>
                            <div class="ec-unit-sym" style="color:#f59e0b;">MWh</div>
                            <input type="number" id="ec-mwh" class="ec-unit-input" placeholder="wpisz wartość" step="any" min="0" oninput="ecConvert('mwh',this.value)">
                            <div class="ec-unit-hint">1 MWh = 3.6 GJ = 1 000 kWh</div>
                        </div>
                        <div class="ec-unit-card">
                            <div class="ec-unit-label">Tony ol. ekwiwalen.</div>
                            <div class="ec-unit-sym" style="color:#f87171;">TOE</div>
                            <input type="number" id="ec-toe" class="ec-unit-input" placeholder="wpisz wartość" step="any" min="0" oninput="ecConvert('toe',this.value)">
                            <div class="ec-unit-hint">1 TOE = 41.868 GJ ≈ 11 630 kWh</div>
                        </div>
                    </div>
                    <div class="ec-pln-box">
                        <div>
                            <div class="ec-pln-label">Szacunkowa wartość energii (gaz ziemny TTF)</div>
                            <div class="ec-pln-value" id="ec-pln-value">— PLN</div>
                        </div>
                        <div class="ec-price-meta">
                            @if($gasPricePln['ok'])
                                TTF: <span>{{ number_format($gasPricePln['priceEurMwh'], 2) }} EUR/MWh</span> ·
                                EUR/PLN: <span>{{ number_format($gasPricePln['eurPln'], 4) }}</span><br>
                                Cena gazu:
                                <input type="number" id="ec-gas-price"
                                    value="{{ $gasPricePln['priceGjPln'] }}"
                                    data-gj="{{ $gasPricePln['priceGjPln'] }}"
                                    min="0" step="0.1"
                                    style="width:76px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);border-radius:6px;color:#fbbf24;padding:2px 7px;font-size:12px;font-weight:700;outline:none;"
                                    oninput="ecConvert('gj', document.getElementById('ec-gj').value)">
                                <select id="ec-gas-unit" onchange="ecGasPriceUnitChange()"
                                    style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);border-radius:6px;color:#fbbf24;padding:2px 6px;font-size:12px;font-weight:700;outline:none;cursor:pointer;">
                                    <option value="gj">PLN/GJ</option>
                                    <option value="mwh">PLN/MWh</option>
                                </select>
                                · Źródło: {{ $gasPricePln['source'] }}<br>
                                Aktualizacja: <span>{{ $gasPricePln['fetchedAt'] }}</span>
                            @else
                                <span style="color:#f87171;">{{ $gasPricePln['message'] ?? 'Nie udało się pobrać danych rynkowych.' }}</span><br>
                                Cena gazu:
                                <input type="number" id="ec-gas-price" value="" data-gj="" min="0" step="0.1"
                                    style="width:76px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);border-radius:6px;color:#fbbf24;padding:2px 7px;font-size:12px;font-weight:700;outline:none;"
                                    oninput="ecConvert('gj', document.getElementById('ec-gj').value)">
                                <select id="ec-gas-unit" onchange="ecGasPriceUnitChange()"
                                    style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);border-radius:6px;color:#fbbf24;padding:2px 6px;font-size:12px;font-weight:700;outline:none;cursor:pointer;">
                                    <option value="gj">PLN/GJ</option>
                                    <option value="mwh">PLN/MWh</option>
                                </select>
                                (wpisz ręcznie)
                            @endif
                            <br><span style="font-size:11px;color:rgba(255,255,255,.45);">1 m³ GZ-50 ≈ </span><span id="ec-gas-price-m3" style="color:#fbbf24;font-weight:700;">—</span><span style="font-size:11px;color:rgba(255,255,255,.45);"> PLN/m³</span>
                        </div>
                    </div>

                    {{-- ── Białe certyfikaty + Redukcja CO₂ ── --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;">
                        {{-- Białe certyfikaty --}}
                        <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:12px 16px;">
                            <div style="font-size:10px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,.5);margin-bottom:3px;">💚 Białe certyfikaty (SEE)</div>
                            <div class="ec-pln-value" id="ec-white-value" style="color:#4ade80;">— PLN</div>
                            <div style="display:flex;align-items:center;gap:8px;margin-top:7px;flex-wrap:wrap;">
                                <span style="font-size:11px;color:rgba(255,255,255,.45);">Cena TOE:</span>
                                <input type="number" id="ec-white-price" value="{{ $toePricePln['ok'] ? (int)round($toePricePln['pricePerToePln']) : 1800 }}" min="0" step="50"
                                    style="width:85px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:6px;color:#fff;padding:4px 8px;font-size:13px;font-weight:600;outline:none;"
                                    oninput="ecUpdateExtra()">
                                <span style="font-size:11px;color:rgba(255,255,255,.45);">PLN/TOE</span>
                            </div>
                            <div style="font-size:10px;color:rgba(255,255,255,.3);margin-top:5px;">Świadectwa efektywności energetycznej · wartość rynkowa (TGE)</div>
                        </div>
                        {{-- Redukcja CO₂ --}}
                        <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:12px 16px;">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap;">
                                <span style="font-size:10px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,.5);">🌿 Redukcja CO₂</span>
                                <select id="ec-co2-fuel"
                                    style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:6px;color:#fff;padding:3px 8px;font-size:11px;font-weight:600;outline:none;"
                                    onchange="ecUpdateExtra()">
                                    <option value="201.6">Gaz ziemny (201.6 g CO₂/kWh)</option>
                                    <option value="341.0">Węgiel kamienny (341 g CO₂/kWh)</option>
                                    <option value="266.0">Olej opałowy (266 g CO₂/kWh)</option>
                                    <option value="300">Energia elektryczna (~300 g CO₂/kWh)</option>
                                    <option value="250">Ciepło z sieci (~250 g CO₂/kWh)</option>
                                    <option value="0">Biomasa / OZE (0 g CO₂/kWh)</option>
                                </select>
                            </div>
                            <div style="display:flex;gap:20px;align-items:flex-end;">
                                <div>
                                    <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:2px;">kg CO₂</div>
                                    <div class="ec-pln-value" id="ec-co2-kg" style="color:#86efac;font-size:22px;">—</div>
                                </div>
                                <div>
                                    <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:2px;">t CO₂</div>
                                    <div class="ec-pln-value" id="ec-co2-t" style="color:#86efac;font-size:22px;">—</div>
                                </div>
                                <div style="font-size:10px;color:rgba(255,255,255,.3);line-height:1.6;padding-bottom:2px;" id="ec-co2-note">&nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             SEKCJA 2 – WAŻNE STAŁE ENERGETYCZNE
        ══════════════════════════════════════════════════════ --}}
        <div class="ec-acc" id="acc-consts">
            <button class="ec-acc-trigger" onclick="ecAccToggle('acc-consts')">
                <div class="ec-acc-icon">📊</div>
                <div class="ec-acc-text">
                    <strong>Ważne stałe energetyczne</strong>
                    <span>Wartości opałowe paliw, współczynniki emisji CO₂ i przeliczniki jednostek wg norm PN/ISO</span>
                </div>
                <div class="ec-acc-chevron">▾</div>
            </button>
            <div class="ec-acc-body">
                <div class="ec-const-grid">

                    {{-- Gaz ziemny --}}
                    <div class="ec-const-card ec-card-gas">
                        <h4>🔥 Gaz ziemny GZ-50</h4>
                        <div class="ec-const-row"><span class="name">Wartość opałowa</span><span class="val">34.39<span class="unit">MJ/m³</span></span></div>
                        <div class="ec-const-row"><span class="name">jako kWh/m³</span><span class="val">9.553<span class="unit">kWh/m³</span></span></div>
                        <div class="ec-const-row"><span class="name">Ciepło spalania</span><span class="val">38.26<span class="unit">MJ/m³</span></span></div>
                        <div class="ec-const-row"><span class="name">Gęstość (0°C, 1 atm)</span><span class="val">0.720<span class="unit">kg/m³</span></span></div>
                        <div class="ec-const-row"><span class="name">Stoich. powietrze (V₀)</span><span class="val">9.52<span class="unit">m³/m³</span></span></div>
                        <div class="ec-const-row"><span class="name">Stoich. spaliny (V₀ sp)</span><span class="val">10.50<span class="unit">m³/m³</span></span></div>
                        <div class="ec-const-row"><span class="name">CO₂ max (spaliny)</span><span class="val">11.7<span class="unit">%</span></span></div>
                        <div class="ec-const-row"><span class="name">Współcz. emisji CO₂</span><span class="val">201.6<span class="unit">g CO₂/kWh</span></span></div>
                    </div>

                    {{-- Węgiel --}}
                    <div class="ec-const-card ec-card-coal">
                        <h4>⚫ Węgiel kamienny</h4>
                        <div class="ec-const-row"><span class="name">Wart. opałowa (ener.)</span><span class="val">24.0<span class="unit">GJ/t</span></span></div>
                        <div class="ec-const-row"><span class="name">jako kWh/t</span><span class="val">6 667<span class="unit">kWh/t</span></span></div>
                        <div class="ec-const-row"><span class="name">Wart. opałowa (koks.)</span><span class="val">26.4<span class="unit">GJ/t</span></span></div>
                        <div class="ec-const-row"><span class="name">Węgiel brunatny</span><span class="val">8.5<span class="unit">GJ/t</span></span></div>
                        <div class="ec-const-row"><span class="name">Koks kamienny</span><span class="val">28.2<span class="unit">GJ/t</span></span></div>
                        <div class="ec-const-row"><span class="name">Stoich. powietrze (V₀)</span><span class="val">6.80<span class="unit">m³/kg</span></span></div>
                        <div class="ec-const-row"><span class="name">Stoich. spaliny (V₀ sp)</span><span class="val">7.30<span class="unit">m³/kg</span></span></div>
                        <div class="ec-const-row"><span class="name">CO₂ max (spaliny)</span><span class="val">19.5<span class="unit">%</span></span></div>
                        <div class="ec-const-row"><span class="name">Współcz. emisji CO₂</span><span class="val">341.0<span class="unit">g CO₂/kWh</span></span></div>
                    </div>

                    {{-- Olej --}}
                    <div class="ec-const-card ec-card-oil">
                        <h4>🛢 Olej opałowy</h4>
                        <div class="ec-const-row"><span class="name">Olej lekki (Eo-L)</span><span class="val">42.7<span class="unit">GJ/t</span></span></div>
                        <div class="ec-const-row"><span class="name">Olej ciężki (Eo-C)</span><span class="val">40.3<span class="unit">GJ/t</span></span></div>
                        <div class="ec-const-row"><span class="name">LPG (propan-butan)</span><span class="val">46.0<span class="unit">GJ/t</span></span></div>
                        <div class="ec-const-row"><span class="name">Gęstość oleju lekkiego</span><span class="val">0.840<span class="unit">kg/l</span></span></div>
                        <div class="ec-const-row"><span class="name">Stoich. powietrze (V₀)</span><span class="val">10.50<span class="unit">m³/kg</span></span></div>
                        <div class="ec-const-row"><span class="name">Stoich. spaliny (V₀ sp)</span><span class="val">11.30<span class="unit">m³/kg</span></span></div>
                        <div class="ec-const-row"><span class="name">CO₂ max (spaliny)</span><span class="val">15.4<span class="unit">%</span></span></div>
                        <div class="ec-const-row"><span class="name">Współcz. emisji CO₂</span><span class="val">266.0<span class="unit">g CO₂/kWh</span></span></div>
                    </div>

                    {{-- Biomasa --}}
                    <div class="ec-const-card ec-card-bio">
                        <h4>🌿 Biomasa / Drewno</h4>
                        <div class="ec-const-row"><span class="name">Drewno suche (wilg. 15%)</span><span class="val">15.5<span class="unit">GJ/t</span></span></div>
                        <div class="ec-const-row"><span class="name">Drewno (wilgotność 30%)</span><span class="val">12.0<span class="unit">GJ/t</span></span></div>
                        <div class="ec-const-row"><span class="name">Pellet drzewny</span><span class="val">17.0<span class="unit">GJ/t</span></span></div>
                        <div class="ec-const-row"><span class="name">Słoma</span><span class="val">14.4<span class="unit">GJ/t</span></span></div>
                        <div class="ec-const-row"><span class="name">Zrębki (wilg. 35%)</span><span class="val">10.0<span class="unit">GJ/t</span></span></div>
                        <div class="ec-const-row"><span class="name">Biogaz (rolniczy)</span><span class="val">21.5<span class="unit">MJ/m³</span></span></div>
                        <div class="ec-const-row"><span class="name">Emisja CO₂ pellet</span><span class="val">0<span class="unit">g/kWh (neutralna)</span></span></div>
                    </div>

                    {{-- Energia elektryczna --}}
                    <div class="ec-const-card ec-card-elec">
                        <h4>⚡ Energia elektryczna</h4>
                        <div class="ec-const-row"><span class="name">1 kWh = MJ</span><span class="val">3.600<span class="unit">MJ</span></span></div>
                        <div class="ec-const-row"><span class="name">Wskaźnik emisji PL — źródła spalania ({{ $co2ElYear }})</span><span class="val">{{ $co2ElCombFactor }}<span class="unit">g CO₂/kWh</span></span></div>
                        <div class="ec-const-row"><span class="name">Wskaźnik emisji PL — krajowy z OZE ({{ $co2ElYear }})</span><span class="val">{{ $co2ElNatFactor }}<span class="unit">g CO₂/kWh</span></span></div>
                        <div class="ec-const-row"><span class="name">Wskaźnik nakładu PL</span><span class="val">2.50<span class="unit">kWh energ. pierwot./kWh</span></span></div>
                        <div class="ec-const-row"><span class="name">Sprawność przesyłu PL</span><span class="val">93.2<span class="unit">%</span></span></div>
                        <div class="ec-const-row"><span class="name">Ciepło w pompie ciepła (COP=3)</span><span class="val">3.0<span class="unit">kWh ciepła/kWh el.</span></span></div>
                        <div class="ec-const-row" style="font-size:10px; padding-top:6px;"><span class="name" style="color:#6b8294;">Źródło: KOBiZE · rok {{ $co2ElYear }} · publ. {{ $co2ElYear + 1 }}</span></div>
                    </div>

                    {{-- Przeliczniki ogólne --}}
                    <div class="ec-const-card ec-card-conv">
                        <h4>🔢 Przeliczniki jednostek</h4>
                        <div class="ec-const-row"><span class="name">1 TOE</span><span class="val">41.868<span class="unit">GJ</span></span></div>
                        <div class="ec-const-row"><span class="name">1 TOE</span><span class="val">11 630<span class="unit">kWh</span></span></div>
                        <div class="ec-const-row"><span class="name">1 boe (baryłka ropy)</span><span class="val">5.712<span class="unit">GJ</span></span></div>
                        <div class="ec-const-row"><span class="name">1 Gcal (Giga-kaloria)</span><span class="val">4.187<span class="unit">GJ</span></span></div>
                        <div class="ec-const-row"><span class="name">1 kcal</span><span class="val">4.187<span class="unit">kJ</span></span></div>
                        <div class="ec-const-row"><span class="name">1 therm (UK)</span><span class="val">0.10551<span class="unit">GJ</span></span></div>
                        <div class="ec-const-row"><span class="name">1 BTU</span><span class="val">1.0551<span class="unit">kJ</span></span></div>
                        <div class="ec-const-row"><span class="name">1 MMBTU</span><span class="val">1.0551<span class="unit">GJ</span></span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             SEKCJA 3 – KALKULATOR MASY SPALIN
        ══════════════════════════════════════════════════════ --}}
        <div class="ec-acc" id="acc-flue">
            <button class="ec-acc-trigger" onclick="ecAccToggle('acc-flue')">
                <div class="ec-acc-icon">🌡️</div>
                <div class="ec-acc-text">
                    <strong>Kalkulator masy spalin</strong>
                    <span>Obliczanie λ, objętości i masy spalin dla gazu, węgla i oleju. Przelicznik ilości spalin dla mocy kotła.</span>
                </div>
                <div class="ec-acc-chevron">▾</div>
            </button>
            <div class="ec-acc-body">

                {{-- ── Analiza spalin ── --}}
                <div class="ec-flue-controls">
                    <div>
                        <div class="ec-flue-label">Rodzaj kotła / paliwa</div>
                        <select id="ec-fuel" class="ec-flue-select" onchange="ecFlueCalc()">
                            <option value="gas">Gaz ziemny GZ-50</option>
                            <option value="coal">Węgiel kamienny (typ 31)</option>
                            <option value="oil">Olej opałowy lekki</option>
                        </select>
                    </div>
                    <div>
                        <div class="ec-flue-label">Zawartość O₂ w spalinach [%]</div>
                        <input type="number" id="ec-o2" class="ec-flue-input" value="3" min="0" max="18" step="0.1" oninput="ecFlueCalc()">
                        <div class="ec-o2-bar" style="margin-top:8px;">
                            <div class="ec-o2-thumb" id="ec-o2-thumb" style="left:16.7%"></div>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:10px; color:#6b8294; margin-top:3px;"><span>0%</span><span>6%</span><span>12%</span><span>18%</span></div>
                    </div>
                    <div style="background:#edf3f8; border-radius:10px; padding:10px 12px; font-size:12px; color:#355468; line-height:1.7;">
                        <strong style="color:#1d4f73;">Wybrane paliwo:</strong><br>
                        <span id="ec-fuel-info">GZ-50: V₀<sub>pow</sub>=9.52 m³/m³, V₀<sub>sp</sub>=10.50 m³/m³</span>
                    </div>
                </div>

                <div class="ec-flue-results">
                    <div class="ec-flue-result-card">
                        <div class="ec-flue-result-label">Nadmiar powietrza λ</div>
                        <div class="ec-flue-result-val" id="ec-lambda">—</div>
                        <div class="ec-flue-result-unit">[-]</div>
                    </div>
                    <div class="ec-flue-result-card">
                        <div class="ec-flue-result-label">Objętość spalin</div>
                        <div class="ec-flue-result-val" id="ec-vol-flue">—</div>
                        <div class="ec-flue-result-unit" id="ec-vol-flue-unit">m³/m³ gazu</div>
                    </div>
                    <div class="ec-flue-result-card">
                        <div class="ec-flue-result-label">Masa spalin</div>
                        <div class="ec-flue-result-val" id="ec-mass-flue">—</div>
                        <div class="ec-flue-result-unit" id="ec-mass-flue-unit">kg/m³ gazu</div>
                    </div>
                    <div class="ec-flue-result-card">
                        <div class="ec-flue-result-label">Temp. punktu rosy</div>
                        <div class="ec-flue-result-val" id="ec-dew">~55</div>
                        <div class="ec-flue-result-unit">°C</div>
                    </div>
                </div>

                <div class="ec-flue-note" id="ec-flue-note">
                    Wprowadź wartość O₂ z analizatora spalin. Wzór: λ = 21 / (21 − O₂[%]).
                    Objętość spalin mokrych = V₀<sub>sp</sub> + (λ−1) × V₀<sub>pow</sub>.
                    Masa spalin = Vsp × ρ<sub>spalin</sub> [≈ 1.25 kg/m³ dla gazu, 1.30 kg/m³ dla węgla].
                </div>

                {{-- ── Przelicznik mocy kotła ── --}}
                <div class="ec-sub-title">🔥 Przelicznik ilości spalin dla mocy kotła</div>

                <div class="ec-flue-controls" style="grid-template-columns:200px 160px 1fr;">
                    <div>
                        <div class="ec-flue-label">Moc kotła [kW]</div>
                        <input type="number" id="ec-power" class="ec-flue-input" min="1" max="100000" step="1" placeholder="np. 100" oninput="ecFlueCalc()">
                    </div>
                    <div>
                        <div class="ec-flue-label">Sprawność kotła [%]</div>
                        <input type="number" id="ec-eff" class="ec-flue-input" value="90" min="50" max="110" step="0.5" oninput="ecFlueCalc()">
                    </div>
                    <div style="background:#edf3f8; border-radius:10px; padding:10px 12px; font-size:12px; color:#355468; line-height:1.8;">
                        <strong style="color:#1d4f73;">Wzór:</strong><br>
                        Q̇<sub>pal</sub> = P&nbsp;/&nbsp;(η·H<sub>u</sub>) → V̇<sub>sp</sub> = Q̇<sub>pal</sub>·V<sub>sp</sub> → ṁ<sub>sp</sub> = V̇<sub>sp</sub>·ρ<sub>sp</sub>
                    </div>
                </div>
                <div class="ec-flue-results" style="grid-template-columns:repeat(4,1fr); margin-top:12px;">
                    <div class="ec-flue-result-card">
                        <div class="ec-flue-result-label">Zużycie paliwa</div>
                        <div class="ec-flue-result-val" id="ec-fuel-flow">—</div>
                        <div class="ec-flue-result-unit" id="ec-fuel-flow-unit">m³/h</div>
                    </div>
                    <div class="ec-flue-result-card">
                        <div class="ec-flue-result-label">Przepływ spalin</div>
                        <div class="ec-flue-result-val" id="ec-flue-flow">—</div>
                        <div class="ec-flue-result-unit">m³/h</div>
                    </div>
                    <div class="ec-flue-result-card">
                        <div class="ec-flue-result-label">Masa spalin</div>
                        <div class="ec-flue-result-val" id="ec-flue-mass-flow">—</div>
                        <div class="ec-flue-result-unit">kg/h</div>
                    </div>
                    <div class="ec-flue-result-card" style="border-color:#bfd7ed; background:#f0f7ff;">
                        <div class="ec-flue-result-label">Masa spalin</div>
                        <div class="ec-flue-result-val" id="ec-flue-mass-flow-s" style="color:#0e5a8a;">—</div>
                        <div class="ec-flue-result-unit">kg/s</div>
                    </div>
                </div>
                <div class="ec-flue-note" id="ec-power-note" style="display:none;"></div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             SEKCJA 4 – KALKULATOR EMISJI CO₂ Z ENERGII ELEKTRYCZNEJ
        ══════════════════════════════════════════════════════ --}}
        <div class="ec-acc" id="acc-co2el">
            <button class="ec-acc-trigger" onclick="ecAccToggle('acc-co2el')">
                <div class="ec-acc-icon">🌿</div>
                <div class="ec-acc-text">
                    <strong>Kalkulator emisji CO₂ z energii elektrycznej (KSE)</strong>
                    <span>Wskaźniki KOBiZE {{ $co2ElYear }} · źródła spalania: {{ $co2ElCombFactor }} g CO₂/kWh · krajowy z OZE: {{ $co2ElNatFactor }} g CO₂/kWh</span>
                </div>
                <div class="ec-acc-chevron">▾</div>
            </button>
            <div class="ec-acc-body">

                {{-- Wskaźniki --}}
                <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-start; margin-bottom:18px;">
                    <div>
                        <div style="font-size:12px; font-weight:700; color:#1a5c2e; margin-bottom:6px;">🔌 Wskaźnik — źródła spalania paliw (rok {{ $co2ElYear }})</div>
                        <div class="ec-co2el-badge">
                            <strong id="co2el-factor-comb">{{ $co2ElCombFactor }}</strong>
                            <span>g CO₂/kWh</span>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:12px; font-weight:700; color:#1a5c2e; margin-bottom:6px;">🌍 Wskaźnik krajowy z OZE + straty sieciowe (rok {{ $co2ElYear }})</div>
                        <div class="ec-co2el-badge">
                            <strong id="co2el-factor-nat">{{ $co2ElNatFactor }}</strong>
                            <span>g CO₂/kWh</span>
                        </div>
                    </div>
                    <div style="align-self:flex-end; font-size:11px; color:#4c6373; background:#edf3f8; border-radius:8px; padding:8px 12px; line-height:1.7;">
                        Źródło: <a href="https://www.kobize.pl/uploads/materialy/materialy_do_pobrania/aktualnosci/2025/142_Wskazniki_emisyjnosci_2025.pdf"
                            target="_blank" rel="noopener noreferrer" style="color:#0e89d8;">KOBiZE, grudzień 2025</a><br>
                        Dane za rok {{ $co2ElYear }} · <em>Stosować do raportowania za rok {{ $co2ElYear + 1 }}</em>
                    </div>
                </div>

                {{-- Kalkulator --}}
                <div class="ec-flue-controls" style="grid-template-columns:180px 210px 1fr;">
                    <div>
                        <div class="ec-flue-label">Zużycie energii [kWh]</div>
                        <input type="number" id="co2el-kwh" class="ec-flue-input" min="0" step="any" placeholder="np. 10 000" oninput="ecCo2ElCalc()">
                    </div>
                    <div>
                        <div class="ec-flue-label">Typ wskaźnika</div>
                        <select id="co2el-type" class="ec-flue-select" onchange="ecCo2ElCalc()">
                            <option value="comb">Źródła spalania ({{ $co2ElCombFactor }} g CO₂/kWh)</option>
                            <option value="nat">Krajowy z OZE ({{ $co2ElNatFactor }} g CO₂/kWh)</option>
                        </select>
                    </div>
                    <div style="background:#edf3f8; border-radius:10px; padding:10px 12px; font-size:12px; color:#355468; line-height:1.8;">
                        <strong style="color:#1a5c2e;">Zastosowanie:</strong><br>
                        <em>Źródła spalania</em> — audyty EU ETS, świadectwa efektywności energetycznej (ŚEE).<br>
                        <em>Krajowy z OZE</em> — raporty CSR, ślad węglowy budynków (PN-EN 15978).
                    </div>
                </div>

                <div class="ec-flue-results" style="grid-template-columns:repeat(3,1fr); margin-top:14px;">
                    <div class="ec-flue-result-card">
                        <div class="ec-flue-result-label">Emisja CO₂</div>
                        <div class="ec-flue-result-val" id="co2el-kg">—</div>
                        <div class="ec-flue-result-unit">kg CO₂</div>
                    </div>
                    <div class="ec-flue-result-card">
                        <div class="ec-flue-result-label">Emisja CO₂</div>
                        <div class="ec-flue-result-val" id="co2el-t">—</div>
                        <div class="ec-flue-result-unit">t CO₂</div>
                    </div>
                    <div class="ec-flue-result-card">
                        <div class="ec-flue-result-label">Równoważnik MWh</div>
                        <div class="ec-flue-result-val" id="co2el-mwh">—</div>
                        <div class="ec-flue-result-unit">MWh</div>
                    </div>
                </div>

                <div class="ec-flue-note" id="co2el-note" style="display:none;"></div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             SEKCJA 5 – KALKULATOR MOCY ODZYSKANEJ ZE SPALIN
        ══════════════════════════════════════════════════════ --}}
        <div class="ec-acc" id="acc-hrec">
            <button class="ec-acc-trigger" onclick="ecAccToggle('acc-hrec')">
                <div class="ec-acc-icon">♻️</div>
                <div class="ec-acc-text">
                    <strong>Kalkulator mocy odzyskanej ze spalin</strong>
                    <span>Moc sucha (jawna) i mokra (kondensacja) · wielostopniowe wymienniki · ekonomajzery</span>
                </div>
                <div class="ec-acc-chevron">▾</div>
            </button>
            <div class="ec-acc-body">
                <style>
                    .hrec-label { font-size:12px; font-weight:700; color:#1d4f73; margin-bottom:5px; display:flex; align-items:center; gap:5px; }
                    .hrec-badge { font-size:10px; background:#e6f4ea; color:#1a5c2e; border-radius:4px; padding:1px 5px; font-weight:600; }
                    .hrec-input { width:100%; box-sizing:border-box; padding:9px 12px; border-radius:9px; border:1.5px solid #c9d7e3; font-size:14px; font-weight:600; color:#0f2330; transition:border-color .15s; }
                    .hrec-input:focus { border-color:#0e89d8; outline:none; }
                    .hrec-input.suggested { border-color:#b0c8d8; color:#6a9ab5; background:#f4f9fc; }
                    .hrec-select { width:100%; padding:9px 12px; border-radius:9px; border:1.5px solid #c9d7e3; font-size:14px; font-weight:600; color:#0f2330; background:#fff; }
                    .hrec-hint { font-size:11px; color:#8aacbe; margin-top:4px; min-height:16px; line-height:1.5; }
                    .hrec-section-title { font-size:13px; font-weight:700; color:#1d4f73; margin:18px 0 10px; padding-top:16px; border-top:2px dashed #c9d7e3; display:flex; align-items:center; gap:6px; }
                    .hrec-hx-block { border:1px solid #d0e4f5; border-radius:12px; padding:14px 16px; margin-bottom:10px; background:#f7fbff; }
                    .hrec-hx-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
                    .hrec-hx-title { font-size:14px; font-weight:800; color:#0f2330; }
                    .hrec-hx-tin { font-size:12px; color:#4c6373; background:#e6eff7; border-radius:6px; padding:3px 8px; }
                    .hrec-result-card { background:#fff; border:1px solid #d2e3f1; border-radius:9px; padding:10px 12px; }
                    .hrec-result-label { font-size:10px; font-weight:700; letter-spacing:.7px; text-transform:uppercase; color:#6b8294; margin-bottom:5px; }
                    .hrec-result-val { font-size:18px; font-weight:800; color:#10344c; }
                    .hrec-result-unit { font-size:11px; color:#6b8294; margin-top:2px; }
                    .hrec-result-card.hrec-dry { border-color:#b3d4ee; background:#f0f7ff; }
                    .hrec-result-card.hrec-dry .hrec-result-val { color:#0e5a8a; }
                    .hrec-result-card.hrec-wet { border-color:#a8d5b5; background:#f0fff4; }
                    .hrec-result-card.hrec-wet .hrec-result-val { color:#1a5c2e; }
                    .hrec-result-card.hrec-tot { border-color:#fbb040; background:#fffdf0; }
                    .hrec-result-card.hrec-tot .hrec-result-val { color:#c47c00; font-size:20px; }
                    .hrec-summary-box { background:linear-gradient(135deg,#0f1e30 0%,#163854 100%); border-radius:13px; padding:16px 20px; margin-top:14px; }
                    .hrec-sum-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
                    .hrec-sum-card { background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.15); border-radius:11px; padding:12px 14px; text-align:center; }
                    .hrec-sum-label { font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:rgba(255,255,255,.5); margin-bottom:6px; }
                    .hrec-sum-val { font-size:22px; font-weight:900; color:#fff; }
                    .hrec-sum-val.hwet { color:#4ade80; }
                    .hrec-sum-val.htot { color:#fbbf24; font-size:26px; }
                    .hrec-sum-unit { font-size:11px; color:rgba(255,255,255,.4); margin-top:2px; }
                    .hrec-note { font-size:12px; color:#4c6373; padding:10px 14px; background:#edf3f8; border-radius:8px; border-left:3px solid #0e89d8; }
                    .hrec-add-btn { display:inline-flex; align-items:center; gap:6px; padding:9px 16px; background:#dbe9f5; color:#1d4f73; border:1px solid #b0c8d8; border-radius:9px; font-size:13px; font-weight:700; cursor:pointer; margin-top:4px; transition:background .15s; }
                    .hrec-add-btn:hover { background:#c5ddef; }
                    .hrec-remove-btn { background:none; border:none; color:#9aabbb; font-size:16px; cursor:pointer; padding:2px 6px; border-radius:6px; transition:color .15s; }
                    .hrec-remove-btn:hover { color:#e53e3e; background:#fee2e2; }
                    .hrec-drag-handle { cursor:grab; color:#b0c8d8; font-size:20px; line-height:1; padding:0 4px 0 0; user-select:none; flex-shrink:0; }
                    .hrec-drag-handle:active { cursor:grabbing; }
                    .hrec-hx-block.hrec-dragging { opacity:0.35; background:#e6f4ff; }
                    .hrec-hx-block.hrec-drag-over-top { border-top:3px solid #0e89d8; margin-top:-2px; }
                    .hrec-hx-block.hrec-drag-over-bottom { border-bottom:3px solid #0e89d8; margin-bottom:-2px; }
                    .hrec-dew-badge { display:inline-flex; align-items:center; gap:4px; font-size:11px; padding:3px 8px; border-radius:6px; font-weight:700; }
                    .hrec-dew-dry { background:#fff3cd; color:#856404; }
                    .hrec-dew-wet { background:#d1e7dd; color:#0a3622; }
                    .hrec-hx-grid { display:grid; grid-template-columns:220px 1fr 1fr 1fr; gap:10px; align-items:end; }
                    /* ── Tooltips – wyjaśnienie wartości po najechaniu ── */
                    .hrec-tip-host { position:relative; }
                    .hrec-tip-host::after {
                        content:attr(data-tip);
                        position:absolute;
                        bottom:calc(100% + 7px);
                        left:0;
                        background:#0f2330;
                        color:#cde6f8;
                        font-size:11.5px;
                        line-height:1.65;
                        padding:9px 13px;
                        border-radius:9px;
                        white-space:pre-line;
                        width:310px;
                        z-index:300;
                        pointer-events:none;
                        opacity:0;
                        transition:opacity .15s;
                        box-shadow:0 4px 16px rgba(0,0,0,.4);
                    }
                    .hrec-tip-host:hover::after { opacity:1; }
                    .hrec-tip-right::after { left:auto; right:0; }
                    @media(max-width:960px) {
                        .hrec-hx-grid { grid-template-columns:1fr 1fr; }
                        .hrec-sum-grid { grid-template-columns:1fr 1fr; }
                    }
                    @media(max-width:600px) {
                        .hrec-hx-grid { grid-template-columns:1fr; }
                        .hrec-sum-grid { grid-template-columns:1fr; }
                    }
                </style>

                {{-- ── Dane kotła ── --}}
                <table style="width:100%;border-collapse:separate;border-spacing:0;margin-bottom:14px;">
                    {{-- nagłówek sekcji --}}
                    <colgroup><col style="width:33.33%"><col style="width:33.33%"><col style="width:33.34%"></colgroup>
                    <tbody>
                    {{-- wiersz 1: paliwo | moc | sprawność --}}
                    <tr style="background:rgba(255,255,255,.03);">
                        <td style="padding:10px 12px;border:1px solid rgba(0,0,0,.07);border-right:none;border-radius:8px 0 0 0;">
                            <div class="hrec-label hrec-tip-host" data-tip="Paliwo wpływa na parametry spalin:&#10;• Gaz GZ-50: cp=1.04 kJ/(kg·K), xH₂O=0.190, T_rosy=57°C, r=2358 kJ/kg&#10;• Węgiel: cp=1.02 kJ/(kg·K), xH₂O=0.085, T_rosy=47°C, r=2380 kJ/kg&#10;cp – ciepło właściwe spalin&#10;xH₂O – udział pary wodnej w spalinach&#10;T_rosy – granica kondensacji&#10;r – ciepło skroplenia pary">Rodzaj kotła / paliwa</div>
                            <select id="hrec-fuel" class="hrec-select" onchange="hrecOnFuelChange()">
                                <option value="gas">🔥 Gaz ziemny (GZ-50)</option>
                                <option value="coal">⚫ Węgiel kamienny</option>
                            </select>
                            <div class="hrec-hint">Temp. punktu rosy spalin: <strong id="hrec-dew-display">57°C</strong></div>
                        </td>
                        <td style="padding:10px 12px;border:1px solid rgba(0,0,0,.07);border-right:none;">
                            <div class="hrec-label hrec-tip-host" data-tip="Moc użyteczna kotła P [kW]&#10;Używana do oszacowania strumienia spalin:&#10;  ṁ_paliwa = P / (η × Hu)&#10;  ṁ_spalin = ṁ_paliwa × V_sp × ρ_sp&#10;Gaz: Hu=9.444 kWh/m³, V_sp=11.45 m³/m³, ρ=1.25 kg/m³&#10;Węgiel: Hu=6.667 kWh/kg, V_sp=8.20 m³/kg, ρ=1.30 kg/m³&#10;⚠ Obliczenia stoichiometryczne (λ≈1)">Moc kotła [kW] <span class="hrec-badge">wymagane</span></div>
                            <input type="number" id="hrec-power" class="hrec-input" min="1" max="100000" step="1" placeholder="np. 500" oninput="hrecOnPowerChange()">
                            <div class="hrec-hint">Po wpisaniu obliczę automatycznie ilość spalin</div>
                        </td>
                        <td style="padding:10px 12px;border:1px solid rgba(0,0,0,.07);border-radius:0 8px 0 0;">
                            <div class="hrec-label hrec-tip-host" data-tip="Sprawność kotła η [%]&#10;Wpływa na zużycie paliwa:&#10;  ṁ_paliwa = P / (η × Hu)&#10;Wyższa η → mniej paliwa → mniejszy strumień spalin&#10;Gaz: typowo 88–92% | Węgiel: 78–84%">Sprawność kotła [%]</div>
                            <input type="number" id="hrec-eff" class="hrec-input" min="50" max="110" step="0.5" placeholder="np. 90" oninput="hrecOnPowerChange()">
                            <div class="hrec-hint" id="hrec-eff-hint">Sugerowana dla gazu: 88–92%</div>
                        </td>
                    </tr>
                    {{-- wiersz 2: temp spalin | O₂ | ilość spalin --}}
                    <tr>
                        <td style="padding:10px 12px;border:1px solid rgba(0,0,0,.07);border-top:none;border-right:none;">
                            <div class="hrec-label hrec-tip-host" data-tip="Temperatura spalin za kotłem, przed HX1 [°C]&#10;Decyduje o dostępnym potencjale cieplnym.&#10;Typowe wartości:&#10;• Gaz: 160–220°C&#10;• Węgiel kamienny: 200–280°C">Temp. spalin wejściowa [°C] <span class="hrec-badge">wymagane</span></div>
                            <input type="number" id="hrec-t-in" class="hrec-input" min="50" max="1200" step="1" placeholder="np. 200" oninput="hrecCalc()">
                            <div class="hrec-hint" id="hrec-tin-hint">Sugerowana dla gazu: 160–220°C</div>
                        </td>
                        <td style="padding:10px 12px;border:1px solid rgba(0,0,0,.07);border-top:none;border-right:none;">
                            <div class="hrec-label hrec-tip-host" data-tip="O₂ w spalinach [%] (analizator spalin)&#10;Używane do obliczenia współczynnika nadmiaru powietrza:&#10;  λ = 21 / (21 − O₂%)&#10;Typowe wartości:&#10;• Gaz ziemny: 2–4% (λ=1.10–1.23)&#10;• Węgiel: 5–8% (λ=1.31–1.62)&#10;Domyślna wartość 3% odpowiada λ=1.167">O₂ w spalinach [%] <span class="hrec-badge">nadmiar pow.</span></div>
                            <input type="number" id="hrec-o2" class="hrec-input" value="3" min="0" max="18" step="0.1" oninput="hrecOnPowerChange()">
                            <div class="hrec-hint" id="hrec-o2-hint">λ = <strong id="hrec-lambda-display">1.167</strong> &middot; gaz: 2–4%, węgiel: 5–8%</div>
                        </td>
                        <td style="padding:10px 12px;border:1px solid rgba(0,0,0,.07);border-top:none;">
                            <div class="hrec-label hrec-tip-host" data-tip="Masowy strumień spalin ṁ [kg/h]&#10;Obliczenie z mocy i O₂:&#10;  λ = 21/(21−O₂%)&#10;  V_sp = V₀_sp + (λ−1)×V₀_pow&#10;  ṁ = Q̇_pal × V_sp × ρ_sp&#10;Wpisz ręcznie z pomiaru (analizator).">Ilość spalin [kg/h] <span class="hrec-badge">wymagane</span></div>
                            <input type="number" id="hrec-mass-flow" class="hrec-input" min="0" step="1" placeholder="np. 2500" oninput="hrecMassFlowEdited()">
                            <div class="hrec-hint" id="hrec-mflow-hint">Lub oblicz automatycznie z mocy kotła ↑</div>
                        </td>
                    </tr>
                    {{-- wiersz 3: xH2O | (puste) | (puste) --}}
                    <tr style="background:rgba(255,255,255,.03);">
                        <td style="padding:10px 12px;border:1px solid rgba(0,0,0,.07);border-top:none;border-right:none;border-radius:0 0 0 8px;">
                            <div class="hrec-label hrec-tip-host" data-tip="Masowy udział pary wodnej xH₂O [kg/kg]&#10;Decyduje o ilości kondensatu:&#10;  ṁ_kond = ṁ × xH₂O × frac&#10;  frac = (T_rosy − T_wyj) / (T_rosy − 20)&#10;  (model liniowy: 0 przy T_rosy, 1 przy 20°C)&#10;Gaz GZ-50: ~0.190 kg/kg&#10;Węgiel kamienny: ~0.085 kg/kg">Zawartość H₂O w spalinach [kg/kg]</div>
                            <input type="number" id="hrec-xh2o" class="hrec-input" min="0.01" max="0.5" step="0.001" placeholder="np. 0.190" oninput="hrecCalc()">
                            <div class="hrec-hint" id="hrec-xh2o-hint">Sugerowana dla gazu: ~0.190 kg/kg (GZ-50)</div>
                        </td>
                        <td colspan="2" style="padding:10px 14px;border:1px solid rgba(0,0,0,.07);border-top:none;border-left:none;border-radius:0 0 8px 0;color:#6b8294;font-size:12px;vertical-align:middle;">
                            <span style="opacity:.55;">💡 Wpisz moc kotła i sprawność → ilość spalin zostanie obliczona automatycznie z uwzględnieniem O₂ (λ).<br>Możesz też wpisać ilość spalin ręcznie z pomiaru.</span>
                        </td>
                    </tr>
                    </tbody>
                </table>

                {{-- ── Czynnik grzewczy ── --}}
                <div class="hrec-section-title">💧 Parametry produkowanego czynnika</div>
                <div style="display:grid; grid-template-columns:1.2fr 1fr 0.7fr 1fr 1fr 1.5fr; gap:12px; margin-bottom:0; align-items:start;">
                    <div>
                        <div class="hrec-label hrec-tip-host" data-tip="Czynnik roboczy – określa cp [kJ/(kg·K)]:&#10;• Woda:      cp = 4.18&#10;• Glikol:    cp = 3.50&#10;• Powietrze: cp = 1.005&#10;cp używane w bilansie wody:&#10;Q = ṁ × cp × ΔT / 3600 [kW]">Produkcja:</div>
                        <select id="hrec-medium" class="hrec-select" onchange="hrecOnMediumChange()">
                            <option value="water">Woda</option>
                            <option value="steam">Para wodna</option>
                            <option value="glycol">Glikol / woda-glikol</option>
                            <option value="air">Powietrze</option>
                            <option value="other">Inne</option>
                        </select>
                    </div>
                    <div>
                        <div class="hrec-label" id="hrec-supply-temp-label">Temperatura zasilania [°C]</div>
                        <input type="number" id="hrec-medium-temp" class="hrec-input" min="-30" max="400" step="1" placeholder="np. 80" oninput="hrecMediumCalc()">
                        <div class="hrec-hint" id="hrec-medium-hint">Sugerowana dla wody grzewczej: 60–80°C</div>
                    </div>
                    <div>
                        <div class="hrec-label hrec-tip-host" data-tip="Ciśnienie robocze czynnika [bar]&#10;Dla pary nasyconej wyznacza T_nas z tablic:&#10;  0.5 bar→81°C | 1 bar→100°C&#10;  4 bar→144°C | 10 bar→180°C | 16 bar→201°C&#10;Dla wody/glikolu: informacja o warunkach pracy">Ciśnienie [bar]</div>
                        <input type="number" id="hrec-medium-pres" class="hrec-input" min="0.1" max="250" step="0.1" placeholder="np. 4" oninput="hrecMediumCalc()">
                        <div class="hrec-hint" id="hrec-pres-hint">Sieć c.o.: 3–6 bar</div>
                    </div>
                    <div id="hrec-ret-wrap">
                        <div class="hrec-label" id="hrec-ret-temp-label">Temperatura powrotu [°C]</div>
                        <input type="number" id="hrec-medium-ret" class="hrec-input" min="-30" max="380" step="1" placeholder="np. 60" oninput="hrecMediumCalc()">
                        <div class="hrec-hint" id="hrec-ret-hint">Sieć c.o.: 50–60°C · ΔT = T_zas − T_pow</div>
                    </div>
                    <div>
                        <div class="hrec-label hrec-tip-host" data-tip="Strumień czynnika ṁ [kg/h]&#10;Tryb auto (moc→strumień):&#10;  Woda/glikol: ṁ = Q×3600 / (cp×ΔT)&#10;  Para:        ṁ = Q×3600 / Δh&#10;Tryb ręczny (strumień→moc):&#10;  wpisz ṁ → obliczę wymaganą moc kotła"><span id="hrec-flow-label">Produkcja wody [kg/h]</span> <span class="hrec-badge" id="hrec-flow-badge">sugerowany</span></div>
                        <input type="number" id="hrec-medium-flow" class="hrec-input suggested" min="0" step="1" placeholder="obliczam…" oninput="hrecMediumFlowEdited()">
                        <div class="hrec-hint" id="hrec-flow-hint">Wpisz ręcznie lub wyliczy się z mocy kotła ↑</div>
                    </div>
                    <div id="hrec-medium-result-card" style="background:#edf3f8; border-radius:10px; padding:10px 12px; font-size:12px; color:#355468; line-height:1.7; display:none; align-self:end;">
                        <div id="hrec-medium-result-text"></div>
                    </div>
                </div>

                {{-- ── Wymienniki ciepła ── --}}
                <div class="hrec-section-title">🔧 Wymienniki ciepła (ekonomajzery)</div>
                <div id="hrec-hx-container"></div>

                <button class="hrec-add-btn" onclick="hrecAddHX()">
                    <span style="font-size:18px; line-height:1;">+</span> Dodaj wymiennik
                </button>

                {{-- ── Podsumowanie ── --}}
                <div class="hrec-summary-box" id="hrec-summary" style="display:none; margin-top:14px;">
                    <div style="color:rgba(255,255,255,.6); font-size:12px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; margin-bottom:12px;">📊 Sumaryczna moc odzyskana ze spalin</div>
                    <div class="hrec-sum-grid">
                        <div class="hrec-sum-card">
                            <div class="hrec-sum-label">Moc sucha (jawna)</div>
                            <div class="hrec-sum-val" id="hrec-sum-dry">—</div>
                            <div class="hrec-sum-unit">kW</div>
                        </div>
                        <div class="hrec-sum-card">
                            <div class="hrec-sum-label">Moc mokra (kondensacja)</div>
                            <div class="hrec-sum-val hwet" id="hrec-sum-wet">—</div>
                            <div class="hrec-sum-unit">kW</div>
                        </div>
                        <div class="hrec-sum-card">
                            <div class="hrec-sum-label">Moc sumaryczna</div>
                            <div class="hrec-sum-val htot" id="hrec-sum-total">—</div>
                            <div class="hrec-sum-unit">kW</div>
                        </div>
                    </div>
                    <div class="hrec-note" id="hrec-sum-note" style="margin-top:12px; display:none; background:rgba(255,255,255,.06); border-left-color:rgba(255,255,255,.3); color:rgba(255,255,255,.75);"></div>
                </div>

                <div class="hrec-note" id="hrec-main-note" style="margin-top:12px; display:none;"></div>

                {{-- ── Zapis kalkulacji (tylko zalogowani) ── --}}
                @auth
                <div class="hrec-section-title" style="margin-top:18px;">💾 Zapisz kalkulację na koncie</div>
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <input type="text" id="hrec-save-name" class="hrec-input" style="max-width:340px;"
                           placeholder="Nazwa kalkulacji, np. Kotłownia A – gaz 500 kW"
                           maxlength="120">
                    <button onclick="hrecSave()" style="padding:9px 18px; background:#1a5c2e; color:#fff; border:none; border-radius:9px; font-size:13px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px;">
                        💾 Zapisz
                    </button>
                    <span id="hrec-save-msg" style="font-size:12px; color:#1a5c2e; display:none;"></span>
                </div>

                {{-- ── Lista zapisanych kalkulacji ── --}}
                @if($savedCalculations->isNotEmpty())
                <div class="hrec-section-title" style="margin-top:18px;">📋 Zapisane kalkulacje</div>
                <div id="hrec-saved-list">
                @foreach($savedCalculations as $sc)
                <div class="hrec-hx-block" id="hrec-saved-{{ $sc->id }}" style="background:#f4f8fb;">
                    <div class="hrec-hx-header">
                        <div>
                            <span class="hrec-hx-title">{{ e($sc->name) }}</span>
                            <span class="hrec-hx-tin" style="margin-left:8px;">{{ $sc->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            <span style="font-size:11px; color:#4c6373;">{{ $sc->fuelLabel() }} · {{ $sc->mediumLabel() }}
                                @if($sc->boiler_power) · {{ number_format($sc->boiler_power, 0, ',', ' ') }} kW @endif
                            </span>
                            <button onclick="hrecLoadSaved({{ $sc->id }})" style="padding:4px 10px; background:#dbe9f5; color:#1d4f73; border:1px solid #b0c8d8; border-radius:7px; font-size:12px; font-weight:700; cursor:pointer;">
                                ↩ Wczytaj
                            </button>
                            <button onclick="hrecDeleteSaved({{ $sc->id }}, this)" class="hrec-remove-btn" title="Usuń">✕</button>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:repeat(3,auto) 1fr; gap:8px 14px; font-size:12px; color:#355468;">
                        <div>🌡️ Sucha: <strong>{{ $sc->result_dry_kw !== null ? number_format($sc->result_dry_kw, 1, ',', ' ').' kW' : '—' }}</strong></div>
                        <div>💧 Mokra: <strong>{{ $sc->result_wet_kw !== null ? number_format($sc->result_wet_kw, 1, ',', ' ').' kW' : '—' }}</strong></div>
                        <div>⚡ Łącznie: <strong>{{ $sc->result_total_kw !== null ? number_format($sc->result_total_kw, 1, ',', ' ').' kW' : '—' }}</strong></div>
                        @if($sc->flue_temp_in)<div style="color:#6b8294;">T_spalin: {{ number_format($sc->flue_temp_in, 0, ',', ' ') }}°C</div>@endif
                    </div>
                </div>
                @endforeach
                </div>
                @else
                <div id="hrec-saved-list"></div>
                @endif

                <script id="hrec-saved-data" type="application/json">
                    {!! json_encode($savedCalculations->keyBy('id')->map(fn($sc) => [
                        'id'              => $sc->id,
                        'name'            => $sc->name,
                        'fuel_type'       => $sc->fuel_type,
                        'boiler_power'    => $sc->boiler_power,
                        'boiler_eff'      => $sc->boiler_efficiency,
                        'flue_temp_in'    => $sc->flue_temp_in,
                        'mass_flow'       => $sc->mass_flow,
                        'xh2o'            => $sc->xh2o,
                        'medium_type'     => $sc->medium_type,
                        'medium_temp'     => $sc->medium_temp_supply,
                        'medium_ret'      => $sc->medium_temp_return,
                        'medium_pres'     => $sc->medium_pressure,
                        'medium_flow'     => $sc->medium_flow,
                        'exchangers'      => $sc->exchangers ?? [],
                        'result_dry'      => $sc->result_dry_kw,
                        'result_wet'      => $sc->result_wet_kw,
                        'result_total'    => $sc->result_total_kw,
                        'created_at'      => $sc->created_at->format('d.m.Y H:i'),
                        'fuel_label'      => $sc->fuelLabel(),
                        'medium_label'    => $sc->mediumLabel(),
                    ])->values()) !!}
                </script>
                @endauth
            </div>
        </div>

        {{-- ── JavaScript ── --}}
        <script>
        // ── Accordion toggle ─────────────────────────────────────
        function ecAccToggle(id) {
            const el = document.getElementById(id);
            if (el) el.classList.toggle('open');
        }

        // ── Unit converter ──────────────────────────────────────────
        const EC_GAS_PLN_PER_GJ = {{ $gasPricePln['ok'] ? (float) $gasPricePln['priceGjPln'] : 'null' }};

        const ecFactors = { gj:1, kwh:1/0.0036, mwh:1/3.6, toe:1/41.868 };
        const ecDecimals = { gj:5, kwh:2, mwh:5, toe:2 };

        function ecConvert(from, rawVal) {
            const val = parseFloat(rawVal);
            const plnEl = document.getElementById('ec-pln-value');
            if (isNaN(val) || rawVal === '') {
                ['gj','kwh','mwh','toe'].forEach(id => {
                    const el = document.getElementById('ec-' + id);
                    if (el && id !== from) el.value = '';
                });
                plnEl.textContent = '— PLN';
                const wv = document.getElementById('ec-white-value'); if (wv) wv.textContent = '— PLN';
                const ckg = document.getElementById('ec-co2-kg'); if (ckg) ckg.textContent = '—';
                const ct  = document.getElementById('ec-co2-t');  if (ct)  ct.textContent  = '—';
                const cn  = document.getElementById('ec-co2-note'); if (cn) cn.innerHTML = '&nbsp;';
                ecCalcGasM3Price();
                return;
            }
            let gj;
            switch(from) {
                case 'gj':  gj = val; break;
                case 'kwh': gj = val * 0.0036; break;
                case 'mwh': gj = val * 3.6; break;
                case 'toe': gj = val * 41.868; break;
                default: return;
            }
            for (const [id, factor] of Object.entries(ecFactors)) {
                if (id === from) continue;
                const el = document.getElementById('ec-' + id);
                if (!el) continue;
                el.value = parseFloat((gj * factor).toFixed(ecDecimals[id] || 4));
            }
            const gasPriceEl = document.getElementById('ec-gas-price');
            const gasUnitEl  = document.getElementById('ec-gas-unit');
            const gasUnit    = gasUnitEl ? gasUnitEl.value : 'gj'; // 'gj' or 'mwh'
            const gasPrice   = gasPriceEl ? parseFloat(gasPriceEl.value) : NaN;
            if (!isNaN(gasPrice) && gasPrice > 0 && gj > 0) {
                // gasUnit='gj': PLN = GJ × PLN/GJ ; gasUnit='mwh': PLN = MWh × PLN/MWh = (GJ/3.6) × PLN/MWh
                const pln = gasUnit === 'mwh' ? (gj / 3.6) * gasPrice : gj * gasPrice;
                plnEl.textContent = pln.toLocaleString('pl-PL', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' PLN';
            } else {
                plnEl.textContent = '— PLN (brak danych rynkowych)';
            }
            ecCalcGasM3Price();
            ecUpdateExtra();
        }

        function ecUpdateExtra() {
            const gjEl = document.getElementById('ec-gj');
            const gj   = parseFloat(gjEl ? gjEl.value : 'NaN');

            // ── Białe certyfikaty ──
            const whiteEl    = document.getElementById('ec-white-value');
            const whitePrEl  = document.getElementById('ec-white-price');
            const whitePrice = parseFloat(whitePrEl ? whitePrEl.value : 'NaN');
            if (whiteEl) {
                if (!isNaN(gj) && gj > 0 && !isNaN(whitePrice) && whitePrice > 0) {
                    const toeVal   = gj / 41.868;
                    const whitePln = toeVal * whitePrice;
                    whiteEl.textContent = whitePln.toLocaleString('pl-PL', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' PLN';
                } else {
                    whiteEl.textContent = '— PLN';
                }
            }

            // ── Redukcja CO₂ ──
            const co2FuelEl  = document.getElementById('ec-co2-fuel');
            const co2KgEl    = document.getElementById('ec-co2-kg');
            const co2TEl     = document.getElementById('ec-co2-t');
            const co2NoteEl  = document.getElementById('ec-co2-note');
            const co2Factor  = parseFloat(co2FuelEl ? co2FuelEl.value : 'NaN'); // g CO₂/kWh
            if (co2KgEl && co2TEl) {
                if (!isNaN(gj) && gj > 0 && !isNaN(co2Factor)) {
                    const kwhVal = gj / 0.0036;
                    const co2Kg  = kwhVal * co2Factor / 1000;
                    const co2T   = co2Kg / 1000;
                    co2KgEl.textContent = co2Kg.toLocaleString('pl-PL', {minimumFractionDigits:1, maximumFractionDigits:1});
                    co2TEl.textContent  = co2T.toLocaleString('pl-PL',  {minimumFractionDigits:3, maximumFractionDigits:3});
                    if (co2NoteEl) co2NoteEl.innerHTML =
                        (gj / 0.0036).toLocaleString('pl-PL', {maximumFractionDigits:0}) + ' kWh<br>× ' + co2Factor + ' g/kWh';
                } else {
                    co2KgEl.textContent = '—';
                    co2TEl.textContent  = '—';
                    if (co2NoteEl) co2NoteEl.innerHTML = '&nbsp;';
                }
            }
        }

        // Converts the displayed gas price when the unit dropdown changes (GJ ↔ MWh)
        function ecGasPriceUnitChange() {
            const unitEl  = document.getElementById('ec-gas-unit');
            const priceEl = document.getElementById('ec-gas-price');
            if (!unitEl || !priceEl) return;
            const val = parseFloat(priceEl.value);
            if (!isNaN(val) && val > 0) {
                if (unitEl.value === 'mwh') {
                    // was PLN/GJ → convert to PLN/MWh: × 3.6
                    priceEl.value = parseFloat((val * 3.6).toFixed(2));
                } else {
                    // was PLN/MWh → convert to PLN/GJ: ÷ 3.6
                    priceEl.value = parseFloat((val / 3.6).toFixed(2));
                }
            }
            ecCalcGasM3Price();
            ecConvert('gj', document.getElementById('ec-gj').value);
        }

        // Price per m³ of natural gas GZ-50 (Hu = 9.444 kWh/m³ = 0.034 GJ/m³)
        function ecCalcGasM3Price() {
            const priceEl = document.getElementById('ec-gas-price');
            const unitEl  = document.getElementById('ec-gas-unit');
            const m3El    = document.getElementById('ec-gas-price-m3');
            if (!m3El) return;
            const price = parseFloat(priceEl ? priceEl.value : 'NaN');
            const unit  = unitEl ? unitEl.value : 'gj';
            if (!isNaN(price) && price > 0) {
                // GZ-50: 1 m³ = 0.034 GJ = 0.009444 MWh
                const priceM3 = unit === 'mwh' ? price * 0.009444 : price * 0.034;
                m3El.textContent = priceM3.toLocaleString('pl-PL', {minimumFractionDigits:2, maximumFractionDigits:2});
            } else {
                m3El.textContent = '—';
            }
        }

        // ── Flue gas calculator ──────────────────────────────────────
        const EC_FUEL = {
            gas:  { v0air:9.52,  v0flue:10.50, rho:1.25, hu:9.444,  fuelUnit:'m³/h',  unit:'m³ gazu',  info:'GZ-50: V₀<sub>pow</sub>=9.52 m³/m³<sub>gaz</sub>, V₀<sub>sp</sub>=10.50 m³/m³<sub>gaz</sub>, ρ<sub>sp</sub>=1.25 kg/m³' },
            coal: { v0air:6.80,  v0flue:7.30,  rho:1.30, hu:7.222,  fuelUnit:'kg/h',  unit:'kg węgla', info:'Węgiel typ-31: V₀<sub>pow</sub>=6.80 m³/kg, V₀<sub>sp</sub>=7.30 m³/kg, ρ<sub>sp</sub>=1.30 kg/m³' },
            oil:  { v0air:10.50, v0flue:11.30, rho:1.28, hu:11.806, fuelUnit:'kg/h',  unit:'kg oleju', info:'Olej Eo-L: V₀<sub>pow</sub>=10.50 m³/kg, V₀<sub>sp</sub>=11.30 m³/kg, ρ<sub>sp</sub>=1.28 kg/m³' },
        };

        function ecFlueCalc() {
            const fuelKey = document.getElementById('ec-fuel').value;
            const o2raw   = parseFloat(document.getElementById('ec-o2').value);
            const fuel    = EC_FUEL[fuelKey];

            const o2Clamped = Math.max(0, Math.min(18, isNaN(o2raw) ? 0 : o2raw));
            const thumb = document.getElementById('ec-o2-thumb');
            if (thumb) thumb.style.left = ((o2Clamped / 18) * 100) + '%';

            const infoEl = document.getElementById('ec-fuel-info');
            if (infoEl) infoEl.innerHTML = fuel.info;

            document.getElementById('ec-vol-flue-unit').textContent  = 'm³/'  + fuel.unit;
            document.getElementById('ec-mass-flue-unit').textContent = 'kg/'  + fuel.unit;

            const fuelFlowUnitEl = document.getElementById('ec-fuel-flow-unit');
            if (fuelFlowUnitEl) fuelFlowUnitEl.textContent = fuel.fuelUnit;

            if (isNaN(o2raw) || o2raw < 0 || o2raw >= 21) {
                ['ec-lambda','ec-vol-flue','ec-mass-flue','ec-fuel-flow','ec-flue-flow','ec-flue-mass-flow','ec-flue-mass-flow-s'].forEach(id => {
                    const el = document.getElementById(id); if (el) el.textContent = '—';
                });
                const pn = document.getElementById('ec-power-note');
                if (pn) pn.style.display = 'none';
                return;
            }

            const lambda = 21 / (21 - o2raw);
            const vFlue  = fuel.v0flue + (lambda - 1) * fuel.v0air;
            const mFlue  = vFlue * fuel.rho;
            const dew    = fuelKey === 'gas' ? 55 : (fuelKey === 'oil' ? 52 : 48);

            document.getElementById('ec-lambda').textContent    = lambda.toFixed(3);
            document.getElementById('ec-vol-flue').textContent  = vFlue.toFixed(2);
            document.getElementById('ec-mass-flue').textContent = mFlue.toFixed(2);
            document.getElementById('ec-dew').textContent       = '~' + dew;

            const noteEl = document.getElementById('ec-flue-note');
            if (noteEl) {
                const q = lambda < 1.05 ? '⚠️ Niedobór powietrza!' : (lambda < 1.3 ? '✅ Prawidłowy zakres' : (lambda < 1.6 ? '⚠️ Duży nadmiar powietrza' : '❌ Zbyt duży nadmiar − straty kominowe!'));
                noteEl.innerHTML = `λ = ${lambda.toFixed(3)} — ${q}<br>Obj. spalin = ${fuel.v0flue.toFixed(2)} + (${lambda.toFixed(3)}−1)×${fuel.v0air.toFixed(2)} = <strong>${vFlue.toFixed(2)} m³/${fuel.unit}</strong> · masa = ${mFlue.toFixed(2)} kg/${fuel.unit}`;
            }

            // Przelicznik mocy kotła
            const powerRaw = parseFloat((document.getElementById('ec-power') || {}).value);
            const effRaw   = parseFloat((document.getElementById('ec-eff')   || {}).value || '90');
            const powerNote = document.getElementById('ec-power-note');

            if (!isNaN(powerRaw) && powerRaw > 0 && !isNaN(effRaw) && effRaw > 0) {
                const eta          = effRaw / 100;
                const fuelFlowRate = powerRaw / (eta * fuel.hu);
                const flueVolFlow  = fuelFlowRate * vFlue;
                const flueMassFlow = flueVolFlow  * fuel.rho;
                const flueMassFlowS = flueMassFlow / 3600;

                const ffEl = document.getElementById('ec-fuel-flow');
                const fvEl = document.getElementById('ec-flue-flow');
                const fmEl = document.getElementById('ec-flue-mass-flow');
                const fsEl = document.getElementById('ec-flue-mass-flow-s');
                if (ffEl) ffEl.textContent = fuelFlowRate.toFixed(2);
                if (fvEl) fvEl.textContent = flueVolFlow.toFixed(1);
                if (fmEl) fmEl.textContent = flueMassFlow.toFixed(1);
                if (fsEl) fsEl.textContent = flueMassFlowS.toFixed(4);

                if (powerNote) {
                    powerNote.style.display = '';
                    powerNote.innerHTML = `Moc kotła <strong>${powerRaw} kW</strong>, sprawność <strong>${effRaw}%</strong> → zużycie paliwa: <strong>${fuelFlowRate.toFixed(2)} ${fuel.fuelUnit}</strong> → przepływ spalin: <strong>${flueVolFlow.toFixed(1)} m³/h</strong>, masa spalin: <strong>${flueMassFlow.toFixed(1)} kg/h</strong> = <strong>${flueMassFlowS.toFixed(4)} kg/s</strong>.`;
                }
            } else {
                ['ec-fuel-flow','ec-flue-flow','ec-flue-mass-flow','ec-flue-mass-flow-s'].forEach(id => {
                    const el = document.getElementById(id); if (el) el.textContent = '—';
                });
                if (powerNote) powerNote.style.display = 'none';
            }
        }

        // ── CO₂ from electricity calculator ─────────────────────────
        // Wskaźniki w g CO₂/kWh (źródło: KOBiZE {{ $co2ElYear }})
        const EC_CO2EL_FACTORS = {
            comb: { value: {{ $co2ElCombFactor }}, labelShort: 'źródła spalania' },
            nat:  { value: {{ $co2ElNatFactor }},  labelShort: 'krajowy z OZE' },
        };

        function ecCo2ElCalc() {
            const kwh     = parseFloat(document.getElementById('co2el-kwh').value);
            const typeKey = document.getElementById('co2el-type').value;
            const factor  = EC_CO2EL_FACTORS[typeKey];
            const noteEl  = document.getElementById('co2el-note');

            if (isNaN(kwh) || kwh <= 0) {
                ['co2el-kg','co2el-t','co2el-mwh'].forEach(id => {
                    const el = document.getElementById(id); if (el) el.textContent = '—';
                });
                if (noteEl) noteEl.style.display = 'none';
                return;
            }

            // factor.value jest w g CO₂/kWh → kg CO₂ = kWh × g/kWh / 1000
            const kgCo2 = kwh * factor.value / 1000;
            const tCo2  = kgCo2 / 1000;
            const mwh   = kwh / 1000;

            const kgEl = document.getElementById('co2el-kg');
            const tEl  = document.getElementById('co2el-t');
            const mEl  = document.getElementById('co2el-mwh');
            if (kgEl) kgEl.textContent = kgCo2.toLocaleString('pl-PL', {maximumFractionDigits:1});
            if (tEl)  tEl.textContent  = tCo2.toLocaleString('pl-PL',  {maximumFractionDigits:3});
            if (mEl)  mEl.textContent  = mwh.toLocaleString('pl-PL',   {maximumFractionDigits:3});

            if (noteEl) {
                noteEl.style.display = '';
                noteEl.innerHTML = `${kwh.toLocaleString('pl-PL')} kWh × ${factor.value} g CO₂/kWh (${factor.labelShort}) = <strong>${kgCo2.toLocaleString('pl-PL', {maximumFractionDigits:1})} kg CO₂</strong> = <strong>${tCo2.toLocaleString('pl-PL', {maximumFractionDigits:3})} t CO₂</strong>.<br><span style="font-size:11px;">Wskaźnik: KOBiZE, rok {{ $co2ElYear }} · <a href="https://www.kobize.pl/uploads/materialy/materialy_do_pobrania/aktualnosci/2025/142_Wskazniki_emisyjnosci_2025.pdf" target="_blank" rel="noopener noreferrer" style="color:#0e89d8;">pobierz PDF</a></span>`;
            }
        }

        document.addEventListener('DOMContentLoaded', () => { ecFlueCalc(); ecCo2ElCalc(); hrecOnFuelChange(); hrecAddHX(); });

        // ── Kalkulator mocy odzyskanej ze spalin ──────────────────────────────
        const HREC_FUEL = {
            gas: {
                cp: 1.04, xH2O: 0.190, tDew: 57, r: 2358,
                tFlueTyp: 200, etaTyp: 90, hu: 9.444,
                v0air: 9.52, v0flue: 10.50, rhoSp: 1.25, fuelUnit: 'm³/h',
                effHint: 'Sugerowana dla gazu: 88–92%',
                tinHint: 'Sugerowana dla gazu: 160–220°C',
                xh2oHint: 'Sugerowana dla gazu: ~0.190 kg/kg (GZ-50)',
            },
            coal: {
                cp: 1.02, xH2O: 0.085, tDew: 47, r: 2380,
                tFlueTyp: 250, etaTyp: 82, hu: 6.667,
                v0air: 6.80, v0flue: 7.30, rhoSp: 1.30, fuelUnit: 'kg/h',
                effHint: 'Sugerowana dla węgla: 78–84%',
                tinHint: 'Sugerowana dla węgla: 200–280°C',
                xh2oHint: 'Sugerowana dla węgla: ~0.085 kg/kg',
            },
        };

        let hrecNextId  = 0;
        let hrecHxList  = [];
        let hrecAutoFlow = false;
        let hrecWaterManualTout = {}; // uid -> true when T_wyjście wody entered manually

        function hrecOnFuelChange() {
            const fuelKey = document.getElementById('hrec-fuel').value;
            const fuel    = HREC_FUEL[fuelKey];
            document.getElementById('hrec-dew-display').textContent = fuel.tDew + '°C';
            document.getElementById('hrec-eff-hint').textContent    = fuel.effHint;
            document.getElementById('hrec-tin-hint').textContent    = fuel.tinHint;
            document.getElementById('hrec-xh2o-hint').textContent   = fuel.xh2oHint;
            const effEl  = document.getElementById('hrec-eff');
            const tinEl  = document.getElementById('hrec-t-in');
            const xEl    = document.getElementById('hrec-xh2o');
            if (!effEl.value) effEl.placeholder = 'np. ' + fuel.etaTyp;
            if (!tinEl.value) tinEl.placeholder  = 'np. ' + fuel.tFlueTyp;
            if (!xEl.value)   xEl.placeholder    = 'np. ' + fuel.xH2O;
            hrecOnPowerChange();
        }

        function hrecOnPowerChange() {
            const fuelKey = document.getElementById('hrec-fuel').value;
            const fuel    = HREC_FUEL[fuelKey];
            const power   = parseFloat(document.getElementById('hrec-power').value);
            const effRaw  = parseFloat(document.getElementById('hrec-eff').value);
            const eff     = isNaN(effRaw) ? fuel.etaTyp : effRaw;
            const o2Raw   = parseFloat((document.getElementById('hrec-o2') || {}).value);
            const o2      = (!isNaN(o2Raw) && o2Raw >= 0 && o2Raw < 21) ? o2Raw : 3;
            const lambda  = 21 / (21 - o2);
            const vFlue   = fuel.v0flue + (lambda - 1) * fuel.v0air;
            // update lambda display
            const ldEl = document.getElementById('hrec-lambda-display');
            if (ldEl) ldEl.textContent = lambda.toFixed(3);
            const mfEl    = document.getElementById('hrec-mass-flow');
            const mfHint  = document.getElementById('hrec-mflow-hint');
            if (!isNaN(power) && power > 0) {
                const eta      = eff / 100;
                const fuelFlow = power / (eta * fuel.hu);
                const massFlow = fuelFlow * vFlue * fuel.rhoSp;
                if (!mfEl.value || hrecAutoFlow) {
                    mfEl.value = Math.round(massFlow);
                    mfEl.classList.add('suggested');
                    hrecAutoFlow = true;
                    mfHint.innerHTML = 'Obliczono: ' + power + ' kW, η=' + eff.toFixed(0) + '%, λ=' + lambda.toFixed(3) + ' → <strong>' + fuelFlow.toFixed(1) + ' ' + fuel.fuelUnit + '</strong> → <strong>' + Math.round(massFlow) + ' kg/h</strong>';
                }
            }
            hrecMediumCalc();
            hrecCalc();
        }

        function hrecMassFlowEdited() {
            hrecAutoFlow = false;
            document.getElementById('hrec-mass-flow').classList.remove('suggested');
            hrecCalc();
        }

        function hrecOnMediumChange() {
            const medium = document.getElementById('hrec-medium').value;
            const hints  = {
                water:  { supLabel: 'Temperatura zasilania [°C]', temp: 'Woda grzewcza zasilanie: 70–90°C',  pres: 'Sieć c.o.: 3–6 bar',      ret: true,  retHint: 'Sieć c.o.: 50–60°C · ΔT = T_zas − T_pow', retPH: 'np. 60', retLabel: 'Temperatura powrotu [°C]',  flowLabel: 'Produkcja wody [kg/h]' },
                steam:  { supLabel: 'Temperatura pary [°C]',      temp: 'Para 4 bar → ~144°C; 10 bar → ~180°C — obliczam z ciśnienia', pres: 'Sugerowane: 4–16 bar', ret: false, retHint: '', retPH: '', retLabel: '',                                                                                                                    flowLabel: 'Produkcja pary [kg/h]' },
                glycol: { supLabel: 'Temperatura zasilania [°C]', temp: 'Glikol: temp. zasilania 50–70°C',  pres: 'Sugerowane: 3–5 bar',     ret: true,  retHint: 'Typowy powrót: 40–55°C',                  retPH: 'np. 45', retLabel: 'Temperatura powrotu [°C]',  flowLabel: 'Produkcja glikolu [kg/h]' },
                air:    { supLabel: 'Temperatura wylotowa [°C]',  temp: 'Powietrze wylot: 30–60°C',          pres: 'Atmosferyczne: ~1 bar',   ret: true,  retHint: 'Temperatura wlotowa powietrza: 15–25°C',  retPH: 'np. 20', retLabel: 'Temperatura wlotowa [°C]',  flowLabel: 'Produkcja powietrza [kg/h]' },
                other:  { supLabel: 'Temperatura czynnika [°C]',  temp: 'Podaj temperaturę zasilania',       pres: 'Podaj ciśnienie robocze', ret: true,  retHint: 'Podaj temperaturę powrotu / wlotową',    retPH: '',       retLabel: 'Temperatura powrotu [°C]',  flowLabel: 'Produkcja czynnika [kg/h]' },
            };
            const h = hints[medium] || hints.other;
            document.getElementById('hrec-supply-temp-label').textContent = h.supLabel;
            document.getElementById('hrec-medium-hint').textContent       = h.temp;
            document.getElementById('hrec-pres-hint').textContent         = h.pres;
            document.getElementById('hrec-flow-label').textContent        = h.flowLabel || 'Produkcja czynnika [kg/h]';
            const retWrap = document.getElementById('hrec-ret-wrap');
            if (h.ret) {
                retWrap.style.display = '';
                document.getElementById('hrec-ret-temp-label').textContent          = h.retLabel;
                document.getElementById('hrec-medium-ret').placeholder             = h.retPH;
                document.getElementById('hrec-ret-hint').textContent               = h.retHint;
                // Auto-fill steam temp from pressure
            } else {
                retWrap.style.display = 'none';
                document.getElementById('hrec-medium-ret').value = '';
            }
            // For steam: autofill temp from pressure
            if (medium === 'steam') {
                const pres = parseFloat(document.getElementById('hrec-medium-pres').value);
                if (!isNaN(pres) && pres > 0) {
                    const tSat = hrecSteamTsat(pres);
                    const tEl  = document.getElementById('hrec-medium-temp');
                    tEl.placeholder = 'auto: ' + tSat.toFixed(0) + '°C';
                    document.getElementById('hrec-medium-hint').textContent = 'Przy ' + pres + ' bar → T_nasycenia ≈ ' + tSat.toFixed(1) + '°C (automatycznie)';
                }
            }
            hrecMediumCalc();
            hrecCalc();
        }

        // ── Uproszczone tablice pary nasyconej ──────────────────────────────
        const HREC_STEAM_TABLE = [
            { p:0.5,  t: 81.3, hg:2645, hf:340,  hfg:2305 },
            { p:1,    t:100.0, hg:2676, hf:419,  hfg:2257 },
            { p:1.5,  t:111.4, hg:2693, hf:467,  hfg:2226 },
            { p:2,    t:120.2, hg:2707, hf:505,  hfg:2202 },
            { p:3,    t:133.5, hg:2725, hf:562,  hfg:2163 },
            { p:4,    t:143.6, hg:2738, hf:605,  hfg:2133 },
            { p:5,    t:151.8, hg:2748, hf:640,  hfg:2108 },
            { p:6,    t:158.8, hg:2756, hf:670,  hfg:2086 },
            { p:8,    t:170.4, hg:2769, hf:721,  hfg:2048 },
            { p:10,   t:179.9, hg:2778, hf:763,  hfg:2015 },
            { p:12,   t:187.9, hg:2784, hf:799,  hfg:1985 },
            { p:16,   t:201.4, hg:2794, hf:858,  hfg:1936 },
            { p:20,   t:212.4, hg:2799, hf:908,  hfg:1891 },
            { p:25,   t:224.0, hg:2803, hf:962,  hfg:1841 },
            { p:40,   t:250.4, hg:2801, hf:1087, hfg:1714 },
        ];

        function hrecSteamInterp(pres, prop) {
            const tbl = HREC_STEAM_TABLE;
            if (pres <= tbl[0].p) return tbl[0][prop];
            if (pres >= tbl[tbl.length-1].p) return tbl[tbl.length-1][prop];
            for (let i = 0; i < tbl.length - 1; i++) {
                if (pres >= tbl[i].p && pres <= tbl[i+1].p) {
                    const f = (pres - tbl[i].p) / (tbl[i+1].p - tbl[i].p);
                    return tbl[i][prop] + f * (tbl[i+1][prop] - tbl[i][prop]);
                }
            }
            return tbl[tbl.length-1][prop];
        }

        function hrecSteamTsat(pres) { return hrecSteamInterp(pres, 't'); }

        // ── Stała: Cp czynników [kJ/(kg·K)] ────────────────────────────────
        const HREC_CP = { water:4.18, glycol:3.5, air:1.005, other:4.0 };

        // ── Zmienna kontroli kierunku obliczeń ──────────────────────────────
        let hrecMedAutoFlow = true; // true = flow suggested from power; false = power from flow

        function hrecMediumFlowEdited() {
            hrecMedAutoFlow = false;
            const flowEl = document.getElementById('hrec-medium-flow');
            const badgeEl = document.getElementById('hrec-flow-badge');
            flowEl.classList.remove('suggested');
            if (badgeEl) badgeEl.textContent = 'wprowadzono';
            hrecMediumCalc();
        }

        function hrecMediumCalc() {
            const medium    = document.getElementById('hrec-medium').value;
            const tSupRaw   = parseFloat(document.getElementById('hrec-medium-temp').value);
            const presRaw   = parseFloat(document.getElementById('hrec-medium-pres').value);
            const tRetRaw   = parseFloat(document.getElementById('hrec-medium-ret').value);
            const powerRaw  = parseFloat(document.getElementById('hrec-power').value);
            const flowEl    = document.getElementById('hrec-medium-flow');
            const flowHint  = document.getElementById('hrec-flow-hint');
            const badgeEl   = document.getElementById('hrec-flow-badge');
            const resultCard = document.getElementById('hrec-medium-result-card');
            const resultText = document.getElementById('hrec-medium-result-text');

            let lines = [];

            // ── Para nasycona ──
            if (medium === 'steam') {
                const pres  = isNaN(presRaw) ? 4 : presRaw;
                const tSat  = hrecSteamTsat(pres);
                const hfg   = hrecSteamInterp(pres, 'hfg');  // kJ/kg
                const hf    = hrecSteamInterp(pres, 'hf');
                const hg    = hrecSteamInterp(pres, 'hg');
                // Enthalpia zasilania (zimna woda do kotła: ~20°C → 84 kJ/kg)
                const hFeedWater = 4.18 * 20; // ≈ 84 kJ/kg
                const deltaH = hg - hFeedWater; // kJ/kg — od wody zasilającej do pary

                // Autofill temp
                const tEl = document.getElementById('hrec-medium-temp');
                tEl.placeholder = 'auto: ' + tSat.toFixed(0) + '°C';
                document.getElementById('hrec-medium-hint').textContent =
                    'Przy ' + pres.toFixed(1) + ' bar → T_nasc ≈ ' + tSat.toFixed(1) + '°C · h_fg=' + Math.round(hfg) + ' kJ/kg · h_g=' + Math.round(hg) + ' kJ/kg';

                if (hrecMedAutoFlow && !isNaN(powerRaw) && powerRaw > 0) {
                    // power → flow
                    const flowKgh = powerRaw * 3600 / deltaH;
                    flowEl.value = flowKgh.toFixed(1);
                    flowEl.classList.add('suggested');
                    if (badgeEl) badgeEl.textContent = 'sugerowany';
                    flowHint.innerHTML = 'Przy ' + powerRaw + ' kW → <strong>' + flowKgh.toFixed(1) + ' kg/h</strong> pary (' + pres.toFixed(1) + ' bar, Δh=' + Math.round(deltaH) + ' kJ/kg)';
                    lines = [
                        '<strong>Para nasycona</strong> @ ' + pres.toFixed(1) + ' bar',
                        'T_nasycenia = ' + tSat.toFixed(1) + '°C',
                        '\u0394h (woda 20\u00b0C \u2192 para) = ' + Math.round(deltaH) + '\u00a0kJ/kg',
                        'Moc ' + powerRaw + ' kW \u2192 <strong>' + flowKgh.toFixed(1) + ' kg/h</strong> pary',
                    ];
                } else if (!hrecMedAutoFlow && !isNaN(parseFloat(flowEl.value)) && parseFloat(flowEl.value) > 0) {
                    // flow → power
                    const flowKgh = parseFloat(flowEl.value);
                    const impliedPower = flowKgh * deltaH / 3600;
                    lines = [
                        '<strong>Para nasycona</strong> @ ' + pres.toFixed(1) + ' bar',
                        'T_nasycenia = ' + tSat.toFixed(1) + '°C · Δh=<span style="white-space:nowrap">' + Math.round(deltaH) + ' kJ/kg</span>',
                        flowKgh.toFixed(1) + ' kg/h pary → potrzebna moc kotła: <strong style="color:#c47c00;">' + impliedPower.toFixed(0) + ' kW</strong>',
                        '<span style="font-size:11px;color:#4c6373;">Uwzględniono podgrzanie wody zasilającej od ~20°C</span>',
                    ];
                    flowHint.textContent = flowKgh.toFixed(1) + ' kg/h → moc kotła ≈ ' + impliedPower.toFixed(0) + ' kW';
                }
            } else {
                // ── Woda / Glikol / Powietrze / Inne ──
                const cp     = HREC_CP[medium] || 4.18;
                const tSup   = isNaN(tSupRaw) ? NaN : tSupRaw;
                const tRet   = isNaN(tRetRaw) ? NaN : tRetRaw;
                const deltaT = (!isNaN(tSup) && !isNaN(tRet)) ? Math.abs(tSup - tRet) : NaN;

                if (!isNaN(deltaT) && deltaT > 0) {
                    if (hrecMedAutoFlow && !isNaN(powerRaw) && powerRaw > 0) {
                        // power → flow
                        const flowKgh = powerRaw * 3600 / (cp * deltaT);
                        flowEl.value = flowKgh.toFixed(0);
                        flowEl.classList.add('suggested');
                        if (badgeEl) badgeEl.textContent = 'sugerowany';
                        flowHint.innerHTML = 'Przy ' + powerRaw + ' kW, ΔT=' + deltaT.toFixed(1) + '°C → <strong>' + flowKgh.toFixed(0) + ' kg/h</strong>';
                        const cpLabel = medium === 'glycol' ? 'glikol c_p=3.5' : (medium === 'air' ? 'powietrze c_p=1.005' : 'woda c_p=4.18');
                        lines = [
                            'ΔT = ' + tSup.toFixed(0) + ' − ' + tRet.toFixed(0) + ' = <strong>' + deltaT.toFixed(1) + '°C</strong>',
                            'c_p (' + cpLabel + ') = ' + cp + ' kJ/(kg·K)',
                            'Q = ṁ · c_p · ΔT → ṁ = Q / (c_p · ΔT)',
                            'Moc ' + powerRaw + ' kW → <strong>' + flowKgh.toFixed(0) + ' kg/h</strong>',
                        ];
                    } else if (!hrecMedAutoFlow && !isNaN(parseFloat(flowEl.value)) && parseFloat(flowEl.value) > 0) {
                        // flow → power
                        const flowKgh  = parseFloat(flowEl.value);
                        const impliedP = flowKgh * cp * deltaT / 3600;
                        const cpLabel  = medium === 'glycol' ? 'glikol c_p=3.5' : (medium === 'air' ? 'c_p=1.005' : 'c_p=4.18');
                        lines = [
                            'ΔT = ' + deltaT.toFixed(1) + '°C · ' + cpLabel + ' kJ/(kg·K)',
                            flowKgh.toFixed(0) + ' kg/h → Q = ' + flowKgh.toFixed(0) + ' × ' + cp + ' × ' + deltaT.toFixed(1) + ' / 3600',
                            'Potrzebna moc kotła: <strong style="color:#c47c00;">' + impliedP.toFixed(0) + ' kW</strong>',
                        ];
                        flowHint.textContent = flowKgh + ' kg/h, ΔT=' + deltaT.toFixed(1) + '°C → moc kotła ≈ ' + impliedP.toFixed(0) + ' kW';
                    } else if (isNaN(parseFloat(document.getElementById('hrec-power').value)) && isNaN(parseFloat(flowEl.value))) {
                        lines = ['Wpisz moc kotła lub przepływ czynnika, aby obliczyć drugą wartość.'];
                    }
                } else if (!isNaN(tSup) && isNaN(tRet)) {
                    const ph = (!isNaN(powerRaw) && powerRaw > 0) ? ' Moc kotła: <strong>' + powerRaw + ' kW</strong>.' : '';
                    lines = ['Podaj temperaturę powrotu/wlotową, aby obliczyć ΔT.' + ph];
                } else if (isNaN(tSup)) {
                    if (!isNaN(powerRaw) && powerRaw > 0) {
                        const cpL = HREC_CP[medium] || 4.18;
                        const f10 = Math.round(powerRaw * 3600 / (cpL * 10));
                        const f20 = Math.round(powerRaw * 3600 / (cpL * 20));
                        lines = [
                            'Moc kotła: <strong>' + powerRaw + ' kW</strong> → szacunkowy przepływ czynnika:',
                            'ΔT=10°C → <strong>' + f10.toLocaleString('pl-PL') + ' kg/h</strong> &nbsp;·&nbsp; ΔT=20°C → <strong>' + f20.toLocaleString('pl-PL') + ' kg/h</strong>',
                            '<span style="font-size:11px;color:#6b8294;">Wpisz temp. zasilania i powrotu, aby obliczyć dokładnie</span>',
                        ];
                    } else {
                        // brak mocy i temp — ukryj kartę
                    }
                }
            }

            if (lines.length > 0) {
                resultText.innerHTML = lines.join('<br>');
                resultCard.style.display = '';
            } else {
                resultCard.style.display = 'none';
            }
        }

        function hrecAddHX() {
            const uid    = hrecNextId++;
            hrecHxList.push(uid);
            const posNum  = hrecHxList.length;
            const isFirst = posNum === 1;
            const fuelKey = document.getElementById('hrec-fuel').value;
            const tDew    = HREC_FUEL[fuelKey].tDew;
            const toutPH  = isFirst ? 'np. 120' : 'np. 40';
            const toutH   = isFirst
                ? ('Powyżej ~' + tDew + '°C – suchy; poniżej – kondensacja')
                : 'Kolejny stopień chłodzenia spalin';
            const div = document.createElement('div');
            div.className = 'hrec-hx-block';
            div.id = 'hrec-hx-block-' + uid;
            div.draggable = true;
            div.addEventListener('dragstart',  hrecDragStart);
            div.addEventListener('dragend',    hrecDragEnd);
            div.addEventListener('dragover',   hrecDragOver);
            div.addEventListener('dragleave',  hrecDragLeave);
            div.addEventListener('drop',       hrecDrop);
            div.innerHTML =
                // ─── Nagłówek ───
                '<div class="hrec-hx-header">' +
                    '<div style="display:flex;align-items:center;gap:10px;">' +
                        '<span class="hrec-drag-handle" title="Przeciągnij aby zmienić kolejność wymienników">⠿</span>' +
                        '<span class="hrec-hx-title" id="hrec-hx-title-' + uid + '">Wymiennik ' + posNum + '</span>' +
                        '<span class="hrec-hx-tin" id="hrec-hx-' + uid + '-tin">T<sub>wej</sub>: —</span>' +
                    '</div>' +
                    '<div style="display:flex;align-items:center;gap:8px;">' +
                        '<span id="hrec-hx-' + uid + '-mode"></span>' +
                        '<button class="hrec-remove-btn" onclick="hrecRemoveHX(' + uid + ')" title="Usuń wymiennik">✕</button>' +
                    '</div>' +
                '</div>' +
                // ─── Strona spalin ───
                '<div class="hrec-hx-grid">' +
                    '<div>' +
                        '<div class="hrec-label">T<sub>wyj</sub> spalin [°C] <span class="hrec-badge">wymagane</span></div>' +
                        '<input type="number" id="hrec-hx-' + uid + '-tout" class="hrec-input" min="5" max="1000" step="1" placeholder="' + toutPH + '" oninput="hrecCalc()">' +
                    '</div>' +
                    '<div class="hrec-result-card hrec-dry hrec-tip-host hrec-tip-right" id="hrec-hx-' + uid + '-dry-card" data-tip="Podaj T_wyj spalin aby zobaczyć obliczenie" style="padding:7px 10px;cursor:help;">' +
                        '<div style="display:flex;justify-content:space-between;align-items:baseline;gap:4px;">' +
                            '<span class="hrec-result-label" style="margin:0;">🌡️ Moc sucha</span>' +
                            '<span style="font-size:10px;color:#6b8294;">ciepło jawne</span>' +
                        '</div>' +
                        '<div style="display:flex;align-items:baseline;gap:4px;margin-top:3px;">' +
                            '<span class="hrec-result-val" id="hrec-hx-' + uid + '-dry">—</span>' +
                            '<span class="hrec-result-unit">kW</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="hrec-result-card hrec-wet hrec-tip-host hrec-tip-right" id="hrec-hx-' + uid + '-wet-card" data-tip="Podaj T_wyj spalin aby zobaczyć obliczenie" style="padding:7px 10px;cursor:help;">' +
                        '<div style="display:flex;justify-content:space-between;align-items:baseline;gap:4px;">' +
                            '<span class="hrec-result-label" style="margin:0;">💧 Moc mokra</span>' +
                            '<span style="font-size:10px;color:#6b8294;">kondensacja</span>' +
                        '</div>' +
                        '<div style="display:flex;align-items:baseline;gap:4px;margin-top:3px;">' +
                            '<span class="hrec-result-val" id="hrec-hx-' + uid + '-wet">—</span>' +
                            '<span class="hrec-result-unit">kW</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="hrec-result-card hrec-tot hrec-tip-host hrec-tip-right" id="hrec-hx-' + uid + '-total-card" data-tip="Podaj T_wyj spalin aby zobaczyć obliczenie" style="padding:7px 10px;cursor:help;">' +
                        '<div style="display:flex;justify-content:space-between;align-items:baseline;gap:4px;">' +
                            '<span class="hrec-result-label" style="margin:0;">⚡ Moc łączna</span>' +
                            '<span style="font-size:10px;color:#6b8294;">sucha + mokra</span>' +
                        '</div>' +
                        '<div style="display:flex;align-items:baseline;gap:4px;margin-top:3px;">' +
                            '<span class="hrec-result-val" id="hrec-hx-' + uid + '-total">—</span>' +
                            '<span class="hrec-result-unit">kW</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                // ─── Strona wody / czynnika ───
                '<div style="margin-top:8px;border-top:1px dashed #c9d7e3;padding-top:8px;">' +
                    '<div style="font-size:11px;font-weight:700;color:#1d4f73;margin-bottom:6px;">' +
                        '💧 Bilans strony wody <span style="font-weight:400;color:#8aacbe;">(kontrolny)</span>' +
                    '</div>' +
                    '<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:8px;align-items:start;">' +
                        '<div>' +
                            '<div class="hrec-label" style="font-size:11px;">Przepływ [kg/h]</div>' +
                            '<input type="number" id="hrec-hx-' + uid + '-wflow" class="hrec-input suggested" min="0" step="1" placeholder="z globalnych" oninput="hrecWaterFlowEdited(' + uid + ')">' +
                            '<div class="hrec-hint" id="hrec-hx-' + uid + '-wflow-hint">z parametrów czynnika ↑</div>' +
                        '</div>' +
                        '<div>' +
                            '<div class="hrec-label" style="font-size:11px;">T<sub>wej</sub> wody [°C]</div>' +
                            '<input type="number" id="hrec-hx-' + uid + '-wtin" class="hrec-input suggested" min="-30" max="400" step="0.5" placeholder="sugerowane" oninput="hrecWaterTinEdited(' + uid + ')">' +
                            '<div class="hrec-hint" id="hrec-hx-' + uid + '-wtin-hint">powrót / wyjście prev. HX</div>' +
                        '</div>' +
                        '<div>' +
                            '<div class="hrec-label" style="font-size:11px;">T<sub>wyj</sub> wody [°C] <span class="hrec-badge" id="hrec-hx-' + uid + '-wtout-badge">auto</span></div>' +
                            '<input type="number" id="hrec-hx-' + uid + '-wtout" class="hrec-input suggested" min="-30" max="400" step="0.5" placeholder="obliczam\u2026" oninput="hrecWaterToutEdited(' + uid + ')">' +
                            '<div class="hrec-hint" id="hrec-hx-' + uid + '-wtout-hint">z Q_spalin lub wpisz ręcznie</div>' +
                        '</div>' +
                        '<div id="hrec-hx-' + uid + '-balance" style="background:#f0f7ff;border-radius:9px;padding:7px 10px;font-size:12px;color:#355468;line-height:1.6;">' +
                            '<span style="color:#b0c8d8;">podaj przepływ i T<sub>wej</sub></span>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            document.getElementById('hrec-hx-container').appendChild(div);
            hrecCalc();
        }

        function hrecRemoveHX(uid) {
            if (hrecHxList.length <= 1) return; // nie usuwaj ostatniego
            hrecHxList = hrecHxList.filter(function(id) { return id !== uid; });
            delete hrecWaterManualTout[uid];
            const block = document.getElementById('hrec-hx-block-' + uid);
            if (block) block.remove();
            hrecHxList.forEach(function(id, idx) {
                const titleEl = document.getElementById('hrec-hx-title-' + id);
                if (titleEl) titleEl.textContent = 'Wymiennik ' + (idx + 1);
            });
            hrecCalc();
        }

        // ── Drag & drop – zmiana kolejności wymienników ────────────────────────
        let hrecDragSrcUid = null;

        function hrecDragStart(e) {
            hrecDragSrcUid = parseInt(this.id.replace('hrec-hx-block-', ''));
            this.classList.add('hrec-dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', String(hrecDragSrcUid));
        }
        function hrecDragEnd() {
            this.classList.remove('hrec-dragging');
            document.querySelectorAll('.hrec-hx-block').forEach(function(b) {
                b.classList.remove('hrec-drag-over-top', 'hrec-drag-over-bottom');
            });
        }
        function hrecDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            const rect = this.getBoundingClientRect();
            this.classList.remove('hrec-drag-over-top', 'hrec-drag-over-bottom');
            this.classList.add(e.clientY < rect.top + rect.height / 2 ? 'hrec-drag-over-top' : 'hrec-drag-over-bottom');
        }
        function hrecDragLeave() {
            this.classList.remove('hrec-drag-over-top', 'hrec-drag-over-bottom');
        }
        function hrecDrop(e) {
            e.preventDefault();
            this.classList.remove('hrec-drag-over-top', 'hrec-drag-over-bottom');
            const srcBlock = document.getElementById('hrec-hx-block-' + hrecDragSrcUid);
            const tgtBlock = this;
            if (!srcBlock || srcBlock === tgtBlock) return;
            const container = document.getElementById('hrec-hx-container');
            const rect = tgtBlock.getBoundingClientRect();
            if (e.clientY < rect.top + rect.height / 2) {
                container.insertBefore(srcBlock, tgtBlock);
            } else {
                container.insertBefore(srcBlock, tgtBlock.nextSibling);
            }
            // Przebuduj hrecHxList na podstawie aktualnej kolejności DOM
            hrecHxList = Array.from(container.querySelectorAll(':scope > .hrec-hx-block'))
                .map(function(b) { return parseInt(b.id.replace('hrec-hx-block-', '')); });
            // Przenumeruj nagłówki
            hrecHxList.forEach(function(id, idx) {
                const titleEl = document.getElementById('hrec-hx-title-' + id);
                if (titleEl) titleEl.textContent = 'Wymiennik ' + (idx + 1);
            });
            // Resetuj sugerowane T_wej wody (łańcuch zmienił się po przestawieniu)
            hrecHxList.forEach(function(id, idx) {
                if (idx > 0) {
                    const wTinEl = document.getElementById('hrec-hx-' + id + '-wtin');
                    if (wTinEl && wTinEl.classList.contains('suggested')) wTinEl.value = '';
                }
            });
            hrecCalc();
        }

        // ── Edycja strony wody ────────────────────────────────────────────────
        function hrecWaterFlowEdited(uid) {
            const el = document.getElementById('hrec-hx-' + uid + '-wflow');
            if (el) el.classList.remove('suggested');
            hrecCalc();
        }
        function hrecWaterTinEdited(uid) {
            const el = document.getElementById('hrec-hx-' + uid + '-wtin');
            if (el) el.classList.remove('suggested');
            hrecCalc();
        }
        function hrecWaterToutEdited(uid) {
            hrecWaterManualTout[uid] = true;
            const el    = document.getElementById('hrec-hx-' + uid + '-wtout');
            const badge = document.getElementById('hrec-hx-' + uid + '-wtout-badge');
            if (el)    el.classList.remove('suggested');
            if (badge) badge.textContent = 'ręcznie';
            hrecCalc();
        }

        function hrecCalc() {
            const fuelKey  = document.getElementById('hrec-fuel').value;
            const fuel     = HREC_FUEL[fuelKey];
            const tIn0     = parseFloat(document.getElementById('hrec-t-in').value);
            const massFlow = parseFloat(document.getElementById('hrec-mass-flow').value);
            const xH2ORaw  = parseFloat(document.getElementById('hrec-xh2o').value);
            const xH2O     = isNaN(xH2ORaw) ? fuel.xH2O : xH2ORaw;
            const noInputs = isNaN(tIn0) || isNaN(massFlow) || massFlow <= 0 || tIn0 <= 0;
            if (noInputs) {
                document.getElementById('hrec-summary').style.display = 'none';
                hrecHxList.forEach(function(uid) {
                    ['dry','wet','total'].forEach(function(p) {
                        const e = document.getElementById('hrec-hx-' + uid + '-' + p);
                        if (e) e.textContent = '—';
                    });
                    const m = document.getElementById('hrec-hx-' + uid + '-mode');
                    if (m) m.innerHTML = '';
                    const t = document.getElementById('hrec-hx-' + uid + '-tin');
                    if (t) t.innerHTML = 'T<sub>wej</sub>: —';
                });
                return;
            }
            let tCur        = tIn0;
            let sumDry      = 0;
            let sumWet      = 0;
            // Initialise water chain from global medium return temperature
            let waterTinCur = NaN;
            const globalRetTemp = parseFloat((document.getElementById('hrec-medium-ret') || {}).value);
            if (!isNaN(globalRetTemp)) waterTinCur = globalRetTemp;

            hrecHxList.forEach(function(uid, idx) {
                const tinEl  = document.getElementById('hrec-hx-' + uid + '-tin');
                if (tinEl) tinEl.innerHTML = 'T<sub>wej</sub>: ' + tCur.toFixed(1) + '°C';
                const tOutEl = document.getElementById('hrec-hx-' + uid + '-tout');
                const tOut   = tOutEl ? parseFloat(tOutEl.value) : NaN;
                const dryEl  = document.getElementById('hrec-hx-' + uid + '-dry');
                const wetEl  = document.getElementById('hrec-hx-' + uid + '-wet');
                const totEl  = document.getElementById('hrec-hx-' + uid + '-total');
                const modeEl = document.getElementById('hrec-hx-' + uid + '-mode');

                let qDry = 0, qWet = 0, qTotal = 0, flueOk = false;

                if (isNaN(tOut) || tOut <= 0) {
                    if (dryEl) dryEl.textContent  = '—';
                    if (wetEl) wetEl.textContent  = '—';
                    if (totEl) totEl.textContent  = '—';
                    if (modeEl) modeEl.innerHTML  = '';
                } else if (tOut >= tCur) {
                    if (dryEl) dryEl.innerHTML  = '<span style="color:#e53e3e;font-size:12px;">T_wyj ≥ T_wej</span>';
                    if (wetEl) wetEl.textContent = '—';
                    if (totEl) totEl.textContent = '—';
                    if (modeEl) modeEl.innerHTML = '';
                } else {
                    const tIn = tCur; // T_wejście do tego HX (dla tooltip)
                    // Ciepło jawne (suche)
                    qDry = massFlow * fuel.cp * (tCur - tOut) / 3600;
                    // Ciepło kondensacji (mokre) – poniżej punktu rosy
                    let frac = 0, mCond = 0;
                    if (tOut < fuel.tDew) {
                        const tRef  = 20;
                        frac  = Math.max(0, Math.min(1, (fuel.tDew - tOut) / Math.max(1, fuel.tDew - tRef)));
                        mCond = massFlow * xH2O * frac;
                        qWet = mCond * fuel.r / 3600;
                    }
                    qTotal = qDry + qWet;
                    flueOk = true;
                    if (dryEl)  dryEl.textContent  = qDry.toFixed(1);
                    if (wetEl)  wetEl.textContent  = qWet.toFixed(1);
                    if (totEl)  totEl.textContent  = qTotal.toFixed(1);
                    // ── Tooltips z dokładnymi wzorami i liczbami ──
                    const dryCard = document.getElementById('hrec-hx-' + uid + '-dry-card');
                    const wetCard = document.getElementById('hrec-hx-' + uid + '-wet-card');
                    const totCard = document.getElementById('hrec-hx-' + uid + '-total-card');
                    if (dryCard) dryCard.setAttribute('data-tip',
                        'Q_sucha = ṁ × cp × ΔT / 3600\n' +
                        'ṁ_spalin = ' + massFlow.toFixed(0) + ' kg/h\n' +
                        'cp = ' + fuel.cp + ' kJ/(kg·K)\n' +
                        'ΔT = ' + tIn.toFixed(1) + ' − ' + tOut.toFixed(1) + ' = ' + (tIn - tOut).toFixed(1) + ' K\n' +
                        '= ' + massFlow.toFixed(0) + ' × ' + fuel.cp + ' × ' + (tIn - tOut).toFixed(1) + ' / 3600\n' +
                        '= ' + qDry.toFixed(1) + ' kW');
                    if (wetCard) {
                        if (tOut < fuel.tDew) {
                            wetCard.setAttribute('data-tip',
                                'T_wyj=' + tOut.toFixed(1) + '°C < T_rosy=' + fuel.tDew + '°C → kondensacja\n' +
                                'frac = (' + fuel.tDew + ' − ' + tOut.toFixed(1) + ') / (' + fuel.tDew + ' − 20) = ' + frac.toFixed(3) + '\n' +
                                'ṁ_kond = ṁ × xH₂O × frac\n' +
                                '= ' + massFlow.toFixed(0) + ' × ' + xH2O.toFixed(3) + ' × ' + frac.toFixed(3) + '\n' +
                                '= ' + mCond.toFixed(1) + ' kg/h kondensatu\n' +
                                'Q_mokra = ṁ_kond × r / 3600\n' +
                                '= ' + mCond.toFixed(1) + ' × ' + fuel.r + ' / 3600\n' +
                                '= ' + qWet.toFixed(1) + ' kW');
                        } else {
                            wetCard.setAttribute('data-tip',
                                'T_wyj=' + tOut.toFixed(1) + '°C > T_rosy=' + fuel.tDew + '°C\n→ praca sucha, brak kondensacji\nQ_mokra = 0 kW');
                        }
                    }
                    if (totCard) totCard.setAttribute('data-tip',
                        'Q_łączna = Q_sucha + Q_mokra\n= ' + qDry.toFixed(1) + ' + ' + qWet.toFixed(1) + ' = ' + qTotal.toFixed(1) + ' kW');
                    if (modeEl) {
                        if (tOut < fuel.tDew) {
                            modeEl.innerHTML = '<span class="hrec-dew-badge hrec-dew-wet">💧 kondensacja (T &lt; ' + fuel.tDew + '°C)</span>';
                        } else {
                            modeEl.innerHTML = '<span class="hrec-dew-badge hrec-dew-dry">🌡️ suchy (T &gt; ' + fuel.tDew + '°C)</span>';
                        }
                    }
                    sumDry += qDry;
                    sumWet += qWet;
                    tCur = tOut;
                }

                // ── Water / medium side balance ───────────────────────────────
                const medium    = document.getElementById('hrec-medium').value;
                const cpW       = HREC_CP[medium] || 4.18;
                const wFlowEl   = document.getElementById('hrec-hx-' + uid + '-wflow');
                const wTinEl    = document.getElementById('hrec-hx-' + uid + '-wtin');
                const wToutEl   = document.getElementById('hrec-hx-' + uid + '-wtout');
                const wBal      = document.getElementById('hrec-hx-' + uid + '-balance');
                const wtBadge   = document.getElementById('hrec-hx-' + uid + '-wtout-badge');
                const wToutHint = document.getElementById('hrec-hx-' + uid + '-wtout-hint');
                const wFlowHint = document.getElementById('hrec-hx-' + uid + '-wflow-hint');
                const wTinHint  = document.getElementById('hrec-hx-' + uid + '-wtin-hint');

                // Auto-fill flow from global medium flow if field is still in 'suggested' state
                const globalMedFlow = parseFloat((document.getElementById('hrec-medium-flow') || {}).value);
                if (wFlowEl && wFlowEl.classList.contains('suggested') && !isNaN(globalMedFlow) && globalMedFlow > 0) {
                    wFlowEl.value = Math.round(globalMedFlow);
                    if (wFlowHint) wFlowHint.textContent = 'Auto z globalnych: ' + Math.round(globalMedFlow) + ' kg/h';
                }

                // Auto-fill T_in from chain if field is still in 'suggested' state
                if (wTinEl && wTinEl.classList.contains('suggested') && !isNaN(waterTinCur)) {
                    wTinEl.value = waterTinCur.toFixed(1);
                    if (wTinHint) wTinHint.textContent = idx === 0
                        ? ('Globalny powrót: ' + waterTinCur.toFixed(1) + '°C')
                        : ('Z wymiennika ' + idx + ': ' + waterTinCur.toFixed(1) + '°C');
                }

                const wFlow = parseFloat(wFlowEl ? wFlowEl.value : 'NaN');
                const wTin  = parseFloat(wTinEl  ? wTinEl.value  : 'NaN');

                if (!isNaN(wFlow) && wFlow > 0 && !isNaN(wTin)) {
                    if (flueOk && qTotal > 0) {
                        if (!hrecWaterManualTout[uid]) {
                            // Auto mode: calculate T_out from Q_flue
                            const wTout = wTin + qTotal * 3600 / (wFlow * cpW);
                            if (wToutEl) { wToutEl.value = wTout.toFixed(1); wToutEl.classList.add('suggested'); }
                            if (wtBadge)   wtBadge.textContent  = 'sugerowane';
                            if (wToutHint) wToutHint.textContent = wTin.toFixed(1) + ' + ' + qTotal.toFixed(1) + '×3600/(' + wFlow.toFixed(0) + '×' + cpW + ') = ' + wTout.toFixed(1) + '°C';
                            if (wBal) wBal.innerHTML =
                                '✅ <strong style="color:#1a5c2e;">Bilans OK</strong><br>' +
                                'Q = <strong>' + qTotal.toFixed(1) + ' kW</strong><br>' +
                                'T_woda: ' + wTin.toFixed(1) + ' → <strong>' + wTout.toFixed(1) + '°C</strong>' +
                                ' (+' + (wTout - wTin).toFixed(1) + ' K)';
                            waterTinCur = wTout;
                        } else {
                            // Manual T_out: check balance
                            const wTout = parseFloat(wToutEl ? wToutEl.value : 'NaN');
                            if (!isNaN(wTout)) {
                                const qWater = wFlow * cpW * Math.abs(wTout - wTin) / 3600;
                                const diff   = qTotal - qWater;
                                const pct    = qTotal > 0 ? Math.abs(diff / qTotal * 100) : 0;
                                const ok     = pct < 5;
                                if (wBal) wBal.innerHTML =
                                    'Q_spaliny: <strong>' + qTotal.toFixed(1) + ' kW</strong><br>' +
                                    'Q_woda: <strong>' + qWater.toFixed(1) + ' kW</strong><br>' +
                                    (ok
                                        ? '<span style="color:#1a5c2e;font-weight:700;">✅ Bilans OK (' + pct.toFixed(1) + '%)</span>'
                                        : '<span style="color:#c53030;font-weight:700;">⚠️ Różnica: ' + Math.abs(diff).toFixed(1) + ' kW (' + pct.toFixed(1) + '%)</span>'
                                    ) +
                                    (Math.abs(diff) > 0.5
                                        ? '<br><span style="font-size:10px;color:#8aacbe;">Może być dolewanie wody / straty</span>'
                                        : '');
                                waterTinCur = wTout;
                            } else {
                                if (wBal) wBal.innerHTML = '<span style="color:#b0c8d8;">Podaj T<sub>wyj</sub> wody (ręcznie)</span>';
                            }
                        }
                    } else {
                        // Flue side not yet computed for this HX
                        if (!hrecWaterManualTout[uid] && wToutEl) { wToutEl.value = ''; }
                        if (wBal) wBal.innerHTML = '<span style="color:#b0c8d8;">Podaj T<sub>wyj</sub> spalin (powyżej)</span>';
                    }
                } else {
                    if (wBal) wBal.innerHTML = '<span style="color:#b0c8d8;">Uzupełnij przepływ i T<sub>wej</sub> wody</span>';
                }
            });
            const sumTotal  = sumDry + sumWet;
            const summaryEl = document.getElementById('hrec-summary');
            if (hrecHxList.length > 0) {
                summaryEl.style.display = '';
                document.getElementById('hrec-sum-dry').textContent   = sumDry.toFixed(1);
                document.getElementById('hrec-sum-wet').textContent   = sumWet.toFixed(1);
                document.getElementById('hrec-sum-total').textContent = sumTotal.toFixed(1);
                const boilerPower = parseFloat(document.getElementById('hrec-power').value);
                const noteEl = document.getElementById('hrec-sum-note');
                if (!isNaN(boilerPower) && boilerPower > 0 && sumTotal > 0) {
                    const pct      = (sumTotal / boilerPower * 100).toFixed(1);
                    const condInfo = sumWet > 0
                        ? '💧 Ekonomajzer kondensacyjny – odzysk skraplania: ' + sumWet.toFixed(1) + ' kW.'
                        : '🌡️ Ekonomajzer suchy – brak kondensacji (T > ' + fuel.tDew + '°C).';
                    noteEl.innerHTML     = 'Odzyskano łącznie <strong>' + sumTotal.toFixed(1) + ' kW</strong> z mocy kotła <strong>' + boilerPower + ' kW</strong> → <strong>' + pct + '%</strong> mocy nominalnej. ' + condInfo;
                    noteEl.style.display = '';
                } else {
                    noteEl.style.display = 'none';
                }
            } else {
                summaryEl.style.display = 'none';
            }
        }

        // ── Zapis / wczytywanie / usuwanie kalkulacji ─────────────────────────
        @auth
        const HREC_STORE_URL   = '{{ route('information.calculations.store') }}';
        const HREC_DELETE_BASE = '{{ url('/informacje/kalkulacje') }}';
        const HREC_CSRF        = '{{ csrf_token() }}';

        // Dane zapisanych kalkulacji załadowane z backendu
        let hrecSavedData = {};
        try {
            const raw = document.getElementById('hrec-saved-data');
            if (raw) {
                const arr = JSON.parse(raw.textContent);
                arr.forEach(function(item) { hrecSavedData[item.id] = item; });
            }
        } catch(e) {}

        function hrecCollectState() {
            const fuel = document.getElementById('hrec-fuel').value;
            const hxList = hrecHxList.map(function(uid) {
                const fv2 = function(sfx) {
                    const v = parseFloat((document.getElementById('hrec-hx-' + uid + sfx) || {}).value);
                    return isNaN(v) ? null : v;
                };
                const tv2 = function(sfx) {
                    const v = parseFloat((document.getElementById('hrec-hx-' + uid + sfx) || {}).textContent);
                    return isNaN(v) ? null : v;
                };
                return {
                    tout:         fv2('-tout'),
                    dry:          tv2('-dry'),
                    wet:          tv2('-wet'),
                    total:        tv2('-total'),
                    wflow:        fv2('-wflow'),
                    wtin:         fv2('-wtin'),
                    wtout:        fv2('-wtout'),
                    wtout_manual: !!hrecWaterManualTout[uid],
                };
            });
            const fv = function(id) {
                const v = parseFloat((document.getElementById(id) || {}).value);
                return isNaN(v) ? null : v;
            };
            const tv = function(id) {
                const v = parseFloat((document.getElementById(id) || {}).textContent);
                return isNaN(v) ? null : v;
            };
            return {
                fuel_type:          fuel,
                boiler_power:       fv('hrec-power'),
                boiler_efficiency:  fv('hrec-eff'),
                flue_temp_in:       fv('hrec-t-in'),
                mass_flow:          fv('hrec-mass-flow'),
                xh2o:               fv('hrec-xh2o'),
                medium_type:        document.getElementById('hrec-medium').value,
                medium_temp_supply: fv('hrec-medium-temp'),
                medium_temp_return: fv('hrec-medium-ret'),
                medium_pressure:    fv('hrec-medium-pres'),
                medium_flow:        fv('hrec-medium-flow'),
                exchangers:         hxList,
                result_dry_kw:      tv('hrec-sum-dry'),
                result_wet_kw:      tv('hrec-sum-wet'),
                result_total_kw:    tv('hrec-sum-total'),
            };
        }

        async function hrecSave() {
            const nameEl  = document.getElementById('hrec-save-name');
            const msgEl   = document.getElementById('hrec-save-msg');
            const name    = (nameEl.value || '').trim();
            if (!name) {
                nameEl.focus();
                nameEl.style.borderColor = '#e53e3e';
                setTimeout(function() { nameEl.style.borderColor = ''; }, 2000);
                return;
            }
            const payload = { name, ...hrecCollectState() };
            msgEl.style.display = '';
            msgEl.textContent   = 'Zapisuję…';
            msgEl.style.color   = '#4c6373';
            try {
                const resp = await fetch(HREC_STORE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': HREC_CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });
                const json = await resp.json();
                if (!resp.ok || !json.ok) throw new Error(json.error || 'Błąd zapisu');
                msgEl.textContent = '✅ Zapisano: ' + name + ' (' + json.created_at + ')';
                msgEl.style.color = '#1a5c2e';
                nameEl.value      = '';
                // Dodaj wiersz do listy
                hrecSavedData[json.id] = { id: json.id, ...payload, created_at: json.created_at };
                hrecInsertSavedRow(json.id, name, json.created_at, payload);
            } catch(err) {
                msgEl.textContent = '❌ ' + (err.message || 'Nie udało się zapisać');
                msgEl.style.color = '#c53030';
            }
        }

        function hrecInsertSavedRow(id, name, createdAt, payload) {
            const list = document.getElementById('hrec-saved-list');
            if (!list) return;
            const fuelLabels   = { gas: 'Gaz ziemny GZ-50', coal: 'Węgiel kamienny' };
            const medLabels    = { water: 'Woda', steam: 'Para wodna', glycol: 'Glikol', air: 'Powietrze', other: 'Inne' };
            const fuelLabel    = fuelLabels[payload.fuel_type] || payload.fuel_type;
            const medLabel     = medLabels[payload.medium_type] || payload.medium_type;
            const powerStr     = payload.boiler_power ? ' · ' + payload.boiler_power + ' kW' : '';
            const dryStr       = payload.result_dry_kw   != null ? payload.result_dry_kw.toFixed(1)   + ' kW' : '—';
            const wetStr       = payload.result_wet_kw   != null ? payload.result_wet_kw.toFixed(1)   + ' kW' : '—';
            const totalStr     = payload.result_total_kw != null ? payload.result_total_kw.toFixed(1) + ' kW' : '—';
            const tempStr      = payload.flue_temp_in    != null ? '<div style="color:#6b8294;">T_spalin: ' + payload.flue_temp_in.toFixed(0) + '°C</div>' : '';
            const div = document.createElement('div');
            div.className = 'hrec-hx-block';
            div.id        = 'hrec-saved-' + id;
            div.style.background = '#f4f8fb';
            div.innerHTML =
                '<div class="hrec-hx-header">' +
                    '<div>' +
                        '<span class="hrec-hx-title">' + name.replace(/</g,'&lt;') + '</span>' +
                        '<span class="hrec-hx-tin" style="margin-left:8px;">' + createdAt + '</span>' +
                    '</div>' +
                    '<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">' +
                        '<span style="font-size:11px;color:#4c6373;">' + fuelLabel + ' · ' + medLabel + powerStr + '</span>' +
                        '<button onclick="hrecLoadSaved(' + id + ')" style="padding:4px 10px;background:#dbe9f5;color:#1d4f73;border:1px solid #b0c8d8;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;">↩ Wczytaj</button>' +
                        '<button onclick="hrecDeleteSaved(' + id + ', this)" class="hrec-remove-btn" title="Usuń">✕</button>' +
                    '</div>' +
                '</div>' +
                '<div style="display:grid;grid-template-columns:repeat(3,auto) 1fr;gap:8px 14px;font-size:12px;color:#355468;">' +
                    '<div>🌡️ Sucha: <strong>' + dryStr + '</strong></div>' +
                    '<div>💧 Mokra: <strong>' + wetStr + '</strong></div>' +
                    '<div>⚡ Łącznie: <strong>' + totalStr + '</strong></div>' +
                    tempStr +
                '</div>';
            list.insertBefore(div, list.firstChild);
        }

        function hrecLoadSaved(id) {
            const data = hrecSavedData[id];
            if (!data) return;
            const sv = function(elId, val) {
                const el = document.getElementById(elId);
                if (el && val != null) el.value = val;
            };
            const selv = function(elId, val) {
                const el = document.getElementById(elId);
                if (el && val) el.value = val;
            };
            selv('hrec-fuel',   data.fuel_type);
            sv('hrec-power',    data.boiler_power);
            sv('hrec-eff',      data.boiler_eff);
            sv('hrec-t-in',     data.flue_temp_in);
            sv('hrec-mass-flow',data.mass_flow);
            sv('hrec-xh2o',     data.xh2o);
            selv('hrec-medium', data.medium_type);
            sv('hrec-medium-temp', data.medium_temp);
            sv('hrec-medium-ret',  data.medium_ret);
            sv('hrec-medium-pres', data.medium_pres);
            sv('hrec-medium-flow', data.medium_flow);
            // Odbuduj wymienniki
            const container = document.getElementById('hrec-hx-container');
            container.innerHTML = '';
            hrecHxList = [];
            hrecNextId = 0;
            const exchangers = Array.isArray(data.exchangers) ? data.exchangers : [];
            if (exchangers.length === 0) {
                hrecAddHX();
            } else {
                exchangers.forEach(function(hx) {
                    hrecAddHX();
                    const uid = hrecHxList[hrecHxList.length - 1];
                    const sv2 = function(sfx, val) {
                        const el = document.getElementById('hrec-hx-' + uid + sfx);
                        if (el && val != null) { el.value = val; el.classList.remove('suggested'); }
                    };
                    sv2('-tout',  hx.tout);
                    sv2('-wflow', hx.wflow);
                    sv2('-wtin',  hx.wtin);
                    if (hx.wtout_manual && hx.wtout != null) {
                        sv2('-wtout', hx.wtout);
                        hrecWaterManualTout[uid] = true;
                        const badge = document.getElementById('hrec-hx-' + uid + '-wtout-badge');
                        if (badge) badge.textContent = 'ręcznie';
                    }
                });
            }
            hrecWaterManualTout = {};
            // Re-apply only manual tout flags from loaded data
            if (Array.isArray(data.exchangers)) {
                data.exchangers.forEach(function(hx, i) {
                    if (hx.wtout_manual && hrecHxList[i] != null) {
                        hrecWaterManualTout[hrecHxList[i]] = true;
                    }
                });
            }
            hrecAutoFlow    = false;
            hrecMedAutoFlow = false;
            hrecOnFuelChange();
            hrecOnMediumChange();
            hrecCalc();
            // Przewiń do sekcji
            const acc = document.getElementById('acc-hrec');
            if (acc && !acc.classList.contains('open')) ecAccToggle('acc-hrec');
            setTimeout(function() {
                document.getElementById('acc-hrec').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }

        async function hrecDeleteSaved(id, btn) {
            if (!confirm('Usunąć tę kalkulację?')) return;
            btn.disabled    = true;
            btn.textContent = '…';
            try {
                const resp = await fetch(HREC_DELETE_BASE + '/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': HREC_CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const json = await resp.json();
                if (!resp.ok || !json.ok) throw new Error(json.error || 'Błąd');
                delete hrecSavedData[id];
                const row = document.getElementById('hrec-saved-' + id);
                if (row) row.remove();
            } catch(err) {
                btn.disabled    = false;
                btn.textContent = '✕';
                alert('Nie udało się usunąć: ' + err.message);
            }
        }
        @endauth
        </script>
    </section>
</x-layouts.app>
