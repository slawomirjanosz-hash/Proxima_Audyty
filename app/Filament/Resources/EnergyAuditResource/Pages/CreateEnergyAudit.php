<?php

namespace App\Filament\Resources\EnergyAuditResource\Pages;

use App\Filament\Resources\EnergyAuditResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEnergyAudit extends CreateRecord
{
    protected static string $resource = EnergyAuditResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
