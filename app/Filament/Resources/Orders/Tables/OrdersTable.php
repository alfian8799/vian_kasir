<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Order ID')
                    ->sortable(),
                TextColumn::make('customer.name')
                 ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->numeric(),
                TextColumn::make('total_price')
                    ->label('Price')
                    ->prefix('Rp. ')
                    ->numeric(
                        decimalPlaces: 0,
                        decimalSeparator: ',',
                        thousandsSeparator: '.',
                    )
                    ->sortable(),   
                TextColumn::make('discount')
                ->label('Discount (%)')
                ->suffix('%')
                ->default(0),  
                TextColumn::make('discount_amount')
                ->label('Discount Amount')
                ->prefix('Rp. ')
                ->default(0)  
                ->numeric(
                        decimalPlaces: 0,
                        decimalSeparator: ',',
                        thousandsSeparator: '.',
                    ),
                TextColumn::make('total_payment')
                ->label('Total Payment')
                ->prefix('Rp. ')

                ->numeric(
                        decimalPlaces: 0,
                        decimalSeparator: ',',
                        thousandsSeparator: '.',
                    )
                ->default(0),   
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'new'        => 'info',
                        'processing' => 'warning',
                        'completed'  => 'success',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    }),


                TextColumn::make('order_date')
                    ->label('Order Date')
                    ->date()
                    ->sortable()
                    ->default(now()),

                
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
          ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('cancel')
                        ->label('Cancel')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status !== 'cancelled')
                        ->action(fn ($record) => $record->update(['status' => 'cancelled'])),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([]),
            ]);
    }
}