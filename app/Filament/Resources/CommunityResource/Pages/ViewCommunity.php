<?php

namespace App\Filament\Resources\CommunityResource\Pages;

use App\Filament\Resources\CommunityResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewCommunity extends ViewRecord
{
    protected static string $resource = CommunityResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Community Details')
                    ->schema([
                        TextEntry::make('name')
                            ->icon('heroicon-o-users'),
                        TextEntry::make('leader_name')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('contact')
                            ->icon('heroicon-o-phone'),
                        TextEntry::make('member_count')
                            ->numeric(),
                        TextEntry::make('district')
                            ->icon('heroicon-o-map-pin'),
                        TextEntry::make('sub_county'),
                        TextEntry::make('parish'),
                        TextEntry::make('village'),
                    ])
                    ->columns(4)
                    ->compact()
                    ->icon('heroicon-o-users'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
