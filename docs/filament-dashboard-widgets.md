# Filament Dashboard & Widgets - Implementation Guide

## 📊 Overview
Dokumen ini berisi struktur lengkap untuk Dashboard dan Widgets Filament Admin Panel.

---

## Dashboard Layout

### File: `app/Filament/Pages/Dashboard.php`

```php
<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Dashboard';
    
    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 3,
            'lg' => 4,
            'xl' => 4,
            '2xl' => 4,
        ];
    }
    
    public function getWidgets(): array
    {
        return [
            // Stats Overview (4 cards)
            Widgets\StatsOverviewWidget::class,
            
            // Charts (2 columns)
            [
                Widgets\BookingsChartWidget::class,
                Widgets\RevenueChartWidget::class,
            ],
            
            // Service Popularity & Recent Bookings
            [
                Widgets\ServicePopularityWidget::class,
                Widgets\LatestBookingsWidget::class,
            ],
            
            // Top Users
            Widgets\TopUsersWidget::class,
        ];
    }
}
```

---

## 1. Stats Overview Widget

### File: `app/Filament/Widgets/StatsOverviewWidget.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        // Calculate stats
        $currentMonth = now()->startOfMonth();
        $previousMonth = now()->subMonth()->startOfMonth();
        
        // Total Users
        $totalUsers = User::count();
        $usersLastMonth = User::where('created_at', '<', $currentMonth)->count();
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
            ->sum('amount');
        $revenueLastMonth = Payment::where('status', 'paid')
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('amount');
        $revenueTrend = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 0;
        
        // Pending Bookings
        $pendingBookings = Booking::where('status', 'pending')->count();
        $pendingYesterday = Booking::where('status', 'pending')
            ->whereDate('created_at', '<', now()->startOfDay())
            ->count();
        $pendingChange = $pendingBookings - $pendingYesterday;
        
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
                ->description($pendingChange > 0 ? "+{$pendingChange} booking baru" : ($pendingChange < 0 ? "{$pendingChange} booking" : "Tidak ada perubahan"))
                ->descriptionIcon($pendingChange > 0 ? 'heroicon-m-arrow-trending-up' : ($pendingChange < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->color($pendingChange > 0 ? 'warning' : ($pendingChange < 0 ? 'success' : 'gray'))
                ->chart([3, 5, 4, 6, 7, 5, 4]),
        ];
    }
    
    protected static ?string $pollingInterval = '60s';
}
```

---

## 2. Bookings Chart Widget

### File: `app/Filament/Widgets/BookingsChartWidget.php`

```php
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
```

---

## 3. Revenue Chart Widget

### File: `app/Filament/Widgets/RevenueChartWidget.php`

```php
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
    
    public ?string $filter = 'all';
    
    protected function getFilters(): ?array
    {
        return [
            'all' => 'Semua Metode',
            'cash' => 'Cash',
            'transfer' => 'Transfer',
            'e-wallet' => 'E-Wallet',
            'credit_card' => 'Kartu Kredit',
        ];
    }
    
    protected function getData(): array
    {
        // Get last 12 months
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(now()->subMonths($i));
        }
        
        // Get revenue per month
        $query = Payment::select(
            DB::raw('YEAR(paid_at) as year'),
            DB::raw('MONTH(paid_at) as month'),
            DB::raw('SUM(amount) as total')
        )
        ->where('status', 'paid')
        ->where('paid_at', '>=', now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month');
        
        if ($this->filter !== 'all') {
            $query->where('method', $this->filter);
        }
        
        $revenueData = $query->get()->mapWithKeys(function ($item) {
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
                    'ticks' => [
                        'callback' => 'function(value) { return "Rp " + value.toLocaleString("id-ID"); }',
                    ],
                ],
            ],
        ];
    }
}
```

---

## 4. Service Popularity Widget

### File: `app/Filament/Widgets/ServicePopularityWidget.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Models\Service;
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
```

---

## 5. Latest Bookings Widget

### File: `app/Filament/Widgets/LatestBookingsWidget.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Filament\Resources\BookingResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestBookingsWidget extends BaseWidget
{
    protected static ?string $heading = 'Booking Terbaru';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')
                    ->label('No. Booking')
                    ->searchable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('user.fullname')
                    ->label('Pelanggan')
                    ->searchable()
                    ->limit(20),
                    
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Jadwal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'confirmed',
                        'primary' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Booking $record): string => BookingResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye')
                    ->label('Lihat'),
            ]);
    }
}
```

---

## 6. Top Users Widget

### File: `app/Filament/Widgets/TopUsersWidget.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Filament\Resources\UserResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopUsersWidget extends BaseWidget
{
    protected static ?string $heading = 'Pengguna Teratas';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->select('users.*')
                    ->selectRaw('COUNT(bookings.id) as bookings_count')
                    ->selectRaw('COALESCE(SUM(bookings.total_price), 0) as total_spent')
                    ->leftJoin('bookings', 'users.id', '=', 'bookings.user_id')
                    ->groupBy('users.id')
                    ->orderBy('bookings_count', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('fullname')
                    ->label('Nama')
                    ->searchable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                    
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),
                    
                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Total Booking')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total_spent')
                    ->label('Total Pengeluaran')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('bookings.0.created_at')
                    ->label('Booking Terakhir')
                    ->dateTime('d/m/Y')
                    ->default('-'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (User $record): string => UserResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye')
                    ->label('Lihat'),
            ]);
    }
}
```

---

## Widget Configuration in Panel Provider

### File: `app/Providers/Filament/AdminPanelProvider.php`

```php
use App\Filament\Widgets;

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->login()
        ->colors([
            'primary' => Color::Blue,
            'success' => Color::Green,
            'warning' => Color::Orange,
            'danger' => Color::Red,
            'info' => Color::Sky,
        ])
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->pages([
            Pages\Dashboard::class,
        ])
        ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
        ->widgets([
            Widgets\StatsOverviewWidget::class,
            Widgets\BookingsChartWidget::class,
            Widgets\RevenueChartWidget::class,
            Widgets\ServicePopularityWidget::class,
            Widgets\LatestBookingsWidget::class,
            Widgets\TopUsersWidget::class,
        ])
        ->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ])
        ->authMiddleware([
            Authenticate::class,
        ])
        ->brandName('Resikan Admin')
        ->brandLogo(asset('images/logo.png'))
        ->brandLogoHeight('2rem')
        ->favicon(asset('images/favicon.png'))
        ->darkMode(true)
        ->viteTheme('resources/css/filament/admin/theme.css');
}
```

---

## Additional Features

### 1. Real-time Updates
```php
// Add to widgets for real-time polling
protected static ?string $pollingInterval = '60s';
```

### 2. Custom Dashboard Layout
Create custom view if needed:

**File: `resources/views/filament/pages/dashboard.blade.php`**
```blade
<x-filament-panels::page>
    <div class="grid gap-4 mb-4">
        @foreach ($this->getHeaderWidgets() as $widget)
            @livewire($widget)
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach ($this->getWidgets() as $widget)
            @if (is_array($widget))
                @foreach ($widget as $subWidget)
                    <div>
                        @livewire($subWidget)
                    </div>
                @endforeach
            @else
                <div class="lg:col-span-2">
                    @livewire($widget)
                </div>
            @endif
        @endforeach
    </div>

    <div class="grid gap-4 mt-4">
        @foreach ($this->getFooterWidgets() as $widget)
            @livewire($widget)
        @endforeach
    </div>
