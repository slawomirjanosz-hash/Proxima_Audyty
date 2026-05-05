<?php

namespace App\Http\Controllers;

use App\Models\ClientChatMessage;
use App\Models\Company;
use App\Models\EnergyAuditMasterData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnergyAuditMasterController extends Controller
{
    /**
     * Show master audit form.
     * Admin/auditor can pass ?company_id=X to fill on behalf of a client.
     */
    public function show(Request $request): View
    {
        $user    = $request->user();
        $isStaff = in_array($user->role->value ?? $user->role, ['admin', 'auditor', 'super_admin'], true);

        // Staff without company_id = preview/read-only mode (no DB record created)
        $previewMode = false;
        if ($isStaff && $request->filled('company_id')) {
            $company = Company::findOrFail($request->integer('company_id'));
        } elseif ($isStaff && !$request->filled('company_id')) {
            $company     = null;
            $previewMode = true;
        } else {
            $company = $this->resolveCompany($user);
        }

        $masterData = (!$previewMode && $company)
            ? EnergyAuditMasterData::firstOrCreate(
                ['company_id' => $company->id],
                ['form_data' => [], 'completion_percent' => 0]
            )
            : null;

        return view('client.energy-audit-master', [
            'company'      => $company,
            'masterData'   => $masterData,
            'formData'     => $masterData ? $masterData->getFormDataSafe() : [],
            'isStaff'      => $isStaff,
            'previewMode'  => $previewMode,
            'chatMessages' => (!$previewMode && $company)
                ? ClientChatMessage::where('company_id', $company->id)
                    ->orderBy('created_at')
                    ->get()
                : collect(),
        ]);
    }

    /**
     * AJAX auto-save: accepts JSON body { fields: { "FIELD-ID": "value", ... }, completion_percent: 42, company_id: X (admin only) }
     */
    public function save(Request $request): JsonResponse
    {
        $user    = $request->user();
        $isStaff = in_array($user->role->value ?? $user->role, ['admin', 'auditor', 'super_admin'], true);
        abort_unless($user->isClient() || $isStaff, 403);

        $request->validate([
            'fields'             => ['required', 'array'],
            'completion_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'company_id'         => ['nullable', 'integer', 'exists:companies,id'],
        ]);

        // Staff can save for any company
        if ($isStaff && $request->filled('company_id')) {
            $company = Company::findOrFail($request->integer('company_id'));
        } else {
            $company = $this->resolveCompany($user);
        }

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
