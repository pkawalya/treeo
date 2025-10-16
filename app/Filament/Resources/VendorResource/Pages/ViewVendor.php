<?php

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewVendor extends ViewRecord
{
    protected static string $resource = VendorResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Vendor Details')
                    ->schema([
                        TextEntry::make('name')
                            ->icon('heroicon-o-building-storefront')
                            ->columnSpan(2),
                        TextEntry::make('vendor_type')
                            ->badge(),
                        IconEntry::make('is_active')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        TextEntry::make('contact_person')
                            ->icon('heroicon-o-user')
                            ->default('N/A'),
                        TextEntry::make('email')
                            ->icon('heroicon-o-envelope')
                            ->default('N/A'),
                        TextEntry::make('phone')
                            ->icon('heroicon-o-phone'),
                        TextEntry::make('city')
                            ->default('N/A'),
                        TextEntry::make('payment_terms')
                            ->badge()
                            ->color('info'),
                        TextEntry::make('credit_limit')
                            ->money('UGX')
                            ->default('N/A'),
                        TextEntry::make('bank_name')
                            ->default('N/A'),
                        TextEntry::make('vendor_rating')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'A' => 'success',
                                'B' => 'info',
                                'C' => 'warning',
                                'D', 'F' => 'danger',
                                default => 'gray',
                            })
                            ->default('N/A'),
                    ])
                    ->columns(4)
                    ->compact()
                    ->icon('heroicon-o-building-storefront'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
