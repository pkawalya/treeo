<?php

namespace App\Filament\Resources\DistributionResource\Pages;

use App\Filament\Resources\DistributionResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewDistribution extends ViewRecord
{
    protected static string $resource = DistributionResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Distribution Details')
                    ->schema([
                        TextEntry::make('recipient_type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'farmer' => 'success',
                                'community' => 'info',
                            }),
                        TextEntry::make('recipient.name')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('seedling.name')
                            ->icon('heroicon-o-sparkles'),
                        TextEntry::make('quantity')
                            ->numeric(),
                        TextEntry::make('cost')
                            ->money('UGX'),
                        TextEntry::make('distribution_date')
                            ->date(),
                        TextEntry::make('distributor.name')
                            ->label('Distributor'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                            }),
                        TextEntry::make('notes')
                            ->default('N/A')
                            ->columnSpanFull(),
                    ])
                    ->columns(4)
                    ->compact()
                    ->icon('heroicon-o-truck'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
