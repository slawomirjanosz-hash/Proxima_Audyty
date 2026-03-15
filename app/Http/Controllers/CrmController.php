<?php

namespace App\Http\Controllers;

use App\Models\CrmActivity;
use App\Models\CrmCompany;
use App\Models\CrmCustomerType;
use App\Models\CrmDeal;
use App\Models\CrmStage;
use App\Models\CrmTask;
use App\Models\CrmTaskChange;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class CrmController extends Controller
{
    public function index(Request $request): View
    {
        $this->syncSystemCompaniesToCrm();

        $userId = $request->user()->id;
        $crmStages = CrmStage::orderBy('order')->get();
        $closedStageSlugs = $crmStages->where('is_closed', true)->pluck('slug')->values()->all();

        if ($closedStageSlugs === []) {
            $closedStageSlugs = ['wygrana', 'przegrana'];
        }

        $companies = CrmCompany::with(['owner', 'customerType', 'systemCompany'])->latest()->take(120)->get();

        $deals = CrmDeal::with(['company', 'assignedUsers', 'owner', 'user'])
            ->where(function ($query) use ($userId): void {
                $query->where('user_id', $userId)
                    ->orWhereHas('assignedUsers', function ($q) use ($userId): void {
                        $q->where('user_id', $userId);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $tasks = CrmTask::with(['company', 'assignedTo', 'deal'])
            ->where('status', '!=', 'zakonczone')
            ->orderBy('due_date', 'asc')
            ->get();

        $completedTasks = CrmTask::with(['company', 'assignedTo'])
            ->where('status', 'zakonczone')
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();

        $activities = CrmActivity::with(['company', 'deal', 'user'])
            ->orderBy('activity_date', 'desc')
            ->limit(50)
            ->get();

        $stats = [
            'total_companies' => CrmCompany::count(),
            'active_deals' => CrmDeal::whereNotIn('stage', $closedStageSlugs)
                ->where(function ($query) use ($userId): void {
                    $query->where('user_id', $userId)
                        ->orWhereHas('assignedUsers', function ($q) use ($userId): void {
                            $q->where('user_id', $userId);
                        });
                })
                ->count(),
            'total_pipeline_value' => (float) CrmDeal::whereNotIn('stage', $closedStageSlugs)
                ->where(function ($query) use ($userId): void {
                    $query->where('user_id', $userId)
                        ->orWhereHas('assignedUsers', function ($q) use ($userId): void {
                            $q->where('user_id', $userId);
                        });
                })
                ->sum('value'),
            'overdue_tasks' => CrmTask::where('status', '!=', 'zakonczone')
                ->whereNotNull('due_date')
                ->where('due_date', '<', now())
                ->count(),
            'deals_by_stage' => CrmDeal::with('company')
                ->whereNotIn('stage', $closedStageSlugs)
                ->where(function ($query) use ($userId): void {
                    $query->where('user_id', $userId)
                        ->orWhereHas('assignedUsers', function ($q) use ($userId): void {
                            $q->where('user_id', $userId);
                        });
                })
                ->get()
                ->groupBy('stage'),
            'recent_won_deals' => CrmDeal::with('company')
                ->whereIn('stage', $closedStageSlugs)
                ->whereNotNull('actual_close_date')
                ->where(function ($query) use ($userId): void {
                    $query->where('user_id', $userId)
                        ->orWhereHas('assignedUsers', function ($q) use ($userId): void {
                            $q->where('user_id', $userId);
                        });
                })
                ->orderBy('actual_close_date', 'desc')
                ->take(20)
                ->get(),
        ];

        return view('crm.index', [
            'companies' => $companies,
            'deals' => $deals,
            'tasks' => $tasks,
            'completedTasks' => $completedTasks,
            'activities' => $activities,
            'taskChanges' => CrmTaskChange::with(['user'])
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get(),
            'crmStages' => $crmStages,
            'customerTypes' => CrmCustomerType::orderBy('name')->get(),
            'users' => User::whereIn('role', ['admin', 'auditor'])->orderBy('name')->get(),
            'stats' => $stats,
        ]);
    }

    public function settings(): View
    {
        return view('crm.settings', [
            'customerTypes' => CrmCustomerType::orderBy('name')->get(),
            'crmStages' => CrmStage::orderBy('order')->get(),
            'taskChanges' => CrmTaskChange::with(['user'])
                ->orderBy('created_at', 'desc')
                ->limit(120)
                ->get(),
        ]);
    }

    public function diagnostics(): View
    {
        return view('crm.diagnostics', [
            'stats' => [
                'companies' => CrmCompany::count(),
                'deals' => CrmDeal::count(),
                'tasks' => CrmTask::count(),
                'activities' => CrmActivity::count(),
                'customer_types' => CrmCustomerType::count(),
                'stages' => CrmStage::count(),
            ],
            'latestCompanies' => CrmCompany::latest()->take(10)->get(),
            'latestDeals' => CrmDeal::latest()->take(10)->get(),
        ]);
    }

    public function searchCompanyByNip(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'min:10', 'max:20'],
        ]);

        $nip = preg_replace('/\D+/', '', (string) $validated['nip']);

        if (strlen($nip) !== 10) {
            return response()->json([
                'success' => false,
                'message' => 'Nieprawidłowy NIP',
            ]);
        }

        $localCompany = CrmCompany::where('nip', $nip)->first();

        if ($localCompany) {
            return response()->json([
                'success' => true,
                'source' => 'local',
                'data' => [
                    'name' => $localCompany->name,
                    'nip' => $localCompany->nip,
                    'email' => $localCompany->email,
                    'phone' => $localCompany->phone,
                    'address' => $localCompany->address,
                    'city' => $localCompany->city,
                    'postal_code' => $localCompany->postal_code,
                ],
            ]);
        }

        $response = Http::timeout(8)
            ->acceptJson()
            ->get("https://wl-api.mf.gov.pl/api/search/nip/{$nip}", [
                'date' => now()->format('Y-m-d'),
            ]);

        if (! $response->ok()) {
            return response()->json([
                'success' => false,
                'message' => 'Nie znaleziono danych dla podanego NIP',
            ]);
        }

        $subject = $response->json('result.subject');

        if (! is_array($subject) || empty($subject['name'])) {
            return response()->json([
                'success' => false,
                'message' => 'Nie znaleziono danych dla podanego NIP',
            ]);
        }

        $address = trim((string) ($subject['workingAddress'] ?? $subject['residenceAddress'] ?? ''));
        $street = '';
        $postalCode = '';
        $city = '';

        if ($address !== '') {
            $parts = preg_split('/\s*,\s*/', $address);
            $street = trim((string) ($parts[0] ?? ''));
            $cityPart = trim((string) ($parts[1] ?? $parts[0] ?? ''));

            if (preg_match('/(?<postal>\d{2}-\d{3})\s+(?<city>.+)$/u', $cityPart, $matches) === 1) {
                $postalCode = trim((string) ($matches['postal'] ?? ''));
                $city = trim((string) ($matches['city'] ?? ''));
            } else {
                $city = $cityPart;
            }
        }

        return response()->json([
            'success' => true,
            'source' => 'vat',
            'data' => [
                'name' => (string) $subject['name'],
                'nip' => $nip,
                'email' => '',
                'phone' => '',
                'address' => $street,
                'city' => $city,
                'postal_code' => $postalCode,
            ],
        ]);
    }

    public function addCompany(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:32'],
            'nip' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'in:klient,potencjalny,partner,konkurencja'],
            'status' => ['required', 'in:aktywny,nieaktywny,zawieszony'],
            'notes' => ['nullable', 'string'],
            'source' => ['nullable', 'string', 'max:100'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'customer_type_id' => ['nullable', 'exists:crm_customer_types,id'],
            'add_to_system' => ['nullable', 'boolean'],
        ]);

        $addToSystem = (bool) ($validated['add_to_system'] ?? false);
        unset($validated['add_to_system']);

        if (! empty($validated['nip'])) {
            $validated['nip'] = preg_replace('/\D+/', '', (string) $validated['nip']);
        }

        $validated['added_by'] = $request->user()->id;

        if (empty($validated['short_name'])) {
            $validated['short_name'] = mb_substr((string) $validated['name'], 0, 12);
        }

        $company = CrmCompany::create($validated);

        if ($addToSystem) {
            $systemCompany = $this->findOrCreateSystemCompanyFromCrmData($validated);
            $company->update(['system_company_id' => $systemCompany->id]);
        }

        $this->logCrmChange($request, 'company', (int) $company->id, 'created', [
            'name' => $company->name,
        ]);

        return redirect()->route('crm.index')->with('status', 'Firma została dodana.');
    }

    public function getCompany(int $id): JsonResponse
    {
        return response()->json(CrmCompany::with('systemCompany')->findOrFail($id));
    }

    public function updateCompany(Request $request, int $id): RedirectResponse
    {
        $company = CrmCompany::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:32'],
            'nip' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'in:klient,potencjalny,partner,konkurencja'],
            'status' => ['required', 'in:aktywny,nieaktywny,zawieszony'],
            'notes' => ['nullable', 'string'],
            'source' => ['nullable', 'string', 'max:100'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'customer_type_id' => ['nullable', 'exists:crm_customer_types,id'],
        ]);

        if (! empty($validated['nip'])) {
            $validated['nip'] = preg_replace('/\D+/', '', (string) $validated['nip']);
        }

        if (empty($validated['short_name'])) {
            $validated['short_name'] = mb_substr((string) $validated['name'], 0, 12);
        }

        $before = $company->getOriginal();
        $company->update($validated);
        $changes = [];
        foreach ($validated as $key => $value) {
            if (array_key_exists($key, $before) && $before[$key] != $value) {
                $changes[$key] = ['old' => $before[$key], 'new' => $value];
            }
        }
        if ($changes !== []) {
            $this->logCrmChange($request, 'company', (int) $company->id, 'updated', [
                'name' => $company->name,
                'changes' => $changes,
            ]);
        }

        return redirect()->route('crm.index')->with('status', 'Firma została zaktualizowana.');
    }

    public function deleteCompany(int $id): RedirectResponse
    {
        $company = CrmCompany::findOrFail($id);
        $companyName = $company->name;
        $company->delete();
        $this->logCrmChange($request, 'company', $id, 'deleted', [
            'name' => $companyName,
        ]);

        return redirect()->route('crm.index')->with('status', 'Firma została usunięta.');
    }

    public function addCompanyToSystem(Request $request, int $id): RedirectResponse
    {
        $crmCompany = CrmCompany::findOrFail($id);

        if ($crmCompany->system_company_id) {
            return redirect()->route('crm.index')->with('status', 'Firma jest już dodana do systemu.');
        }

        $systemCompany = $this->findOrCreateSystemCompanyFromCrmData([
            'name' => $crmCompany->name,
            'short_name' => $crmCompany->short_name,
            'nip' => $crmCompany->nip,
            'city' => $crmCompany->city,
            'postal_code' => $crmCompany->postal_code,
            'address' => $crmCompany->address,
            'phone' => $crmCompany->phone,
            'email' => $crmCompany->email,
            'notes' => $crmCompany->notes,
        ]);

        $crmCompany->update(['system_company_id' => $systemCompany->id]);

        $this->logCrmChange($request, 'company', (int) $crmCompany->id, 'updated', [
            'name' => $crmCompany->name,
            'changes' => [
                'system_company_id' => ['old' => null, 'new' => $systemCompany->id],
            ],
        ]);

        return redirect()->route('crm.index')->with('status', 'Firma została dodana do systemu i połączona z CRM.');
    }

    public function addDeal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_id' => ['nullable', 'exists:crm_companies,id'],
            'value' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'stage' => ['required', 'exists:crm_stages,slug'],
            'probability' => ['required', 'integer', 'min:0', 'max:100'],
            'expected_close_date' => ['nullable', 'date'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string'],
            'assigned_users' => ['nullable', 'array'],
            'assigned_users.*' => ['exists:users,id'],
        ]);

        $validated['user_id'] = $request->user()->id;

        if (empty($validated['owner_id'])) {
            $validated['owner_id'] = $request->user()->id;
        }

        $assignedUsers = $validated['assigned_users'] ?? [];
        unset($validated['assigned_users']);

        if (empty($validated['currency'])) {
            $validated['currency'] = 'PLN';
        }

        $deal = CrmDeal::create($validated);
        $deal->assignedUsers()->sync($assignedUsers);
        $this->logCrmChange($request, 'deal', (int) $deal->id, 'created', [
            'name' => $deal->name,
            'stage' => $deal->stage,
        ]);

        return redirect()->route('crm.index')->with('status', 'Szansa sprzedażowa została dodana.');
    }

    public function getDeal(int $id): JsonResponse
    {
        $deal = CrmDeal::with(['assignedUsers', 'company', 'tasks.assignedTo', 'activities'])->findOrFail($id);

        return response()->json($deal);
    }

    public function updateDeal(Request $request, int $id): RedirectResponse
    {
        $deal = CrmDeal::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_id' => ['nullable', 'exists:crm_companies,id'],
            'value' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'stage' => ['required', 'exists:crm_stages,slug'],
            'probability' => ['required', 'integer', 'min:0', 'max:100'],
            'expected_close_date' => ['nullable', 'date'],
            'actual_close_date' => ['nullable', 'date'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string'],
            'lost_reason' => ['nullable', 'string'],
            'assigned_users' => ['nullable', 'array'],
            'assigned_users.*' => ['exists:users,id'],
        ]);

        $assignedUsers = $validated['assigned_users'] ?? [];
        unset($validated['assigned_users']);

        if (empty($validated['currency'])) {
            $validated['currency'] = 'PLN';
        }

        $closedStageSlugs = CrmStage::where('is_closed', true)->pluck('slug')->values()->all();
        if ($closedStageSlugs === []) {
            $closedStageSlugs = ['wygrana', 'przegrana'];
        }

        if (in_array((string) $validated['stage'], $closedStageSlugs, true)
            && ! in_array((string) $deal->stage, $closedStageSlugs, true)
            && empty($validated['actual_close_date'])) {
            $validated['actual_close_date'] = now();
        }

        if (! in_array((string) $validated['stage'], $closedStageSlugs, true)
            && in_array((string) $deal->stage, $closedStageSlugs, true)) {
            $validated['actual_close_date'] = null;
        }

        $before = $deal->getOriginal();
        $deal->update($validated);
        $deal->assignedUsers()->sync($assignedUsers);
        $changes = [];
        foreach ($validated as $key => $value) {
            if (array_key_exists($key, $before) && $before[$key] != $value) {
                $changes[$key] = ['old' => $before[$key], 'new' => $value];
            }
        }
        if ($changes !== []) {
            $this->logCrmChange($request, 'deal', (int) $deal->id, 'updated', [
                'name' => $deal->name,
                'changes' => $changes,
            ]);
        }

        return redirect()->route('crm.index')->with('status', 'Szansa sprzedażowa została zaktualizowana.');
    }

    public function deleteDeal(int $id): RedirectResponse
    {
        $deal = CrmDeal::findOrFail($id);
        $dealName = $deal->name;
        $deal->delete();
        $this->logCrmChange($request, 'deal', $id, 'deleted', [
            'name' => $dealName,
        ]);

        return redirect()->route('crm.index')->with('status', 'Szansa sprzedażowa została usunięta.');
    }

    public function addTask(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:telefon,email,spotkanie,zadanie,follow_up'],
            'priority' => ['required', 'in:niska,normalna,wysoka,pilna'],
            'status' => ['required', 'in:do_zrobienia,w_trakcie,zakonczone,anulowane'],
            'due_date' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'company_id' => ['nullable', 'exists:crm_companies,id'],
            'deal_id' => ['nullable', 'exists:crm_deals,id'],
        ]);

        $validated['created_by'] = $request->user()->id;

        $task = CrmTask::create($validated);
        $this->logCrmChange($request, 'task', (int) $task->id, 'created', [
            'title' => $task->title,
        ], (int) $task->id);

        return redirect()->route('crm.index')->with('status', 'Zadanie zostało dodane.');
    }

    public function getTask(int $id): JsonResponse
    {
        return response()->json(CrmTask::findOrFail($id));
    }

    public function updateTask(Request $request, int $id): RedirectResponse
    {
        $task = CrmTask::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:telefon,email,spotkanie,zadanie,follow_up'],
            'priority' => ['required', 'in:niska,normalna,wysoka,pilna'],
            'status' => ['required', 'in:do_zrobienia,w_trakcie,zakonczone,anulowane'],
            'due_date' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'company_id' => ['nullable', 'exists:crm_companies,id'],
            'deal_id' => ['nullable', 'exists:crm_deals,id'],
        ]);

        if (($validated['status'] ?? '') === 'zakonczone' && empty($validated['completed_at'])) {
            $validated['completed_at'] = now();
        }

        $before = $task->getOriginal();
        $task->update($validated);
        $changes = [];
        foreach ($validated as $key => $value) {
            if (array_key_exists($key, $before) && $before[$key] != $value) {
                $changes[$key] = ['old' => $before[$key], 'new' => $value];
            }
        }
        if ($changes !== []) {
            $this->logCrmChange($request, 'task', (int) $task->id, 'updated', [
                'title' => $task->title,
                'changes' => $changes,
            ], (int) $task->id);
        }

        return redirect()->route('crm.index')->with('status', 'Zadanie zostało zaktualizowane.');
    }

    public function deleteTask(int $id): RedirectResponse
    {
        $task = CrmTask::findOrFail($id);
        $taskTitle = $task->title;
        $task->delete();
        $this->logCrmChange($request, 'task', $id, 'deleted', [
            'title' => $taskTitle,
        ], $id);

        return redirect()->route('crm.index')->with('status', 'Zadanie zostało usunięte.');
    }

    public function addActivity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:telefon,email,spotkanie,notatka,sms,oferta,umowa,faktura,reklamacja'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'activity_date' => ['required', 'date'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'outcome' => ['nullable', 'in:pozytywny,neutralny,negatywny,brak_odpowiedzi'],
            'company_id' => ['nullable', 'exists:crm_companies,id'],
            'deal_id' => ['nullable', 'exists:crm_deals,id'],
        ]);

        $validated['user_id'] = $request->user()->id;

        $activity = CrmActivity::create($validated);
        $this->logCrmChange($request, 'activity', (int) $activity->id, 'created', [
            'subject' => $activity->subject,
            'type' => $activity->type,
        ]);

        return redirect()->route('crm.index')->with('status', 'Aktywność została dodana.');
    }

    public function getActivity(int $id): JsonResponse
    {
        return response()->json(CrmActivity::findOrFail($id));
    }

    public function updateActivity(Request $request, int $id): RedirectResponse
    {
        $activity = CrmActivity::findOrFail($id);

        $validated = $request->validate([
            'type' => ['required', 'in:telefon,email,spotkanie,notatka,sms,oferta,umowa,faktura,reklamacja'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'activity_date' => ['required', 'date'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'outcome' => ['nullable', 'in:pozytywny,neutralny,negatywny,brak_odpowiedzi'],
            'company_id' => ['nullable', 'exists:crm_companies,id'],
            'deal_id' => ['nullable', 'exists:crm_deals,id'],
        ]);

        $before = $activity->getOriginal();
        $activity->update($validated);
        $changes = [];
        foreach ($validated as $key => $value) {
            if (array_key_exists($key, $before) && $before[$key] != $value) {
                $changes[$key] = ['old' => $before[$key], 'new' => $value];
            }
        }
        if ($changes !== []) {
            $this->logCrmChange($request, 'activity', (int) $activity->id, 'updated', [
                'subject' => $activity->subject,
                'changes' => $changes,
            ]);
        }

        return redirect()->route('crm.index')->with('status', 'Aktywność została zaktualizowana.');
    }

    public function deleteActivity(int $id): RedirectResponse
    {
        $activity = CrmActivity::findOrFail($id);
        $subject = $activity->subject;
        $activity->delete();
        $this->logCrmChange($request, 'activity', $id, 'deleted', [
            'subject' => $subject,
        ]);

        return redirect()->route('crm.index')->with('status', 'Aktywność została usunięta.');
    }

    private function logCrmChange(Request $request, string $entityType, int $entityId, string $changeType, array $changeDetails = [], ?int $taskId = null): void
    {
        CrmTaskChange::create([
            'task_id' => $taskId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'user_id' => $request->user()?->id,
            'change_type' => $changeType,
            'change_details' => $changeDetails,
        ]);
    }

    private function syncSystemCompaniesToCrm(): void
    {
        $systemCompanies = Company::query()->orderBy('name')->get();

        foreach ($systemCompanies as $systemCompany) {
            $nip = ! empty($systemCompany->nip) ? preg_replace('/\D+/', '', (string) $systemCompany->nip) : null;

            $crmCompany = CrmCompany::query()
                ->where(function ($query) use ($systemCompany, $nip): void {
                    $query->where('system_company_id', $systemCompany->id);

                    if ($nip) {
                        $query->orWhere('nip', $nip);
                    }

                    $query->orWhere('name', (string) $systemCompany->name);
                })
                ->first();

            if (! $crmCompany) {
                CrmCompany::create([
                    'name' => (string) $systemCompany->name,
                    'short_name' => (string) ($systemCompany->short_name ?: mb_substr((string) $systemCompany->name, 0, 12)),
                    'nip' => $nip,
                    'email' => $systemCompany->email,
                    'phone' => $systemCompany->phone,
                    'address' => $systemCompany->street,
                    'city' => $systemCompany->city,
                    'postal_code' => $systemCompany->postal_code,
                    'country' => 'Polska',
                    'type' => 'klient',
                    'status' => 'aktywny',
                    'notes' => $systemCompany->description,
                    'source' => 'system',
                    'system_company_id' => $systemCompany->id,
                ]);

                continue;
            }

            $updates = [];

            if (! $crmCompany->system_company_id) {
                $updates['system_company_id'] = $systemCompany->id;
            }

            if (blank($crmCompany->nip) && $nip) {
                $updates['nip'] = $nip;
            }

            if (blank($crmCompany->email) && ! empty($systemCompany->email)) {
                $updates['email'] = $systemCompany->email;
            }

            if (blank($crmCompany->phone) && ! empty($systemCompany->phone)) {
                $updates['phone'] = $systemCompany->phone;
            }

            if (blank($crmCompany->address) && ! empty($systemCompany->street)) {
                $updates['address'] = $systemCompany->street;
            }

            if (blank($crmCompany->city) && ! empty($systemCompany->city)) {
                $updates['city'] = $systemCompany->city;
            }

            if (blank($crmCompany->postal_code) && ! empty($systemCompany->postal_code)) {
                $updates['postal_code'] = $systemCompany->postal_code;
            }

            if (blank($crmCompany->notes) && ! empty($systemCompany->description)) {
                $updates['notes'] = $systemCompany->description;
            }

            if (blank($crmCompany->source)) {
                $updates['source'] = 'system';
            }

            if ($updates !== []) {
                $crmCompany->update($updates);
            }
        }
    }

    private function findOrCreateSystemCompanyFromCrmData(array $crmData): Company
    {
        $name = Company::normalizeLegalForm(trim((string) ($crmData['name'] ?? '')));
        $shortName = trim((string) ($crmData['short_name'] ?? ''));
        $nip = ! empty($crmData['nip']) ? preg_replace('/\D+/', '', (string) $crmData['nip']) : null;

        $existing = Company::query()
            ->when($nip, function ($query) use ($nip): void {
                $query->where('nip', $nip);
            }, function ($query) use ($name): void {
                $query->where('name', $name);
            })
            ->first();

        if ($existing) {
            return $existing;
        }

        return Company::create([
            'name' => $name,
            'short_name' => $shortName !== '' ? $shortName : mb_substr($name, 0, 12),
            'nip' => $nip,
            'city' => $crmData['city'] ?? null,
            'street' => $crmData['address'] ?? null,
            'postal_code' => $crmData['postal_code'] ?? null,
            'description' => $crmData['notes'] ?? null,
            'phone' => $crmData['phone'] ?? null,
            'email' => $crmData['email'] ?? null,
            'auditor_id' => $crmData['owner_id'] ?? null,
        ]);
    }
}
