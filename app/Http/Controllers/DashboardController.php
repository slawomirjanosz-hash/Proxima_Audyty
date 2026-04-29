<?php

namespace App\Http\Controllers;

use App\Models\ClientChatMessage;
use App\Models\ClientInquiry;
use App\Models\ClientRegistration;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
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
        // Aggregates input/output tokens from ai_messages.metadata JSON
        // joined through ai_conversations.user_id → companies.client_id
        $tokenRows = DB::select(
            "SELECT c.id AS company_id,
                    COALESCE(SUM(CAST(json_extract(am.metadata, '$.input_tokens')  AS INTEGER)), 0) AS input_tokens,
                    COALESCE(SUM(CAST(json_extract(am.metadata, '$.output_tokens') AS INTEGER)), 0) AS output_tokens
             FROM companies c
             LEFT JOIN users u              ON u.id  = c.client_id
             LEFT JOIN ai_conversations ac  ON ac.user_id = u.id
             LEFT JOIN ai_messages am       ON am.ai_conversation_id = ac.id AND am.role = 'assistant'
             GROUP BY c.id"
        );

        $tokensByCompany = collect($tokenRows)->keyBy('company_id')->map(function ($row) {
            $input  = (int) $row->input_tokens;
            $output = (int) $row->output_tokens;
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
