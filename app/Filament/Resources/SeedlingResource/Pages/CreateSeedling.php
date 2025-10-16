<?php

namespace App\Filament\Resources\SeedlingResource\Pages;

use App\Filament\Resources\SeedlingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSeedling extends CreateRecord
{
    protected static string $resource = SeedlingResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure growth_stages is always an array
        if (isset($data['growth_stages']) && !is_array($data['growth_stages'])) {
            $data['growth_stages'] = [];
        }
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
