<?php

namespace App\Filament\Resources\EnergyAuditResource\Pages;

use App\Filament\Resources\EnergyAuditResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEnergyAudit extends EditRecord
{
    protected static string $resource = EnergyAuditResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
