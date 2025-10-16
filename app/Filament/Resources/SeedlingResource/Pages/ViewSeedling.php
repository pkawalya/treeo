<?php

namespace App\Filament\Resources\SeedlingResource\Pages;

use App\Filament\Resources\SeedlingResource;
use Filament\Actions;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewSeedling extends ViewRecord
{
    protected static string $resource = SeedlingResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Seedling Details')
                    ->schema([
                        ImageEntry::make('image_url')
                            ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=7F9CF5&background=EBF4FF')
                            ->circular()
                            ->size(80),
                        TextEntry::make('name')
                            ->icon('heroicon-o-sparkles')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        TextEntry::make('type')
                            ->badge(),
                        TextEntry::make('description')
                            ->default('N/A')
                            ->columnSpan(2),
                    ])
                    ->columns(4)
                    ->compact()
                    ->icon('heroicon-o-sparkles'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
