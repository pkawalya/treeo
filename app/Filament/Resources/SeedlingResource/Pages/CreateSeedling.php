<?php

namespace App\Filament\Resources\SeedlingResource\Pages;

use App\Filament\Resources\SeedlingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSeedling extends CreateRecord
{
    protected static string $resource = SeedlingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
