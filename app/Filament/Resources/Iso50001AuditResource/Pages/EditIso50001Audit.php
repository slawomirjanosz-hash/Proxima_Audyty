<?php

namespace App\Filament\Resources\Iso50001AuditResource\Pages;

use App\Filament\Resources\Iso50001AuditResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIso50001Audit extends EditRecord
{
    protected static string $resource = Iso50001AuditResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
