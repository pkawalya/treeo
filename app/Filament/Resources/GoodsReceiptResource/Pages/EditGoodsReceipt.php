<?php

namespace App\Filament\Resources\GoodsReceiptResource\Pages;

use App\Filament\Resources\GoodsReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGoodsReceipt extends EditRecord
{
    protected static string $resource = GoodsReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->status === 'draft'),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => $this->record->status === 'draft'),
            Actions\RestoreAction::make(),
            Actions\Action::make('print')
                ->label('Print GRN')
                ->icon('heroicon-o-printer')
                ->url(route('goods-receipts.print', $this->record))
                ->openUrlInNewTab(),
        ];
    }

    protected function afterSave(): void
    {
        // Update the total quantity if items were modified
        if ($this->record->wasChanged()) {
            $this->record->update([
                'total_quantity' => $this->record->items()->sum('quantity_received')
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
