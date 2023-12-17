<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ProductsChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = $this->getProductsPerMonth();

        return [
            'datasets' => [
                [
                    'label' => 'Products created',
                    'data' => $data['productsPerMonth']
                ]
            ],
            'labels' => $data['months']
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getProductsPerMonthAtPreviousYear(): array
    {
        $now = now();

        // Calculate the start and end date for the 12 months.
        $startMonth = $now->copy()->subMonths(11)->startOfMonth();
        $endMonth = $now->copy()->endOfMonth();

        // Get the counts for each month.
        $counts = Product::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', $startMonth)
            ->where('created_at', '<=', $endMonth)
            ->groupBy('month')
            ->pluck('count', 'month');

        $months = [];
        $productsPerMonth = [];

        // Loop through the 12 months and populate the counts and month names.
        for ($i = 0; $i < 12; $i++) {
            $currentMonth = $startMonth->copy()->addMonths($i);
            $monthKey = $currentMonth->format('Y-m');
            $months[] = $currentMonth->format('M');
            $productsPerMonth[] = $counts->get($monthKey, 0); // Use 0 if no count is found.
        }

        return [
            'productsPerMonth' => $productsPerMonth,
            'months' => $months,
        ];
    }

    private function getProductsPerMonth(): array
    {
        $now = now();

        // Calculate the start and end dates for the current year.
        $startMonth = $now->copy()->startOfYear();
        $endMonth = $now->copy()->endOfYear();

        // Get the counts for each month.
        $counts = Product::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', $startMonth)
            ->where('created_at', '<=', $endMonth)
            ->groupBy('month')
            ->pluck('count', 'month');

        $months = [];
        $productsPerMonth = [];

        // Loop through the 12 months and populate the counts and month names.
        for ($i = 0; $i < 12; $i++) {
            $currentMonth = $startMonth->copy()->addMonths($i);
            $monthKey = $currentMonth->format('Y-m');
            $months[] = $currentMonth->format('M');
            $productsPerMonth[] = $counts->get($monthKey, 0); // Use 0 if no count is found.
        }

        return [
            'productsPerMonth' => $productsPerMonth,
            'months' => $months,
        ];
    }
}
