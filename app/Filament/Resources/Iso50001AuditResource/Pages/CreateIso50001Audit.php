<?php

namespace App\Filament\Resources\Iso50001AuditResource\Pages;

use App\Filament\Resources\Iso50001AuditResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIso50001Audit extends CreateRecord
{
    protected static string $resource = Iso50001AuditResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
