<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrderItem;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ReceivePurchaseOrder extends Page
{
    protected static string $resource = PurchaseOrderResource::class;
    protected static string $view = 'filament.resources.purchase-order-resource.pages.receive-purchase-order';

    public ?array $data = [];
    public $purchaseOrder;
    public $goodsReceipt;

    public function mount($record): void
    {
        $this->purchaseOrder = $this->getResource()::getModel()::with(['items.seedling', 'goodsReceipts.items'])->findOrFail($record);
        
        // Create a new goods receipt if none exists
        if ($this->purchaseOrder->goodsReceipts->isEmpty()) {
            $this->goodsReceipt = $this->purchaseOrder->goodsReceipts()->create([
                'grn_number' => 'GRN-' . date('Ymd') . '-' . strtoupper(uniqid()),
                'receipt_date' => now()->toDateString(),
                'received_by' => auth()->user()->name,
                'status' => 'draft',
                'total_quantity' => 0,
            ]);

            // Create receipt items from PO items
            foreach ($this->purchaseOrder->items as $item) {
                $this->goodsReceipt->items()->create([
                    'purchase_order_item_id' => $item->id,
                    'seedling_id' => $item->seedling_id,
                    'quantity_ordered' => $item->quantity,
                    'quantity_received' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                    'batch_number' => 'BATCH-' . strtoupper(uniqid()),
                    'condition' => 'good',
                ]);
            }
        } else {
            $this->goodsReceipt = $this->purchaseOrder->goodsReceipts->first();
        }

        $this->form->fill([
            'grn_number' => $this->goodsReceipt->grn_number,
            'receipt_date' => $this->goodsReceipt->receipt_date,
            'received_by' => $this->goodsReceipt->received_by,
            'status' => $this->goodsReceipt->status,
            'notes' => $this->goodsReceipt->notes,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('grn_number')
                    ->label('GRN Number')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\DatePicker::make('receipt_date')
                    ->required(),
                Forms\Components\TextInput::make('received_by')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'received' => 'Received',
                        'inspected' => 'Inspected',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->goodsReceipt->items()->getQuery())
            ->columns([
                Tables\Columns\TextColumn::make('seedling.name')
                    ->label('Seedling'),
                Tables\Columns\TextColumn::make('quantity_ordered')
                    ->label('Ordered Qty')
                    ->numeric(),
                Tables\Columns\TextColumn::make('quantity_received')
                    ->label('Received Qty')
                    ->numeric(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('UGX'),
                Tables\Columns\TextColumn::make('total')
                    ->money('UGX'),
                Tables\Columns\TextColumn::make('batch_number')
                    ->label('Batch #'),
                Tables\Columns\TextColumn::make('condition')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'good' => 'success',
                        'damaged' => 'warning',
                        'expired' => 'danger',
                        default => 'gray',
                    }),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $this->goodsReceipt->update($data);
        
        if ($data['status'] === 'received') {
            $this->purchaseOrder->update(['status' => 'received']);
            $this->goodsReceipt->updateInventory();
        }
        
        $this->dispatch('saved');
        
        $this->notification()->success(
            __('filament-panels::resources/pages/edit-record.notifications.saved.title'),
            body: __('filament-panels::resources/pages/edit-record.notifications.saved.body'),
        );
        
        $this->redirect($this->getResource()::getUrl('view', ['record' => $this->purchaseOrder]));
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save')
                ->keyBindings(['mod+s']),
            Actions\Action::make('cancel')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
                ->url($this->getResource()::getUrl('view', ['record' => $this->purchaseOrder]))
                ->color('gray'),
        ];
    }

    public function getBreadcrumbs(): array
    {
        $resource = $this->getResource();

        $breadcrumb = $this->getBreadcrumb();

        return [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('view', ['record' => $this->purchaseOrder]) => $this->purchaseOrder->po_number,
            $resource::getUrl('receive', ['record' => $this->purchaseOrder]) => $breadcrumb,
        ];
    }

    public function getTitle(): string
    {
        return __('Receive Purchase Order #:number', ['number' => $this->purchaseOrder->po_number]);
    }

    public function getBreadcrumb(): string
    {
        return __('Receive Items');
    }
}
