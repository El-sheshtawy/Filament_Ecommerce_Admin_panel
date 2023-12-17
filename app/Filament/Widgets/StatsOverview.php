<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

   protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Total customers', '192 K')
                ->description('This is the total count of company customers.')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),

            Stat::make('Total products', Product::count())
                ->description('This is the total count of company products.')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Pending orders', Order::query()->whereStatus(OrderStatusEnum::PENDING->value)->count())
                ->description('This is the total count of pending orders.')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color('warning'),
        ];
    }
}
