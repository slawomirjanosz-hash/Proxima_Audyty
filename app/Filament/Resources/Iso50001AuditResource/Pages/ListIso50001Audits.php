<?php

namespace App\Filament\Resources\Iso50001AuditResource\Pages;

use App\Filament\Resources\Iso50001AuditResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIso50001Audits extends ListRecords
{
    protected static string $resource = Iso50001AuditResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
