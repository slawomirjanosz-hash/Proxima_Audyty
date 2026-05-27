<?php

namespace App\Filament\Resources\CrmCompanyResource\Pages;

use App\Filament\Resources\CrmCompanyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCrmCompany extends CreateRecord
{
    protected static string $resource = CrmCompanyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
