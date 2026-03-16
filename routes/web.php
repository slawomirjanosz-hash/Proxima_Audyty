<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuditsController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\CrmCustomerTypeController;
use App\Http\Controllers\CrmStageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/oferta', function () {
    return view('oferta');
})->name('oferta');

Route::get('/lang', function () {
    $locale = (string) request('locale', '');
    $supportedLocales = array_keys(config('localization.supported_locales', ['pl' => 'PL', 'en' => 'EN']));

    if (! in_array($locale, $supportedLocales, true)) {
        abort(404);
    }

    session(['locale' => $locale]);

    return redirect()->back();
})->name('locale.switch');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:admin,auditor')
        ->name('dashboard');
    Route::get('/audyty', [AuditsController::class, 'index'])
        ->middleware('role:admin,auditor')
        ->name('audits.index');
    Route::post('/audyty', [AuditsController::class, 'store'])
        ->middleware('role:admin,auditor')
        ->name('audits.store');
    Route::get('/audyty/{audit}/info', [AuditsController::class, 'show'])
        ->middleware('role:admin,auditor')
        ->name('audits.show');
    Route::get('/audyty/{audit}/edytuj', [AuditsController::class, 'edit'])
        ->middleware('role:admin,auditor')
        ->name('audits.edit');
    Route::patch('/audyty/{audit}', [AuditsController::class, 'update'])
        ->middleware('role:admin,auditor')
        ->name('audits.update');
    Route::patch('/audyty/{audit}/zakoncz', [AuditsController::class, 'complete'])
        ->middleware('role:admin,auditor')
        ->name('audits.complete');
    Route::patch('/audyty/{audit}/wznow', [AuditsController::class, 'reopen'])
        ->middleware('role:admin,auditor')
        ->name('audits.reopen');
    Route::get('/audyty/ustawienia', [AuditsController::class, 'settings'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings');
    Route::get('/audyty/diagnostyka', [AuditsController::class, 'diagnostics'])
        ->middleware('role:admin,auditor')
        ->name('audits.diagnostics');
    Route::post('/audyty/diagnostyka/napraw', [AuditsController::class, 'runDiagnosticsRepair'])
        ->middleware('role:admin,auditor')
        ->name('audits.diagnostics.repair');
    Route::post('/audyty/ustawienia/rodzaje', [AuditsController::class, 'storeAuditType'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.audit-type-store');
    Route::patch('/audyty/ustawienia/rodzaje/{auditType}', [AuditsController::class, 'updateAuditType'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.audit-type-update');
    Route::post('/audyty/ustawienia/jednostki', [AuditsController::class, 'storeUnit'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.unit-store');
    Route::patch('/audyty/ustawienia/jednostki/{unit}', [AuditsController::class, 'updateUnit'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.unit-update');
    Route::delete('/audyty/ustawienia/jednostki/{unit}', [AuditsController::class, 'destroyUnit'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.unit-destroy');
    Route::delete('/audyty/ustawienia/rodzaje/{auditType}', [AuditsController::class, 'destroyAuditType'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.audit-type-destroy');
    Route::get('/crm', [CrmController::class, 'index'])
        ->middleware('role:admin,auditor')
        ->name('crm.index');
    Route::get('/crm/ustawienia', [CrmController::class, 'settings'])
        ->middleware('role:admin,auditor')
        ->name('crm.settings');
    Route::get('/crm-diagnostics', [CrmController::class, 'diagnostics'])
        ->middleware('role:admin,auditor')
        ->name('crm.diagnostics');
    Route::get('/crm/company/search-by-nip', [CrmController::class, 'searchCompanyByNip'])
        ->middleware('role:admin,auditor')
        ->name('crm.company.searchByNip');
    Route::get('/crm/company/{id}/edit', [CrmController::class, 'getCompany'])
        ->middleware('role:admin,auditor')
        ->name('crm.company.edit');
    Route::post('/crm/company', [CrmController::class, 'addCompany'])
        ->middleware('role:admin,auditor')
        ->name('crm.company.add');
    Route::put('/crm/company/{id}', [CrmController::class, 'updateCompany'])
        ->middleware('role:admin,auditor')
        ->name('crm.company.update');
    Route::post('/crm/company/{id}/add-to-system', [CrmController::class, 'addCompanyToSystem'])
        ->middleware('role:admin,auditor')
        ->name('crm.company.addToSystem');
    Route::delete('/crm/company/{id}', [CrmController::class, 'deleteCompany'])
        ->middleware('role:admin,auditor')
        ->name('crm.company.delete');
    Route::get('/crm/deal/{id}/edit', [CrmController::class, 'getDeal'])
        ->middleware('role:admin,auditor')
        ->name('crm.deal.edit');
    Route::post('/crm/deal', [CrmController::class, 'addDeal'])
        ->middleware('role:admin,auditor')
        ->name('crm.deal.add');
    Route::put('/crm/deal/{id}', [CrmController::class, 'updateDeal'])
        ->middleware('role:admin,auditor')
        ->name('crm.deal.update');
    Route::delete('/crm/deal/{id}', [CrmController::class, 'deleteDeal'])
        ->middleware('role:admin,auditor')
        ->name('crm.deal.delete');
    Route::get('/crm/task/{id}/edit', [CrmController::class, 'getTask'])
        ->middleware('role:admin,auditor')
        ->name('crm.task.edit');
    Route::post('/crm/task', [CrmController::class, 'addTask'])
        ->middleware('role:admin,auditor')
        ->name('crm.task.add');
    Route::put('/crm/task/{id}', [CrmController::class, 'updateTask'])
        ->middleware('role:admin,auditor')
        ->name('crm.task.update');
    Route::delete('/crm/task/{id}', [CrmController::class, 'deleteTask'])
        ->middleware('role:admin,auditor')
        ->name('crm.task.delete');
    Route::get('/crm/activity/{id}/edit', [CrmController::class, 'getActivity'])
        ->middleware('role:admin,auditor')
        ->name('crm.activity.edit');
    Route::post('/crm/activity', [CrmController::class, 'addActivity'])
        ->middleware('role:admin,auditor')
        ->name('crm.activity.add');
    Route::put('/crm/activity/{id}', [CrmController::class, 'updateActivity'])
        ->middleware('role:admin,auditor')
        ->name('crm.activity.update');
    Route::delete('/crm/activity/{id}', [CrmController::class, 'deleteActivity'])
        ->middleware('role:admin,auditor')
        ->name('crm.activity.delete');
    Route::get('/crm/customer-types', [CrmCustomerTypeController::class, 'index'])
        ->middleware('role:admin,auditor')
        ->name('crm.customer-types');
    Route::post('/crm/customer-types', [CrmCustomerTypeController::class, 'store'])
        ->middleware('role:admin,auditor')
        ->name('crm.customer-types.store');
    Route::get('/crm/customer-types/{id}', [CrmCustomerTypeController::class, 'show'])
        ->middleware('role:admin,auditor')
        ->name('crm.customer-types.show');
    Route::put('/crm/customer-types/{id}', [CrmCustomerTypeController::class, 'update'])
        ->middleware('role:admin,auditor')
        ->name('crm.customer-types.update');
    Route::delete('/crm/customer-types/{id}', [CrmCustomerTypeController::class, 'destroy'])
        ->middleware('role:admin,auditor')
        ->name('crm.customer-types.destroy');
    Route::post('/crm/stage', [CrmStageController::class, 'store'])
        ->middleware('role:admin,auditor')
        ->name('crm.stage.add');
    Route::get('/crm/stage/{id}/edit', [CrmStageController::class, 'edit'])
        ->middleware('role:admin,auditor')
        ->name('crm.stage.edit');
    Route::put('/crm/stage/{id}', [CrmStageController::class, 'update'])
        ->middleware('role:admin,auditor')
        ->name('crm.stage.update');
    Route::delete('/crm/stage/{id}', [CrmStageController::class, 'destroy'])
        ->middleware('role:admin,auditor')
        ->name('crm.stage.delete');
    Route::get('/strefa-klienta', [ClientController::class, 'index'])->name('strefa-klienta');
    Route::get('/settings', [SettingsController::class, 'index'])
        ->middleware('role:admin')
        ->name('settings.index');

    Route::post('/settings/users', [SettingsController::class, 'storeUser'])
        ->middleware('role:admin')
        ->name('settings.user-store');

    Route::patch('/settings/users/{user}/role', [SettingsController::class, 'updateRole'])
        ->middleware('role:admin')
        ->name('settings.user-role');

    Route::patch('/settings/users/{user}/access', [SettingsController::class, 'updateUserAccess'])
        ->middleware('role:admin')
        ->name('settings.user-access');

    Route::delete('/settings/users/{user}', [SettingsController::class, 'destroyUser'])
        ->middleware('role:admin')
        ->name('settings.user-destroy');

    Route::patch('/settings/companies/{company}/assignments', [SettingsController::class, 'updateCompanyAssignments'])
        ->middleware('role:admin')
        ->name('settings.company-assignments');

    Route::patch('/settings/companies/{company}', [SettingsController::class, 'updateCompany'])
        ->middleware('role:admin')
        ->name('settings.company-update');

    Route::delete('/settings/companies/{company}', [SettingsController::class, 'destroyCompany'])
        ->middleware('role:admin')
        ->name('settings.company-destroy');

    Route::post('/settings/companies', [SettingsController::class, 'storeCompany'])
        ->middleware('role:admin')
        ->name('settings.company-store');

    Route::post('/settings/companies/{company}/clients', [SettingsController::class, 'storeCompanyClient'])
        ->middleware('role:admin')
        ->name('settings.company-client-store');

    Route::patch('/settings/companies/{company}/clients/{user}', [SettingsController::class, 'updateCompanyClient'])
        ->middleware('role:admin')
        ->name('settings.company-client-update');

    Route::get('/settings/companies/lookup-by-nip', [SettingsController::class, 'lookupCompanyByNip'])
        ->middleware('role:admin')
        ->name('settings.company-lookup-by-nip');
});
