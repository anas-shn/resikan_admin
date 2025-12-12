<?php

namespace App\Filament\Widgets;

use App\Models\BookingItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ServicePopularityWidget extends ChartWidget
{
    protected static ?string $heading = 'Layanan Terpopuler';
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Get top 5 services by booking count
        $topServices = BookingItem::select('service_id', DB::raw('SUM(quantity) as total'))
            ->groupBy('service_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->with('service')
            ->get();

        $labels = $topServices->map(function ($item) {
            return $item->service->name ?? 'Unknown';
        })->toArray();

        $data = $topServices->pluck('total')->toArray();

        // If no data, return empty
        if (empty($data)) {
            return [
                'datasets' => [
                    [
                        'label' => 'Total Pesanan',
                        'data' => [1],
                        'backgroundColor' => ['rgba(156, 163, 175, 0.8)'],
                    ],
                ],
                'labels' => ['Belum ada data'],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pesanan',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(249, 115, 22)',
                        'rgb(139, 92, 246)',
                        'rgb(236, 72, 153)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
