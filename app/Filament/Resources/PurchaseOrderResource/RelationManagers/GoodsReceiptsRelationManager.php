<?php

namespace App\Filament\Resources\PurchaseOrderResource\RelationManagers;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GoodsReceiptsRelationManager extends RelationManager
{
    protected static string $relationship = 'goodsReceipts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('grn_number')
                    ->label('GRN Number')
                    ->default('GRN-' . date('Ymd') . '-' . strtoupper(uniqid()))
                    ->required()
                    ->maxLength(50)
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\DatePicker::make('receipt_date')
                    ->required()
                    ->default(now()->toDateString()),
                Forms\Components\TextInput::make('received_by')
                    ->required()
                    ->maxLength(255)
                    ->default(auth()->user()->name),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'received' => 'Received',
                        'inspected' => 'Inspected',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('draft')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('grn_number')
            ->columns([
                Tables\Columns\TextColumn::make('grn_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('receipt_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'received' => 'info',
                        'inspected' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'received' => 'Received',
                        'inspected' => 'Inspected',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['purchase_order_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    })
                    ->using(function (array $data, string $model): Model {
                        return DB::transaction(function () use ($data, $model) {
                            $goodsReceipt = $model::create($data);
                            
                            // Create receipt items from PO items
                            $poItems = $this->getOwnerRecord()->items;
                            $receiptItems = [];
                            
                            foreach ($poItems as $item) {
                                $receiptItems[] = [
                                    'purchase_order_item_id' => $item->id,
                                    'seedling_id' => $item->seedling_id,
                                    'quantity_ordered' => $item->quantity,
                                    'quantity_received' => $item->quantity,
                                    'unit_price' => $item->unit_price,
                                    'total' => $item->total,
                                    'batch_number' => 'BATCH-' . strtoupper(uniqid()),
                                    'condition' => 'good',
                                ];
                            }
                            
                            $goodsReceipt->items()->createMany($receiptItems);
                            
                            return $goodsReceipt;
                        });
                    })
                    ->visible(fn () => $this->getOwnerRecord()->status === 'sent' || $this->getOwnerRecord()->status === 'received'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Model $record): string => route('filament.admin.resources.goods-receipts.view', ['record' => $record])),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Model $record): bool => $record->status === 'draft')
                    ->url(fn (Model $record): string => route('filament.admin.resources.goods-receipts.edit', ['record' => $record])),
                Tables\Actions\Action::make('receive')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (Model $record) {
                        $record->update(['status' => 'received']);
                        $record->updateInventory();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Model $record): bool => $record->status === 'draft')
                    ->color('success'),
                Tables\Actions\Action::make('inspect')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->action(fn (Model $record) => $record->update(['status' => 'inspected']))
                    ->requiresConfirmation()
                    ->visible(fn (Model $record): bool => $record->status === 'received')
                    ->color('success'),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->required(),
                    ])
                    ->action(function (array $data, Model $record): void {
                        $record->update([
                            'status' => 'rejected',
                            'notes' => ($record->notes ? $record->notes . "\n\n" : '') . 'Rejection Reason: ' . $data['rejection_reason'],
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Model $record): bool => in_array($record->status, ['received', 'draft']))
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => $this->getOwnerRecord()->canBeEdited()),
                ]),
            ]);
    }
}
