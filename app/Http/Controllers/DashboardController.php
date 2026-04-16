<?php

namespace App\Http\Controllers;

use App\Models\ClientInquiry;
use App\Models\Company;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $companies = Company::with(['client', 'auditor', 'assignedUsers'])->orderBy('name')->get();

        // Group new inquiries by company_id for quick lookup
        $newInquiriesByCompany = ClientInquiry::where('status', 'new')
            ->whereNotNull('company_id')
            ->selectRaw('company_id, count(*) as cnt')
            ->groupBy('company_id')
            ->pluck('cnt', 'company_id');

        // Count inquiries without a company (user has no company assigned)
        $orphanInquiries = ClientInquiry::where('status', 'new')
            ->whereNull('company_id')
            ->count();

        return view('dashboard.index', [
            'companies'              => $companies,
            'newInquiriesByCompany'  => $newInquiriesByCompany,
            'orphanInquiries'        => $orphanInquiries,
        ]);
    }
}
