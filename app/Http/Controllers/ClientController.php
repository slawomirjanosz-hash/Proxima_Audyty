<?php

namespace App\Http\Controllers;

use App\Models\AuditType;
use App\Models\ClientInquiry;
use App\Models\Company;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $auditTypes = AuditType::orderBy('name')->get();
        $contactEmail = (string) SystemSetting::get('company_contact_email', '');

        if (! $user->isClient()) {
            $inquiries = ClientInquiry::with(['user', 'company', 'auditType'])->latest()->get();
            return view('client.index', [
                'auditTypes'   => $auditTypes,
                'inquiries'    => $inquiries,
                'contactEmail' => $contactEmail,
                'previewMode'  => true,
            ]);
        }

        $inquiries = ClientInquiry::with(['auditType'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $company = null;
        if ($user->company_id) {
            $company = Company::find($user->company_id);
        }
        if (! $company) {
            $company = Company::where('client_id', $user->id)->first();
        }

        return view('client.index', [
            'auditTypes'   => $auditTypes,
            'inquiries'    => $inquiries,
            'contactEmail' => $contactEmail,
            'company'      => $company,
            'previewMode'  => false,
        ]);
    }

    public function storeInquiry(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'audit_type_id' => ['required', 'exists:audit_types,id'],
            'message'       => ['nullable', 'string', 'max:2000'],
        ]);

        $auditType = AuditType::findOrFail((int) $validated['audit_type_id']);

        $companyId = $user->company_id;
        if (! $companyId) {
            $comp = Company::where('client_id', $user->id)->first();
            $companyId = $comp?->id;
        }

        ClientInquiry::create([
            'user_id'         => $user->id,
            'company_id'      => $companyId,
            'audit_type_id'   => $auditType->id,
            'audit_type_name' => $auditType->name,
            'message'         => $validated['message'] ?? null,
            'status'          => 'new',
        ]);

        return back()->with('inquiry_status', 'Zapytanie zostało wysłane. Skontaktujemy się z Tobą wkrótce.');
    }
}