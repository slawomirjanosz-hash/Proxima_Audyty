<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientController;
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

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/strefa-klienta', [ClientController::class, 'index'])->name('strefa-klienta');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    Route::patch('/settings/users/{user}/role', [SettingsController::class, 'updateRole'])
        ->middleware('role:admin')
        ->name('settings.user-role');

    Route::patch('/settings/users/{user}/access', [SettingsController::class, 'updateUserAccess'])
        ->middleware('role:admin')
        ->name('settings.user-access');

    Route::patch('/settings/companies/{company}/assignments', [SettingsController::class, 'updateCompanyAssignments'])
        ->middleware('role:admin')
        ->name('settings.company-assignments');
});
