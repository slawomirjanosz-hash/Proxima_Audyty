<?php

namespace App\Http\Controllers;

use App\Models\AuditType;
use App\Models\AuditTypeSection;
use App\Models\AuditUnit;
use App\Models\Company;
use App\Models\EnergyAudit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuditsController extends Controller
{
    public function index(Request $request): View
    {
        $activeTab = (string) $request->query('tab', 'in-progress');

        $inProgressAudits = EnergyAudit::with(['company', 'auditor', 'auditType'])
            ->whereNotIn('status', ['completed', 'done', 'closed', 'cancelled', 'archived'])
            ->latest()
            ->get();

        $completedAudits = EnergyAudit::with(['company', 'auditor', 'auditType'])
            ->whereIn('status', ['completed', 'done', 'closed', 'cancelled', 'archived'])
            ->orderByDesc('completed_at')
            ->orderByDesc('updated_at')
            ->get();

        $companies = Company::query()->orderBy('name')->get();

        $auditors = User::query()
            ->whereIn('role', ['admin', 'auditor'])
            ->orderBy('name')
            ->get();

        $auditTypes = AuditType::with('sections')->orderBy('name')->get();

        return view('audits.index', [
            'inProgressAudits' => $inProgressAudits,
            'completedAudits' => $completedAudits,
            'companies' => $companies,
            'auditors' => $auditors,
            'auditTypes' => $auditTypes,
            'activeTab' => in_array($activeTab, ['new', 'in-progress', 'completed'], true) ? $activeTab : 'in-progress',
        ]);
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

    public function settings(): View
    {
        $auditTypes = AuditType::with('sections')->orderBy('name')->get();
        $units = AuditUnit::query()->orderBy('name')->get();

        return view('audits.settings', [
            'auditTypes' => $auditTypes,
            'units' => $units,
        ]);
    }

    public function storeAuditType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('audit_types', 'name')],
            'sections' => ['nullable', 'array'],
            'sections.*.name' => ['nullable', 'string', 'max:255'],
            'sections.*.tasks_text' => ['nullable', 'string', 'max:5000'],
            'sections.*.rows' => ['nullable', 'array'],
            'sections.*.rows.*.key' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.name' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.unit' => ['nullable', 'string', 'max:64'],
            'sections.*.rows.*.default_value' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.notes' => ['nullable', 'string', 'max:1000'],
            'sections.*.rows.*.options_text' => ['nullable', 'string', 'max:5000'],
            'sections.*.formulas' => ['nullable', 'array'],
            'sections.*.formulas.*.label' => ['nullable', 'string', 'max:255'],
            'sections.*.formulas.*.expression' => ['nullable', 'string', 'max:2000'],
            'sections.*.formulas.*.unit' => ['nullable', 'string', 'max:64'],
        ]);

        $auditType = AuditType::create([
            'name' => trim((string) $validated['name']),
        ]);

        $this->syncAuditTypeSections($auditType, $validated['sections'] ?? []);

        return back()->with('status', 'Rodzaj audytu został dodany.');
    }

    public function updateAuditType(Request $request, AuditType $auditType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('audit_types', 'name')->ignore($auditType->id)],
            'sections' => ['nullable', 'array'],
            'sections.*.name' => ['nullable', 'string', 'max:255'],
            'sections.*.tasks_text' => ['nullable', 'string', 'max:5000'],
            'sections.*.rows' => ['nullable', 'array'],
            'sections.*.rows.*.key' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.name' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.unit' => ['nullable', 'string', 'max:64'],
            'sections.*.rows.*.default_value' => ['nullable', 'string', 'max:255'],
            'sections.*.rows.*.notes' => ['nullable', 'string', 'max:1000'],
            'sections.*.rows.*.options_text' => ['nullable', 'string', 'max:5000'],
            'sections.*.formulas' => ['nullable', 'array'],
            'sections.*.formulas.*.label' => ['nullable', 'string', 'max:255'],
            'sections.*.formulas.*.expression' => ['nullable', 'string', 'max:2000'],
            'sections.*.formulas.*.unit' => ['nullable', 'string', 'max:64'],
        ]);

        $auditType->update([
            'name' => trim((string) $validated['name']),
        ]);

        $this->syncAuditTypeSections($auditType, $validated['sections'] ?? []);

        return back()->with('status', 'Rodzaj audytu został zaktualizowany.');
    }

    public function destroyAuditType(AuditType $auditType): RedirectResponse
    {
        $auditType->delete();

        return back()->with('status', 'Rodzaj audytu został usunięty.');
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

        return back()->with('status', 'Jednostka została dodana.');
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

        return back()->with('status', 'Jednostka została zaktualizowana.');
    }

    public function destroyUnit(AuditUnit $unit): RedirectResponse
    {
        $unit->delete();

        return back()->with('status', 'Jednostka została usunięta.');
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

            $rows = collect($section['rows'] ?? [])
                ->map(function ($row) use (&$usedFieldKeys) {
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

                    return [
                        'key' => $candidateKey,
                        'name' => $fieldName,
                        'unit' => trim((string) ($row['unit'] ?? '')),
                        'default_value' => trim((string) ($row['default_value'] ?? '')),
                        'notes' => trim((string) ($row['notes'] ?? '')),
                        'options_text' => trim((string) ($row['options_text'] ?? '')),
                    ];
                })
                ->filter()
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
                'formulas' => $this->normalizeFormulas($section['formulas'] ?? []),
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
        return collect($formulas)
            ->map(function ($formula) {
                $label = trim((string) ($formula['label'] ?? ''));
                $expression = trim((string) ($formula['expression'] ?? ''));
                $unit = trim((string) ($formula['unit'] ?? ''));

                if ($label === '' || $expression === '') {
                    return null;
                }

                return [
                    'label' => $label,
                    'expression' => $expression,
                    'unit' => $unit,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
