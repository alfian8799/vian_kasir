<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CustomersInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nama Pelanggan'),

                TextEntry::make('address')
                    ->label('Alamat'),

                TextEntry::make('phone')
                    ->label('No. HP'),

                TextEntry::make('created_at')
                    ->label('Dibuat Pada')
                    ->timezone('Asia/Jakarta')
                    ->dateTime('d F Y H:i'),

                TextEntry::make('updated_at')
                    ->timezone('Asia/Jakarta')
                    ->label('Diperbarui Pada')
                    ->dateTime('d F Y H:i'),
            ]);
    }
}
