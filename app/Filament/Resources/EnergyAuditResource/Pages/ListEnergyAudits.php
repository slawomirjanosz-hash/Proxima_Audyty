<?php

namespace App\Filament\Resources\EnergyAuditResource\Pages;

use App\Filament\Resources\EnergyAuditResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEnergyAudits extends ListRecords
{
    protected static string $resource = EnergyAuditResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
