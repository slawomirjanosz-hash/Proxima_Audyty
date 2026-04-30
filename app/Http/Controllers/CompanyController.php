<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Mail\WelcomeClientMail;
use App\Models\AuditType;
use App\Models\ClientChatMessage;
use App\Models\ClientInquiry;
use App\Models\Company;
use App\Models\EnergyAudit;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function show(Company $company): View
    {
        $company->load(['client', 'auditor', 'energyAudits.auditType', 'energyAudits.auditor', 'assignedUsers']);

        $auditTypes = AuditType::orderBy('name')->get();
        $auditors   = User::whereIn('role', [UserRole::Admin->value, UserRole::Auditor->value])
            ->orderBy('name')
            ->get();

        $assignedIds = $company->assignedUsers->pluck('id')->toArray();
        $availableUsers = User::whereNotIn('role', [UserRole::Admin->value, UserRole::SuperAdmin->value])
            ->whereNotIn('id', $assignedIds)
            ->orderBy('name')
            ->get();

        $inquiries = ClientInquiry::where('company_id', $company->id)
            ->with(['user', 'offer'])
            ->latest()
            ->get();

        $chatMessages = ClientChatMessage::where('company_id', $company->id)
            ->with('user')
            ->oldest()
            ->get();

        // Mark unread client messages as read by admin
        ClientChatMessage::where('company_id', $company->id)
            ->where('is_from_admin', false)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $portfolioOffers = Offer::where('status', 'portfolio')
            ->orderBy('offer_title')
            ->get(['id', 'offer_number', 'offer_title', 'total_price']);

        $companyOffers = Offer::where('company_id', $company->id)
            ->whereIn('status', ['inprogress', 'sent', 'accepted', 'portfolio'])
            ->with('creator')
            ->latest()
            ->get();

        return view('firma.show', compact(
            'company', 'auditTypes', 'auditors', 'availableUsers',
            'inquiries', 'chatMessages', 'portfolioOffers', 'companyOffers'
        ));
    }

    public function storeAudit(Request $request, Company $company): RedirectResponse
    {
        $agentTypes = [
            'general', 'compressor_room', 'boiler_room', 'drying_room', 'buildings', 'technological_processes',
            'iso50001',
            'bc_general', 'bc_compressor_room', 'bc_boiler_room', 'bc_drying_room', 'bc_buildings', 'bc_technological_processes',
        ];

        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'audit_type_id' => ['required', 'exists:audit_types,id'],
            'auditor_id'    => ['nullable', 'exists:users,id'],
            'agent_type'    => ['required', 'string', 'in:' . implode(',', $agentTypes)],
        ]);

        $auditType = AuditType::findOrFail((int) $validated['audit_type_id']);

        EnergyAudit::create([
            'title'         => $validated['title'],
            'audit_type_id' => $auditType->id,
            'audit_type'    => $auditType->name,
            'agent_type'    => $validated['agent_type'],
            'company_id'    => $company->id,
            'auditor_id'    => $validated['auditor_id'] ?? null,
            'status'        => 'wysłany',
            'data_payload'  => [],
        ]);

        // Close any offer_accepted inquiries for this company — audit has been assigned
        \App\Models\ClientInquiry::where('company_id', $company->id)
            ->where('status', 'offer_accepted')
            ->update(['status' => 'closed']);

        return redirect()->route('firma.show', $company)
            ->with('status', 'Audyt został przydzielony firmie.');
    }

    public function storeClient(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8'],
        ]);

        $name = trim($validated['first_name'] . ' ' . $validated['last_name']);
        $shortName = mb_substr($validated['first_name'], 0, 3) . mb_substr($validated['last_name'], 0, 3);

        $plainPassword = $validated['password'];

        $user = User::create([
            'name'       => $name,
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'short_name' => $shortName,
            'email'      => $validated['email'],
            'password'   => $plainPassword,
            'role'       => UserRole::Client->value,
            'company_id' => $company->id,
        ]);

        // Link as primary client of the company
        $company->update(['client_id' => $user->id]);

        // Also assign as a company user so they appear in Użytkownicy firmy
        $company->assignedUsers()->syncWithoutDetaching([$user->id]);

        // Send welcome e-mail with login credentials
        $mailError = null;
        $mailerName = config('mail.default');
        try {
            Mail::to($user->email)->send(new WelcomeClientMail($user, $company, $plainPassword));
        } catch (\Throwable $e) {
            report($e);
            $mailError = $e->getMessage();
        }

        $statusMsg = 'Dane do logowania zostały utworzone i przypisane do firmy.';
        if ($mailError) {
            $statusMsg .= ' ⚠ Nie udało się wysłać e-maila: ' . $mailError;
        } elseif ($mailerName === 'log') {
            $statusMsg .= ' ⚠ UWAGA: MAIL_MAILER=log – e-mail zapisany do logów, NIE wysłany do ' . $user->email . '!';
        } else {
            $statusMsg .= ' ✉ E-mail wysłany na ' . $user->email . ' (mailer: ' . $mailerName . ').';
        }

        return redirect()->route('firma.show', $company)
            ->with('status', $statusMsg);
    }

    public function showAudit(Company $company, EnergyAudit $audit): View
    {
        abort_unless((int) $audit->company_id === $company->id, 404);

        $audit->load(['auditType.sections', 'auditor', 'company']);

        $conversation = \App\Models\AiConversation::where('context_id', $audit->id)
            ->latest()
            ->first();

        $chatMessages = ClientChatMessage::where('company_id', $company->id)
            ->with('user')
            ->oldest()
            ->get();

        return view('firma.audit', compact('company', 'audit', 'conversation', 'chatMessages'));
    }

    public function updateStatus(Request $request, Company $company, EnergyAudit $audit): RedirectResponse
    {
        abort_unless((int) $audit->company_id === $company->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(EnergyAudit::STATUSES))],
        ]);

        $doneStatuses = EnergyAudit::DONE_STATUSES;
        $completedAt  = in_array($validated['status'], $doneStatuses, true)
            ? ($audit->completed_at ?? now())
            : null;

        $audit->update([
            'status'       => $validated['status'],
            'completed_at' => $completedAt,
        ]);

        return redirect()->route('firma.audit', [$company, $audit])
            ->with('status', 'Status audytu zostal zmieniony na: ' . EnergyAudit::STATUSES[$validated['status']]);
    }

    public function destroyAudit(Company $company, EnergyAudit $audit): RedirectResponse
    {
        abort_unless((int) $audit->company_id === $company->id, 404);

        $audit->delete();

        return redirect()->route('firma.show', $company)
            ->with('status', 'Audyt został usunięty.');
    }

    public function report(Company $company, EnergyAudit $audit): View
    {
        abort_unless((int) $audit->company_id === $company->id, 404);

        $audit->load(['auditor', 'company']);

        $conversation = \App\Models\AiConversation::where('context_id', $audit->id)
            ->latest()
            ->first();

        $questionnaireQuestions = null;
        if ($audit->questionnaire_completed && !empty($audit->questionnaire_answers)) {
            $questionnaireQuestions = \App\Models\Iso50001QuestionnaireQuestion::active()
                ->orderBy('sort_order')
                ->get()
                ->groupBy('block_key');
        }

        return view('firma.report', compact('company', 'audit', 'conversation', 'questionnaireQuestions'));
    }

    public function addUser(Request $request, Company $company): RedirectResponse
    {
        // Attach existing user by id
        if ($request->filled('user_id')) {
            $user = User::findOrFail($request->integer('user_id'));
            $company->assignedUsers()->syncWithoutDetaching([$user->id]);

            return redirect()->route('firma.show', $company)
                ->with('status', 'Użytkownik ' . $user->name . ' otrzymał dostęp do firmy.');
        }

        // Create new user
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8'],
            'role'       => ['required', 'in:client,auditor'],
        ]);

        $name          = trim($validated['first_name'] . ' ' . $validated['last_name']);
        $plainPassword = $validated['password'];

        $user = User::create([
            'name'       => $name,
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'short_name' => mb_substr($validated['first_name'], 0, 3) . mb_substr($validated['last_name'], 0, 3),
            'email'      => $validated['email'],
            'password'   => $plainPassword,
            'role'       => $validated['role'],
            'company_id' => $company->id,
        ]);

        $company->assignedUsers()->syncWithoutDetaching([$user->id]);

        $mailError  = null;
        $mailerName = config('mail.default');
        try {
            Mail::to($user->email)->send(new WelcomeClientMail($user, $company, $plainPassword));
        } catch (\Throwable $e) {
            report($e);
            $mailError = $e->getMessage();
        }

        $statusMsg = 'Nowy użytkownik ' . $user->name . ' został utworzony i przypisany do firmy.';
        if ($mailError) {
            $statusMsg .= ' ⚠ Nie udało się wysłać e-maila: ' . $mailError;
        } elseif ($mailerName === 'log') {
            $statusMsg .= ' ⚠ UWAGA: MAIL_MAILER=log – e-mail zapisany do logów, NIE wysłany do ' . $user->email . '!';
        } else {
            $statusMsg .= ' ✉ E-mail wysłany na ' . $user->email . ' (mailer: ' . $mailerName . ').';
        }

        return redirect()->route('firma.show', $company)
            ->with('status', $statusMsg);
    }

    public function removeUser(Company $company, User $user): RedirectResponse
    {
        $company->assignedUsers()->detach($user->id);

        return redirect()->route('firma.show', $company)
            ->with('status', 'Dostęp użytkownika ' . $user->name . ' do firmy został usunięty.');
    }

    public function resendMail(Company $company, User $user): RedirectResponse
    {
        // Generate new temporary password
        $chars         = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789!@#';
        $plainPassword = substr(str_shuffle(str_repeat($chars, 3)), 0, 10);

        $user->update(['password' => $plainPassword]);

        $mailerName = config('mail.default');
        try {
            Mail::to($user->email)->send(new WelcomeClientMail($user, $company, $plainPassword));
            if ($mailerName === 'log') {
                $msg = '⚠ UWAGA: MAIL_MAILER=log – e-mail zapisany do logów, NIE wysłany do ' . $user->email . '. Zmień zmienną MAIL_MAILER na smtp.';
            } else {
                $msg = '✉ E-mail z danymi logowania wysłany do ' . $user->email . ' (mailer: ' . $mailerName . ').';
            }
        } catch (\Throwable $e) {
            $msg = 'Nie udało się wysłać e-maila (' . $e->getMessage() . '). Hasło zostało zresetowane.';
        }

        return redirect()->route('firma.show', $company)->with('status', $msg);
    }

    // ── Inquiry management ────────────────────────────────────────────────

    public function acceptInquiry(ClientInquiry $inquiry): RedirectResponse
    {
        $inquiry->update(['status' => 'accepted']);
        return redirect()->route('firma.show', $inquiry->company_id)
            ->with('status', 'Zapytanie zostało przyjęte.');
    }

    public function rejectInquiry(ClientInquiry $inquiry): RedirectResponse
    {
        $inquiry->update(['status' => 'rejected']);
        return redirect()->route('firma.show', $inquiry->company_id)
            ->with('status', 'Zapytanie zostało odrzucone.');
    }

    // ── Chat ──────────────────────────────────────────────────────────────

    public function sendChatMessage(Request $request, Company $company): RedirectResponse
    {
        $request->validate(['message' => 'required|string|max:2000']);

        ClientChatMessage::create([
            'company_id'    => $company->id,
            'user_id'       => auth()->id(),
            'message'       => $request->input('message'),
            'is_from_admin' => true,
        ]);

        return redirect()->route('firma.show', $company)
            ->with('status', 'Wiadomość wysłana do klienta.');
    }

    public function sendChatMessageAjax(Request $request, Company $company): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $msg = ClientChatMessage::create([
            'company_id'    => $company->id,
            'user_id'       => auth()->id(),
            'message'       => $request->input('message'),
            'is_from_admin' => true,
        ]);

        $msg->load('user');

        return response()->json([
            'id'            => $msg->id,
            'message'       => $msg->message,
            'is_from_admin' => true,
            'user_name'     => $msg->user?->name ?? '—',
            'created_at'    => $msg->created_at->format('d.m.Y H:i'),
        ]);
    }

    public function pollChat(Request $request, Company $company): JsonResponse
    {
        $afterId = (int) $request->query('after', 0);

        $messages = ClientChatMessage::where('company_id', $company->id)
            ->where('id', '>', $afterId)
            ->with('user')
            ->oldest()
            ->get()
            ->map(fn($m) => [
                'id'            => $m->id,
                'message'       => $m->message,
                'is_from_admin' => (bool) $m->is_from_admin,
                'user_name'     => $m->user?->name ?? '—',
                'created_at'    => $m->created_at->format('d.m.Y H:i'),
            ]);

        // Mark newly fetched client messages as read
        if ($messages->isNotEmpty()) {
            ClientChatMessage::where('company_id', $company->id)
                ->where('id', '>', $afterId)
                ->where('is_from_admin', false)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return response()->json(['messages' => $messages]);
    }
}
