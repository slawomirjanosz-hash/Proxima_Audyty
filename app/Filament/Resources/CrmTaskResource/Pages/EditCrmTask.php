<?php

namespace App\Filament\Resources\CrmTaskResource\Pages;

use App\Filament\Resources\CrmTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCrmTask extends EditRecord
{
    protected static string $resource = CrmTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make(), Actions\RestoreAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
