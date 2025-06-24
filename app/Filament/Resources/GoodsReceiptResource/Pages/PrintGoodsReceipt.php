<?php

namespace App\Filament\Resources\GoodsReceiptResource\Pages;

use App\Filament\Resources\GoodsReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;

class PrintGoodsReceipt extends Page
{
    protected static string $resource = GoodsReceiptResource::class;
    protected static string $view = 'filament.resources.goods-receipt-resource.pages.print-goods-receipt';
    
    public $record;
    public $goodsReceipt;
    public $items;
    public $organization;

    public function mount($record): void
    {
        $this->record = $record;
        $this->goodsReceipt = \App\Models\GoodsReceipt::with([
            'purchaseOrder',
            'purchaseOrder.vendor',
            'items',
            'items.seedling',
            'items.purchaseOrderItem'
        ])->findOrFail($record);
        
        $this->items = $this->goodsReceipt->items;
        $this->organization = [
            'name' => config('app.name', 'TreeO'),
            'address' => '123 Forest Drive, Kampala, Uganda',
            'phone' => '+256 700 000000',
            'email' => 'info@treeo.org',
            'logo' => asset('images/logo.png'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action('print')
                ->extraAttributes(['onclick' => 'window.print()']),
        ];
    }

    public function print(): void
    {
        $this->dispatch('print');
    }
}
