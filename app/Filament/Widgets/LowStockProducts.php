<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;


class LowStockProducts extends TableWidget
{
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('stock', '<', 10)
                    ->orderBy('stock', 'asc') 
                    ->limit(10)
            )
            ->columns([
                ImageColumn::make('image')->label('Image')->disk('public')->square(),
                TextColumn::make('name')->label('Product'),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->badge()
                    ->color(fn ($state) => $state <= 0 ? 'danger' : ($state <= 5 ? 'danger' : 'warning'))
                    ->formatStateUsing(fn ($state) => $state <= 0 ? 'Out of Stock (0)' : $state)
                    ->sortable(),
                TextColumn::make('price')->label('Price')->prefix('Rp. '),
            ])
            ->heading('Low Stock & Out of Stock Products')
            ->description('Products that are out of stock or need restocking');
    }
}