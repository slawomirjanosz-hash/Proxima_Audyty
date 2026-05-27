<?php

namespace App\Filament\Resources\CrmStageResource\Pages;

use App\Filament\Resources\CrmStageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCrmStages extends ListRecords
{
    protected static string $resource = CrmStageResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
