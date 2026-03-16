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
</x-layouts.app>
