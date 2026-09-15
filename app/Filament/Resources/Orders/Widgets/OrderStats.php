<?php

namespace App\Filament\Resources\Orders\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;

class OrderStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $newOrders = Order::where('status', 'new')->count();
        $totalPayment = Order::where('status', 'completed')->sum('total_payment');
        $completedOrders = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        $processingOrders = Order::where('status', 'processing')->count();

       return [
        Stat::make('New Orders', $newOrders)
            ->description('New Orders Waiting for Process')
            ->descriptionIcon('heroicon-m-clock')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('info'),

        Stat::make('Processing Orders', $processingOrders)
            ->description('Orders Being Processed')
            ->descriptionIcon('heroicon-m-arrow-path')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('warning'),  
       
        Stat::make('Completed Orders', $completedOrders)
            ->description('Completed Orders')
            ->descriptionIcon('heroicon-m-check')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),

         Stat::make('Total Payment', 'Rp. ' . number_format($totalPayment, 0, ',', '.'))
            ->description('Total Payment')
            ->descriptionIcon('heroicon-m-banknotes')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),
            
        ];
    }
}
