<?php

namespace App\Filament\Resources\MonitoringResource\Pages;

use App\Filament\Resources\MonitoringResource;
use Filament\Actions;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewMonitoring extends ViewRecord
{
    protected static string $resource = MonitoringResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Monitoring Details')
                    ->schema([
                        TextEntry::make('distribution.recipient.name')
                            ->label('Recipient')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('distribution.seedling.name')
                            ->label('Seedling')
                            ->icon('heroicon-o-sparkles'),
                        TextEntry::make('growth_stage')
                            ->badge(),
                        TextEntry::make('observation_date')
                            ->date(),
                        TextEntry::make('supervisor.name')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('notes')
                            ->default('N/A')
                            ->columnSpan(3),
                    ])
                    ->columns(4)
                    ->compact()
                    ->icon('heroicon-o-clipboard-document-check'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
