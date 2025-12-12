<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BookingsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Grafik Booking (30 Hari Terakhir)';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        return [
            'all' => 'Semua',
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    protected function getData(): array
    {
        // Get last 30 days
        $dates = collect();
        for ($i = 29; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        // Get bookings per day
        $query = Booking::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date');

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        $bookingsData = $query->get()->pluck('count', 'date');

        // Fill missing dates with 0
        $data = $dates->map(function ($date) use ($bookingsData) {
            return $bookingsData->get($date, 0);
        });

        return [
            'datasets' => [
                [
                    'label' => 'Booking',
                    'data' => $data->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $dates->map(function ($date) {
                return date('d/m', strtotime($date));
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
