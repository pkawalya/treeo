<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\InventoryResource;

class ViewInventory extends ViewRecord
{
    protected static string $resource = InventoryResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Inventory Details')
                    ->schema([
                        TextEntry::make('name')
                            ->icon('heroicon-o-cube'),
                        TextEntry::make('sku')
                            ->default('N/A'),
                        TextEntry::make('category.name')
                            ->badge()
                            ->default('N/A'),
                        TextEntry::make('quantity')
                            ->numeric(),
                        TextEntry::make('unit_of_measure')
                            ->label('Unit')
                            ->badge(),
                        TextEntry::make('unit_cost')
                            ->money('UGX')
                            ->default('N/A'),
                        TextEntry::make('reorder_level')
                            ->numeric(),
                        TextEntry::make('last_stocked_at')
                            ->label('Last Stocked')
                            ->dateTime()
                            ->default('N/A'),
                    ])
                    ->columns(4)
                    ->compact()
                    ->icon('heroicon-o-cube'),
            ]);
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
