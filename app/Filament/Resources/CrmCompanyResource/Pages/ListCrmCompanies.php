<?php

namespace App\Filament\Resources\CrmCompanyResource\Pages;

use App\Filament\Resources\CrmCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCrmCompanies extends ListRecords
{
    protected static string $resource = CrmCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
