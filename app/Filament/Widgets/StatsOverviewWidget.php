<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        // Total Users
        $totalUsers = User::count();
        $usersLastMonth = User::where('created_at', '<', now()->startOfMonth())->count();
        $usersTrend = $usersLastMonth > 0 
            ? round((($totalUsers - $usersLastMonth) / $usersLastMonth) * 100, 1)
            : 0;

        // Total Bookings This Month
        $bookingsThisMonth = Booking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $bookingsLastMonth = Booking::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        $bookingsTrend = $bookingsLastMonth > 0
            ? round((($bookingsThisMonth - $bookingsLastMonth) / $bookingsLastMonth) * 100, 1)
            : 0;

        // Revenue This Month
        $revenueThisMonth = Payment::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount') ?? 0;
        $revenueLastMonth = Payment::where('status', 'paid')
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('amount') ?? 0;
        $revenueTrend = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 0;

        // Pending Bookings
        $pendingBookings = Booking::where('status', 'pending')->count();

        return [
            Stat::make('Total Pengguna', number_format($totalUsers, 0, ',', '.'))
                ->description($usersTrend >= 0 ? "+{$usersTrend}% dari bulan lalu" : "{$usersTrend}% dari bulan lalu")
                ->descriptionIcon($usersTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($usersTrend >= 0 ? 'success' : 'danger')
                ->chart([7, 8, 10, 12, 15, 18, 20]),

            Stat::make('Booking Bulan Ini', number_format($bookingsThisMonth, 0, ',', '.'))
                ->description($bookingsTrend >= 0 ? "+{$bookingsTrend}% dari bulan lalu" : "{$bookingsTrend}% dari bulan lalu")
                ->descriptionIcon($bookingsTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($bookingsTrend >= 0 ? 'success' : 'danger')
                ->chart([5, 10, 8, 12, 15, 18, 20]),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($revenueThisMonth, 0, ',', '.'))
                ->description($revenueTrend >= 0 ? "+{$revenueTrend}% dari bulan lalu" : "{$revenueTrend}% dari bulan lalu")
                ->descriptionIcon($revenueTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueTrend >= 0 ? 'success' : 'danger')
                ->chart([10000, 15000, 20000, 25000, 30000, 35000, 40000]),

            Stat::make('Booking Pending', number_format($pendingBookings, 0, ',', '.'))
                ->description('Menunggu konfirmasi')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([3, 5, 4, 6, 7, 5, 4]),
        ];
    }
}
