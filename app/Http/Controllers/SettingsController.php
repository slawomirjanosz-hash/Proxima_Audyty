<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $users = User::query()
            ->where('role', '!=', UserRole::SuperAdmin->value)
            ->orderBy('name')
            ->get();
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

        if ($user->isSuperAdmin()) {
            abort(404);
        }

        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in([
                UserRole::Admin->value,
                UserRole::Auditor->value,
                UserRole::Client->value,
            ])],
            'tabs' => ['array'],
            'tabs.*' => ['nullable', 'boolean'],
        ]);

        $allTabs = array_keys(User::tabLabels());
        $submittedTabs = (array) ($validated['tabs'] ?? []);

        $permissions = [];
        foreach ($allTabs as $tab) {
            $permissions[$tab] = (bool) ($submittedTabs[$tab] ?? false);
        }

        $firstName = trim((string) ($validated['first_name'] ?? ''));
        $lastName = trim((string) ($validated['last_name'] ?? ''));

        if ($firstName === '' && Schema::hasColumn('users', 'first_name')) {
            $firstName = (string) ($user->first_name ?? '');
        }

        if ($lastName === '' && Schema::hasColumn('users', 'last_name')) {
            $lastName = (string) ($user->last_name ?? '');
        }

        $computedName = trim($firstName.' '.$lastName);
        if ($computedName === '') {
            $computedName = (string) $user->name;
        }

        $shortName = $validated['short_name'] ?? null;
        if ($shortName === null || $shortName === '') {
            $shortName = mb_substr($firstName, 0, 3) . mb_substr($lastName, 0, 3);
        }

        $payload = [
            'name' => $computedName,
            'email' => $validated['email'],
            'role' => $validated['role'],
            'tab_permissions' => $permissions,
            'short_name' => $shortName,
        ];

        if (Schema::hasColumn('users', 'first_name')) {
            $payload['first_name'] = $firstName !== '' ? $firstName : null;
        }

        if (Schema::hasColumn('users', 'last_name')) {
            $payload['last_name'] = $lastName !== '' ? $lastName : null;
        }

        if (Schema::hasColumn('users', 'phone')) {
            $payload['phone'] = $validated['phone'] ?? null;
        }

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        return back()->with('status', __('ui.messages.user_permissions_updated'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();

        if (! $actor->canManageEverything()) {
            abort(403);
        }

        if ($user->isSuperAdmin()) {
            abort(404);
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
