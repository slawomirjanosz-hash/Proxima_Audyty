<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Throwable;

class InformationController extends Controller
{
    private const SOURCE_ALIASES = [
        'WĘGIEL KAMIENNY' => ['B05'],
        'WĘGIEL BRUNATNY' => ['B02'],
        'GAZ' => ['B03', 'B04'],
        'WIATR LĄDOWY' => ['B19', 'B20'],
        'WODA' => ['B10', 'B11', 'B12'],
        'BIOMASA' => ['B01'],
        'ROPA NAFTOWA' => ['B06'],
        'BIOGAZ' => ['B15'],
        'SŁOŃCE' => ['B16'],
    ];

    private const SOURCE_COLORS = [
        'WĘGIEL KAMIENNY' => '#4a4a4a',
        'WĘGIEL BRUNATNY' => '#a61a1a',
        'GAZ' => '#f4b000',
        'WIATR LĄDOWY' => '#7ec0c9',
        'WODA' => '#2f4f8f',
        'BIOMASA' => '#3cae46',
        'ROPA NAFTOWA' => '#e4d178',
        'BIOGAZ' => '#006b2e',
        'SŁOŃCE' => '#f0dd1e',
        'INNE' => '#5e6d7a',
    ];

    public function index(): View
    {
        return view('information.index', [
            'generationData' => $this->getGenerationStructureSnapshot(),
        ]);
    }

    public function snapshot(): JsonResponse
    {
        return response()->json($this->getGenerationStructureSnapshot());
    }

