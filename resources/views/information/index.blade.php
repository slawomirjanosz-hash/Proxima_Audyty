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
                <p class="muted" style="margin:4px 0 0;">Aktualna struktura generacji mocy (Energetyczny Kompas). Widok odświeża się automatycznie.</p>
            </div>
            <a href="{{ route('information.index') }}" class="btn-secondary" style="text-decoration:none; padding:8px 10px; border-radius:9px; background:#dbe9f5; color:#1d4f73;">Odśwież teraz</a>
        </div>

        <div id="generation-status" class="muted" style="font-size:13px; margin-bottom:8px;">
            @if(!($generationData['ok'] ?? false))
                {{ $generationData['message'] ?? 'Brak danych.' }}
            @else
                Dane zaktualizowano: {{ $generationData['fetchedAt'] ?? '—' }}
            @endif
        </div>

        <div class="info-structure-box" id="generation-structure-box">
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
        <div class="ec-acc open" id="acc-units">
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
                            <div class="ec-pln-label">Szacunkowa wartość energii (wg cen ropy Brent)</div>
                            <div class="ec-pln-value" id="ec-pln-value">— PLN</div>
                        </div>
                        <div class="ec-price-meta">
                            @if($toePricePln['ok'])
                                Ropa Brent: <span>{{ number_format($toePricePln['pricePerBarrelUsd'], 2) }} USD/bbl</span> ·
                                USD/PLN: <span>{{ number_format($toePricePln['usdPln'], 4) }}</span><br>
                                Cena 1 TOE: <span>{{ number_format($toePricePln['pricePerToePln'], 0, ',', ' ') }} PLN</span> ·
                                Źródło: {{ $toePricePln['source'] }}<br>
                                Aktualizacja: <span>{{ $toePricePln['fetchedAt'] }}</span>
                            @else
                                <span style="color:#f87171;">{{ $toePricePln['message'] ?? 'Nie udało się pobrać danych rynkowych.' }}</span><br>
                                Przeliczenie PLN niedostępne.
                            @endif
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
        <div class="ec-acc open" id="acc-flue">
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
        <div class="ec-acc open" id="acc-co2el">
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

        {{-- ── JavaScript ── --}}
        <script>
        // ── Accordion toggle ─────────────────────────────────────
        function ecAccToggle(id) {
            const el = document.getElementById(id);
            if (el) el.classList.toggle('open');
        }

        // ── Unit converter ──────────────────────────────────────────
        const EC_TOE_PLN = {{ $toePricePln['ok'] ? (float) $toePricePln['pricePerToePln'] : 'null' }};

        const ecFactors = { gj:1, kwh:1/0.0036, mwh:1/3.6, toe:1/41.868 };
        const ecDecimals = { gj:5, kwh:2, mwh:5, toe:7 };

        function ecConvert(from, rawVal) {
            const val = parseFloat(rawVal);
            const plnEl = document.getElementById('ec-pln-value');
            if (isNaN(val) || rawVal === '') {
                ['gj','kwh','mwh','toe'].forEach(id => {
                    const el = document.getElementById('ec-' + id);
                    if (el && id !== from) el.value = '';
                });
                plnEl.textContent = '— PLN';
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
            const toeVal = gj / 41.868;
            if (EC_TOE_PLN && toeVal > 0) {
                const pln = toeVal * EC_TOE_PLN;
                plnEl.textContent = pln.toLocaleString('pl-PL', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' PLN';
            } else {
                plnEl.textContent = '— PLN (brak danych rynkowych)';
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

        document.addEventListener('DOMContentLoaded', () => { ecFlueCalc(); ecCo2ElCalc(); });
        </script>
    </section>
</x-layouts.app>
