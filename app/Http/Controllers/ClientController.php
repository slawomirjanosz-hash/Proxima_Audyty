<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\EnergyAudit;
use App\Models\Iso50001Audit;
use App\Models\Iso50001Template;
use App\Models\Offer;
use App\Support\Iso50001TemplateDefinition;
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
            $isoAudits = Iso50001Audit::with(['company', 'creator', 'reviewer'])->latest()->get();
            $templateSteps = $this->resolveIsoSteps();
            $maxIsoTasks = Iso50001TemplateDefinition::maxTasks($templateSteps);

            $isoAudits->each(function (Iso50001Audit $audit) use ($templateSteps, $maxIsoTasks): void {
                $filledTasks = Iso50001TemplateDefinition::filledTasks((array) ($audit->answers ?? []), $templateSteps);
                $audit->setAttribute('progress_filled', $filledTasks);
                $audit->setAttribute('progress_max', $maxIsoTasks);
            });

            $auditsByCompany = $audits
                ->filter(fn ($audit) => $audit->company_id !== null)
                ->groupBy(fn ($audit) => (string) $audit->company_id);

            return view('client.index', [
                'companies' => $companies,
                'offers' => $offers,
                'audits' => $audits,
                'isoAudits' => $isoAudits,
                'auditsByCompany' => $auditsByCompany,
                'maxIsoTasks' => $maxIsoTasks,
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

        $isoAudits = Iso50001Audit::with(['company', 'creator', 'reviewer'])
            ->where('created_by_user_id', $user->id)
            ->whereIn('company_id', $companyIds)
            ->latest()
            ->get();

        $templateSteps = $this->resolveIsoSteps();
        $maxIsoTasks = Iso50001TemplateDefinition::maxTasks($templateSteps);

        $isoAudits->each(function (Iso50001Audit $audit) use ($templateSteps, $maxIsoTasks): void {
            $filledTasks = Iso50001TemplateDefinition::filledTasks((array) ($audit->answers ?? []), $templateSteps);
            $audit->setAttribute('progress_filled', $filledTasks);
            $audit->setAttribute('progress_max', $maxIsoTasks);
        });

        $auditsByCompany = $audits
            ->filter(fn ($audit) => $audit->company_id !== null)
            ->groupBy(fn ($audit) => (string) $audit->company_id);

        return view('client.index', [
            'companies' => $companies,
            'offers'    => $offers,
            'audits'    => $audits,
            'isoAudits' => $isoAudits,
            'auditsByCompany' => $auditsByCompany,
            'maxIsoTasks' => $maxIsoTasks,
            'previewMode' => false,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function resolveIsoSteps(): array
    {
        $template = Iso50001Template::query()->first();
        if (! $template) {
            return Iso50001TemplateDefinition::defaultSteps();
        }

        return Iso50001TemplateDefinition::normalizeSteps((array) $template->steps);
    }
}
