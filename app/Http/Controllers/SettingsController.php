<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $users = User::query()->orderBy('name')->get();
        $companies = Company::with(['client', 'auditor'])->orderBy('name')->get();
        $auditors = User::where('role', UserRole::Auditor)->orderBy('name')->get();
        $clients = User::where('role', UserRole::Client)->orderBy('name')->get();

        return view('settings.index', [
            'users' => $users,
            'companies' => $companies,
            'auditors' => $auditors,
            'clients' => $clients,
            'tabLabels' => User::tabLabels(),
            'canManage' => $user->canManageEverything(),
            'isSuperAdmin' => $user->isSuperAdmin(),
        ]);
    }

    public function updateUserAccess(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();

        if (! $actor->canManageEverything()) {
            abort(403);
        }

        if ($user->isSuperAdmin() && ! $actor->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'role' => ['required', new Enum(UserRole::class)],
            'tabs' => ['array'],
            'tabs.*' => ['nullable', 'boolean'],
        ]);

        if (
            $validated['role'] === UserRole::SuperAdmin
            && ! $actor->isSuperAdmin()
        ) {
            abort(403);
        }

        $allTabs = array_keys(User::tabLabels());
        $submittedTabs = (array) ($validated['tabs'] ?? []);

        $permissions = [];
        foreach ($allTabs as $tab) {
            $permissions[$tab] = (bool) ($submittedTabs[$tab] ?? false);
        }

        $user->update([
            'role' => $validated['role'],
            'tab_permissions' => $permissions,
        ]);

        return back()->with('status', __('ui.messages.user_permissions_updated'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();

        if (! $actor->canManageEverything()) {
            abort(403);
        }

        if ($user->isSuperAdmin() && ! $actor->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'role' => ['required', new Enum(UserRole::class)],
        ]);

        if (
            $validated['role'] === UserRole::SuperAdmin
            && ! $actor->isSuperAdmin()
        ) {
            abort(403);
        }

        $user->update([
            'role' => $validated['role'],
        ]);

        return back()->with('status', __('ui.messages.user_role_updated'));
    }

    public function updateCompanyAssignments(Request $request, Company $company): RedirectResponse
    {
        if (! $request->user()->canManageEverything()) {
            abort(403);
        }

        $validated = $request->validate([
            'auditor_id' => ['nullable', 'exists:users,id'],
            'client_id' => ['nullable', 'exists:users,id'],
        ]);

        $company->update($validated);

        return back()->with('status', __('ui.messages.company_assignments_updated'));
    }
}
