<?php

namespace App\Filament\Resources\CrmDealResource\Pages;

use App\Filament\Resources\CrmDealResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCrmDeal extends CreateRecord
{
    protected static string $resource = CrmDealResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
