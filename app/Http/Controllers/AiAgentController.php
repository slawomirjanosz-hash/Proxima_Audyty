<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
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
            'context_type' => ['nullable', 'string', 'in:general,energy_audit,iso50001,offer'],
            'context_id'   => ['nullable', 'integer'],
        ]);

        $conversation = $this->agent->startConversation(
            userId: auth()->id(),
            contextType: $request->input('context_type', 'general'),
            contextId: $request->input('context_id'),
        );

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
            report($e);

            return response()->json([
                'success' => false,
                'error'   => 'Błąd połączenia z asystentem AI. Spróbuj ponownie.',
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
    public function generateProtocol(AiConversation $aiConversation)
    {
        abort_unless($aiConversation->user_id === auth()->id(), 403);

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

    /**
     * Widok protokołu z danymi w tabeli.
     */
    public function protocol(AiConversation $aiConversation): View
    {
        abort_unless($aiConversation->user_id === auth()->id(), 403);

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
        abort_unless($aiConversation->user_id === auth()->id(), 403);

        if (empty($aiConversation->protocol_data)) {
            return redirect()->route('ai.show', $aiConversation)
                ->with('error', 'Najpierw wygeneruj protokół.');
        }

        $pdf = Pdf::loadView('ai.protocol-pdf', [
            'conversation' => $aiConversation,
            'protocol'     => $aiConversation->protocol_data,
        ])->setPaper('a4');

        $filename = 'protokol-' . str($aiConversation->title)->slug() . '-' . $aiConversation->id . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Podgląd protokołu jako PDF w przeglądarce (inline stream).
     */
    public function previewPdf(AiConversation $aiConversation)
    {
        abort_unless($aiConversation->user_id === auth()->id(), 403);

        if (empty($aiConversation->protocol_data)) {
            return redirect()->route('ai.show', $aiConversation)
                ->with('error', 'Najpierw wygeneruj protokół.');
        }

        $pdf = Pdf::loadView('ai.protocol-pdf', [
            'conversation' => $aiConversation,
            'protocol'     => $aiConversation->protocol_data,
        ])->setPaper('a4');

        $filename = 'protokol-' . str($aiConversation->title)->slug() . '-' . $aiConversation->id . '.pdf';

        return $pdf->stream($filename);
    }
}
