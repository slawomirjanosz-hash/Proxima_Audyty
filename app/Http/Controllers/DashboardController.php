<?php

namespace App\Http\Controllers;

use App\Models\ClientChatMessage;
use App\Models\ClientInquiry;
use App\Models\ClientRegistration;
use App\Models\Company;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // Claude Haiku 4.5 pricing (USD per token)
    private const INPUT_COST_PER_TOKEN  = 0.80  / 1_000_000;  // $0.80 per 1M input tokens
    private const OUTPUT_COST_PER_TOKEN = 4.00  / 1_000_000;  // $4.00 per 1M output tokens
    private const USD_PLN_RATE          = 3.85;                // approximate; update as needed

    public function index(): View
    {
        $companies = Company::with(['client', 'auditor', 'assignedUsers', 'energyAudits'])->orderBy('name')->get();

        // Group new inquiries by company_id for quick lookup
        $newInquiriesByCompany = ClientInquiry::where('status', 'new')
            ->whereNotNull('company_id')
            ->selectRaw('company_id, count(*) as cnt')
            ->groupBy('company_id')
            ->pluck('cnt', 'company_id');

        // Companies where client accepted an offer → admin needs to assign audit
        $acceptedOffersByCompany = ClientInquiry::where('status', 'offer_accepted')
            ->whereNotNull('company_id')
            ->selectRaw('company_id, count(*) as cnt')
            ->groupBy('company_id')
            ->pluck('cnt', 'company_id');

        // Count inquiries without a company (user has no company assigned)
        $orphanInquiries = ClientInquiry::where('status', 'new')
            ->whereNull('company_id')
            ->count();

        // Pending company registrations awaiting admin approval
        $pendingRegistrations = ClientRegistration::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        // Unread client chat messages per company
        $unreadChatByCompany = ClientChatMessage::where('is_from_admin', false)
            ->whereNull('read_at')
            ->selectRaw('company_id, count(*) as cnt')
            ->groupBy('company_id')
            ->pluck('cnt', 'company_id');

        // ── AI token usage per company ─────────────────────────────────────
        // Load assistant messages with their conversations, then aggregate per company.
        // Uses Eloquent (no raw JSON SQL) so it works on SQLite, MySQL and PostgreSQL.
        $assistantMessages = \App\Models\AiMessage::where('role', 'assistant')
            ->whereNotNull('metadata')
            ->with('conversation:id,user_id')
            ->get(['id', 'ai_conversation_id', 'metadata']);

        // Build user_id → company_id map from already-loaded $companies
        $clientToCompany = $companies->filter(fn($c) => $c->client_id)
            ->pluck('id', 'client_id'); // [user_id => company_id]

        $tokensByCompany = collect();
        foreach ($assistantMessages as $msg) {
            $userId    = $msg->conversation?->user_id;
            $companyId = $clientToCompany[$userId] ?? null;
            if (!$companyId) continue;

            $meta   = is_array($msg->metadata) ? $msg->metadata : [];
            $input  = (int) ($meta['input_tokens']  ?? 0);
            $output = (int) ($meta['output_tokens'] ?? 0);

            if (!$tokensByCompany->has($companyId)) {
                $tokensByCompany->put($companyId, ['input' => 0, 'output' => 0]);
            }
            $tokensByCompany[$companyId] = [
                'input'  => $tokensByCompany[$companyId]['input']  + $input,
                'output' => $tokensByCompany[$companyId]['output'] + $output,
            ];
        }

        $tokensByCompany = $tokensByCompany->map(function ($row) {
            $input   = $row['input'];
            $output  = $row['output'];
            $costUsd = $input  * self::INPUT_COST_PER_TOKEN
                     + $output * self::OUTPUT_COST_PER_TOKEN;
            return [
                'input'    => $input,
                'output'   => $output,
                'total'    => $input + $output,
                'cost_usd' => $costUsd,
                'cost_pln' => $costUsd * self::USD_PLN_RATE,
            ];
        });

        $totalInput  = $tokensByCompany->sum('input');
        $totalOutput = $tokensByCompany->sum('output');
        $totalCostUsd = $totalInput  * self::INPUT_COST_PER_TOKEN
                      + $totalOutput * self::OUTPUT_COST_PER_TOKEN;
        $aiSummary = [
            'input'    => $totalInput,
            'output'   => $totalOutput,
            'total'    => $totalInput + $totalOutput,
            'cost_usd' => $totalCostUsd,
            'cost_pln' => $totalCostUsd * self::USD_PLN_RATE,
        ];

        return view('dashboard.index', [
            'companies'               => $companies,
            'newInquiriesByCompany'   => $newInquiriesByCompany,
            'acceptedOffersByCompany' => $acceptedOffersByCompany,
            'orphanInquiries'         => $orphanInquiries,
            'pendingRegistrations'    => $pendingRegistrations,
            'unreadChatByCompany'     => $unreadChatByCompany,
            'tokensByCompany'         => $tokensByCompany,
            'aiSummary'               => $aiSummary,
        ]);
    }
}
