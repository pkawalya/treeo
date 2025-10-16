<?php

namespace App\Filament\Resources\FarmerResource\Pages;

use App\Filament\Resources\FarmerResource;
use App\Filament\Resources\FarmerResource\Widgets\FarmerDistributionStats;
use App\Filament\Resources\FarmerResource\Widgets\FarmerLocationMap;
use App\Filament\Resources\FarmerResource\Widgets\FarmerProfileCard;
use App\Filament\Resources\FarmerResource\Widgets\RecentDistributions;
use App\Filament\Resources\FarmerResource\Widgets\SeedlingDistributionChart;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFarmer extends ViewRecord
{
    protected static string $resource = FarmerResource::class;
    
    protected function hasInfolist(): bool
    {
        return false;
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
    
    
    protected function getFooterWidgets(): array
    {
        return [
            SeedlingDistributionChart::class,
            FarmerDistributionStats::class,
            RecentDistributions::class,
            FarmerLocationMap::class,
        ];
    }
}
