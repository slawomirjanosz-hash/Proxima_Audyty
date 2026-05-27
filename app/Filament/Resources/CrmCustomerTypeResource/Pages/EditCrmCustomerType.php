<?php

namespace App\Filament\Resources\CrmCustomerTypeResource\Pages;

use App\Filament\Resources\CrmCustomerTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCrmCustomerType extends EditRecord
{
    protected static string $resource = CrmCustomerTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
