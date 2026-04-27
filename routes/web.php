<?php

use App\Http\Controllers\AiAgentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuditsController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\OffersController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\CrmCustomerTypeController;
use App\Http\Controllers\CrmStageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosticsController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\Iso50001AuditController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/oferty', [OffersController::class, 'index'])->name('offers.index');

Route::get('/oferta', function () {
    return redirect()->route('offers.index');
})->name('oferta');

// Public company registration
Route::get('/rejestracja', [RegistrationController::class, 'index'])->name('register.form');
Route::get('/rejestracja/nip-szukaj', [RegistrationController::class, 'lookupNip'])
    ->name('register.nip-lookup')
    ->middleware('throttle:30,1');
Route::post('/rejestracja', [RegistrationController::class, 'store'])
    ->name('register.store')
    ->middleware('throttle:5,10');

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

Route::get('/informacje', [InformationController::class, 'index'])
    ->name('information.index');
Route::get('/informacje/pse-kse', [InformationController::class, 'snapshot'])
    ->name('information.pse-kse');
Route::post('/informacje/kalkulacje', [InformationController::class, 'storeCalculation'])
    ->middleware('auth')
    ->name('information.calculations.store');
Route::delete('/informacje/kalkulacje/{calculation}', [InformationController::class, 'destroyCalculation'])
    ->middleware('auth')
    ->name('information.calculations.destroy');

// Diagnostics – dostępne bez logowania (tylko informacje o stanie serwera)
Route::get('/admin/diagnostyka', [DiagnosticsController::class, 'index'])
    ->name('diagnostics.index');
Route::post('/admin/diagnostyka/migrate', [DiagnosticsController::class, 'migrate'])
    ->middleware('auth')
    ->name('diagnostics.migrate');
