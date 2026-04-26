<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Models\SystemSetting;
use App\Services\AiAgentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AiAgentController extends Controller
{
    public function __construct(private AiAgentService $agent) {}

    /**
     * Lista konwersacji zalogowanego użytkownika.
     */
    public function index(): View
    {
        $conversations = AiConversation::where('user_id', auth()->id())
            ->active()
            ->latest()
            ->get();

        return view('ai.index', compact('conversations'));
    }

    /**
     * Formularz / strona nowej rozmowy.
     */
    public function create(Request $request): View
    {
        $contextType = $request->query('type', 'general');
        $contextId   = $request->query('context_id');

        return view('ai.create', compact('contextType', 'contextId'));
    }

    /**
     * Startuje nową konwersację i przekierowuje do czatu.
     */
    public function store(Request $request)
    {
        $request->validate([
            'context_type' => ['nullable', 'string', 'in:general,energy_audit,iso50001,offer,compressor_room,boiler_room,drying_room,buildings,technological_processes,bc_general,bc_compressor_room,bc_boiler_room,bc_drying_room,bc_buildings,bc_technological_processes'],
            'context_id'   => ['nullable', 'integer'],
        ]);

        try {
            $conversation = $this->agent->startConversation(
                userId: auth()->id(),
                contextType: $request->input('context_type', 'general'),
                contextId: $request->input('context_id'),
            );
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Nie udało się uruchomić rozmowy. Spróbuj ponownie.');
        }

        return redirect()->route('ai.show', $conversation);
    }

    /**
     * Widok czatu dla konkretnej konwersacji.
     */
    public function show(AiConversation $aiConversation): View
    {
        abort_unless($aiConversation->user_id === auth()->id(), 403);

        $messages  = $aiConversation->messages()->orderBy('created_at')->get();
        $suggested = $this->agent->getSuggestedMessages($aiConversation->context_type ?? 'general');

        return view('ai.show', [
            'conversation' => $aiConversation,
            'messages'     => $messages,
            'suggested'    => $suggested,
        ]);
    }

    /**
     * Wysłanie wiadomości — AJAX endpoint.
     */
    public function sendMessage(Request $request, AiConversation $aiConversation): JsonResponse
    {
        abort_unless($aiConversation->user_id === auth()->id(), 403);

        $request->validate([
            'message' => ['required', 'string', 'max:4000'],
        ]);

        try {
            $response = $this->agent->chat($aiConversation, $request->input('message'));

            return response()->json([
                'success'  => true,
                'response' => $response,
            ]);
        } catch (\Throwable $e) {
            \Log::error('AiAgentController::sendMessage failed', [
                'exception' => $e->getMessage(),
                'class'     => get_class($e),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Błąd połączenia z asystentem: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Analiza załączonego pliku / zdjęcia tabliczki znamionowej przez AI.
     */
    public function analyzeFile(Request $request, AiConversation $aiConversation): JsonResponse
    {
        abort_unless($aiConversation->user_id === auth()->id(), 403);

        $request->validate([
            'file'    => ['required', 'file', 'max:10240', 'mimes:jpeg,jpg,png,gif,webp,pdf,txt,csv'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $response = $this->agent->analyzeFileContent(
                $aiConversation,
                $request->file('file'),
                $request->input('message', '')
            );

            return response()->json(['success' => true, 'response' => $response]);
        } catch (\Throwable $e) {
            \Log::error('AiAgentController::analyzeFile failed', [
                'exception' => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Błąd analizy pliku: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Analiza danych audytu — zwraca analizę AI jako JSON.
     */
    public function analyzeAudit(Request $request): JsonResponse
    {
        $request->validate([
            'audit_data'  => ['required', 'array'],
            'audit_type'  => ['required', 'string', 'in:energy_audit,iso50001'],
        ]);

        try {
            $analysis = $this->agent->analyzeAuditData(
                $request->input('audit_data'),
                $request->input('audit_type')
            );

            return response()->json([
                'success'  => true,
                'analysis' => $analysis,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'error'   => 'Błąd analizy AI. Spróbuj ponownie.',
            ], 500);
        }
    }

    /**
     * Archiwizacja konwersacji.
     */
    public function destroy(AiConversation $aiConversation)
    {
        abort_unless($aiConversation->user_id === auth()->id(), 403);

        $aiConversation->update(['status' => 'archived']);

        return redirect()->route('ai.index')->with('success', 'Rozmowa zarchiwizowana.');
    }

    /**
     * Trwałe usunięcie konwersacji wraz z wiadomościami.
     */
    public function forceDelete(AiConversation $aiConversation)
    {
        abort_unless($aiConversation->user_id === auth()->id(), 403);

        $aiConversation->messages()->delete();
        $aiConversation->delete();

        return redirect()->route('ai.index')->with('success', 'Rozmowa została usunięta.');
    }

    /**
     * Generuje protokół z rozmowy (AI wyciąga ustrukturyzowane dane).
     */
    private function canAccessConversation(AiConversation $aiConversation): bool
    {
        $user = auth()->user();
        return $aiConversation->user_id === $user->id
            || $user->isAdmin()
            || $user->isSuperAdmin()
            || $user->isAuditor();
    }

    public function generateProtocol(AiConversation $aiConversation)
    {
        abort_unless($this->canAccessConversation($aiConversation), 403);

        try {
            $this->agent->generateProtocol($aiConversation);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('ai.show', $aiConversation)
                ->with('error', 'Błąd generowania protokołu: ' . $e->getMessage());
        }

        return redirect()->route('ai.protocol', $aiConversation)
            ->with('success', 'Protokół wygenerowany pomyślnie.');
    }

    public function generateRecommendations(AiConversation $aiConversation)
    {
        abort_unless($this->canAccessConversation($aiConversation), 403);
        abort_unless(!empty($aiConversation->protocol_data), 422);

        try {
            $this->agent->appendRecommendations($aiConversation);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Błąd generowania rekomendacji: ' . $e->getMessage());
        }

        return back()->with('success', 'Rekomendacje zostały wygenerowane.');
    }

    /**
     * Widok protokołu z danymi w tabeli.
     */
    public function protocol(AiConversation $aiConversation): View
    {
        abort_unless($this->canAccessConversation($aiConversation), 403);

        return view('ai.protocol', [
            'conversation' => $aiConversation,
            'protocol'     => $aiConversation->protocol_data ?? [],
        ]);
    }

    /**
     * Pobierz protokół jako PDF.
     */
    public function downloadPdf(AiConversation $aiConversation)
    {
        abort_unless($this->canAccessConversation($aiConversation), 403);

        if (empty($aiConversation->protocol_data)) {
            return redirect()->route('ai.show', $aiConversation)
                ->with('error', 'Najpierw wygeneruj protokół.');
        }

        $context = $aiConversation->contextModel();
        $company = null;
        if ($context && method_exists($context, 'company')) {
            $company = $context->company;
        }

        $pdf = Pdf::loadView('ai.protocol-pdf', [
            'conversation' => $aiConversation,
            'protocol'     => $aiConversation->protocol_data,
            'company'      => $company,
        ])->setPaper('a4');

        $filename = 'protokol-' . str($aiConversation->title)->slug() . '-' . $aiConversation->id . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Podgląd protokołu — strona z PDF.js renderującym dokument.
     */
    public function previewPdf(AiConversation $aiConversation)
    {
        abort_unless($this->canAccessConversation($aiConversation), 403);

        if (empty($aiConversation->protocol_data)) {
            return redirect()->route('ai.protocol', $aiConversation)
                ->with('error', 'Najpierw wygeneruj protokół.');
        }

        return view('ai.protocol-viewer', [
            'conversation' => $aiConversation,
            'pdfUrl'       => route('ai.protocol.pdf', $aiConversation),
        ]);
    }

    /**
     * Zapisuje niestandardowy prompt (trening) agenta AI w ustawieniach systemowych.
     */
    public function saveAgentTraining(Request $request, string $agentType)
    {
        $allowed = ['general', 'compressor_room', 'boiler_room', 'drying_room', 'buildings', 'technological_processes', 'iso50001', 'bc_general', 'bc_compressor_room', 'bc_boiler_room', 'bc_drying_room', 'bc_buildings', 'bc_technological_processes'];
        abort_unless(in_array($agentType, $allowed, true), 404);

        $request->validate([
            'prompt' => ['required', 'string', 'max:32000'],
        ]);

        SystemSetting::set(
            "ai_agent_prompt_{$agentType}",
            trim($request->input('prompt')),
            auth()->id()
        );

        $tab = match(true) {
            $agentType === 'iso50001'                => 'iso50001',
            str_starts_with($agentType, 'bc_')      => 'biale-certyfikaty',
            default                                  => 'energetyczne',
        };

        return redirect()
            ->route('audits.types', ['tab' => $tab])
            ->with('status', 'Trening agenta „' . $agentType . '" został zapisany.');
    }

    /**
     * Przywraca domyślny prompt agenta AI (usuwa niestandardowy trening).
     */
    public function resetAgentTraining(string $agentType)
    {
        $allowed = ['general', 'compressor_room', 'boiler_room', 'drying_room', 'buildings', 'technological_processes', 'iso50001', 'bc_general', 'bc_compressor_room', 'bc_boiler_room', 'bc_drying_room', 'bc_buildings', 'bc_technological_processes'];
        abort_unless(in_array($agentType, $allowed, true), 404);

        $setting = SystemSetting::find("ai_agent_prompt_{$agentType}");
        if ($setting) {
            $setting->delete();
            \Illuminate\Support\Facades\Cache::forget("system_setting_ai_agent_prompt_{$agentType}");
        }

        $tab = match(true) {
            $agentType === 'iso50001'           => 'iso50001',
            str_starts_with($agentType, 'bc_') => 'biale-certyfikaty',
            default                             => 'energetyczne',
        };

        return redirect()
            ->route('audits.types', ['tab' => $tab])
            ->with('status', 'Trening agenta „' . $agentType . '" został przywrócony do domyślnego.');
    }
}
