<?php

namespace App\Http\Controllers;

use App\Models\AuditType;
use App\Models\AuditTypeSection;
use App\Models\Company;
use App\Models\EnergyAudit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return view('audits.settings', [
            'auditTypes' => $auditTypes,
        ]);
    }

    public function storeAuditType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('audit_types', 'name')],
            'sections' => ['nullable', 'array'],
            'sections.*.name' => ['nullable', 'string', 'max:255'],
            'sections.*.tasks_text' => ['nullable', 'string', 'max:5000'],
            'sections.*.fields_text' => ['nullable', 'string', 'max:5000'],
        ]);

        $auditType = AuditType::create([
            'name' => trim((string) $validated['name']),
        ]);

        $sections = is_array($validated['sections'] ?? null) ? $validated['sections'] : [];
        $position = 1;

        foreach ($sections as $section) {
            $name = trim((string) ($section['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $tasks = collect(preg_split('/\r\n|\r|\n/', (string) ($section['tasks_text'] ?? '')))
                ->map(fn ($line) => trim((string) $line))
                ->filter()
                ->values()
                ->all();

            $fields = collect(preg_split('/\r\n|\r|\n/', (string) ($section['fields_text'] ?? '')))
                ->map(fn ($line) => trim((string) $line))
                ->filter()
                ->values()
                ->all();

            AuditTypeSection::create([
                'audit_type_id' => $auditType->id,
                'name' => $name,
                'position' => $position,
                'tasks' => $tasks,
                'data_fields' => $fields,
            ]);

            $position++;
        }

        return back()->with('status', 'Rodzaj audytu został dodany.');
    }

    public function destroyAuditType(AuditType $auditType): RedirectResponse
    {
        $auditType->delete();

        return back()->with('status', 'Rodzaj audytu został usunięty.');
    }

    private function normalizeSectionPayload(AuditType $auditType, array $rawPayload): array
    {
        $result = [];

        foreach ($auditType->sections as $section) {
            $sectionKey = (string) $section->id;
            $sectionPayload = is_array($rawPayload[$sectionKey] ?? null) ? $rawPayload[$sectionKey] : [];

            $allowedTasks = collect($section->tasks ?? [])->map(fn ($task) => trim((string) $task))->filter()->values()->all();
            $allowedFields = collect($section->data_fields ?? [])->map(fn ($field) => trim((string) $field))->filter()->values()->all();

            $selectedTasks = is_array($sectionPayload['tasks'] ?? null) ? $sectionPayload['tasks'] : [];
            $selectedMap = [];

            foreach ($allowedTasks as $taskName) {
                $selectedMap[$taskName] = isset($selectedTasks[$taskName]);
            }

            $fieldValues = is_array($sectionPayload['fields'] ?? null) ? $sectionPayload['fields'] : [];
            $normalizedFields = [];
            foreach ($allowedFields as $fieldName) {
                $normalizedFields[$fieldName] = trim((string) ($fieldValues[$fieldName] ?? ''));
            }

            $result[$sectionKey] = [
                'tasks' => $selectedMap,
                'fields' => $normalizedFields,
            ];
        }

        return $result;
    }
}
