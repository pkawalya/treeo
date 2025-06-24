<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->canBeDeleted()),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => $this->record->canBeDeleted()),
            Actions\RestoreAction::make(),
            Actions\Action::make('print')
                ->label('Print PO')
                ->icon('heroicon-o-printer')
                ->url(route('purchase-orders.print', $this->record))
                ->openUrlInNewTab(),
        ];
    }

    protected function afterSave(): void
    {
        // Recalculate totals after saving
        $this->record->calculateTotals();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
