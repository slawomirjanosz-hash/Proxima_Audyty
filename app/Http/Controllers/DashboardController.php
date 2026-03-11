<?php

namespace App\Http\Controllers;

use App\Models\EnergyAudit;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $activeAudits = EnergyAudit::with(['company', 'auditor'])
            ->whereNotIn('status', ['completed', 'done', 'closed', 'cancelled', 'archived'])
            ->latest()
            ->get();

        return view('dashboard.index', [
            'activeAudits' => $activeAudits,
        ]);
    }
}
