<?php

namespace App\Filament\Resources\SeedlingResource\Pages;

use App\Filament\Resources\SeedlingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSeedlings extends ListRecords
{
    protected static string $resource = SeedlingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
