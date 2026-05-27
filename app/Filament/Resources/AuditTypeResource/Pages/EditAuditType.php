<?php

namespace App\Filament\Resources\AuditTypeResource\Pages;

use App\Filament\Resources\AuditTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAuditType extends EditRecord
{
    protected static string $resource = AuditTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
