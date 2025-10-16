<?php

namespace App\Filament\Resources\FarmerResource\Pages;

use App\Filament\Resources\FarmerResource;
use App\Filament\Resources\FarmerResource\Widgets\FarmerDistributionStats;
use App\Filament\Resources\FarmerResource\Widgets\FarmerLocationMap;
use App\Filament\Resources\FarmerResource\Widgets\FarmerProfileCard;
use App\Filament\Resources\FarmerResource\Widgets\RecentDistributions;
use App\Filament\Resources\FarmerResource\Widgets\SeedlingDistributionChart;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewFarmer extends ViewRecord
{
    protected static string $resource = FarmerResource::class;
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Farmer Details')
                    ->schema([
                        TextEntry::make('name')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('phone')
                            ->icon('heroicon-o-phone'),
                        TextEntry::make('email')
                            ->default('N/A'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'danger',
                            }),
                        TextEntry::make('district')
                            ->icon('heroicon-o-map-pin'),
                        TextEntry::make('sub_county'),
                        TextEntry::make('parish'),
                        TextEntry::make('village'),
                    ])
                    ->columns(4)
                    ->compact()
                    ->icon('heroicon-o-user-circle'),
            ]);
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil')
                ->color('success'),
            Actions\Action::make('download_report')
                ->label('Download Report')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->url(fn () => '#'),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            FarmerDistributionStats::class,
        ];
    }
    
    protected function getFooterWidgets(): array
    {
        return [
            SeedlingDistributionChart::class,
            RecentDistributions::class,
            FarmerLocationMap::class,
        ];
    }
}
