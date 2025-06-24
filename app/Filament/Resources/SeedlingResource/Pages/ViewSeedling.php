<?php

namespace App\Filament\Resources\SeedlingResource\Pages;

use App\Filament\Resources\SeedlingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSeedling extends ViewRecord
{
    protected static string $resource = SeedlingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
