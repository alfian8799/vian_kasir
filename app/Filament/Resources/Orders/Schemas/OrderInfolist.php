<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('Order ID'),
                TextEntry::make('customer.name')
                    ->label('Customer'),
                TextEntry::make('total_price')
                    ->prefix('Rp. ')
                    ->default(0),
                TextEntry::make('discount')
                    ->label('Discount (%)')
                    ->default(0),
                TextEntry::make('discount_amount')
                    ->label('Discount Amount')
                    ->prefix('Rp. ')
                    ->default(0),
                TextEntry::make('total_payment')
                    ->label('Total Payment')
                    ->prefix('Rp. ')
                    ->default(0),
                TextEntry::make('status')
                    ->label('Status'),
                TextEntry::make('order_date')
                    ->label('Order Date')
                    ->dateTime()
                    ->default(now()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
