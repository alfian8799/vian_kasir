<?php

namespace App\Filament\Widgets;

use App\Models\OrderDetail;
use App\Models\Product;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class BestSelling extends TableWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = '1/2';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->select('products.*')
                    ->selectSub(
                        OrderDetail::query()
                            ->selectRaw('SUM(quantity)')
                            ->whereColumn('product_id', 'products.id'),
                        'total_sold'
                    )
                    ->orderByDesc('total_sold')
                    ->limit(5)
            )
            ->columns([
                ImageColumn::make('image')->label('Image')->disk('public')->square(),
                TextColumn::make('name')->label('Product'),
                TextColumn::make('total_sold')->label('Total Sold')->numeric()->sortable()->default(0),
                TextColumn::make('price')->label('Price')->prefix('Rp. '),
                TextColumn::make('stock')->label('Current Stock'),
            ]);
    }
}
