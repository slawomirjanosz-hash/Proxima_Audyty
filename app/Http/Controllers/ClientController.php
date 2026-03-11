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

        $companies = Company::with(['client', 'auditor'])
            ->where('client_id', $user->id)
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
