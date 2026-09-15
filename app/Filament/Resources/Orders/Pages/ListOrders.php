<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Orders\Widgets\OrderStats;
use Filament\Schemas\Components\Tabs\Tab;
use App\Models\Order;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()
                ->query(fn ($query) => $query->where('status', '!=', 'cancelled'))
                ->label('All')
                ->icon('heroicon-m-list-bullet')
                ->badge(Order::where('status', '!=', 'cancelled')->count())
                ->badgeColor('gray'),
            'New' => Tab::make()
                ->query(fn ($query) => $query->where('status', 'new'))
                ->label('New')
                ->icon('heroicon-m-document-text')
                ->badge(Order::where('status', 'new')->count())
                ->badgeColor('info'),
            'Processing' => Tab::make()
                ->query(fn ($query) => $query->where('status', 'processing'))
                ->label('Processing')
                ->icon('heroicon-m-arrow-path')
                ->badge(Order::where('status', 'processing')->count())
                ->badgeColor('warning'),
            'Completed' => Tab::make()
                ->query(fn ($query) => $query->where('status', 'completed'))
                ->label('Completed')
                ->icon('heroicon-m-check-circle')
                ->badge(Order::where('status', 'completed')->count())
                ->badgeColor('success'),
            'Cancelled' => Tab::make()
                ->query(fn ($query) => $query->where('status', 'cancelled'))
                ->label('Cancelled')
                ->icon('heroicon-m-x-circle')
                ->badge(Order::where('status', 'cancelled')->count())
                ->badgeColor('danger'),
        ];
    }

    

    
}
