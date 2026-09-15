<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomersForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                TextInput::make('address')
                    ->label('alamat')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('No. Hp')
                    ->tel()
                    ->required()
                    ->maxLength(15),
            ]);
    }
}