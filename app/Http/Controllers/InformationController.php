<?php

namespace App\Http\Controllers;

use App\Models\HeatRecoveryCalculation;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Throwable;

class InformationController extends Controller
{
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
    ];

    private const ALIAS_TO_SOURCE = [
        'B05' => 'WĘGIEL KAMIENNY',
        'B02' => 'WĘGIEL BRUNATNY',
        'B03' => 'GAZ',
        'B04' => 'GAZ',
        'B19' => 'WIATR LĄDOWY',
        'B10' => 'WODA',
        'B11' => 'WODA',
        'B12' => 'WODA',
        'B01' => 'BIOMASA',
        'B06' => 'ROPA NAFTOWA',
        'B15' => 'BIOGAZ',
        'B16' => 'SŁOŃCE',
    ];

    private const SOURCE_ORDER = [
        'WĘGIEL KAMIENNY',
        'WIATR LĄDOWY',
        'WĘGIEL BRUNATNY',
        'SŁOŃCE',
        'GAZ',
        'ROPA NAFTOWA',
        'WODA',
        'BIOMASA',
        'BIOGAZ',
    ];

    private const RES_SOURCES = [
        'WIATR LĄDOWY',
        'SŁOŃCE',
        'WODA',
    ];

    public function index(): View
    {
        if (! auth()->check() && ! SystemSetting::get('informacje_public', '1')) {
            abort(403, 'Dostęp do tej strony wymaga zalogowania.');
        }

        $savedCalculations = auth()->check()
            ? HeatRecoveryCalculation::where('user_id', auth()->id())
                ->orderByDesc('created_at')
                ->get()
            : collect();

        return view('information.index', [
            'generationData'       => $this->getGenerationStructureSnapshot(),
            'toePricePln'          => $this->getToePricePln(),
            'gasPricePln'          => $this->getGasPricePln(),
            'co2ElCombFactor'      => (int) SystemSetting::get('co2_el_comb_factor', '717'),
            'co2ElNatFactor'       => (int) SystemSetting::get('co2_el_nat_factor',  '552'),
            'co2ElYear'            => (string) SystemSetting::get('co2_el_year', '2024'),
            'savedCalculations'    => $savedCalculations,
        ]);
    }

    public function storeCalculation(Request $request): JsonResponse
    {
        if (! auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:120'],
            'fuel_type'          => ['required', 'in:gas,coal'],
            'boiler_power'       => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'boiler_efficiency'  => ['nullable', 'numeric', 'min:0', 'max:200'],
            'boiler_load_pct'    => ['nullable', 'numeric', 'min:1', 'max:100'],
            'flue_temp_in'       => ['nullable', 'numeric', 'min:0', 'max:2000'],
            'mass_flow'          => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'xh2o'               => ['nullable', 'numeric', 'min:0', 'max:1'],
            'medium_type'        => ['required', 'in:water,steam,glycol,air,other'],
            'medium_temp_supply' => ['nullable', 'numeric', 'min:-100', 'max:1000'],
            'medium_temp_return' => ['nullable', 'numeric', 'min:-100', 'max:1000'],
            'medium_pressure'    => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'medium_flow'        => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'exchangers'         => ['nullable', 'array'],
            'exchangers.*.tout'  => ['nullable', 'numeric'],
            'exchangers.*.dry'   => ['nullable', 'numeric'],
            'exchangers.*.wet'   => ['nullable', 'numeric'],
            'exchangers.*.total' => ['nullable', 'numeric'],
            'result_dry_kw'      => ['nullable', 'numeric'],
            'result_wet_kw'      => ['nullable', 'numeric'],
            'result_total_kw'    => ['nullable', 'numeric'],
        ]);

        $calc = HeatRecoveryCalculation::create([
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        return response()->json([
            'ok' => true,
            'id' => $calc->id,
            'created_at' => $calc->created_at->format('d.m.Y H:i'),
        ]);
    }

    public function destroyCalculation(HeatRecoveryCalculation $calculation): JsonResponse
    {
        if (! auth()->check() || $calculation->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $calculation->delete();

        return response()->json(['ok' => true]);
    }

    public function snapshot(): JsonResponse
    {
        return response()->json($this->getGenerationStructureSnapshot());
    }

    private function getGenerationStructureSnapshot(): array
    {
        return Cache::remember('energetyczny_kompas_generation_v7', now()->addMinute(), function (): array {
            try {
                $warsawNow = now('Europe/Warsaw');
                $today = $warsawNow->format('Y-m-d');

                $response = Http::timeout(15)
                    ->retry(2, 300)
                    ->get('https://api.raporty.pse.pl/api/his-gen-pal', [
                        '$orderby' => 'dtime desc',
                        '$filter' => "business_date eq '{$today}'",
                        '$first' => 13,
                    ]);

                if (! $response->successful()) {
                    return [
                        'ok' => false,
                        'message' => 'Nie udało się pobrać danych źródłowych struktury generacji.',
                        'sourceUrl' => 'https://www.energetycznykompas.pl',
                    ];
                }

                $records = collect($response->json('value', []))
                    ->filter(fn ($item) => is_array($item))
                    ->values();

                if ($records->isEmpty()) {
                    return [
                        'ok' => false,
                        'message' => 'Brak danych struktury generacji z API Energetycznego Kompasu.',
                        'sourceUrl' => 'https://www.energetycznykompas.pl',
                    ];
                }

                $publishedAt = (string) ($records->first()['dtime'] ?? $warsawNow->toDateTimeString());

                $sourceMwh = collect(self::SOURCE_ORDER)->mapWithKeys(fn (string $name): array => [$name => 0.0]);

                foreach ($records as $record) {
                    $alias = (string) ($record['alias_entsoe'] ?? '');
                    $sourceName = self::ALIAS_TO_SOURCE[$alias] ?? null;
                    if ($sourceName === null) {
                        continue;
                    }

                    $value = $this->parseValue((string) ($record['value'] ?? '0'));
                    $sourceMwh->put($sourceName, (float) $sourceMwh->get($sourceName, 0.0) + $value);
                }

                $sourceMwh = $sourceMwh
                    ->map(fn (float $value): int => (int) round($value))
                    ->all();

                $total = array_sum($sourceMwh);
                if ($total <= 0) {
                    return [
                        'ok' => false,
                        'message' => 'Nie udało się obliczyć struktury generacji mocy.',
                        'sourceUrl' => 'https://www.energetycznykompas.pl',
                    ];
                }

                $renewablesTotal = collect(self::RES_SOURCES)
                    ->sum(fn (string $name): int => (int) ($sourceMwh[$name] ?? 0));
                $conventionalTotal = max(0, $total - $renewablesTotal);

                $sources = collect($sourceMwh)
                    ->map(function (int $value, string $name) use ($total): array {
                        $share = $total > 0 ? round(($value / $total) * 100, 1) : 0.0;

                        return [
                            'name' => $name,
                            'color' => self::SOURCE_COLORS[$name] ?? '#0e89d8',
                            'shareValue' => $share,
                            'share' => rtrim(rtrim(number_format($share, 1, '.', ''), '0'), '.') . '%',
                            'mwh' => (string) $value,
                        ];
                    })
                    ->sortByDesc('shareValue')
                    ->values()
                    ->map(function (array $row): array {
                        unset($row['shareValue']);

                        return $row;
                    })
                    ->all();

                $renewablesShare = rtrim(rtrim(number_format(($renewablesTotal / $total) * 100, 1, '.', ''), '0'), '.') . '%';
                $conventionalShare = rtrim(rtrim(number_format(($conventionalTotal / $total) * 100, 1, '.', ''), '0'), '.') . '%';

                return [
                    'ok' => true,
                    'title' => 'Aktualna struktura generacji mocy',
                    'sourceUrl' => 'https://www.energetycznykompas.pl',
                    'renewablesShare' => $renewablesShare,
                    'conventionalShare' => $conventionalShare,
                    'sources' => $sources,
                    'publishedAt' => $publishedAt,
                    'fetchedAt' => $warsawNow->toDateTimeString(),
                ];
            } catch (Throwable $e) {
                return [
                    'ok' => false,
                    'message' => 'Błąd podczas pobierania danych: ' . $e->getMessage(),
                    'sourceUrl' => 'https://www.energetycznykompas.pl',
                ];
            }
        });
    }

    private function parseValue(string $raw): float
    {
        $normalized = str_replace([' ', ','], ['', '.'], trim($raw));

        return is_numeric($normalized) ? (float) $normalized : 0.0;
    }

    private function getGasPricePln(): array
    {
        return Cache::remember('gas_price_pln_v1', now()->addHours(4), function (): array {
            try {
                // EUR/PLN from NBP open API
                $nbp = Http::timeout(8)->get('https://api.nbp.pl/api/exchangerates/rates/a/eur/?format=json');
                $eurPln = $nbp->successful()
                    ? (float) ($nbp->json('rates.0.mid') ?? 4.25)
                    : 4.25;

                // TTF Natural Gas Futures (EUR/MWh) from Yahoo Finance
                $gas = Http::timeout(10)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; energy-calc/1.0)'])
                    ->get('https://query1.finance.yahoo.com/v8/finance/chart/TTF=F', [
                        'interval' => '1d',
                        'range'    => '5d',
                    ]);

                $priceEurMwh = null;
                if ($gas->successful()) {
                    $priceEurMwh = $gas->json('chart.result.0.meta.regularMarketPrice');
                }

                if (! is_numeric($priceEurMwh)) {
                    return [
                        'ok'        => false,
                        'eurPln'    => round($eurPln, 4),
                        'fetchedAt' => now('Europe/Warsaw')->toDateTimeString(),
                        'message'   => 'Brak danych o cenie gazu TTF.',
                    ];
                }

                // 1 MWh = 3.6 GJ → PLN/GJ = (EUR/MWh × EUR/PLN) / 3.6
                $priceGjPln = round((float) $priceEurMwh * $eurPln / 3.6, 2);

                return [
                    'ok'          => true,
                    'priceEurMwh' => round((float) $priceEurMwh, 2),
                    'eurPln'      => round($eurPln, 4),
                    'priceGjPln'  => $priceGjPln,
                    'source'      => 'Yahoo Finance (TTF) / NBP',
                    'fetchedAt'   => now('Europe/Warsaw')->toDateTimeString(),
                ];
            } catch (Throwable $e) {
                return [
                    'ok'        => false,
                    'fetchedAt' => now('Europe/Warsaw')->toDateTimeString(),
                    'message'   => 'Błąd pobierania danych: ' . $e->getMessage(),
                ];
            }
        });
    }

    private function getToePricePln(): array
    {
        return Cache::remember('toe_price_pln_v3', now()->addHours(4), function (): array {
            try {
                // USD/PLN from NBP open API
                $nbp = Http::timeout(8)->get('https://api.nbp.pl/api/exchangerates/rates/a/usd/?format=json');
                $usdPln = $nbp->successful()
                    ? (float) ($nbp->json('rates.0.mid') ?? 4.10)
                    : 4.10;

                // Brent crude (USD/barrel) from Yahoo Finance public endpoint
                $oil = Http::timeout(10)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; energy-calc/1.0)'])
                    ->get('https://query1.finance.yahoo.com/v8/finance/chart/BZ=F', [
                        'interval' => '1d',
                        'range'    => '5d',
                    ]);

                $barrel = null;
                if ($oil->successful()) {
                    $barrel = $oil->json('chart.result.0.meta.regularMarketPrice');
                }

                if (! is_numeric($barrel)) {
                    return [
                        'ok'        => false,
                        'usdPln'    => round($usdPln, 4),
                        'fetchedAt' => now('Europe/Warsaw')->toDateTimeString(),
                        'message'   => 'Brak danych o cenie ropy Brent.',
                    ];
                }

                // 1 TOE = approx. 7.33 barrels of crude oil (IEA standard)
                $priceToePln = round((float) $barrel * 7.33 * $usdPln, 2);

                return [
                    'ok'                => true,
                    'pricePerBarrelUsd' => round((float) $barrel, 2),
                    'usdPln'            => round($usdPln, 4),
                    'pricePerToePln'    => $priceToePln,
                    'source'            => 'Yahoo Finance / NBP',
                    'fetchedAt'         => now('Europe/Warsaw')->toDateTimeString(),
                ];
            } catch (Throwable $e) {
                return [
                    'ok'        => false,
                    'fetchedAt' => now('Europe/Warsaw')->toDateTimeString(),
                    'message'   => 'Błąd pobierania danych: ' . $e->getMessage(),
                ];
            }
        });
    }
}