    private function getGenerationStructureSnapshot(): array
    {
        return Cache::remember('energetyczny_kompas_generation_v6', now()->addMinute(), function (): array {
            try {
                $htmlSnapshot = $this->fetchFromEnergetycznyKompasHtml();
                if (($htmlSnapshot['ok'] ?? false) === true) {
                    return $htmlSnapshot;
                }

                $warsawNow = now('Europe/Warsaw');
                $today = $warsawNow->format('Y-m-d');
                $hourFrom = $warsawNow->copy()->subHours(6)->format('Y-m-d H:00:00');

                $response = Http::timeout(15)
                    ->retry(2, 300)
                    ->get('https://api.raporty.pse.pl/api/his-gen-pal', [
                        '$filter' => "business_date eq '{$today}' and dtime ge '{$hourFrom}'",
                    ]);

                if (! $response->successful()) {
                    return [
                        'ok' => false,
                        'message' => 'Nie udało się pobrać danych źródłowych struktury generacji.',
                        'sourceUrl' => 'https://www.energetycznykompas.pl',
                    ];
                }

                $payload = $response->json();
                $items = collect($payload['value'] ?? [])->filter(fn ($item) => is_array($item));
                if ($items->isEmpty()) {
                    return [
                        'ok' => false,
                        'message' => 'Brak danych struktury generacji z API Energetycznego Kompasu.',
                        'sourceUrl' => 'https://www.energetycznykompas.pl',
                    ];
                }

                $knownAliases = collect(self::SOURCE_ALIASES)->flatten()->unique()->values()->all();
                $latestDtime = (string) ($items->max('dtime') ?? '');

                if ($latestDtime === '') {
                    return [
                        'ok' => false,
                        'message' => 'Brak dostępnego snapshotu struktury generacji.',
                        'sourceUrl' => 'https://www.energetycznykompas.pl',
                    ];
                }

                $aliasSnapshot = collect($knownAliases)
                    ->mapWithKeys(function (string $alias) use ($items, $latestDtime): array {
                        $row = $items
                            ->filter(fn ($item) => (string) ($item['alias_entsoe'] ?? '') === $alias)
                            ->filter(fn ($item) => (string) ($item['dtime'] ?? '') <= $latestDtime)
                            ->sortByDesc('dtime')
                            ->first();

                        $value = $row ? $this->parseValue((string) ($row['value'] ?? '0')) : 0.0;

                        return [$alias => $value];
                    });

                $snapshot = $items->where('dtime', $latestDtime)->values();

                $byAlias = $snapshot
                    ->filter(fn ($item) => ! empty($item['alias_entsoe']))
                    ->reduce(function (
                        \Illuminate\Support\Collection $carry,
                        array $item
                    ): \Illuminate\Support\Collection {
                        $alias = (string) ($item['alias_entsoe'] ?? '');
                        if ($alias === '') {
                            return $carry;
                        }

                        $value = $this->parseValue((string) ($item['value'] ?? '0'));
                        $carry->put($alias, (float) $carry->get($alias, 0.0) + $value);

                        return $carry;
                    }, collect())
                    ->union($aliasSnapshot);

                $sourceMwh = collect(self::SOURCE_ALIASES)
                    ->map(fn (array $aliases): float => $this->sumAliases($byAlias, $aliases))
                    ->all();

                $otherMwh = $byAlias
                    ->reject(fn (float $value, string $alias): bool => in_array($alias, $knownAliases, true))
                    ->sum();

                if ($otherMwh > 0) {
                    $sourceMwh['INNE'] = (float) $otherMwh;
                }

                $total = array_sum($sourceMwh);
                if ($total <= 0.0) {
                    return [
                        'ok' => false,
                        'message' => 'Nie udało się obliczyć struktury generacji mocy.',
                        'sourceUrl' => 'https://www.energetycznykompas.pl',
                    ];
                }

                $renewablesTotal = $sourceMwh['WIATR LĄDOWY'] + $sourceMwh['WODA'] + $sourceMwh['BIOMASA'] + $sourceMwh['BIOGAZ'] + $sourceMwh['SŁOŃCE'];
                $conventionalTotal = $sourceMwh['WĘGIEL KAMIENNY'] + $sourceMwh['WĘGIEL BRUNATNY'] + $sourceMwh['GAZ'] + $sourceMwh['ROPA NAFTOWA'];

                $sources = collect($sourceMwh)->map(function (float $value, string $name) use ($total): array {
                    $share = $total > 0 ? round(($value / $total) * 100, 1) : 0.0;

                    return [
                        'name' => $name,
                        'color' => self::SOURCE_COLORS[$name] ?? self::SOURCE_COLORS['INNE'],
                        'shareValue' => $share,
                        'share' => rtrim(rtrim(number_format($share, 1, '.', ''), '0'), '.').'%',
                        'mwh' => (string) (int) round($value),
                    ];
                })->sortByDesc('shareValue')->values()->map(function (array $row): array {
                    unset($row['shareValue']);

                    return $row;
                })->all();

                $renewablesShare = rtrim(rtrim(number_format(($renewablesTotal / $total) * 100, 1, '.', ''), '0'), '.').'%';
                $conventionalShare = rtrim(rtrim(number_format(($conventionalTotal / $total) * 100, 1, '.', ''), '0'), '.').'%';

                return [
                    'ok' => true,
                    'title' => 'Aktualna struktura generacji mocy',
                    'sourceUrl' => 'https://www.energetycznykompas.pl',
                    'renewablesShare' => $renewablesShare,
                    'conventionalShare' => $conventionalShare,
                    'sources' => $sources,
                    'publishedAt' => $latestDtime,
                    'fetchedAt' => $warsawNow->toDateTimeString(),
                ];
            } catch (Throwable $e) {
                return [
                    'ok' => false,
                    'message' => 'Błąd podczas pobierania danych: '.$e->getMessage(),
                    'sourceUrl' => 'https://www.energetycznykompas.pl',
                ];
            }
        });
    }

