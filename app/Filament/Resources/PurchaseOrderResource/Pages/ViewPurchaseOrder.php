<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Purchase Order Details')
                    ->schema([
                        TextEntry::make('po_number')
                            ->icon('heroicon-o-document-text')
                            ->copyable(),
                        TextEntry::make('vendor.name')
                            ->icon('heroicon-o-building-storefront'),
                        TextEntry::make('order_date')
                            ->date(),
                        TextEntry::make('expected_delivery_date')
                            ->label('Expected Delivery')
                            ->date(),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'sent' => 'warning',
                                'received' => 'success',
                                'cancelled' => 'danger',
                            }),
                        TextEntry::make('total_amount')
                            ->money('UGX'),
                        TextEntry::make('payment_terms')
                            ->default('N/A')
                            ->columnSpan(2),
                    ])
                    ->columns(4)
                    ->compact()
                    ->icon('heroicon-o-shopping-cart'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('print')
                ->label('Print PO')
                ->icon('heroicon-o-printer')
                ->url(route('purchase-orders.print', $this->record))
                ->openUrlInNewTab(),
            Actions\Action::make('send')
                ->label('Send to Vendor')
                ->icon('heroicon-o-paper-airplane')
                ->action(fn () => $this->record->update(['status' => 'sent']))
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'draft')
                ->color('warning'),
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->record->update([
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);
                })
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'sent' && !$this->record->approved_at)
                ->color('success'),
            Actions\Action::make('cancel')
                ->label('Cancel PO')
                ->icon('heroicon-o-x-circle')
                ->action(fn () => $this->record->update(['status' => 'cancelled']))
                ->requiresConfirmation()
                ->visible(fn () => in_array($this->record->status, ['draft', 'sent']))
                ->color('danger'),
        ];
    }
}
