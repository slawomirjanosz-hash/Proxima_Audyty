<?php

namespace App\Filament\Resources\CrmCustomerTypeResource\Pages;

use App\Filament\Resources\CrmCustomerTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCrmCustomerTypes extends ListRecords
{
    protected static string $resource = CrmCustomerTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
