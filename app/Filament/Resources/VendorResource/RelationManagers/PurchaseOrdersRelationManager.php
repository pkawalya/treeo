<?php

namespace App\Filament\Resources\VendorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseOrders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('order_date')
                    ->required()
                    ->default(now()->toDateString()),
                Forms\Components\DatePicker::make('expected_delivery_date')
                    ->required()
                    ->default(now()->addWeek()->toDateString()),
                Forms\Components\Textarea::make('delivery_terms')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('payment_terms')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('po_number')
            ->columns([
                Tables\Columns\TextColumn::make('po_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'warning',
                        'received' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total')
                    ->money('UGX')
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'received' => 'Received',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('approved')
                    ->label('Approval Status')
                    ->options([
                        '1' => 'Approved',
                        '0' => 'Not Approved',
                    ])
                    ->query(fn (Builder $query, string $value) => 
                        $value === '1' 
                            ? $query->whereNotNull('approved_at')
                            : $query->whereNull('approved_at')
                    ),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['vendor_id'] = $this->getOwnerRecord()->id;
                        $data['status'] = 'draft';
                        return $data;
                    })
                    ->using(function (array $data, string $model): Model {
                        return $this->getRelationship()->create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Model $record): string => route('filament.admin.resources.purchase-orders.view', ['record' => $record])),
                Tables\Actions\EditAction::make()
                    ->url(fn (Model $record): string => route('filament.admin.resources.purchase-orders.edit', ['record' => $record]))
                    ->visible(fn (Model $record): bool => $record->status === 'draft'),
                Tables\Actions\Action::make('approve')
                    ->action(fn (Model $record) => $record->update([
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]))
                    ->requiresConfirmation()
                    ->visible(fn (Model $record): bool => $record->status === 'sent' && !$record->approved_at)
                    ->color('success'),
                Tables\Actions\Action::make('mark_received')
                    ->action(fn (Model $record) => $record->update(['status' => 'received']))
                    ->requiresConfirmation()
                    ->visible(fn (Model $record): bool => $record->status === 'sent' && $record->approved_at)
                    ->color('success'),
                Tables\Actions\Action::make('cancel')
                    ->action(fn (Model $record) => $record->update(['status' => 'cancelled']))
                    ->requiresConfirmation()
                    ->visible(fn (Model $record): bool => in_array($record->status, ['draft', 'sent']))
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
