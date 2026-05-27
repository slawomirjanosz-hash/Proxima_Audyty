<?php

namespace App\Filament\Resources\CrmCustomerTypeResource\Pages;

use App\Filament\Resources\CrmCustomerTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCrmCustomerType extends CreateRecord
{
    protected static string $resource = CrmCustomerTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
