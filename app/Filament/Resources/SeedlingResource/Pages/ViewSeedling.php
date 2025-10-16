<?php

namespace App\Filament\Resources\SeedlingResource\Pages;

use App\Filament\Resources\SeedlingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSeedling extends ViewRecord
{
    protected static string $resource = SeedlingResource::class;
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure growth_stages is always an array
        if (isset($data['growth_stages'])) {
            if (is_string($data['growth_stages'])) {
                $data['growth_stages'] = json_decode($data['growth_stages'], true) ?? [];
            } elseif (!is_array($data['growth_stages'])) {
                $data['growth_stages'] = [];
            }
        } else {
            $data['growth_stages'] = [];
        }
        
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
