<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

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
                    $this->refreshFormData(['approved_by', 'approved_at']);
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['items'] = $this->record->items->map(function ($item) {
            return [
                'seedling_id' => $item->seedling_id,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
                'description' => $item->description,
            ];
        })->toArray();

        return $data;
    }
}
