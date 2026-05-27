<?php

namespace App\Filament\Resources\CrmDealResource\Pages;

use App\Filament\Resources\CrmDealResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCrmDeal extends EditRecord
{
    protected static string $resource = CrmDealResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make(), Actions\RestoreAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
