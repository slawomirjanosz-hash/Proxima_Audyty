<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\EnergyAudit;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if (! $user->isClient()) {
            $companies = Company::with(['client', 'auditor'])->latest()->get();
            $offers = Offer::with('company')->latest()->get();
            $audits = EnergyAudit::with(['company', 'auditor'])->latest()->get();

            return view('client.index', [
                'companies' => $companies,
                'offers' => $offers,
                'audits' => $audits,
                'previewMode' => true,
            ]);
        }

        $assignedCompanyIds = $user->assignedCompanies()->pluck('companies.id');

        $companies = Company::with(['client', 'auditor'])
            ->where(function ($query) use ($user, $assignedCompanyIds) {
                $query->where('client_id', $user->id)
                    ->orWhereIn('id', $assignedCompanyIds);

                if ($user->company_id) {
                    $query->orWhere('id', $user->company_id);
                }
            })
            ->latest()
            ->get();

        $companyIds = $companies->pluck('id');

        $offers = Offer::with('company')
            ->whereIn('company_id', $companyIds)
            ->latest()
            ->get();

        $audits = EnergyAudit::with(['company', 'auditor'])
            ->whereIn('company_id', $companyIds)
            ->latest()
            ->get();

        return view('client.index', [
            'companies' => $companies,
            'offers'    => $offers,
            'audits'    => $audits,
            'previewMode' => false,
        ]);
    }
}
