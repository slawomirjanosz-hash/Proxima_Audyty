<?php

namespace App\Filament\Resources\CrmTaskResource\Pages;

use App\Filament\Resources\CrmTaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCrmTask extends CreateRecord
{
    protected static string $resource = CrmTaskResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
