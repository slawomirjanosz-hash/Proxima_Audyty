<?php

namespace App\Filament\Resources\CrmStageResource\Pages;

use App\Filament\Resources\CrmStageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCrmStage extends CreateRecord
{
    protected static string $resource = CrmStageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
