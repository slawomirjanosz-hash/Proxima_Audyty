<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\EnergyAuditMasterData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnergyAuditMasterController extends Controller
{
    /**
     * Show master audit form for the logged-in client's company.
     */
    public function show(Request $request): View
    {
        $user    = $request->user();
        $company = $this->resolveCompany($user);

        $masterData = $company
            ? EnergyAuditMasterData::firstOrCreate(
                ['company_id' => $company->id],
                ['form_data' => [], 'completion_percent' => 0]
            )
            : null;

        return view('client.energy-audit-master', [
            'company'    => $company,
            'masterData' => $masterData,
            'formData'   => $masterData ? $masterData->getFormDataSafe() : [],
        ]);
    }

    /**
     * AJAX auto-save: accepts JSON body { fields: { "FIELD-ID": "value", ... }, completion_percent: 42 }
     */
    public function save(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isClient() || in_array($user->role, ['admin', 'auditor'], true), 403);

        $request->validate([
            'fields'             => ['required', 'array'],
            'completion_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $company = $this->resolveCompany($user);
        if (! $company) {
            return response()->json(['error' => 'Brak przypisanej firmy.'], 422);
        }

        // Sanitize: only allow string/numeric values, no nested arrays
        $fields = collect($request->input('fields'))
            ->filter(fn($v) => is_string($v) || is_numeric($v) || $v === null)
            ->map(fn($v) => $v === null ? null : (string) $v)
            ->toArray();

        $masterData = EnergyAuditMasterData::firstOrCreate(
            ['company_id' => $company->id],
            ['form_data' => [], 'completion_percent' => 0]
        );

        // Merge incoming fields with existing data
        $existingData = $masterData->getFormDataSafe();
        foreach ($fields as $key => $value) {
            if ($value === null || $value === '') {
                unset($existingData[$key]);
            } else {
                $existingData[$key] = $value;
            }
        }

        $masterData->update([
            'form_data'          => $existingData,
            'completion_percent' => $request->input('completion_percent', $masterData->completion_percent),
            'last_saved_at'      => now(),
        ]);

        return response()->json([
            'ok'                 => true,
            'completion_percent' => $masterData->completion_percent,
            'last_saved_at'      => $masterData->last_saved_at?->format('d.m.Y H:i:s'),
        ]);
    }

    /**
     * JSON endpoint — returns master form data for pre-populating specialized audit forms.
     */
    public function getDataForAudit(Request $request): JsonResponse
    {
        $user    = $request->user();
        $company = $this->resolveCompany($user);

        if (! $company) {
            return response()->json(['form_data' => [], 'completion_percent' => 0]);
        }

        $masterData = EnergyAuditMasterData::where('company_id', $company->id)->first();

        return response()->json([
            'form_data'          => $masterData?->getFormDataSafe() ?? [],
            'completion_percent' => $masterData?->completion_percent ?? 0,
            'last_saved_at'      => $masterData?->last_saved_at?->format('d.m.Y H:i:s'),
        ]);
    }

    private function resolveCompany($user): ?Company
    {
        if ($user->company_id) {
            return Company::find($user->company_id);
        }
        return Company::where('client_id', $user->id)->first();
    }
}
