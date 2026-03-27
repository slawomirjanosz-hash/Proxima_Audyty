<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Iso50001Audit;
use App\Models\Iso50001Template;
use App\Models\User;
use App\Enums\UserRole;
use App\Support\Iso50001TemplateDefinition;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class Iso50001AuditController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->isClient()) {
            return redirect()->route('strefa-klienta');
        }

        return redirect()->route('audits.settings');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isClient()) {
            $companyIds = $this->clientCompanyIds($user)->all();

            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'company_id' => ['required', Rule::in($companyIds)],
            ]);

            $audit = Iso50001Audit::create([
                'title' => $validated['title'],
                'company_id' => (int) $validated['company_id'],
                'created_by_user_id' => $user->id,
                'status' => 'draft',
                'current_step' => 1,
                'answers' => [],
            ]);

            return redirect()->route('iso50001.step', [
                'isoAudit' => $audit,
                'step' => 1,
            ])->with('status', 'Nowy audyt ISO 50001 został utworzony.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'client_user_id' => ['required', Rule::exists('users', 'id')->where('role', UserRole::Client->value)],
        ]);

        $clientUser = User::query()->findOrFail((int) $validated['client_user_id']);
        $clientCompanyIds = $this->clientCompanyIds($clientUser)->all();

        $companyValidation = $request->validate([
            'company_id' => ['required', Rule::in($clientCompanyIds)],
        ], [
            'company_id.in' => 'Wybrana firma nie jest przypisana do wskazanego klienta.',
        ]);

        $audit = Iso50001Audit::create([
            'title' => $validated['title'],
            'company_id' => (int) $companyValidation['company_id'],
            'created_by_user_id' => (int) $validated['client_user_id'],
            'status' => 'draft',
            'current_step' => 1,
            'answers' => [],
        ]);

        return redirect()->route('iso50001.review', [
            'isoAudit' => $audit,
        ])->with('status', 'Utworzono audyt ISO 50001 dla klienta. Klient może go teraz uzupełniać krok po kroku.');
    }

    public function showStep(Request $request, Iso50001Audit $isoAudit, int $step): View
    {
        $this->authorizeAuditAccess($request->user(), $isoAudit);

        $steps = $this->stepDefinitions();
        $totalSteps = count($steps);

        if ($step < 1 || $step > $totalSteps) {
            abort(404);
        }

        $stepDefinition = $steps[$step - 1];
        $answers = (array) ($isoAudit->answers[$stepDefinition['key']] ?? []);

        return view('iso50001.step', [
            'audit' => $isoAudit->load(['company', 'creator', 'reviewer']),
            'step' => $step,
            'totalSteps' => $totalSteps,
            'steps' => $steps,
            'stepDefinition' => $stepDefinition,
            'answers' => $answers,
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function saveStep(Request $request, Iso50001Audit $isoAudit, int $step): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeAuditAccess($user, $isoAudit);

        if (! $user->isClient() || (int) $isoAudit->created_by_user_id !== (int) $user->id) {
            abort(403);
        }

        if ($isoAudit->status === 'approved') {
            return back()->with('status', 'Audyt został zatwierdzony i nie można go już edytować.');
        }

        $steps = $this->stepDefinitions();
        $totalSteps = count($steps);

        if ($step < 1 || $step > $totalSteps) {
            abort(404);
        }

        $stepDefinition = $steps[$step - 1];
        $actionValidation = $request->validate([
            'action' => ['required', Rule::in(['save', 'previous', 'next'])],
        ]);
        $action = (string) $actionValidation['action'];

        $rules = [];

        foreach ($stepDefinition['fields'] as $field) {
            $fieldRules = ['nullable', 'string', 'max:4000'];

            if ($field['type'] === 'number') {
                $fieldRules = ['nullable', 'numeric'];
            }

            if ($field['type'] === 'date') {
                $fieldRules = ['nullable', 'date'];
            }

            if ($action === 'next' && ! empty($field['required'])) {
                array_unshift($fieldRules, 'required');
            }

            $rules[$field['name']] = $fieldRules;
        }

        $validated = $request->validate($rules);

        $stepAnswers = Arr::only($validated, array_column($stepDefinition['fields'], 'name'));

        $allAnswers = is_array($isoAudit->answers) ? $isoAudit->answers : [];
        $allAnswers[$stepDefinition['key']] = $stepAnswers;

        $nextStep = match ($action) {
            'previous' => max(1, $step - 1),
            'next' => min($totalSteps, $step + 1),
            default => $step,
        };

        $isoAudit->update([
            'answers' => $allAnswers,
            'current_step' => max((int) $isoAudit->current_step, $nextStep),
            'status' => in_array($isoAudit->status, ['draft', 'changes_required'], true) ? 'in_progress' : $isoAudit->status,
        ]);

        return redirect()->route('iso50001.step', [
            'isoAudit' => $isoAudit,
            'step' => $nextStep,
        ])->with('status', 'Krok audytu został zapisany.');
    }

    public function submit(Request $request, Iso50001Audit $isoAudit): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeAuditAccess($user, $isoAudit);

        if (! $user->isClient() || (int) $isoAudit->created_by_user_id !== (int) $user->id) {
            abort(403);
        }

        if ($isoAudit->status === 'approved') {
            return back()->with('status', 'Ten audyt został już zatwierdzony.');
        }

        $isoAudit->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'current_step' => count($this->stepDefinitions()),
        ]);

        return redirect()->route('iso50001.index')
            ->with('status', 'Audyt ISO 50001 został przesłany do weryfikacji audytora.');
    }

    public function review(Request $request, Iso50001Audit $isoAudit): View
    {
        $user = $request->user();
        $this->authorizeAuditAccess($user, $isoAudit);

        $steps = $this->stepDefinitions();

        return view('iso50001.review', [
            'audit' => $isoAudit->load(['company', 'creator', 'reviewer']),
            'steps' => $steps,
            'statusOptions' => $this->statusOptions(),
            'canReview' => ! $user->isClient(),
        ]);
    }

    public function updateReview(Request $request, Iso50001Audit $isoAudit): RedirectResponse
    {
        $user = $request->user();

        if ($user->isClient()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['in_review', 'changes_required', 'approved'])],
            'reviewer_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $isoAudit->update([
            'status' => $validated['status'],
            'reviewer_notes' => (string) ($validated['reviewer_notes'] ?? ''),
            'reviewer_id' => $user->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('iso50001.review', $isoAudit)
            ->with('status', 'Status audytu ISO 50001 został zaktualizowany.');
    }

    private function authorizeAuditAccess(User $user, Iso50001Audit $isoAudit): void
    {
        if (! $user->isClient()) {
            return;
        }

        if ((int) $isoAudit->created_by_user_id !== (int) $user->id) {
            abort(403);
        }

        $companyIds = $this->clientCompanyIds($user);

        if (! $companyIds->contains((int) $isoAudit->company_id)) {
            abort(403);
        }
    }

    /**
     * @return Collection<int, int>
     */
    private function clientCompanyIds(User $user): Collection
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

    /**
     * @return array<int, array<string, mixed>>
     */
    private function stepDefinitions(): array
    {
        $template = Iso50001Template::query()->first();

        if (! $template) {
            return Iso50001TemplateDefinition::defaultSteps();
        }

        return Iso50001TemplateDefinition::normalizeSteps((array) $template->steps);
    }

    /**
     * @return array<string, string>
     */
    private function statusOptions(): array
    {
        return Iso50001Audit::statusLabels();
    }
}
