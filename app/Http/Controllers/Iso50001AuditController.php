<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Iso50001Audit;
use App\Models\Iso50001QuestionnaireQuestion;
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
                'questionnaire_answers' => [],
                'questionnaire_completed' => false,
            ]);

            return redirect()->route('iso50001.questionnaire', $audit)
                ->with('status', 'Nowy audyt ISO 50001 został utworzony. Wypełnij kwestionariusz wstępny.');
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
            'title' => trim((string) $validated['title']),
            'company_id' => (int) $companyValidation['company_id'],
            'created_by_user_id' => (int) $validated['client_user_id'],
            'status' => 'draft',
            'current_step' => 1,
            'answers' => [],
            'questionnaire_answers' => [],
            'questionnaire_completed' => false,
        ]);

        return redirect()->route('iso50001.review', [
            'isoAudit' => $audit,
        ])->with('status', 'Utworzono audyt ISO 50001 dla klienta. Klient może go teraz uzupełniać krok po kroku.');
    }

    public function showStep(Request $request, Iso50001Audit $isoAudit, int $step): View
    {
        $this->authorizeAuditAccess($request->user(), $isoAudit);

        // Client must complete the questionnaire before accessing any step
        if ($request->user()->isClient() && ! $isoAudit->questionnaire_completed) {
            return redirect()->route('iso50001.questionnaire', $isoAudit)
                ->with('status', 'Najpierw wypełnij kwestionariusz wstępny.');
        }

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

        if (! $isoAudit->questionnaire_completed) {
            return redirect()->route('iso50001.questionnaire', $isoAudit)
                ->with('status', 'Najpierw wypełnij kwestionariusz wstępny.');
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
     * Show the pre-audit questionnaire for a client.
     */
    public function showQuestionnaire(Request $request, Iso50001Audit $isoAudit): View
    {
        $user = $request->user();
        $this->authorizeAuditAccess($user, $isoAudit);

        $questions = Iso50001QuestionnaireQuestion::query()
            ->active()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('block_key');

        $answers = (array) ($isoAudit->questionnaire_answers ?? []);

        $isoAudit->load(['company', 'creator']);
        $company = $isoAudit->company;
        $creator = $isoAudit->creator;

        $prefilled = [];
        if ($company) {
            if ($company->name) $prefilled['A1'] = $company->name;
            if ($company->nip)  $prefilled['A2'] = $company->nip;
            $addr = collect([$company->street, trim($company->postal_code . ' ' . $company->city)])->filter()->implode(', ');
            if ($addr) $prefilled['A3'] = $addr;
            if ($company->description) $prefilled['A7'] = $company->description;
        }
        if ($creator) {
            $contact = collect([$creator->name, $creator->position, $creator->email, $creator->phone])->filter()->implode(', ');
            if ($contact) $prefilled['A4'] = $contact;
        }

        return view('iso50001.questionnaire', [
            'audit'       => $isoAudit,
            'questions'   => $questions,
            'blockLabels' => Iso50001QuestionnaireQuestion::$blockLabels,
            'answers'     => $answers,
            'prefilled'   => $prefilled,
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    /**
     * Save questionnaire answers and redirect to step 1.
     */
    public function saveQuestionnaire(Request $request, Iso50001Audit $isoAudit): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeAuditAccess($user, $isoAudit);

        if (! $user->isClient() || (int) $isoAudit->created_by_user_id !== (int) $user->id) {
            abort(403);
        }

        $answers = $request->input('answers', []);

        if (! is_array($answers)) {
            $answers = [];
        }

        // Sanitize: only string values, max 2000 chars each
        $sanitized = [];
        foreach ($answers as $key => $value) {
            $sanitized[(string) $key] = mb_substr((string) $value, 0, 2000);
        }

        // Save as draft (no completion flag)
        if ($request->boolean('save_as_draft')) {
            $isoAudit->update(['questionnaire_answers' => $sanitized]);
            return redirect()->route('iso50001.questionnaire', $isoAudit)
                ->with('draft_saved', true);
        }

        // Require at least a few answers before marking as completed
        $nonEmpty = array_filter($sanitized, fn($v) => trim($v) !== '');
        if (count($nonEmpty) < 3) {
            return redirect()->route('iso50001.questionnaire', $isoAudit)
                ->withErrors(['Wypełnij co najmniej kilka pól przed zapisaniem kwestionariusza.']);
        }

        $isoAudit->update([
            'questionnaire_answers' => $sanitized,
            'questionnaire_completed' => true,
            'status' => in_array($isoAudit->status, ['draft'], true) ? 'in_progress' : $isoAudit->status,
        ]);

        return redirect()->route('iso50001.step', ['isoAudit' => $isoAudit, 'step' => 1])
            ->with('status', 'Kwestionariusz został zapisany. Teraz wypełnij formularz audytu krok po kroku.');
    }

    /**
     * @return array<string, string>
     */
    private function statusOptions(): array
    {
        return Iso50001Audit::statusLabels();
    }
}
