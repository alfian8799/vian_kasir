<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Customer;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class CustomersWidget extends ChartWidget
{
    protected static ?int $sort = 2;
    protected ?string $heading = 'Customers Chart';

    protected function getData(): array
    {
        $data = Trend::model(Customer::class)
            ->between(
                start: now()->subMonths(6),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Customers',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#36A2EB',          // Warna garis biru
                    'backgroundColor' => '#9BD0F5',      // Warna latar/area bawah garis
                    'fill' => true,                      // Mengaktifkan warna area di bawah garis
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => date('M Y', strtotime($value->date))),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}