<?php

namespace App\Http\Controllers;

use App\Models\ClientChatMessage;
use App\Models\ClientInquiry;
use App\Models\ClientRegistration;
use App\Models\Company;
use Illuminate\View\View;

class DashboardController extends Controller
{
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

        return view('dashboard.index', [
            'companies'               => $companies,
            'newInquiriesByCompany'   => $newInquiriesByCompany,
            'acceptedOffersByCompany' => $acceptedOffersByCompany,
            'orphanInquiries'         => $orphanInquiries,
            'pendingRegistrations'    => $pendingRegistrations,
            'unreadChatByCompany'     => $unreadChatByCompany,
        ]);
    }
}
