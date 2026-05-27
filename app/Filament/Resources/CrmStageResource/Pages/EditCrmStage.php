<?php

namespace App\Filament\Resources\CrmStageResource\Pages;

use App\Filament\Resources\CrmStageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCrmStage extends EditRecord
{
    protected static string $resource = CrmStageResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
