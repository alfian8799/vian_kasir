<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LastOrders extends TableWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = '1/2';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(5))
            ->columns([
                TextColumn::make('id')->label('Order ID')->sortable(),
                TextColumn::make('customer.name')->label('Customer'),
                TextColumn::make('total_payment')->label('Total')->prefix('Rp. '),
                TextColumn::make('status')->badge()->color(fn ($state) => match ($state) {
                    'new' => 'info',
                    'processing' => 'warning',
                    'completed' => 'success',
                    'cancelled' => 'danger',
                    default => 'gray',
                }),
                TextColumn::make('order_date')->date(),
            ]);
    }
}