    private function fetchFromEnergetycznyKompasHtml(): array
    {
        $response = Http::timeout(15)
            ->retry(2, 300)
            ->get('https://www.energetycznykompas.pl');

        if (! $response->successful()) {
            return ['ok' => false];
        }

        $html = (string) $response->body();
        if ($html === '') {
            return ['ok' => false];
        }

        $renewablesShare = null;
        $conventionalShare = null;
        if (preg_match('/([0-9]+(?:[\.,][0-9]+)?)%\s*([0-9]+(?:[\.,][0-9]+)?)%\s*ŹRÓDŁA ODNAWIALNE\s*ŹRÓDŁA KONWENCJONALNE/u', $html, $match) === 1) {
            $renewablesShare = $this->formatPercentText($match[1]);
            $conventionalShare = $this->formatPercentText($match[2]);
        }

        $sourcesOrder = [
            'WĘGIEL KAMIENNY',
            'WĘGIEL BRUNATNY',
            'GAZ',
            'WIATR LĄDOWY',
            'WODA',
            'BIOMASA',
            'ROPA NAFTOWA',
            'BIOGAZ',
            'SŁOŃCE',
        ];

        $sources = collect($sourcesOrder)->map(function (string $name) use ($html): ?array {
            $escapedName = preg_quote($name, '/');
            $pattern = '/([0-9]+(?:[\.,][0-9]+)?)%\s*([0-9\s]+)\s*'.$escapedName.'/u';

            if (preg_match($pattern, $html, $match) !== 1) {
                return null;
            }

            $share = $this->formatPercentText($match[1]);
            $mwh = preg_replace('/\s+/', '', (string) $match[2]);
            $mwh = preg_replace('/[^0-9]/', '', (string) $mwh);

            return [
                'name' => $name,
                'color' => self::SOURCE_COLORS[$name] ?? self::SOURCE_COLORS['INNE'],
                'share' => $share,
                'mwh' => $mwh !== '' ? $mwh : '0',
            ];
        })->filter()->values()->all();

        if (count($sources) < 5) {
            return ['ok' => false];
        }

        if ($renewablesShare === null || $conventionalShare === null) {
            $total = collect($sources)->sum(fn (array $row) => (float) $row['mwh']);
            $renewablesNames = ['WIATR LĄDOWY', 'WODA', 'BIOMASA', 'BIOGAZ', 'SŁOŃCE'];
            $renewablesTotal = collect($sources)
                ->filter(fn (array $row) => in_array($row['name'], $renewablesNames, true))
                ->sum(fn (array $row) => (float) $row['mwh']);

            $renewables = $total > 0 ? round(($renewablesTotal / $total) * 100, 1) : 0.0;
            $renewablesShare = rtrim(rtrim(number_format($renewables, 1, '.', ''), '0'), '.').'%';
            $conventionalShare = rtrim(rtrim(number_format(100 - $renewables, 1, '.', ''), '0'), '.').'%';
        }

        $publishedAt = null;
        if (preg_match('/Ostatnia aktualizacja:\s*([0-9]{4}-[0-9]{2}-[0-9]{2}\s+[0-9]{2}:[0-9]{2}:[0-9]{2})/u', $html, $updatedMatch) === 1) {
            $publishedAt = $updatedMatch[1];
        }

        return [
            'ok' => true,
            'title' => 'Aktualna struktura generacji mocy',
            'sourceUrl' => 'https://www.energetycznykompas.pl',
            'renewablesShare' => $renewablesShare,
            'conventionalShare' => $conventionalShare,
            'sources' => $sources,
            'publishedAt' => $publishedAt ?? now('Europe/Warsaw')->toDateTimeString(),
            'fetchedAt' => now('Europe/Warsaw')->toDateTimeString(),
        ];
    }

    private function parseValue(string $raw): float
    {
        $normalized = str_replace([' ', ','], ['', '.'], trim($raw));

        return is_numeric($normalized) ? (float) $normalized : 0.0;
    }

    private function sumAliases(\Illuminate\Support\Collection $byAlias, array $aliases): float
    {
        return collect($aliases)->reduce(function (float $sum, string $alias) use ($byAlias): float {
            return $sum + (float) ($byAlias->get($alias, 0.0));
        }, 0.0);
    }

    private function formatPercentText(string $value): string
    {
        $normalized = str_replace(',', '.', trim($value));
        $number = is_numeric($normalized) ? (float) $normalized : 0.0;

        return rtrim(rtrim(number_format($number, 1, '.', ''), '0'), '.').'%';
    }
}
