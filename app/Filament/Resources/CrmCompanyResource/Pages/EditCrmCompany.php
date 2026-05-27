<?php

namespace App\Filament\Resources\CrmCompanyResource\Pages;

use App\Filament\Resources\CrmCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCrmCompany extends EditRecord
{
    protected static string $resource = CrmCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make(), Actions\RestoreAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
