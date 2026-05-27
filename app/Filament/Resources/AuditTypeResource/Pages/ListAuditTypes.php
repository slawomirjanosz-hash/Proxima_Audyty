<?php

namespace App\Filament\Resources\AuditTypeResource\Pages;

use App\Filament\Resources\AuditTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAuditTypes extends ListRecords
{
    protected static string $resource = AuditTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
