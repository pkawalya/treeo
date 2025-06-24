<?php

namespace App\Filament\Resources\MonitoringResource\Pages;

use App\Filament\Resources\MonitoringResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMonitoring extends CreateRecord
{
    protected static string $resource = MonitoringResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
