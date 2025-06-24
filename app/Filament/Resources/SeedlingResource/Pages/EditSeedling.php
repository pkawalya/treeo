<?php

namespace App\Filament\Resources\SeedlingResource\Pages;

use App\Filament\Resources\SeedlingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeedling extends EditRecord
{
    protected static string $resource = SeedlingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
