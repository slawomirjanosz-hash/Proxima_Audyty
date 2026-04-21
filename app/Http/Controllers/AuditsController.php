<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\AuditType;
use App\Models\AuditTypeSection;
use App\Models\AuditUnit;
use App\Models\Company;
use App\Models\EnergyAudit;
use App\Models\Iso50001Audit;
use App\Models\Iso50001Template;
use App\Models\User;
use App\Support\Iso50001TemplateDefinition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class AuditsController extends Controller
{
    public function index(): View
    {
        return $this->settings();
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'audit_type_id' => ['required', 'exists:audit_types,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'auditor_id' => ['nullable', 'exists:users,id'],
        ]);

        $auditType = AuditType::findOrFail((int) $validated['audit_type_id']);

        $validated['audit_type'] = $auditType->name;
        $validated['status'] = 'in_progress';
        $validated['completed_at'] = null;
        $validated['data_payload'] = [];

        EnergyAudit::create($validated);

        return redirect()->route('audits.index', ['tab' => 'in-progress'])
            ->with('status', 'Nowy audyt został dodany do zakładki „Audyty w toku”.');
    }

    public function edit(EnergyAudit $audit): View
    {
        $audit->load(['company', 'auditor', 'auditType.sections']);

        return view('audits.edit', [
            'audit' => $audit,
            'companies' => Company::query()->orderBy('name')->get(),
            'auditors' => User::query()->whereIn('role', ['admin', 'auditor'])->orderBy('name')->get(),
            'auditTypes' => AuditType::with('sections')->orderBy('name')->get(),
        ]);
    }

    public function show(EnergyAudit $audit): View
    {
        $audit->load(['company', 'auditor', 'auditType.sections']);

        return view('audits.show', [
            'audit' => $audit,
        ]);
    }

    public function update(Request $request, EnergyAudit $audit): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'audit_type_id' => ['required', 'exists:audit_types,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'auditor_id' => ['nullable', 'exists:users,id'],
            'section_payload' => ['nullable', 'array'],
        ]);

        $auditType = AuditType::with('sections')->findOrFail((int) $validated['audit_type_id']);
        $payload = $this->normalizeSectionPayload($auditType, $validated['section_payload'] ?? []);

        $validated['audit_type'] = $auditType->name;
        $validated['data_payload'] = $payload;
        unset($validated['section_payload']);

        $audit->update($validated);

        return redirect()->route('audits.index', ['tab' => 'in-progress'])
            ->with('status', 'Audyt został zaktualizowany.');
    }

    public function complete(EnergyAudit $audit): RedirectResponse
    {
        $audit->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('audits.index', ['tab' => 'completed'])
            ->with('status', 'Audyt został przeniesiony do zakończonych.');
    }

    public function reopen(EnergyAudit $audit): RedirectResponse
    {
        $audit->update([
            'status' => 'in_progress',
            'completed_at' => null,
        ]);

        return redirect()->route('audits.index', ['tab' => 'in-progress'])
            ->with('status', 'Audyt wrócił do zakładki „Audyty w toku”.');
    }

    public function settings(string $activeTabOverride = ''): View
    {
        $validTabs = ['energetyczne', 'iso50001', 'biale-certyfikaty'];
        $activeTab = $activeTabOverride !== '' && in_array($activeTabOverride, $validTabs)
            ? $activeTabOverride
            : (in_array(request('tab'), $validTabs) ? request('tab') : 'energetyczne');

        $auditTypes = AuditType::with('sections')->orderBy('name')->get();
        $units = AuditUnit::query()->orderBy('name')->get();
        $template = Iso50001Template::query()->first();

        if (! $template) {
            $template = Iso50001Template::query()->create([
                'name' => 'Szablon ISO 50001',
                'steps' => Iso50001TemplateDefinition::defaultSteps(),
            ]);
        }

        $steps = Iso50001TemplateDefinition::normalizeSteps((array) $template->steps);
        if ($steps !== (array) $template->steps) {
            $template->update(['steps' => $steps]);
        }

        $clients = User::query()
            ->where('role', UserRole::Client->value)
            ->orderBy('name')
            ->get();

        $companies = Company::query()->orderBy('name')->get();
        $isoAudits = Iso50001Audit::with(['company', 'creator', 'reviewer'])->latest()->get();

        // AI agents for the "energetyczne" tab
        $agentService = app(\App\Services\AiAgentService::class);
        $agentDefs = [
            ['type' => 'general',                 'icon' => '💬', 'name' => 'Ogólnie',                   'description' => 'Ogólna rozmowa o audytach energetycznych i efektywności'],
            ['type' => 'compressor_room',          'icon' => '🔧', 'name' => 'Sprężarkownia',              'description' => 'Zbieranie danych do audytu sprężarkowni i instalacji sprężonego powietrza'],
            ['type' => 'boiler_room',             'icon' => '🔥', 'name' => 'Kotłownia',                  'description' => 'Zbieranie danych do audytu kotłowni — kotły, paliwo, sprawność, c.w.u.'],
            ['type' => 'drying_room',             'icon' => '🌡️', 'name' => 'Suszarnia',                  'description' => 'Zbieranie danych do audytu suszarni i procesów suszenia'],
            ['type' => 'buildings',               'icon' => '🏢', 'name' => 'Budynki',                    'description' => 'Zbieranie danych do audytu energetycznego budynków i infrastruktury'],
            ['type' => 'technological_processes', 'icon' => '⚙️', 'name' => 'Procesy technologiczne',     'description' => 'Zbieranie danych do audytu procesów technologicznych i produkcyjnych'],
        ];
        $aiAgents = array_map(function (array $def) use ($agentService): array {
            $customPrompt = \App\Models\SystemSetting::get("ai_agent_prompt_{$def['type']}");
            $def['has_custom_prompt'] = !empty(trim((string) ($customPrompt ?? '')));
            $def['current_prompt'] = $def['has_custom_prompt']
                ? (string) $customPrompt
                : $agentService->getDefaultSystemPrompt($def['type']);
            return $def;
        }, $agentDefs);

        // AI agent for the "iso50001" tab
        $isoAgentDefs = [
            ['type' => 'iso50001', 'icon' => '🏭', 'name' => 'ISO 50001', 'description' => 'Rozmowy i analiza dotycząca normy ISO 50001 — systemy zarządzania energią'],
        ];
        $isoAgents = array_map(function (array $def) use ($agentService): array {
            $customPrompt = \App\Models\SystemSetting::get("ai_agent_prompt_{$def['type']}");
            $def['has_custom_prompt'] = !empty(trim((string) ($customPrompt ?? '')));
            $def['current_prompt'] = $def['has_custom_prompt']
                ? (string) $customPrompt
                : $agentService->getDefaultSystemPrompt($def['type']);
            return $def;
        }, $isoAgentDefs);

        // AI agents for the "biale-certyfikaty" tab
        $bcAgentDefs = [
            ['type' => 'bc_general',                 'icon' => '📋', 'name' => 'Ogólnie',                   'description' => 'Wstępne doradztwo i informacje o białych certyfikatach (świadectwach efektywności energetycznej)'],
            ['type' => 'bc_compressor_room',          'icon' => '🔧', 'name' => 'Sprężarkownia',              'description' => 'Zbieranie danych do białych certyfikatów — modernizacja sprężarkowni i instalacji sprężonego powietrza'],
            ['type' => 'bc_boiler_room',              'icon' => '🔥', 'name' => 'Kotłownia',                  'description' => 'Zbieranie danych do białych certyfikatów — modernizacja kotłowni i systemów cieplnych'],
            ['type' => 'bc_drying_room',              'icon' => '🌡️', 'name' => 'Suszarnia',                  'description' => 'Zbieranie danych do białych certyfikatów — modernizacja suszarni i procesów suszenia'],
            ['type' => 'bc_buildings',                'icon' => '🏢', 'name' => 'Budynki',                    'description' => 'Zbieranie danych do białych certyfikatów — termomodernizacja i modernizacja budynków'],
            ['type' => 'bc_technological_processes',  'icon' => '⚙️', 'name' => 'Procesy technologiczne',     'description' => 'Zbieranie danych do białych certyfikatów — modernizacja procesów technologicznych i produkcyjnych'],
        ];
        $bcAgents = array_map(function (array $def) use ($agentService): array {
            $customPrompt = \App\Models\SystemSetting::get("ai_agent_prompt_{$def['type']}");
            $def['has_custom_prompt'] = !empty(trim((string) ($customPrompt ?? '')));
            $def['current_prompt'] = $def['has_custom_prompt']
                ? (string) $customPrompt
                : $agentService->getDefaultSystemPrompt($def['type']);
            return $def;
        }, $bcAgentDefs);

        return view('audits.settings', [
            'auditTypes' => $auditTypes,
            'units' => $units,
            'isoTemplate' => $template,
            'isoTemplateJson' => json_encode($steps, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'isoStatusOptions' => Iso50001Audit::statusLabels(),
            'isoClients' => $clients,
            'isoCompanies' => $companies,
            'isoAudits' => $isoAudits,
            'activeTab' => $activeTab,
            'aiAgents' => $aiAgents,
            'isoAgents' => $isoAgents,
            'bcAgents' => $bcAgents,
        ]);
    }

    public function updateIso50001Template(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'template_json' => ['required', 'string'],
        ]);

        $decoded = json_decode((string) $validated['template_json'], true);

        if (! is_array($decoded)) {
            return redirect()->route('audits.types', ['tab' => 'iso50001'])
                ->with('status', 'Blad: nieprawidlowy JSON szablonu ISO 50001.');
        }

        $normalizedSteps = Iso50001TemplateDefinition::normalizeSteps($decoded);

        $template = Iso50001Template::query()->first();
        if (! $template) {
            $template = new Iso50001Template();
        }

        $template->fill([
            'name' => trim((string) $validated['name']),
            'steps' => $normalizedSteps,
        ]);
        $template->save();

        return redirect()->route('audits.types', ['tab' => 'iso50001'])->with('status', 'Konfiguracja audytu ISO 50001 zostala zapisana.');
    }

    public function storeIso50001Audit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'client_user_id' => ['required', Rule::exists('users', 'id')->where('role', UserRole::Client->value)],
            'company_id' => ['required', 'exists:companies,id'],
            'due_date' => ['nullable', 'date'],
        ]);

        $client = User::query()->findOrFail((int) $validated['client_user_id']);
        $clientCompanyIds = $this->clientCompanyIds($client)->all();
        if (! in_array((int) $validated['company_id'], $clientCompanyIds, true)) {
            return redirect()->route('audits.types', ['tab' => 'iso50001'])->with('status', 'Blad: wybrana firma nie jest przypisana do wybranego klienta.');
        }

        Iso50001Audit::create([
            'title' => trim((string) $validated['title']),
            'company_id' => (int) $validated['company_id'],
            'created_by_user_id' => (int) $validated['client_user_id'],
            'reviewer_id' => $request->user()?->id,
            'status' => 'draft',
            'current_step' => 1,
            'due_date' => $validated['due_date'] ?? null,
            'answers' => [],
        ]);

        return redirect()->route('audits.types', ['tab' => 'iso50001'])->with('status', 'Audyt ISO 50001 zostal utworzony i przypisany klientowi.');
    }

    public function updateIso50001Audit(Request $request, Iso50001Audit $isoAudit): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'client_user_id' => ['required', Rule::exists('users', 'id')->where('role', UserRole::Client->value)],
            'company_id' => ['required', 'exists:companies,id'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(array_keys(Iso50001Audit::statusLabels()))],
        ]);

        $client = User::query()->findOrFail((int) $validated['client_user_id']);
        $clientCompanyIds = $this->clientCompanyIds($client)->all();
        if (! in_array((int) $validated['company_id'], $clientCompanyIds, true)) {
            return redirect()->route('audits.types', ['tab' => 'iso50001'])->with('status', 'Blad: wybrana firma nie jest przypisana do wybranego klienta.');
        }

        $isoAudit->update([
            'title' => trim((string) $validated['title']),
            'created_by_user_id' => (int) $validated['client_user_id'],
            'company_id' => (int) $validated['company_id'],
            'due_date' => $validated['due_date'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('audits.types', ['tab' => 'iso50001'])->with('status', 'Audyt ISO 50001 zostal zaktualizowany.');
    }

    public function diagnostics(): View
    {
        $checks = [];
        $failed = 0;

        $pushCheck = function (string $group, string $name, bool $ok, string $details = '') use (&$checks, &$failed): void {
            $checks[] = [
                'group' => $group,
                'name' => $name,
                'ok' => $ok,
                'details' => $details,
            ];

            if (! $ok) {
                $failed++;
            }
        };

        $pushCheck('Środowisko', 'APP_ENV', ! empty(config('app.env')), (string) config('app.env'));
        $pushCheck('Środowisko', 'APP_DEBUG', config('app.debug') !== null, config('app.debug') ? 'true' : 'false');
        $pushCheck('Środowisko', 'APP_URL', ! empty(config('app.url')), (string) config('app.url'));
        $pushCheck('Środowisko', 'Wersja PHP', version_compare(PHP_VERSION, '8.2.0', '>='), PHP_VERSION);
        $pushCheck('Środowisko', 'Wersja Laravel', true, app()->version());

        $dbConnected = false;
        try {
            DB::connection()->getPdo();
            $dbConnected = true;
            $pushCheck('Baza danych', 'Połączenie DB', true, 'Połączono z: '.config('database.default'));
        } catch (Throwable $e) {
            $pushCheck('Baza danych', 'Połączenie DB', false, $e::class.': '.$e->getMessage());
        }

        $requiredTables = [
            'audit_types',
            'audit_type_sections',
            'audit_units',
            'energy_audits',
            'migrations',
        ];

        foreach ($requiredTables as $table) {
            $exists = Schema::hasTable($table);
            $pushCheck('Baza danych', "Tabela {$table}", $exists, $exists ? 'OK' : 'Brak tabeli');
        }

        $requiredColumns = [
            'audit_types' => ['id', 'name', 'formulas', 'created_at', 'updated_at'],
            'audit_type_sections' => ['id', 'audit_type_id', 'name', 'position', 'tasks', 'data_fields', 'formulas'],
            'energy_audits' => ['id', 'audit_type_id', 'audit_type', 'data_payload'],
        ];

        foreach ($requiredColumns as $table => $columns) {
            foreach ($columns as $column) {
                $exists = Schema::hasTable($table) && Schema::hasColumn($table, $column);
                $pushCheck('Baza danych', "Kolumna {$table}.{$column}", $exists, $exists ? 'OK' : 'Brak kolumny');
            }
        }

        $requiredMigrations = [
            '2026_03_15_201000_create_audit_types_table',
            '2026_03_15_201100_create_audit_type_sections_table',
            '2026_03_15_201200_add_audit_type_id_and_data_payload_to_energy_audits_table',
            '2026_03_15_223000_add_formulas_to_audit_types_table',
            '2026_03_15_224000_add_formulas_to_audit_type_sections_table',
        ];

        if ($dbConnected && Schema::hasTable('migrations')) {
            try {
                $ran = DB::table('migrations')->pluck('migration')->all();
                foreach ($requiredMigrations as $migration) {
                    $ok = in_array($migration, $ran, true);
                    $pushCheck('Migracje', $migration, $ok, $ok ? 'Wykonana' : 'Brak wykonania');
                }
            } catch (Throwable $e) {
                $pushCheck('Migracje', 'Odczyt tabeli migrations', false, $e::class.': '.$e->getMessage());
            }
        } else {
            $pushCheck('Migracje', 'Weryfikacja migracji', false, 'Brak połączenia DB lub tabeli migrations');
        }

        try {
            $cacheKey = 'audit_diag_'.Str::random(10);
            Cache::put($cacheKey, 'ok', now()->addMinutes(1));
            $value = Cache::get($cacheKey);
            Cache::forget($cacheKey);
            $pushCheck('Cache / Sesja', 'Zapis/odczyt cache', $value === 'ok', 'Driver: '.config('cache.default'));
        } catch (Throwable $e) {
            $pushCheck('Cache / Sesja', 'Zapis/odczyt cache', false, $e::class.': '.$e->getMessage());
        }

        try {
            $disk = config('filesystems.default');
            $path = 'diagnostics/audits_'.Str::random(10).'.txt';
            Storage::disk($disk)->put($path, 'ok '.now()->toDateTimeString());
            $exists = Storage::disk($disk)->exists($path);
            Storage::disk($disk)->delete($path);
            $pushCheck('Pliki', 'Zapis/odczyt storage', $exists, 'Disk: '.$disk);
        } catch (Throwable $e) {
            $pushCheck('Pliki', 'Zapis/odczyt storage', false, $e::class.': '.$e->getMessage());
        }

        $requiredExtensions = ['pdo', 'pdo_pgsql', 'mbstring', 'json', 'openssl'];
        foreach ($requiredExtensions as $ext) {
            $loaded = extension_loaded($ext);
            $pushCheck('PHP Extensions', $ext, $loaded, $loaded ? 'Załadowane' : 'Brak rozszerzenia');
        }

        if (
            $dbConnected
            && Schema::hasTable('audit_types')
            && Schema::hasTable('audit_type_sections')
            && Schema::hasColumn('audit_types', 'formulas')
            && Schema::hasColumn('audit_type_sections', 'formulas')
        ) {
            DB::beginTransaction();
            try {
                $auditType = AuditType::create([
                    'name' => 'diagnostyka_'.Str::random(14),
                ]);

                $this->syncAuditTypeSections($auditType, [
                    [
                        'name' => 'Sekcja testowa',
                        'tasks_text' => "Krok 1\nKrok 2",
                        'rows' => [
                            [
                                'key' => 'zuzycie',
                                'name' => 'Zużycie',
                                'unit' => 'kWh',
                                'default_value' => '10',
                                'notes' => 'test',
                                'options_text' => '',
                            ],
                        ],
                        'formulas' => [
                            [
                                'label' => 'Suma',
                                'expression' => '1 + 1',
                                'unit' => 'kWh',
                            ],
                        ],
                    ],
                ]);

                $auditType->update(['name' => $auditType->name.'_upd']);

                $this->syncAuditTypeSections($auditType, []);

                DB::rollBack();
                $pushCheck('Symulacja zapisu rodzaju audytu', 'Create + update + sync sections', true, 'Symulacja zakończona bez wyjątku (transakcja rollback)');
            } catch (Throwable $e) {
                DB::rollBack();
                $pushCheck('Symulacja zapisu rodzaju audytu', 'Create + update + sync sections', false, $e::class.': '.$e->getMessage());
            }
        } else {
            $pushCheck('Symulacja zapisu rodzaju audytu', 'Create + update + sync sections', false, 'Pominięto: brak wymaganych tabel/kolumn lub połączenia DB');
        }

        $groupedChecks = collect($checks)->groupBy('group')->all();

        return view('audits.diagnostics', [
            'checks' => $checks,
            'groupedChecks' => $groupedChecks,
            'failedCount' => $failed,
            'okCount' => count($checks) - $failed,
            'generatedAt' => now(),
        ]);
    }

    public function runDiagnosticsRepair(Request $request): RedirectResponse
    {
        $actor = $request->user();
        if (! $actor || ! $actor->canManageEverything()) {
            abort(403);
        }

        $messages = [];

        try {
            Artisan::call('migrate', ['--force' => true]);
            $messages[] = 'Migracje uruchomione.';
        } catch (Throwable $e) {
            $messages[] = 'Błąd migrate: '.$e::class.': '.$e->getMessage();
        }

        try {
            if (Schema::hasTable('audit_types') && ! Schema::hasColumn('audit_types', 'formulas')) {
                Schema::table('audit_types', function ($table) {
                    $table->json('formulas')->nullable()->after('name');
                });
                $messages[] = 'Dodano kolumnę audit_types.formulas.';
            }

            if (Schema::hasTable('audit_type_sections') && ! Schema::hasColumn('audit_type_sections', 'formulas')) {
                Schema::table('audit_type_sections', function ($table) {
                    $table->json('formulas')->nullable()->after('data_fields');
                });
                $messages[] = 'Dodano kolumnę audit_type_sections.formulas.';
            }
        } catch (Throwable $e) {
            $messages[] = 'Błąd naprawy schema: '.$e::class.': '.$e->getMessage();
        }

        $isFixed = Schema::hasTable('audit_types')
            && Schema::hasTable('audit_type_sections')
            && Schema::hasColumn('audit_types', 'formulas')
            && Schema::hasColumn('audit_type_sections', 'formulas');

        if ($isFixed) {
            return redirect()->route('audits.diagnostics')->with('status', 'Naprawa wykonana. '.implode(' ', $messages));
        }

        return redirect()->route('audits.diagnostics')->with('error', 'Naprawa niepełna. '.implode(' ', $messages));
    }

    public function storeAuditType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('audit_types', 'name')],
            'variables' => ['nullable', 'array'],
            'variables.*.name' => ['nullable', 'string', 'max:255'],
            'variables.*.key' => ['nullable', 'string', 'max:255'],
            'variables.*.default_value' => ['nullable', 'string', 'max:255'],
            'sections' => ['nullable', 'array'],
            'sections.*.name' => ['nullable', 'string', 'max:255'],
            'sections.*.tasks_text' => ['nullable', 'string', 'max:5000'],
            'sections.*.rows' => ['nullable', 'array'],
            'sections.*.rows.*.key' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.name' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.unit' => ['nullable', 'string', 'max:64'],
            'sections.*.rows.*.parent_token' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.show_when' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.default_value' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.notes' => ['nullable', 'string', 'max:1000'],
            'sections.*.rows.*.options_text' => ['nullable', 'string', 'max:5000'],
            'sections.*.formulas' => ['nullable', 'array'],
            'sections.*.formulas.*.key' => ['nullable', 'string', 'max:255'],
            'sections.*.formulas.*.label' => ['nullable', 'string', 'max:255'],
            'sections.*.formulas.*.expression' => ['nullable', 'string', 'max:2000'],
            'sections.*.formulas.*.unit' => ['nullable', 'string', 'max:64'],
        ]);

        $auditType = AuditType::create([
            'name' => trim((string) $validated['name']),
            'variables' => $this->normalizeVariables($validated['variables'] ?? []),
        ]);

        $this->syncAuditTypeSections($auditType, $validated['sections'] ?? []);

        return redirect()->route('audits.types', ['tab' => 'energetyczne'])->with('status', 'Rodzaj audytu został dodany.');
    }

    public function updateAuditType(Request $request, AuditType $auditType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('audit_types', 'name')->ignore($auditType->id)],
            'variables' => ['nullable', 'array'],
            'variables.*.name' => ['nullable', 'string', 'max:255'],
            'variables.*.key' => ['nullable', 'string', 'max:255'],
            'variables.*.default_value' => ['nullable', 'string', 'max:255'],
            'sections' => ['nullable', 'array'],
            'sections.*.name' => ['nullable', 'string', 'max:255'],
            'sections.*.tasks_text' => ['nullable', 'string', 'max:5000'],
            'sections.*.rows' => ['nullable', 'array'],
            'sections.*.rows.*.key' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.name' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.unit' => ['nullable', 'string', 'max:64'],
            'sections.*.rows.*.parent_token' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.show_when' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.default_value' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.notes' => ['nullable', 'string', 'max:1000'],
            'sections.*.rows.*.options_text' => ['nullable', 'string', 'max:5000'],
            'sections.*.formulas' => ['nullable', 'array'],
            'sections.*.formulas.*.key' => ['nullable', 'string', 'max:255'],
            'sections.*.formulas.*.label' => ['nullable', 'string', 'max:255'],
            'sections.*.formulas.*.expression' => ['nullable', 'string', 'max:2000'],
            'sections.*.formulas.*.unit' => ['nullable', 'string', 'max:64'],
        ]);

        $auditType->update([
            'name' => trim((string) $validated['name']),
            'variables' => $this->normalizeVariables($validated['variables'] ?? []),
        ]);

        $this->syncAuditTypeSections($auditType, $validated['sections'] ?? []);

        return redirect()->route('audits.types', ['tab' => 'energetyczne'])->with('status', 'Rodzaj audytu został zaktualizowany.');
    }

    public function destroyAuditType(AuditType $auditType): RedirectResponse
    {
        $auditType->delete();

        return redirect()->route('audits.types', ['tab' => 'energetyczne'])->with('status', 'Rodzaj audytu został usunięty.');
    }

    public function copyAuditType(AuditType $auditType): RedirectResponse
    {
        $copy = AuditType::create([
            'name' => $auditType->name.' (kopia)',
            'variables' => $auditType->variables,
        ]);

        foreach ($auditType->sections as $section) {
            AuditTypeSection::create([
                'audit_type_id' => $copy->id,
                'name' => $section->name,
                'position' => $section->position,
                'tasks' => $section->tasks,
                'data_fields' => $section->data_fields,
                'formulas' => $section->formulas,
            ]);
        }

        return redirect()->route('audits.types', ['tab' => 'energetyczne'])->with('status', 'Rodzaj audytu został skopiowany.');
    }

    public function storeUnit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:64', Rule::unique('audit_units', 'name')],
            'kind' => ['required', Rule::in(['text', 'number', 'boolean', 'select'])],
        ]);

        AuditUnit::create([
            'name' => trim((string) $validated['name']),
            'kind' => $validated['kind'],
        ]);

        return redirect()->route('audits.types', ['tab' => 'ustawienia'])->with('status', 'Jednostka została dodana.');
    }

    public function updateUnit(Request $request, AuditUnit $unit): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:64', Rule::unique('audit_units', 'name')->ignore($unit->id)],
            'kind' => ['required', Rule::in(['text', 'number', 'boolean', 'select'])],
        ]);

        $unit->update([
            'name' => trim((string) $validated['name']),
            'kind' => $validated['kind'],
        ]);

        return redirect()->route('audits.types', ['tab' => 'ustawienia'])->with('status', 'Jednostka została zaktualizowana.');
    }

    public function destroyUnit(AuditUnit $unit): RedirectResponse
    {
        $unit->delete();

        return redirect()->route('audits.types', ['tab' => 'ustawienia'])->with('status', 'Jednostka została usunięta.');
    }

    private function syncAuditTypeSections(AuditType $auditType, array $sections): void
    {
        $existingSections = $auditType->sections()->orderBy('position')->get()->values();

        $position = 1;
        $savedSectionIds = [];

        foreach ($sections as $section) {
            $name = trim((string) ($section['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $usedFieldKeys = [];

            $tasks = collect(preg_split('/\r\n|\r|\n/', (string) ($section['tasks_text'] ?? '')))
                ->map(fn ($line) => trim((string) $line))
                ->filter()
                ->values()
                ->all();

            $tokenMap = [];

            $rows = collect($section['rows'] ?? [])
                ->map(function ($row) use (&$usedFieldKeys, &$tokenMap) {
                    $fieldName = trim((string) ($row['name'] ?? ''));
                    if ($fieldName === '') {
                        return null;
                    }

                    $providedKey = Str::slug(trim((string) ($row['key'] ?? '')), '_');
                    $baseKey = $providedKey !== '' ? $providedKey : Str::slug($fieldName, '_');
                    if ($baseKey === '') {
                        $baseKey = 'pole';
                    }

                    $candidateKey = $baseKey;
                    $suffix = 2;
                    while (in_array($candidateKey, $usedFieldKeys, true)) {
                        $candidateKey = $baseKey.'_'.$suffix;
                        $suffix++;
                    }
                    $usedFieldKeys[] = $candidateKey;

                    if ($providedKey !== '') {
                        $tokenMap[$providedKey] = $candidateKey;
                    }

                    return [
                        'key' => $candidateKey,
                        'name' => $fieldName,
                        'unit' => trim((string) ($row['unit'] ?? '')),
                        'parent_token' => Str::slug(trim((string) ($row['parent_token'] ?? '')), '_'),
                        'show_when' => trim((string) ($row['show_when'] ?? '')),
                        'default_value' => trim((string) ($row['default_value'] ?? '')),
                        'notes' => trim((string) ($row['notes'] ?? '')),
                        'options_text' => trim((string) ($row['options_text'] ?? '')),
                    ];
                })
                ->filter()
                ->values()
                ->all();

            $rows = collect($rows)
                ->map(function (array $row) use ($tokenMap): array {
                    $parentToken = (string) ($row['parent_token'] ?? '');
                    $resolvedParentToken = (string) ($tokenMap[$parentToken] ?? $parentToken);

                    if ($resolvedParentToken === (string) ($row['key'] ?? '')) {
                        $resolvedParentToken = '';
                    }

                    $row['parent_token'] = $resolvedParentToken;

                    return $row;
                })
                ->values()
                ->all();

            $unitKinds = AuditUnit::query()->pluck('kind', 'name');
            $rows = collect($rows)
                ->map(function (array $row) use ($unitKinds): array {
                    $unitName = (string) ($row['unit'] ?? '');
                    $kind = (string) ($unitKinds[$unitName] ?? 'number');

                    $options = [];
                    if ($kind === 'select') {
                        $options = collect(preg_split('/\r\n|\r|\n/', (string) ($row['options_text'] ?? '')))
                            ->map(fn ($line) => trim((string) $line))
                            ->filter()
                            ->values()
                            ->all();
                    }

                    return [
                        'key' => (string) ($row['key'] ?? ''),
                        'name' => (string) ($row['name'] ?? ''),
                        'unit' => $unitName,
                        'kind' => $kind,
                        'parent_token' => (string) ($row['parent_token'] ?? ''),
                        'show_when' => (string) ($row['show_when'] ?? ''),
                        'options' => $options,
                        'default_value' => (string) ($row['default_value'] ?? ''),
                        'notes' => (string) ($row['notes'] ?? ''),
                    ];
                })
                ->values()
                ->all();

            $sectionAttributes = [
                'name' => $name,
                'position' => $position,
                'tasks' => $tasks,
                'data_fields' => $rows,
                'formulas' => $this->normalizeFormulasWithTokenMap($section['formulas'] ?? [], $tokenMap),
            ];

            $existingSection = $existingSections->get($position - 1);
            if ($existingSection) {
                $existingSection->update($sectionAttributes);
                $savedSectionIds[] = $existingSection->id;
            } else {
                $createdSection = AuditTypeSection::create([
                    'audit_type_id' => $auditType->id,
                    ...$sectionAttributes,
                ]);
                $savedSectionIds[] = $createdSection->id;
            }

            $position++;
        }

        if (count($savedSectionIds) === 0) {
            $auditType->sections()->delete();

            return;
        }

        $auditType->sections()
            ->whereNotIn('id', $savedSectionIds)
            ->delete();
    }

    private function normalizeSectionPayload(AuditType $auditType, array $rawPayload): array
    {
        $result = [];

        foreach ($auditType->sections as $section) {
            $sectionKey = (string) $section->id;
            $sectionPayload = is_array($rawPayload[$sectionKey] ?? null) ? $rawPayload[$sectionKey] : [];

            $allowedTasks = collect($section->tasks ?? [])->map(fn ($task) => trim((string) $task))->filter()->values()->all();
            $allowedFields = collect($section->data_fields ?? [])
                ->map(function ($field) {
                    if (is_array($field)) {
                        return [
                            'key' => trim((string) ($field['key'] ?? Str::slug((string) ($field['name'] ?? ''), '_'))),
                            'name' => trim((string) ($field['name'] ?? '')),
                            'unit' => trim((string) ($field['unit'] ?? '')),
                            'kind' => trim((string) ($field['kind'] ?? 'number')),
                            'options' => collect($field['options'] ?? [])->map(fn ($item) => trim((string) $item))->filter()->values()->all(),
                        ];
                    }

                    return [
                        'key' => Str::slug((string) $field, '_'),
                        'name' => trim((string) $field),
                        'unit' => '',
                        'kind' => 'number',
                        'options' => [],
                    ];
                })
                ->filter(fn ($field) => $field['name'] !== '')
                ->values()
                ->all();

            $selectedTasks = is_array($sectionPayload['tasks'] ?? null) ? $sectionPayload['tasks'] : [];
            $selectedMap = [];

            foreach ($allowedTasks as $taskName) {
                $selectedMap[$taskName] = isset($selectedTasks[$taskName]);
            }

            $rowValues = is_array($sectionPayload['rows'] ?? null) ? $sectionPayload['rows'] : [];
            $legacyFieldValues = is_array($sectionPayload['fields'] ?? null) ? $sectionPayload['fields'] : [];
            $normalizedFields = [];

            foreach ($allowedFields as $index => $fieldConfig) {
                $fieldName = $fieldConfig['name'];
                $fieldKey = trim((string) ($fieldConfig['key'] ?? Str::slug($fieldName, '_')));
                $rowValue = is_array($rowValues[(string) $index] ?? null) ? $rowValues[(string) $index] : [];

                $value = trim((string) ($rowValue['value'] ?? ''));
                if ($value === '' && isset($legacyFieldValues[$fieldName])) {
                    $value = trim((string) $legacyFieldValues[$fieldName]);
                }

                $normalizedFields[] = [
                    'key' => $fieldKey,
                    'name' => $fieldName,
                    'unit' => $fieldConfig['unit'],
                    'kind' => $fieldConfig['kind'],
                    'options' => $fieldConfig['options'],
                    'value' => $value,
                    'notes' => trim((string) ($rowValue['notes'] ?? '')),
                ];
            }

            $result[$sectionKey] = [
                'tasks' => $selectedMap,
                'fields' => $normalizedFields,
            ];
        }

        return $result;
    }

    private function normalizeFormulas(array $formulas): array
    {
        $usedKeys = [];

        return collect($formulas)
            ->map(function ($formula) use (&$usedKeys) {
                $label = trim((string) ($formula['label'] ?? ''));
                $expression = trim((string) ($formula['expression'] ?? ''));
                $unit = trim((string) ($formula['unit'] ?? ''));

                if ($label === '' || $expression === '') {
                    return null;
                }

                $providedKey = Str::slug(trim((string) ($formula['key'] ?? '')), '_');
                $baseKey = $providedKey !== '' ? $providedKey : Str::slug($label, '_');
                if ($baseKey === '') {
                    $baseKey = 'wzor';
                }

                $candidateKey = $baseKey;
                $suffix = 2;
                while (in_array($candidateKey, $usedKeys, true)) {
                    $candidateKey = $baseKey.'_'.$suffix;
                    $suffix++;
                }
                $usedKeys[] = $candidateKey;

                return [
                    'key' => $candidateKey,
                    'label' => $label,
                    'expression' => $expression,
                    'unit' => $unit,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeFormulasWithTokenMap(array $formulas, array $tokenMap): array
    {
        $normalized = $this->normalizeFormulas($formulas);

        if ($tokenMap === []) {
            return $normalized;
        }

        return collect($normalized)
            ->map(function (array $formula) use ($tokenMap): array {
                $expression = (string) ($formula['expression'] ?? '');

                foreach ($tokenMap as $oldToken => $newToken) {
                    if ($oldToken === '' || $oldToken === $newToken) {
                        continue;
                    }

                    $expression = preg_replace('/\{'.preg_quote($oldToken, '/').'\}/', '{'.$newToken.'}', $expression) ?? $expression;
                }

                $formula['expression'] = $expression;

                return $formula;
            })
            ->values()
            ->all();
    }

    private function normalizeVariables(array $variables): array
    {
        $usedKeys = [];

        return collect($variables)
            ->map(function (array $var) use (&$usedKeys): ?array {
                $name = trim((string) ($var['name'] ?? ''));
                if ($name === '') {
                    return null;
                }

                $providedKey = Str::slug(trim((string) ($var['key'] ?? '')), '_');
                $baseKey = $providedKey !== '' ? $providedKey : Str::slug($name, '_');
                if ($baseKey === '') {
                    $baseKey = 'zmienna';
                }

                $candidateKey = $baseKey;
                $suffix = 2;
                while (in_array($candidateKey, $usedKeys, true)) {
                    $candidateKey = $baseKey.'_'.$suffix;
                    $suffix++;
                }
                $usedKeys[] = $candidateKey;

                return [
                    'key' => $candidateKey,
                    'name' => $name,
                    'default_value' => trim((string) ($var['default_value'] ?? '')),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, int>
     */
    private function clientCompanyIds(User $user): \Illuminate\Database\Eloquent\Collection
    {
        $assignedCompanyIds = $user->assignedCompanies()->pluck('companies.id');

        return Company::query()
            ->where(function ($query) use ($user, $assignedCompanyIds): void {
                $query->where('client_id', $user->id)
                    ->orWhereIn('id', $assignedCompanyIds);

                if ($user->company_id) {
                    $query->orWhere('id', $user->company_id);
                }
            })
            ->pluck('id');
    }
}
