<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function storeUser(Request $request): RedirectResponse
    {
        if (! $request->user()->canManageEverything()) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in([
                UserRole::Admin->value,
                UserRole::Auditor->value,
                UserRole::Client->value,
            ])],
            'tabs' => ['array'],
            'tabs.*' => ['nullable', 'boolean'],
        ]);

        $firstName = trim((string) $validated['first_name']);
        $lastName = trim((string) $validated['last_name']);
        $computedName = trim($firstName.' '.$lastName);

        $shortName = mb_substr($firstName, 0, 3).mb_substr($lastName, 0, 3);
        if ($shortName === '') {
            $shortName = mb_substr((string) $validated['email'], 0, 6);
        }

        $allTabs = array_keys(User::tabLabels());
        $submittedTabs = (array) ($validated['tabs'] ?? []);
        $permissions = [];
        foreach ($allTabs as $tab) {
            $permissions[$tab] = (bool) ($submittedTabs[$tab] ?? false);
        }

        $payload = [
            'name' => $computedName,
            'short_name' => $shortName,
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'tab_permissions' => $permissions,
        ];

        if (Schema::hasColumn('users', 'first_name')) {
            $payload['first_name'] = $firstName;
        }

        if (Schema::hasColumn('users', 'last_name')) {
            $payload['last_name'] = $lastName;
        }

        if (Schema::hasColumn('users', 'phone')) {
            $payload['phone'] = $validated['phone'] ?? null;
        }

        User::create($payload);

        return back()->with('status', __('ui.messages.user_created'));
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $users = User::query()
            ->where('role', '!=', UserRole::SuperAdmin->value)
            ->orderBy('name')
            ->get();
        $companies = Company::with(['auditor', 'assignedUsers'])->orderBy('name')->get();
        $auditors = User::whereIn('role', [UserRole::Auditor->value, UserRole::Admin->value])->orderBy('name')->get();
        $clients = User::where('role', UserRole::Client)->orderBy('name')->get();

        return view('settings.index', [
            'users'            => $users,
            'companies'        => $companies,
            'auditors'         => $auditors,
            'clients'          => $clients,
            'tabLabels'        => User::tabLabels(),
            'canManage'        => $user->canManageEverything(),
            'isSuperAdmin'     => $user->isSuperAdmin(),
            'co2ElCombFactor'  => (float) SystemSetting::get('co2_el_comb_factor', '0.710'),
            'co2ElNatFactor'   => (float) SystemSetting::get('co2_el_nat_factor',  '0.640'),
            'co2ElGridDisplay' => (int)   SystemSetting::get('co2_el_grid_display', '553'),
            'co2ElYear'        => (string)SystemSetting::get('co2_el_year', '2024'),
        ]);
    }

    public function updateEnergyIndicators(Request $request): RedirectResponse
    {
        if (! $request->user()->canManageEverything()) {
            abort(403);
        }

        $validated = $request->validate([
            'co2_el_year'         => ['required', 'integer', 'min:2015', 'max:2100'],
            'co2_el_comb_factor'  => ['required', 'numeric', 'min:0.01', 'max:2.00'],
            'co2_el_nat_factor'   => ['required', 'numeric', 'min:0.01', 'max:2.00'],
            'co2_el_grid_display' => ['required', 'integer', 'min:1', 'max:2000'],
        ]);

        $userId = $request->user()->id;

        SystemSetting::set('co2_el_year',         (string) $validated['co2_el_year'],         $userId);
        SystemSetting::set('co2_el_comb_factor',  (string) $validated['co2_el_comb_factor'],  $userId);
        SystemSetting::set('co2_el_nat_factor',   (string) $validated['co2_el_nat_factor'],   $userId);
        SystemSetting::set('co2_el_grid_display', (string) $validated['co2_el_grid_display'], $userId);

        return back()->with('co2_settings_status', 'Wskaźniki emisji CO₂ zostały zapisane.');
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

    public function destroyUser(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();

        if (! $actor->canManageEverything()) {
            abort(403);
        }

        if ($user->isSuperAdmin() || $user->id === $actor->id) {
            abort(403);
        }

        $user->delete();

        return back()->with('status', __('ui.messages.user_deleted'));
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
        ]);

        $company->update($validated);

        return back()->with('status', __('ui.messages.company_assignments_updated'));
    }

    public function storeCompany(Request $request): RedirectResponse
    {
        if (! $request->user()->canManageEverything()) {
            abort(403);
        }

        $validated = $request->validate([
            'nip' => ['nullable', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:2000'],
            'auditor_id' => ['nullable', 'exists:users,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $normalizedName = Company::normalizeLegalForm(trim((string) $validated['name']));
        $shortName = mb_substr($normalizedName, 0, 3);

        $payload = [
            'name' => $normalizedName,
            'short_name' => $shortName,
            'city' => $validated['city'] ?? null,
            'street' => $validated['street'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'description' => $validated['description'] ?? null,
            'auditor_id' => $validated['auditor_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
        ];

        if (Schema::hasColumn('companies', 'nip')) {
            $payload['nip'] = ! empty($validated['nip']) ? preg_replace('/\D+/', '', (string) $validated['nip']) : null;
        }

        Company::create($payload);

        return back()->with('status', __('ui.messages.company_created'));
    }

    public function lookupCompanyByNip(Request $request): JsonResponse
    {
        if (! $request->user()->canManageEverything()) {
            abort(403);
        }

        $validated = $request->validate([
            'nip' => ['required', 'string', 'min:10', 'max:20'],
        ]);

        $nip = preg_replace('/\D+/', '', (string) $validated['nip']);

        if (strlen($nip) !== 10) {
            return response()->json([
                'ok' => false,
                'message' => __('ui.settings.clients.lookup.invalid_nip'),
            ], 422);
        }

        $date = now()->format('Y-m-d');
        $response = Http::timeout(8)
            ->acceptJson()
            ->get("https://wl-api.mf.gov.pl/api/search/nip/{$nip}", [
                'date' => $date,
            ]);

        if (! $response->ok()) {
            return response()->json([
                'ok' => false,
                'message' => __('ui.settings.clients.lookup.not_found'),
            ], 404);
        }

        $subject = $response->json('result.subject');

        if (! is_array($subject) || empty($subject['name'])) {
            return response()->json([
                'ok' => false,
                'message' => __('ui.settings.clients.lookup.not_found'),
            ], 404);
        }

        $name = trim((string) ($subject['name'] ?? ''));
        $address = trim((string) ($subject['workingAddress'] ?? $subject['residenceAddress'] ?? ''));
        $street = '';
        $postalCode = '';
        $city = '';

        if ($address !== '') {
            $parts = preg_split('/\s*,\s*/', $address);
            $street = trim((string) ($parts[0] ?? ''));
            $cityPart = trim((string) ($parts[1] ?? $parts[0] ?? ''));

            if (preg_match('/(?<postal>\d{2}-\d{3})\s+(?<city>.+)$/u', $cityPart, $matches) === 1) {
                $postalCode = trim((string) ($matches['postal'] ?? ''));
                $city = trim((string) ($matches['city'] ?? ''));
            } else {
                $city = $cityPart;
            }
        }

        return response()->json([
            'ok' => true,
            'data' => [
                'nip' => $nip,
                'name' => $name,
                'street' => $street,
                'postal_code' => $postalCode,
                'city' => $city,
            ],
        ]);
    }

    public function storeCompanyClient(Request $request, Company $company): RedirectResponse
    {
        if (! $request->user()->canManageEverything()) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $existingUser = User::where('email', $validated['email'])->first();

        if ($existingUser) {
            if (! $existingUser->isClient()) {
                return back()->withErrors([
                    'email' => __('ui.settings.clients.assigned_person_must_be_client'),
                ])->withInput();
            }

            $existingUser->assignedCompanies()->syncWithoutDetaching([$company->id]);

            $allTabs = array_keys(User::tabLabels());
            $tabPermissions = array_fill_keys($allTabs, false);
            $tabPermissions[User::TAB_HOME] = true;
            $tabPermissions[User::TAB_CLIENT_ZONE] = true;

            $existingUser->update([
                'role' => UserRole::Client->value,
                'tab_permissions' => $tabPermissions,
            ]);

            if (empty($existingUser->company_id)) {
                $existingUser->update(['company_id' => $company->id]);
            }

            return back()->with('status', __('ui.messages.company_client_created'));
        }

        if (empty($validated['password'])) {
            return back()->withErrors([
                'password' => __('ui.settings.clients.password_required_for_new_person'),
            ])->withInput();
        }

        $firstName = trim((string) ($validated['first_name'] ?? ''));
        $lastName = trim((string) ($validated['last_name'] ?? ''));

        $shortName = trim((string) ($validated['short_name'] ?? ''));
        if ($shortName === '') {
            $shortName = mb_substr($firstName, 0, 3) . mb_substr($lastName, 0, 3);
        }

        $computedName = trim($firstName.' '.$lastName);
        if ($computedName === '') {
            $computedName = $validated['email'];
        }

        $allTabs = array_keys(User::tabLabels());
        $tabPermissions = array_fill_keys($allTabs, false);
        $tabPermissions[User::TAB_HOME] = true;
        $tabPermissions[User::TAB_CLIENT_ZONE] = true;

        $clientUser = User::create([
            'name' => $computedName,
            'first_name' => $firstName !== '' ? $firstName : null,
            'last_name' => $lastName !== '' ? $lastName : null,
            'short_name' => $shortName,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role' => UserRole::Client->value,
            'company_id' => $company->id,
            'tab_permissions' => $tabPermissions,
        ]);

        $clientUser->assignedCompanies()->syncWithoutDetaching([$company->id]);

        return back()->with('status', __('ui.messages.company_client_created'));
    }

    public function updateCompanyClient(Request $request, Company $company, User $user): RedirectResponse
    {
        if (! $request->user()->canManageEverything()) {
            abort(403);
        }

        if (! $user->isClient()) {
            abort(404);
        }

        $isAssigned = $company->assignedUsers()->where('users.id', $user->id)->exists();
        if (! $isAssigned) {
            abort(404);
        }

        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $firstName = trim((string) ($validated['first_name'] ?? ''));
        $lastName = trim((string) ($validated['last_name'] ?? ''));
        $shortName = trim((string) ($validated['short_name'] ?? ''));
        if ($shortName === '') {
            $shortName = mb_substr($firstName, 0, 3) . mb_substr($lastName, 0, 3);
        }

        $computedName = trim($firstName.' '.$lastName);
        if ($computedName === '') {
            $computedName = $validated['email'];
        }

        $allTabs = array_keys(User::tabLabels());
        $tabPermissions = array_fill_keys($allTabs, false);
        $tabPermissions[User::TAB_HOME] = true;
        $tabPermissions[User::TAB_CLIENT_ZONE] = true;

        $payload = [
            'name' => $computedName,
            'first_name' => $firstName !== '' ? $firstName : null,
            'last_name' => $lastName !== '' ? $lastName : null,
            'short_name' => $shortName !== '' ? $shortName : null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => UserRole::Client->value,
            'tab_permissions' => $tabPermissions,
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        return back()->with('status', __('ui.messages.company_client_updated'));
    }

    public function updateCompany(Request $request, Company $company): RedirectResponse
    {
        if (! $request->user()->canManageEverything()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:32'],
            'street' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'nip' => ['nullable', 'string', 'max:20'],
            'auditor_id' => ['nullable', 'exists:users,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $normalizedName = Company::normalizeLegalForm(trim((string) $validated['name']));
        $shortName = trim((string) ($validated['short_name'] ?? ''));
        if ($shortName === '') {
            $shortName = mb_substr($normalizedName, 0, 3);
        }

        $payload = [
            'name' => $normalizedName,
            'short_name' => $shortName,
            'street' => $validated['street'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'city' => $validated['city'] ?? null,
            'description' => $validated['description'] ?? null,
            'auditor_id' => $validated['auditor_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
        ];

        if (Schema::hasColumn('companies', 'nip')) {
            $payload['nip'] = ! empty($validated['nip']) ? preg_replace('/\D+/', '', (string) $validated['nip']) : null;
        }

        $company->update($payload);

        return back()->with('status', __('ui.messages.company_updated'));
    }

    public function destroyCompany(Request $request, Company $company): RedirectResponse
    {
        if (! $request->user()->canManageEverything()) {
            abort(403);
        }

        $company->delete();

        return back()->with('status', __('ui.messages.company_deleted'));
    }

}
