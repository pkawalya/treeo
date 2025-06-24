<?php

namespace App\Filament\Resources\GoodsReceiptResource\Pages;

use App\Filament\Resources\GoodsReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGoodsReceipt extends ViewRecord
{
    protected static string $resource = GoodsReceiptResource::class;

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
                    $this->refreshFormData(['status']);
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
                    $this->refreshFormData(['status']);
                })
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'received')
                ->color('success'),
        ];
    }
}