</x-filament-panels::page>
```

### 3. Export Functionality
Add export action to table widgets:

```php
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

->bulkActions([
    ExportBulkAction::make()
        ->label('Export ke Excel'),
])
```

---

## Performance Optimization

### 1. Cache Results
```php
use Illuminate\Support\Facades\Cache;

protected function getData(): array
{
    return Cache::remember('widget-bookings-chart-' . $this->filter, 300, function () {
        // Your data query here
    });
}
```

### 2. Eager Loading
```php
->query(
    Booking::query()
        ->with(['user', 'cleaner', 'bookingItems.service'])
        ->latest()
        ->limit(10)
)
```

### 3. Database Indexing
Ensure indexes on frequently queried columns:
```sql
CREATE INDEX idx_bookings_created_at ON bookings(created_at);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_payments_paid_at ON payments(paid_at);
CREATE INDEX idx_payments_status ON payments(status);
```

---

## Testing Checklist

- [ ] Stats cards menampilkan data yang benar
- [ ] Trend indicators menampilkan perubahan yang akurat
- [ ] Bookings chart menampilkan data 30 hari terakhir
- [ ] Revenue chart menampilkan data 12 bulan terakhir
- [ ] Service popularity chart menampilkan top 5 services
- [ ] Latest bookings widget menampilkan 10 booking terbaru
- [ ] Top users widget menampilkan pengguna dengan booking terbanyak
- [ ] Filter pada chart widgets berfungsi dengan baik
- [ ] Real-time polling bekerja (jika diaktifkan)
- [ ] Responsive design pada semua ukuran layar
- [ ] Click actions pada widgets berfungsi
- [ ] Export functionality bekerja

---

**Version:** 1.0  
**Last Updated:** 2024  
**Status:** Ready for Implementation ✅