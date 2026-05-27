<?php

namespace App\Filament\Resources\AuditTypeResource\Pages;

use App\Filament\Resources\AuditTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuditType extends CreateRecord
{
    protected static string $resource = AuditTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
