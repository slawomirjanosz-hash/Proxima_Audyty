<?php

namespace App\Filament\Widgets;

use App\Models\ClientRegistration;
use App\Models\Company;
use App\Models\CrmTask;
use App\Models\EnergyAudit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeAudits = EnergyAudit::whereIn('status', EnergyAudit::ACTIVE_STATUSES)->count();
        $companies    = Company::count();
        $overdueTasks = CrmTask::where('status', '!=', 'zakonczone')
            ->where('status', '!=', 'anulowane')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->count();
        $pendingRegs  = ClientRegistration::where('status', 'pending')->count();

        return [
            Stat::make('Aktywne audyty', $activeAudits)
                ->description('W trakcie realizacji')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('info'),

            Stat::make('Firmy klientów', $companies)
                ->description('Łącznie w systemie')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('success'),

            Stat::make('Zadania przeterminowane', $overdueTasks)
                ->description('CRM – wymaga uwagi')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($overdueTasks > 0 ? 'danger' : 'success'),

            Stat::make('Nowe rejestracje', $pendingRegs)
                ->description('Oczekują na akceptację')
                ->descriptionIcon('heroicon-o-user-plus')
                ->color($pendingRegs > 0 ? 'warning' : 'gray'),
        ];
    }
}
