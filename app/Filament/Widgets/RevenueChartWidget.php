<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan (12 Bulan Terakhir)';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Get last 12 months
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(now()->subMonths($i));
        }

        // Get revenue per month using PostgreSQL syntax
        $revenueData = Payment::select(
            DB::raw("EXTRACT(YEAR FROM paid_at) as year"),
            DB::raw("EXTRACT(MONTH FROM paid_at) as month"),
            DB::raw('SUM(amount) as total')
        )
        ->where('status', 'paid')
        ->where('paid_at', '>=', now()->subMonths(12))
        ->groupBy(DB::raw("EXTRACT(YEAR FROM paid_at)"), DB::raw("EXTRACT(MONTH FROM paid_at)"))
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT) => $item->total];
        });

        // Fill missing months with 0
        $data = $months->map(function ($month) use ($revenueData) {
            $key = $month->format('Y-m');
            return $revenueData->get($key, 0);
        });

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                        'rgba(244, 63, 94, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                    ],
                ],
            ],
            'labels' => $months->map(function ($month) {
                return $month->format('M Y');
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
