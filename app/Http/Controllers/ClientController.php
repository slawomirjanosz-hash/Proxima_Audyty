<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Models\AuditType;
use App\Models\ClientChatMessage;
use App\Models\ClientInquiry;
use App\Models\Company;
use App\Models\EnergyAudit;
use App\Models\Offer;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $auditTypeOptions = $this->buildAuditTypeOptions();
        $contactEmail = (string) SystemSetting::get('company_contact_email', '');

        if (! $user->isClient()) {
            $inquiries = ClientInquiry::with(['user', 'company', 'auditType'])->latest()->get();
            $auditTypesByCategory = $this->groupAuditTypesByCategory($auditTypeOptions);
            return view('client.index', [
                'auditTypeOptions'     => $auditTypeOptions,
                'auditTypesByCategory' => $auditTypesByCategory,
                'inquiries'            => $inquiries,
                'contactEmail'         => $contactEmail,
                'previewMode'          => true,
                'company'              => null,
                'chatMessages'         => collect(),
                'companyAudits'        => collect(),
            ]);
        }

        $inquiries = ClientInquiry::with(['auditType', 'offer'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $company = null;
        if ($user->company_id) {
            $company = Company::find($user->company_id);
        }
        if (! $company) {
            $company = Company::where('client_id', $user->id)->first();
        }

        $chatMessages  = collect();
        $companyAudits = collect();
        if ($company) {
            $chatMessages = ClientChatMessage::where('company_id', $company->id)
                ->with('user')
                ->oldest()
                ->get();
            // Mark admin messages as read when client views the page
            ClientChatMessage::where('company_id', $company->id)
                ->where('is_from_admin', true)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            $companyAudits = EnergyAudit::where('company_id', $company->id)
                ->with('auditType')
                ->latest()
                ->get();
        }

        $auditTypesByCategory = $this->groupAuditTypesByCategory($auditTypeOptions);
        return view('client.index', [
            'auditTypeOptions'     => $auditTypeOptions,
            'auditTypesByCategory' => $auditTypesByCategory,
            'inquiries'            => $inquiries,
            'contactEmail'         => $contactEmail,
            'company'              => $company,
            'previewMode'          => false,
            'chatMessages'         => $chatMessages,
            'companyAudits'        => $companyAudits,
        ]);
    }

    public function storeInquiry(Request $request): RedirectResponse
    {
        $user = $request->user();
        $auditTypeOptions = $this->buildAuditTypeOptions()->keyBy('value');

        $validated = $request->validate([
            'audit_type' => ['required', 'string', Rule::in($auditTypeOptions->keys()->all())],
            'message'       => ['nullable', 'string', 'max:2000'],
        ]);

        $selectedType = $auditTypeOptions->get($validated['audit_type']);
        $auditTypeId = $selectedType['id'] ?? null;
        $auditTypeName = (string) ($selectedType['name'] ?? '');

        $companyId = $user->company_id;
        if (! $companyId) {
            $comp = Company::where('client_id', $user->id)->first();
            $companyId = $comp?->id;
        }

        ClientInquiry::create([
            'user_id'         => $user->id,
            'company_id'      => $companyId,
            'audit_type_id'   => $auditTypeId,
            'audit_type_name' => $auditTypeName,
            'message'         => $validated['message'] ?? null,
            'status'          => 'new',
        ]);

        return back()->with('inquiry_status', 'Zapytanie zostało wysłane. Skontaktujemy się z Tobą wkrótce.');
    }

    private function buildAuditTypeOptions(): \Illuminate\Support\Collection
    {
        return collect([
            // ── Audyty energetyczne ──────────────────────────────
            ['value' => 'agent:general',                  'id' => null, 'name' => 'Ogólnie',                'category' => 'energy'],
            ['value' => 'agent:compressor_room',          'id' => null, 'name' => 'Sprężarkownia',          'category' => 'energy'],
            ['value' => 'agent:boiler_room',              'id' => null, 'name' => 'Kotłownia',              'category' => 'energy'],
            ['value' => 'agent:drying_room',              'id' => null, 'name' => 'Suszarnia',              'category' => 'energy'],
            ['value' => 'agent:buildings',                'id' => null, 'name' => 'Budynki',                'category' => 'energy'],
            ['value' => 'agent:technological_processes',  'id' => null, 'name' => 'Procesy technologiczne', 'category' => 'energy'],
            // ── ISO 50001 ────────────────────────────────────────
            ['value' => 'agent:iso50001',                 'id' => null, 'name' => 'ISO 50001',              'category' => 'iso'],
            // ── Białe certyfikaty ────────────────────────────────
            ['value' => 'agent:bc_general',                  'id' => null, 'name' => 'Ogólnie',                'category' => 'white_cert'],
            ['value' => 'agent:bc_compressor_room',          'id' => null, 'name' => 'Sprężarkownia',          'category' => 'white_cert'],
            ['value' => 'agent:bc_boiler_room',              'id' => null, 'name' => 'Kotłownia',              'category' => 'white_cert'],
            ['value' => 'agent:bc_drying_room',              'id' => null, 'name' => 'Suszarnia',              'category' => 'white_cert'],
            ['value' => 'agent:bc_buildings',                'id' => null, 'name' => 'Budynki',                'category' => 'white_cert'],
            ['value' => 'agent:bc_technological_processes',  'id' => null, 'name' => 'Procesy technologiczne', 'category' => 'white_cert'],
        ]);
    }

    private function groupAuditTypesByCategory(\Illuminate\Support\Collection $options): array
    {
        return [
            'energy'     => $options->where('category', 'energy')->values()->all(),
            'iso'        => $options->where('category', 'iso')->values()->all(),
            'white_cert' => $options->where('category', 'white_cert')->values()->all(),
        ];
    }

    public function sendChatAjax(Request $request): JsonResponse
    {
        $user = $request->user();
        $request->validate(['message' => 'required|string|max:2000']);

        $companyId = $user->company_id;
        if (! $companyId) {
            $comp = Company::where('client_id', $user->id)->first();
            $companyId = $comp?->id;
        }

        if (! $companyId) {
            return response()->json(['error' => 'Brak przypisanej firmy.'], 422);
        }

        $msg = ClientChatMessage::create([
            'company_id'    => $companyId,
            'user_id'       => $user->id,
            'message'       => $request->input('message'),
            'is_from_admin' => false,
        ]);

        $msg->load('user');

        return response()->json([
            'id'            => $msg->id,
            'message'       => $msg->message,
            'is_from_admin' => false,
            'user_name'     => $msg->user?->name ?? '—',
            'created_at'    => $msg->created_at->format('d.m.Y H:i'),
        ]);
    }

    public function pollChat(Request $request): JsonResponse
    {
        $user    = $request->user();
        $afterId = (int) $request->query('after', 0);

        $companyId = $user->company_id;
        if (! $companyId) {
            $comp = Company::where('client_id', $user->id)->first();
            $companyId = $comp?->id;
        }

        if (! $companyId) {
            return response()->json(['messages' => []]);
        }

        $messages = ClientChatMessage::where('company_id', $companyId)
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

        // Mark newly fetched admin messages as read
        if ($messages->isNotEmpty()) {
            ClientChatMessage::where('company_id', $companyId)
                ->where('id', '>', $afterId)
                ->where('is_from_admin', true)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return response()->json(['messages' => $messages]);
    }

    public function sendChat(Request $request): RedirectResponse
    {
        $user = $request->user();
        $request->validate(['message' => 'required|string|max:2000']);

        $companyId = $user->company_id;
        if (! $companyId) {
            $comp = Company::where('client_id', $user->id)->first();
            $companyId = $comp?->id;
        }

        if (! $companyId) {
            return back()->with('chat_error', 'Nie znaleziono przypisanej firmy — skontaktuj się z administratorem.');
        }

        ClientChatMessage::create([
            'company_id'    => $companyId,
            'user_id'       => $user->id,
            'message'       => $request->input('message'),
            'is_from_admin' => false,
        ]);

        return back()->with('chat_status', 'Wiadomość wysłana.');
    }

    public function acceptOffer(Request $request, ClientInquiry $inquiry): RedirectResponse
    {
        $user = $request->user();
        abort_unless((int) $inquiry->user_id === $user->id, 403);
        abort_unless($inquiry->offer_id !== null, 422, 'Brak oferty do zaakceptowania.');
        abort_unless($inquiry->status === 'in_review', 422, 'Oferta nie jest w statusie do oceny.');

        $inquiry->update(['status' => 'offer_accepted']);

        return back()->with('inquiry_status', 'Zaakceptowałeś ofertę. Nasz zespół wkrótce przydzieli Ci odpowiedni audyt.');
    }

    public function downloadOfferPdf(Offer $offer): mixed
    {
        $user = auth()->user();

        // Verify the client owns an inquiry linked to this offer
        $owns = ClientInquiry::where('offer_id', $offer->id)
            ->where('user_id', $user->id)
            ->exists();

        abort_unless($owns, 403);

        $pdf      = \Barryvdh\DomPDF\Facade\Pdf::loadView('offers.print', compact('offer'));
        $filename = 'oferta-' . ($offer->offer_number ?: $offer->id) . '.pdf';

        return $pdf->download($filename);
    }

    public function startAuditAi(EnergyAudit $audit): \Illuminate\Http\RedirectResponse
    {
        $user    = auth()->user();
        $company = Company::find($audit->company_id);

        abort_unless($company !== null, 404);

        // Verify client belongs to this company
        $clientCompanyId = $user->company_id
            ?? Company::where('client_id', $user->id)->value('id');
        abort_unless((int) $audit->company_id === (int) $clientCompanyId, 403);

        $agentType = $audit->agent_type ?: 'general';

        // Check if conversation already exists
        $existing = AiConversation::where([
            'user_id'      => $user->id,
            'context_type' => $agentType,
            'context_id'   => $audit->id,
        ])->first();

        if ($existing) {
            return redirect()->route('client.audit.work', ['audit' => $audit->id, 'conversation' => $existing->id]);
        }

        // Create new conversation with initial AI greeting
        try {
            $conversation = app(\App\Services\AiAgentService::class)->startConversation(
                userId:      $user->id,
                contextType: $agentType,
                contextId:   $audit->id,
                title:       $audit->title,
            );
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Nie udało się uruchomić rozmowy z asystentem. Sprawdź połączenie i spróbuj ponownie.');
        }

        return redirect()->route('client.audit.work', ['audit' => $audit->id, 'conversation' => $conversation->id]);
    }

    public function auditWork(EnergyAudit $audit, AiConversation $conversation): \Illuminate\View\View
    {
        $user = auth()->user();

        // Verify client owns this audit
        $clientCompanyId = $user->company_id
            ?? Company::where('client_id', $user->id)->value('id');
        abort_unless((int) $audit->company_id === (int) $clientCompanyId, 403);

        // Verify conversation belongs to user and this audit
        abort_unless((int) $conversation->user_id === $user->id, 403);
        abort_unless((int) $conversation->context_id === $audit->id, 403);

        $messages = $conversation->messages()->orderBy('id')->get();

        $agentLabels = [
            'general'                 => 'Ogólnie',
            'compressor_room'         => 'Sprężarkownia',
            'boiler_room'             => 'Kotłownia',
            'drying_room'             => 'Suszarnia',
            'buildings'               => 'Budynki',
            'technological_processes' => 'Procesy technologiczne',
            'iso50001'                => 'ISO 50001',
            'bc_general'              => 'Białe certyfikaty — Ogólnie',
            'bc_compressor_room'      => 'Białe certyfikaty — Sprężarkownia',
            'bc_boiler_room'          => 'Białe certyfikaty — Kotłownia',
            'bc_drying_room'          => 'Białe certyfikaty — Suszarnia',
            'bc_buildings'            => 'Białe certyfikaty — Budynki',
            'bc_technological_processes' => 'Białe certyfikaty — Procesy technologiczne',
        ];

        $agentLabel = $agentLabels[$conversation->context_type] ?? $conversation->context_type;

        return view('client.audit-work', compact('audit', 'conversation', 'messages', 'agentLabel'));
    }

    public function finishAuditAi(EnergyAudit $audit, AiConversation $conversation): RedirectResponse
    {
        $user = auth()->user();

        $clientCompanyId = $user->company_id
            ?? Company::where('client_id', $user->id)->value('id');
        abort_unless((int) $audit->company_id === (int) $clientCompanyId, 403);
        abort_unless((int) $conversation->user_id === $user->id, 403);
        abort_unless((int) $conversation->context_id === $audit->id, 403);

        try {
            $aiService = app(\App\Services\AiAgentService::class);
            $aiService->generateProtocol($conversation);
            $aiService->appendRecommendations($conversation->fresh());
        } catch (\Throwable $e) {
            report($e);
        }

        $audit->update(['status' => 'do_analizy']);

        return redirect()->route('strefa-klienta')
            ->with('status', 'Audyt zakończony. Dane zostały zapisane i przekazane do analizy.');
    }

    /**
     * Formularz ręcznej edycji danych protokołu przez klienta.
     */
    public function editAuditData(\App\Models\EnergyAudit $audit): \Illuminate\Contracts\View\View
    {
        $user = auth()->user();
        $clientCompanyId = $user->company_id
            ?? \App\Models\Company::where('client_id', $user->id)->value('id');
        abort_unless((int) $audit->company_id === (int) $clientCompanyId, 403);

        $conversation = \App\Models\AiConversation::where('context_id', $audit->id)
            ->latest()->first();
        abort_unless($conversation !== null, 404, 'Brak danych audytu do edycji.');

        return view('client.audit-edit', compact('audit', 'conversation'));
    }

    /**
     * Zapisuje ręcznie edytowane dane protokołu i uruchamia analizę AI.
     */
    public function updateAuditData(\App\Models\EnergyAudit $audit, \Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        $clientCompanyId = $user->company_id
            ?? \App\Models\Company::where('client_id', $user->id)->value('id');
        abort_unless((int) $audit->company_id === (int) $clientCompanyId, 403);

        $conversation = \App\Models\AiConversation::where('context_id', $audit->id)
            ->latest()->first();
        abort_unless($conversation !== null, 404);

        // Zbierz edytowane pola z formularza
        $fields = $request->input('fields', []);   // fields[sekcja_idx][pole_idx] = wartość
        $protocol = $conversation->protocol_data ?? [];

        foreach ($fields as $sekcjaIdx => $pola) {
            foreach ($pola as $poleIdx => $wartosc) {
                if (isset($protocol['sekcje'][$sekcjaIdx]['pola'][$poleIdx])) {
                    $protocol['sekcje'][$sekcjaIdx]['pola'][$poleIdx]['wartosc'] = strip_tags((string) $wartosc);
                }
            }
        }

        $conversation->update(['protocol_data' => $protocol]);

        // AI analizuje zaktualizowane dane
        try {
            app(\App\Services\AiAgentService::class)->appendRecommendations($conversation->fresh());
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('client.audit.edit', $audit)
            ->with('status', 'Dane zostały zapisane. Asystent AI przeanalizował dane i dodał rekomendacje na dole strony.');
    }
}