Route::post('/admin/diagnostyka/cache-clear', [DiagnosticsController::class, 'clearCache'])
    ->middleware('auth')
    ->name('diagnostics.cache-clear');

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profil/haslo', [ProfileController::class, 'password'])->name('profile.password');
    Route::post('/profil/haslo', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:admin,auditor')
        ->name('dashboard');
    Route::get('/audyty', [AuditsController::class, 'index'])
        ->middleware('role:admin,auditor')
        ->name('audits.index');
    Route::get('/audyty/{audit}/edytuj', [AuditsController::class, 'edit'])
        ->middleware('role:admin,auditor')
        ->name('audits.edit');
    Route::patch('/audyty/{audit}', [AuditsController::class, 'update'])
        ->middleware('role:admin,auditor')
        ->name('audits.update');
    Route::get('/audyty/rodzaje/{tab}', function (string $tab) {
        return app(\App\Http\Controllers\AuditsController::class)->settings($tab);
    })
        ->where('tab', 'energetyczne|iso50001|biale-certyfikaty|ai-audyty|ustawienia')
        ->middleware('role:admin,auditor')
        ->name('audits.types');
    Route::get('/audyty/ustawienia', function () {
        return redirect()->route('audits.types', ['tab' => 'energetyczne']);
    })
        ->middleware('role:admin,auditor')
        ->name('audits.settings');
    Route::post('/audyty/ustawienia/rodzaje', [AuditsController::class, 'storeAuditType'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.audit-type-store');
    Route::get('/audyty/rodzaje', function () {
        return redirect()->route('audits.types', ['tab' => 'energetyczne']);
    })
        ->middleware('role:admin,auditor');
    Route::patch('/audyty/ustawienia/rodzaje/{auditType}', [AuditsController::class, 'updateAuditType'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.audit-type-update');
    Route::post('/audyty/ustawienia/jednostki', [AuditsController::class, 'storeUnit'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.unit-store');
    Route::get('/audyty/ustawienia/jednostki', function () {
        return redirect()->route('audits.types', ['tab' => 'ustawienia']);
    })
        ->middleware('role:admin,auditor');
    Route::patch('/audyty/ustawienia/jednostki/{unit}', [AuditsController::class, 'updateUnit'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.unit-update');
    Route::delete('/audyty/ustawienia/jednostki/{unit}', [AuditsController::class, 'destroyUnit'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.unit-destroy');
    Route::delete('/audyty/ustawienia/rodzaje/{auditType}', [AuditsController::class, 'destroyAuditType'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.audit-type-destroy');
    Route::post('/audyty/ustawienia/rodzaje/{auditType}/kopiuj', [AuditsController::class, 'copyAuditType'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.audit-type-copy');
    Route::patch('/audyty/ustawienia/iso50001/template', [AuditsController::class, 'updateIso50001Template'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.iso50001.template-update');
    Route::post('/audyty/ustawienia/iso50001/audits', [AuditsController::class, 'storeIso50001Audit'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.iso50001.audit-store');
    Route::patch('/audyty/ustawienia/iso50001/audits/{isoAudit}', [AuditsController::class, 'updateIso50001Audit'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.iso50001.audit-update');

    // Questionnaire questions management (admin)
    Route::post('/audyty/ustawienia/iso50001/kwestionariusz', [AuditsController::class, 'storeQuestionnaireQuestion'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.iso50001.questionnaire-store');
    Route::patch('/audyty/ustawienia/iso50001/kwestionariusz/{question}', [AuditsController::class, 'updateQuestionnaireQuestion'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.iso50001.questionnaire-update');
    Route::delete('/audyty/ustawienia/iso50001/kwestionariusz/{question}', [AuditsController::class, 'destroyQuestionnaireQuestion'])
        ->middleware('role:admin,auditor')
        ->name('audits.settings.iso50001.questionnaire-destroy');

    Route::post('/audyty/ai-agent/{agentType}/trening', [AiAgentController::class, 'saveAgentTraining'])
        ->middleware('role:admin,auditor')
        ->name('audits.ai-agent.train')
        ->where('agentType', 'general|compressor_room|boiler_room|drying_room|buildings|technological_processes|iso50001|bc_general|bc_compressor_room|bc_boiler_room|bc_drying_room|bc_buildings|bc_technological_processes');
    Route::post('/audyty/ai-agent/{agentType}/reset', [AiAgentController::class, 'resetAgentTraining'])
        ->middleware('role:admin,auditor')
        ->name('audits.ai-agent.reset')
        ->where('agentType', 'general|compressor_room|boiler_room|drying_room|buildings|technological_processes|iso50001|bc_general|bc_compressor_room|bc_boiler_room|bc_drying_room|bc_buildings|bc_technological_processes');

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

    // ── Oferty ──────────────────────────────────────────────────────────────
    Route::middleware('role:admin,auditor')->prefix('oferty')->name('offers.')->group(function () {
        Route::get('/portfolio',         [OffersController::class, 'portfolio'])   ->name('portfolio');
        Route::get('/nowa',              [OffersController::class, 'create'])      ->name('create');
        Route::post('/',                 [OffersController::class, 'store'])       ->name('store');
        Route::get('/w-toku',            [OffersController::class, 'inprogress'])  ->name('inprogress');
        Route::get('/zarchiwizowane',    [OffersController::class, 'archived'])    ->name('archived');
        Route::get('/ustawienia',        [OffersController::class, 'settings'])    ->name('settings');
        Route::post('/ustawienia',       [OffersController::class, 'saveSettings'])->name('settings.save');
        Route::post('/kopiuj-dla-firmy/{company}', [OffersController::class, 'copyForCompany'])->name('copyForCompany');
        Route::post('/{offer}/archiwizuj', [OffersController::class, 'archive'])  ->name('archive');
        Route::post('/{offer}/kopiuj',   [OffersController::class, 'copy'])       ->name('copy');
        Route::post('/{offer}/wyslij-do-klienta', [OffersController::class, 'sendToClient'])->name('sendToClient');
        Route::get('/{offer}/edytuj',    [OffersController::class, 'edit'])       ->name('edit');
        Route::put('/{offer}',           [OffersController::class, 'update'])     ->name('update');
        Route::delete('/{offer}',        [OffersController::class, 'destroy'])    ->name('destroy');
        Route::get('/{offer}/pdf',       [OffersController::class, 'generatePdf'])->name('generatePdf');
        Route::get('/{offer}/word',      [OffersController::class, 'generateWord'])->name('generateWord');
    });

    // ── Zapytania klientów ───────────────────────────────────────────────
    Route::patch('/zapytania/{inquiry}/accept', [CompanyController::class, 'acceptInquiry'])
        ->middleware('role:admin,auditor')->name('inquiry.accept');
    Route::patch('/zapytania/{inquiry}/reject', [CompanyController::class, 'rejectInquiry'])
        ->middleware('role:admin,auditor')->name('inquiry.reject');

    // ── Chat admin → klient ──────────────────────────────────────────────
    Route::post('/chat/{company}', [CompanyController::class, 'sendChatMessage'])
        ->middleware('role:admin,auditor')->name('chat.admin.send');
    Route::post('/chat/{company}/ajax', [CompanyController::class, 'sendChatMessageAjax'])
        ->middleware('role:admin,auditor')->name('chat.admin.send.ajax');
    Route::get('/chat/{company}/poll', [CompanyController::class, 'pollChat'])
        ->middleware('role:admin,auditor')->name('chat.admin.poll');
    Route::post('/strefa-klienta/chat', [ClientController::class, 'sendChat'])
        ->middleware('role:client')->name('client.chat.send');
    Route::post('/strefa-klienta/chat/ajax', [ClientController::class, 'sendChatAjax'])
        ->middleware('role:client')->name('client.chat.send.ajax');
    Route::get('/strefa-klienta/chat/poll', [ClientController::class, 'pollChat'])
        ->middleware('role:client')->name('client.chat.poll');
    Route::post('/strefa-klienta/zapytanie/{inquiry}/akceptuj-oferte', [ClientController::class, 'acceptOffer'])
        ->middleware('role:client')->name('client.offer.accept');
    Route::get('/strefa-klienta/oferta/{offer}/pdf', [ClientController::class, 'downloadOfferPdf'])
        ->middleware('role:client')->name('client.offer.pdf');
    Route::get('/moje-audyty/{audit}/ai', [ClientController::class, 'startAuditAi'])
        ->middleware('role:client')->name('client.audit.ai');
    Route::get('/moje-audyty/{audit}/kwestionariusz-iso', [ClientController::class, 'showIsoQuestionnaire'])
        ->middleware('role:client')->name('client.audit.iso.questionnaire');
    Route::post('/moje-audyty/{audit}/kwestionariusz-iso', [ClientController::class, 'saveIsoQuestionnaire'])
        ->middleware('role:client')->name('client.audit.iso.questionnaire.save');
    Route::get('/moje-audyty/{audit}/praca/{conversation}', [ClientController::class, 'auditWork'])
        ->middleware('role:client')->name('client.audit.work');
    Route::post('/moje-audyty/{audit}/praca/{conversation}/zakoncz', [ClientController::class, 'finishAuditAi'])
        ->middleware('role:client')->name('client.audit.finish');
    Route::get('/moje-audyty/{audit}/edytuj', [ClientController::class, 'editAuditData'])
        ->middleware('role:client')->name('client.audit.edit');
    Route::post('/moje-audyty/{audit}/edytuj', [ClientController::class, 'updateAuditData'])
        ->middleware('role:client')->name('client.audit.update');
    // ────────────────────────────────────────────────────────────────────────
    Route::post('/strefa-klienta/zapytanie', [ClientController::class, 'storeInquiry'])
        ->middleware('role:client')
        ->name('client.inquiry.store');
    Route::get('/iso50001', [Iso50001AuditController::class, 'index'])
        ->name('iso50001.index');
    Route::post('/iso50001', [Iso50001AuditController::class, 'store'])
        ->middleware('role:admin,auditor,client')
        ->name('iso50001.store');
    Route::get('/iso50001/{isoAudit}/kwestionariusz', [Iso50001AuditController::class, 'showQuestionnaire'])
        ->middleware('role:client')
        ->name('iso50001.questionnaire');
    Route::post('/iso50001/{isoAudit}/kwestionariusz', [Iso50001AuditController::class, 'saveQuestionnaire'])
        ->middleware('role:client')
        ->name('iso50001.questionnaire.save');
    Route::get('/iso50001/{isoAudit}/krok/{step}', [Iso50001AuditController::class, 'showStep'])
        ->name('iso50001.step');
    Route::patch('/iso50001/{isoAudit}/krok/{step}', [Iso50001AuditController::class, 'saveStep'])
        ->name('iso50001.step.update');
    Route::patch('/iso50001/{isoAudit}/submit', [Iso50001AuditController::class, 'submit'])
        ->middleware('role:client')
        ->name('iso50001.submit');
    Route::get('/iso50001/{isoAudit}/review', [Iso50001AuditController::class, 'review'])
        ->name('iso50001.review');
    Route::patch('/iso50001/{isoAudit}/review', [Iso50001AuditController::class, 'updateReview'])
        ->middleware('role:admin,auditor')
        ->name('iso50001.review.update');

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

    Route::patch('/settings/energy-indicators', [SettingsController::class, 'updateEnergyIndicators'])
        ->middleware('role:admin,super_admin')
        ->name('settings.energy-indicators');

    Route::patch('/settings/public-access', [SettingsController::class, 'updatePublicAccess'])
        ->middleware('role:admin,super_admin')
        ->name('settings.public-access');

    Route::patch('/settings/my-company', [SettingsController::class, 'updateMyCompany'])
        ->middleware('role:admin')
        ->name('settings.my-company');

    Route::post('/settings/co2-history', [SettingsController::class, 'storeCo2History'])
        ->middleware('role:admin,super_admin')
        ->name('settings.co2-history-store');

    Route::delete('/settings/co2-history/{history}', [SettingsController::class, 'destroyCo2History'])
        ->middleware('role:admin,super_admin')
        ->name('settings.co2-history-destroy');

    // Registration management (admin)
    Route::post('/rejestracja/{id}/akceptuj', [RegistrationController::class, 'accept'])
        ->middleware('role:admin,super_admin')
        ->name('register.accept');
    Route::delete('/rejestracja/{id}', [RegistrationController::class, 'destroy'])
        ->middleware('role:admin,super_admin')
        ->name('register.destroy');

    // Company management
    Route::get('/firmy/{company}', [CompanyController::class, 'show'])
        ->middleware('role:admin,auditor')
        ->name('firma.show');
    Route::post('/firmy/{company}/audyt', [CompanyController::class, 'storeAudit'])
        ->middleware('role:admin,auditor')
        ->name('firma.storeAudit');
    Route::post('/firmy/{company}/klient', [CompanyController::class, 'storeClient'])
        ->middleware('role:admin,auditor')
        ->name('firma.storeClient');
    Route::get('/firmy/{company}/audyt/{audit}', [CompanyController::class, 'showAudit'])
        ->middleware('role:admin,auditor')
        ->name('firma.audit');
    Route::patch('/firmy/{company}/audyt/{audit}/status', [CompanyController::class, 'updateStatus'])
        ->middleware('role:admin,auditor')
        ->name('firma.updateStatus');
    Route::delete('/firmy/{company}/audyt/{audit}', [CompanyController::class, 'destroyAudit'])
        ->middleware('role:admin,auditor')
        ->name('firma.destroyAudit');
    Route::get('/firmy/{company}/audyt/{audit}/raport', [CompanyController::class, 'report'])
        ->middleware('role:admin,auditor')
        ->name('firma.report');
    Route::post('/firmy/{company}/uzytkownik', [CompanyController::class, 'addUser'])
        ->middleware('role:admin,auditor')
        ->name('firma.addUser');
    Route::delete('/firmy/{company}/uzytkownik/{user}', [CompanyController::class, 'removeUser'])
        ->middleware('role:admin,auditor')
        ->name('firma.removeUser');
    Route::post('/firmy/{company}/uzytkownik/{user}/mail', [CompanyController::class, 'resendMail'])
        ->middleware('role:admin,auditor')
        ->name('firma.resendMail');

    // AI Agent
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/', [AiAgentController::class, 'index'])->name('index');
        Route::get('/nowa', [AiAgentController::class, 'create'])->name('create');
        Route::post('/', [AiAgentController::class, 'store'])->name('store');
        Route::get('/{aiConversation}', [AiAgentController::class, 'show'])->name('show');
        Route::post('/{aiConversation}/wiadomosc', [AiAgentController::class, 'sendMessage'])->name('message');
        Route::post('/{aiConversation}/plik', [AiAgentController::class, 'analyzeFile'])->name('file');
        Route::delete('/{aiConversation}', [AiAgentController::class, 'destroy'])->name('destroy');
        Route::delete('/{aiConversation}/usun', [AiAgentController::class, 'forceDelete'])->name('force-delete');
        Route::post('/analiza', [AiAgentController::class, 'analyzeAudit'])->name('analyze');
        Route::post('/{aiConversation}/protokol', [AiAgentController::class, 'generateProtocol'])->name('protocol.generate');
        Route::post('/{aiConversation}/rekomendacje', [AiAgentController::class, 'generateRecommendations'])->name('recommendations.generate');
        Route::get('/{aiConversation}/protokol', [AiAgentController::class, 'protocol'])->name('protocol');
        Route::get('/{aiConversation}/protokol/pdf', [AiAgentController::class, 'downloadPdf'])->name('protocol.pdf');
        Route::get('/{aiConversation}/protokol/podglad', [AiAgentController::class, 'previewPdf'])->name('protocol.preview');
    });
});
