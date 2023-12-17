<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected static ?int $sort = 4;

    protected function getData(): array
    {

        $data = Order::query()
            ->toBase()
            ->selectRaw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) AS pending')
            ->selectRaw('SUM(CASE WHEN status = "processing" THEN 1 ELSE 0 END) AS processing')
            ->selectRaw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) AS completed')
            ->selectRaw('SUM(CASE WHEN status = "declined" THEN 1 ELSE 0 END) AS declined')
            ->first();

//        $dataByEloquent = Order::query()
//            ->selectRaw('status, COUNT(*) as count')
//            ->whereIn('status', OrderStatusEnum::cases())
//            ->groupBy('status')
//            ->pluck('count', 'status')
//            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => array_values((array)$data),
                ],
            ],
            'labels' => OrderStatusEnum::cases(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
