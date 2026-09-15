<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Customer;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use filament\Forms\Components\hiddenlabel;
use Filament\Schemas\Schema;
use App\Models\Product;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;


class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Order Information')
                            ->schema([
                                DateTimePicker::make('order_date')
                                    ->label('Date:')
                                    ->default(now())
                                    ->disabled()
                                    ->prefix('Date:')
                                    ->required()
                                    ->hiddenlabel(),

                                Section::make('Customer Information')
                                    ->schema([
                                        Select::make('customer_id')
                                            ->label('Customer')
                                            ->relationship('customer', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $customer = Customer::find($state);
                                                    if ($customer) {
                                                        $set('phone', $customer->phone);
                                                        $set('address', $customer->address);
                                                    }
                                                } else {
                                                    $set('phone', null);
                                                    $set('address', null);
                                                }
                                            })
                                            ->required(),

                                        Placeholder::make('phone')
                                            ->content(fn(Get $get) => Customer::find($get('customer_id'))?->phone ?? '-')
                                            ->label('Phone')
                                            ->disabled()
                                            ->dehydrated(false),

                                        Placeholder::make('address')
                                            ->content(fn(Get $get) => Customer::find($get('customer_id'))?->address ?? '-')
                                            ->label('Address')
                                            ->disabled()
                                            ->dehydrated(false),
                                    ])
                                    ->columns(3),

                                Section::make('Order Details')
                                    ->schema([
                                        Repeater::make('order_details')
                                            ->relationship('orderdetails')
                                            ->schema([
                                                Select::make('product_id')
                                                    ->label('Product')
                                                    ->relationship(
                                                        'product',
                                                        'name',
                                                        modifyQueryUsing: fn($query, Get $get) => $query->where(function ($q) use ($get) {
                                                            $q->where('stock', '>', 0);
                                                            if ($currentProductId = $get('product_id')) {
                                                                $q->orWhere('id', $currentProductId);
                                                            }
                                                        }),
                                                    )
                                                    ->getOptionLabelUsing(fn($value): ?string => Product::find($value)?->name)
                                                    ->searchable()
                                                    ->preload()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                    ->live()
                                                    
                                                    ->required()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        $product = $state ? Product::find($state) : null;
                                                        $price = $product->price ?? 0;
                                                        $quantity = $get('quantity') ?: 1;
                                                        $subtotal = $price * $quantity;

                                                        $set('price', $price);
                                                        $set('quantity', $quantity);
                                                        $set('subtotal', $subtotal);

                                                        $items = $get('../../order_details') ?? [];
                                                        $total = collect($items)->sum(fn($item) => $item['subtotal'] ?? 0);

                                                        $set('../../total_price', $total);
                                                        $discount = (float) ($get('../../discount') ?: 0);
                                                        $discountAmount = ($discount / 100) * $total;

                                                        $set('../../discount_amount', $discountAmount);
                                                        $set('../../total_payment', $total - $discountAmount);
                                                    }),

                                                TextInput::make('quantity')
                                                    ->label('Quantity')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->minValue(1)
                                                    ->helperText(function (Get $get): string {
                                                        $productId = $get('product_id');

                                                        if (! $productId) {
                                                            return 'Pilih produk untuk melihat stok tersedia.';
                                                        }

                                                        $product = Product::query()->find($productId);

                                                        if (! $product) {
                                                            return 'Produk tidak ditemukan.';
                                                        }

                                                        $qty = (int) ($get('quantity') ?: 0);
                                                        $original = 0;
                                                        $detailId = $get('id');

                                                        if ($detailId && $detail = \App\Models\OrderDetail::query()->find($detailId)) {
                                                            $original = (int) $detail->quantity;
                                                        }

                                                        $available = (int) $product->stock + $original;
                                                        $remaining = $available - $qty;

                                                        if ($remaining < 0) {
                                                            return "Stok tidak cukup! Tersedia: {$available}, kurang " . abs($remaining) . '.';
                                                        }

                                                        return "Stok tersedia: {$available} | Sisa setelah diambil {$qty}: {$remaining}";
                                                    })
                                                    ->maxValue(function (Get $get): ?int {
                                                        $productId = $get('product_id');

                                                        if (! $productId) {
                                                            return null;
                                                        }

                                                        $stock = Product::query()->find($productId)?->stock ?? 0;
                                                        $detailId = $get('id');

                                                        if ($detailId && $detail = \App\Models\OrderDetail::query()->find($detailId)) {
                                                            $stock += (int) $detail->quantity;
                                                        }

                                                        return (int) $stock;
                                                    })
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                        $quantity = (float) ($state ?: 0);
                                                        $price = (float) ($get('price') ?: 0);
                                                        $set('subtotal', $price * $quantity);

                                                        $items = $get('../../order_details') ?? [];
                                                        $total = collect($items)->sum(fn($item) => (float) ($item['subtotal'] ?? 0));
                                                        $set('../../total_price', $total);
                                                        $discount = (float) ($get('../../discount') ?: 0);
                                                        $discountAmount = ($discount / 100) * $total;

                                                        $set('../../discount_amount', $discountAmount);
                                                        $set('../../total_payment', $total - $discountAmount);
                                                    }),

                                                TextInput::make('price')
                                                    ->label('Price')
                                                    ->numeric()
                                                    ->required()
                                                    ->live()
                                                    ->default(0)
                                                    ->disabled()
                                                    ->prefix('Rp. ')
                                                    ->formatStateUsing(fn($state, Get $get) => $state ?? Product::find($get('product_id'))?->price ?? 0),

                                                TextInput::make('subtotal')
                                                    ->label('Subtotal')
                                                    ->numeric()
                                                    ->disabled()
                                                    ->default(0)
                                                    ->readOnly()
                                                    ->prefix('Rp. ')
                                                    ->dehydrated(),
                                            ])
                                            ->columns(2)
                                        ->hiddenlabel(),
                                    ]),
                            ])
                            ->columnSpan(2),

                        Section::make('Payment Information')
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'new' => 'New',
                                        'processing' => 'Processing',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('new')
                                    ->required(),

                                TextInput::make('total_price')
                                    ->label('Total price')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $totalPrice = (float) ($state ?: 0);
                                        $discount = (float) ($get('discount') ?: 0);
                                        $discountAmount = ($discount / 100) * $totalPrice;

                                        $set('discount_amount', $discountAmount);
                                        $set('total_payment', $totalPrice - $discountAmount);
                                    })
                                    ->prefix('Rp. '),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('discount')
                                            ->label('Discount')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('%')
                                            ->reactive()
                                            ->default(0)
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $discount = (float) ($state ?: 0);
                                                $totalPrice = (float) ($get('total_price') ?: 0);
                                                $discountAmount = ($discount / 100) * $totalPrice;

                                                $set('discount_amount', $discountAmount);
                                                $set('total_payment', $totalPrice - $discountAmount);
                                            }),

                                        TextInput::make('discount_amount')
                                            ->label('Discount Amount')
                                            ->numeric()
                                            ->disabled()
                                            ->default(0)
                                            ->prefix('Rp. ')
                                            ->dehydrated(),
                                    ]),

                                TextInput::make('total_payment')
                                    ->label('Total Payment')
                                    ->numeric()
                                    ->disabled()
                                    ->default(0)
                                    ->dehydrated()
                                    ->prefix('Rp. '),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
