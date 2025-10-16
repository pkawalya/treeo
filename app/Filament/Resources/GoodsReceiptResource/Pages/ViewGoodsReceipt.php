<?php

namespace App\Filament\Resources\GoodsReceiptResource\Pages;

use App\Filament\Resources\GoodsReceiptResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewGoodsReceipt extends ViewRecord
{
    protected static string $resource = GoodsReceiptResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Goods Receipt Details')
                    ->schema([
                        TextEntry::make('grn_number')
                            ->icon('heroicon-o-document-text')
                            ->copyable(),
                        TextEntry::make('purchaseOrder.po_number')
                            ->label('PO Number')
                            ->icon('heroicon-o-shopping-cart'),
                        TextEntry::make('purchaseOrder.vendor.name')
                            ->label('Vendor')
                            ->icon('heroicon-o-building-storefront'),
                        TextEntry::make('receipt_date')
                            ->date(),
                        TextEntry::make('received_by')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'received' => 'warning',
                                'inspected' => 'success',
                                'rejected' => 'danger',
                                'cancelled' => 'danger',
                            }),
                        TextEntry::make('total_quantity')
                            ->numeric(),
                        TextEntry::make('total_amount')
                            ->money('UGX'),
                    ])
                    ->columns(4)
                    ->compact()
                    ->icon('heroicon-o-inbox-arrow-down'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('print')
                ->label('Print GRN')
                ->icon('heroicon-o-printer')
                ->url(route('goods-receipts.print', $this->record))
                ->openUrlInNewTab(),
            Actions\Action::make('receive')
                ->label('Mark as Received')
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->record->update(['status' => 'received']);
                })
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'draft')
                ->color('success'),
            Actions\Action::make('inspect')
                ->label('Mark as Inspected')
                ->icon('heroicon-o-clipboard-document-check')
                ->action(function () {
                    $this->record->update(['status' => 'inspected']);
                    $this->record->updateInventory();
                })
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'received')
                ->color('success'),
        ];
    }
}
