<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;

class StatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected function getStats(): array
    {
        return [
            Stat::make('Total Order', Order::count())
                ->description('Total orders placed')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success')
                ->icon('heroicon-o-shopping-cart'),

            Stat::make('Total Customer', Customer::count())
                ->description('Active registered customers')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([3, 5, 8, 12, 15, 20, 25])
                ->color('info')
                ->icon('heroicon-o-user-circle'),

            Stat::make('Total Product', Product::count())
                ->description('Items available in stock')
                ->descriptionIcon('heroicon-m-cube')
                ->chart([10, 12, 14, 15, 18, 20, 22])
                ->color('warning')
                ->icon('heroicon-o-shopping-cart'), 

            Stat::make('Total Revenue', 'Rp ' . number_format(Order::sum('total_price'), 0, ',', '.'))
                ->description('Overall income generated')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([15, 25, 20, 35, 40, 50, 65])
                ->color('success')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}