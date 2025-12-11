# Filament Admin Panel - Step-by-Step Implementation Guide

## 🚀 Panduan Implementasi Lengkap

Dokumen ini berisi langkah-langkah detail untuk mengimplementasikan Filament Admin Panel dari awal hingga selesai.

---

## Persiapan Awal

### Prerequisites
- Laravel 12 sudah terinstall
- PHP 8.2+
- Composer
- Node.js & NPM
- Database Supabase (PostgreSQL) sudah setup
- Git untuk version control

### Cek Environment
```bash
php -v                  # Pastikan PHP 8.2+
composer --version      # Pastikan Composer terinstall
npm -v                  # Pastikan NPM terinstall
php artisan --version   # Pastikan Laravel berjalan
```

---

## FASE 1: INSTALASI FILAMENT (30 menit)

### Step 1.1: Install Filament Package
```bash
cd resikan_php
composer require filament/filament:"^3.2" -W
```

### Step 1.2: Install Filament Panel
```bash
php artisan filament:install --panels
```

Pilih opsi:
- Panel ID: `admin`
- Panel path: `/admin`

### Step 1.3: Publish Assets
```bash
php artisan filament:assets
```

### Step 1.4: Setup Database Connection
Edit file `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=db.xxxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-password
```

### Step 1.5: Test Database Connection
```bash
php artisan migrate:status
```

### Step 1.6: Create Admin User
```bash
php artisan make:filament-user
```

Input:
- Name: Admin
- Email: admin@resikan.com
- Password: (pilih password yang kuat)

### Step 1.7: Test Admin Panel
```bash
php artisan serve
```

Buka browser: `http://localhost:8000/admin`
Login dengan credentials yang dibuat.

✅ **Checkpoint 1:** Admin panel bisa diakses dan login berhasil.

---

## FASE 2: SETUP MODELS & MIGRATIONS (45 menit)

### Step 2.1: Generate Models

```bash
# User model (sudah ada, akan diedit)
php artisan make:model Booking -m
php artisan make:model BookingItem -m
php artisan make:model Service -m
php artisan make:model Cleaner -m
php artisan make:model Payment -m
php artisan make:model Rating -m
php artisan make:model Subscription -m
```

### Step 2.2: Edit User Model
File: `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'fullname',
        'email',
        'phone',
        'address',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
```

### Step 2.3: Edit Service Model
File: `app/Models/Service.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Service extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'name',
        'description',
        'base_price',
        'default_duration_minutes',
        'active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }
}
```

### Step 2.4: Edit Booking Model
File: `app/Models/Booking.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Booking extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'booking_number',
        'user_id',
        'cleaner_id',
        'scheduled_at',
        'duration_minutes',
        'total_price',
        'status',
        'address',
        'location',
        'extras',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'location' => 'array',
        'extras' => 'array',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class);
    }

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
}
```

### Step 2.5: Edit BookingItem Model
File: `app/Models/BookingItem.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BookingItem extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'service_id',
        'quantity',
        'price',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
```

### Step 2.6: Edit Cleaner Model
File: `app/Models/Cleaner.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Cleaner extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'employee_code',
        'fullname',
        'phone',
        'email',
        'status',
        'availability',
        'notes',
        'hired_at',
    ];

    protected $casts = [
        'availability' => 'array',
        'hired_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
```

### Step 2.7: Edit Payment Model
File: `app/Models/Payment.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Payment extends Model
{
    use HasFactory, HasUuids;

    const UPDATED_AT = null;

    protected $fillable = [
        'booking_id',
        'amount',
        'method',
        'status',
        'transaction_id',
        'gateway_response',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
```

### Step 2.8: Edit Rating Model
File: `app/Models/Rating.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Rating extends Model
{
    use HasFactory, HasUuids;

    const UPDATED_AT = null;

    protected $fillable = [
        'booking_id',
        'user_id',
        'cleaner_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class);
    }
}
```

### Step 2.9: Edit Subscription Model
File: `app/Models/Subscription.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Subscription extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'plan_code',
        'price',
        'start_date',
        'end_date',
        'status',
        'meta',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### Step 2.10: Verify Models
```bash
php artisan tinker
```

Dalam tinker:
```php
App\Models\User::count();
App\Models\Service::count();
App\Models\Booking::count();
exit
```

✅ **Checkpoint 2:** Semua models bisa diakses tanpa error.

---

## FASE 3: GENERATE FILAMENT RESOURCES (2 jam)

### Step 3.1: Generate User Resource
```bash
php artisan make:filament-resource User --generate --view
```

Edit file: `app/Filament/Resources/UserResource.php`

Sesuaikan form schema (lihat detail di `filament-resources-structure.md`)

### Step 3.2: Generate Service Resource
```bash
php artisan make:filament-resource Service --generate --view
```

Edit file: `app/Filament/Resources/ServiceResource.php`

### Step 3.3: Generate Booking Resource
```bash
php artisan make:filament-resource Booking --generate --view
```

Edit file: `app/Filament/Resources/BookingResource.php`

### Step 3.4: Generate Booking Relations
```bash
php artisan make:filament-relation-manager BookingResource bookingItems service_id
php artisan make:filament-relation-manager BookingResource payments booking_id
```

### Step 3.5: Setup Navigation
Edit setiap Resource file, tambahkan:

```php
protected static ?string $navigationIcon = 'heroicon-o-users';
protected static ?string $navigationLabel = 'Pengguna';
protected static ?string $navigationGroup = 'Manajemen';
protected static ?int $navigationSort = 1;
```

### Step 3.6: Setup Global Search
Tambahkan di setiap Resource:

```php
public static function getGloballySearchableAttributes(): array
{
    return ['fullname', 'email', 'phone'];
}
```

### Step 3.7: Test Resources
```bash
php artisan serve
```

Buka browser dan test:
- Create User baru
- Create Service baru
- Create Booking baru
- Edit & Delete berfungsi

✅ **Checkpoint 3:** Semua CRUD operations berfungsi untuk User, Service, dan Booking.

---

## FASE 4: DASHBOARD & WIDGETS (1.5 jam)

### Step 4.1: Generate Stats Widget
```bash
php artisan make:filament-widget StatsOverviewWidget --stats
```

Edit file: `app/Filament/Widgets/StatsOverviewWidget.php`

Copy code dari `filament-dashboard-widgets.md`

### Step 4.2: Generate Bookings Chart
```bash
php artisan make:filament-widget BookingsChartWidget --chart
```

Edit file: `app/Filament/Widgets/BookingsChartWidget.php`

### Step 4.3: Generate Revenue Chart
```bash
php artisan make:filament-widget RevenueChartWidget --chart
```

Edit file: `app/Filament/Widgets/RevenueChartWidget.php`

### Step 4.4: Generate Service Popularity Chart
```bash
php artisan make:filament-widget ServicePopularityWidget --chart
```

Edit file: `app/Filament/Widgets/ServicePopularityWidget.php`

### Step 4.5: Generate Latest Bookings Widget
```bash
php artisan make:filament-widget LatestBookingsWidget
```

Edit file: `app/Filament/Widgets/LatestBookingsWidget.php`

Extend dari `TableWidget`

### Step 4.6: Generate Top Users Widget
```bash
php artisan make:filament-widget TopUsersWidget
```

Edit file: `app/Filament/Widgets/TopUsersWidget.php`

### Step 4.7: Register Widgets
Edit file: `app/Providers/Filament/AdminPanelProvider.php`

```php
->widgets([
    Widgets\StatsOverviewWidget::class,
    Widgets\BookingsChartWidget::class,
    Widgets\RevenueChartWidget::class,
    Widgets\ServicePopularityWidget::class,
    Widgets\LatestBookingsWidget::class,
    Widgets\TopUsersWidget::class,
])
```

### Step 4.8: Test Dashboard
```bash
php artisan serve
```

Buka `/admin` dan verify:
- Stat cards muncul
- Charts menampilkan data
- Table widgets berfungsi

✅ **Checkpoint 4:** Dashboard lengkap dengan semua widgets.

---

## FASE 5: ADVANCED FEATURES (1 jam)

### Step 5.1: Install Export Package
```bash
composer require pxlrbt/filament-excel
```

### Step 5.2: Add Export Actions
Di setiap Resource Table, tambahkan:

```php
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

->bulkActions([
    Tables\Actions\BulkActionGroup::make([
        ExportBulkAction::make()
            ->label('Export ke Excel'),
        Tables\Actions\DeleteBulkAction::make(),
    ]),
])
```

### Step 5.3: Install Notification Package (Optional)
```bash
composer require filament/notifications
```

### Step 5.4: Add Custom Actions to Booking
Edit `BookingResource.php`:

```php
Tables\Actions\Action::make('assign_cleaner')
    ->label('Assign Petugas')
    ->icon('heroicon-o-user-plus')
    ->form([
        Forms\Components\Select::make('cleaner_id')
            ->relationship('cleaner', 'fullname')
            ->required(),
    ])
    ->action(function ($record, array $data) {
        $record->update(['cleaner_id' => $data['cleaner_id']]);
        Notification::make()
            ->title('Petugas berhasil ditugaskan')
            ->success()
            ->send();
    }),
```

### Step 5.5: Setup Bahasa Indonesia
```bash
composer require filament/spatie-laravel-translatable-plugin
php artisan vendor:publish --tag=filament-translations
```

Edit `config/app.php`:
```php
'locale' => 'id',
'fallback_locale' => 'en',
```

Edit `AdminPanelProvider.php`:
```php
->locale('id')
```

✅ **Checkpoint 5:** Export, notifications, dan bahasa Indonesia berfungsi.

---

## FASE 6: CUSTOMIZATION & POLISH (45 menit)

### Step 6.1: Customize Brand
Edit `AdminPanelProvider.php`:

```php
->brandName('Resikan Admin')
->brandLogo(asset('images/logo.png'))
->brandLogoHeight('2rem')
->favicon(asset('images/favicon.png'))
```

### Step 6.2: Setup Colors
```php
use Filament\Support\Colors\Color;

->colors([
    'primary' => Color::Blue,
    'success' => Color::Green,
    'warning' => Color::Orange,
    'danger' => Color::Red,
    'info' => Color::Sky,
])
```

### Step 6.3: Enable Dark Mode
```php
->darkMode(true)
```

### Step 6.4: Customize Navigation
Group navigation items:

```php
// In each Resource
protected static ?string $navigationGroup = 'Manajemen';
```

### Step 6.5: Add Navigation Badges
Di `BookingResource.php`:

```php
protected static ?string $navigationBadge = fn () => static::getModel()::where('status', 'pending')->count();
protected static ?string $navigationBadgeColor = 'warning';
```

### Step 6.6: Create Vite Theme (Optional)
```bash
php artisan make:filament-theme
```

### Step 6.7: Optimize Performance
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

✅ **Checkpoint 6:** Admin panel fully customized.

---

## FASE 7: TESTING & QA (30 menit)

### Step 7.1: Test User Management
- [ ] Create new user
- [ ] Edit user information
- [ ] Delete user
- [ ] Search user by name/email/phone
- [ ] Filter users by date
- [ ] View user detail
- [ ] Export users to Excel

### Step 7.2: Test Service Management
- [ ] Create new service
- [ ] Edit service
- [ ] Toggle active/inactive
- [ ] Delete service
- [ ] Search services
- [ ] Filter by active status
- [ ] Export services

### Step 7.3: Test Booking Management
- [ ] Create new booking
- [ ] Assign cleaner
- [ ] Change booking status
- [ ] Edit booking details
- [ ] Add booking items
- [ ] Calculate total price correctly
- [ ] Delete booking
- [ ] Filter by status, date, user
- [ ] Export bookings

### Step 7.4: Test Dashboard
- [ ] Stat cards show correct numbers
- [ ] Charts display accurate data
- [ ] Filters on charts work
- [ ] Latest bookings update
- [ ] Top users display correctly
- [ ] Widgets responsive on mobile

### Step 7.5: Test Advanced Features
- [ ] Global search works
- [ ] Bulk actions function
- [ ] Export to Excel successful
- [ ] Notifications appear
- [ ] Custom actions work
- [ ] Bahasa Indonesia active

✅ **Checkpoint 7:** All features tested and working.

---

## FASE 8: SEED DATA (OPTIONAL)

### Step 8.1: Create Seeders
```bash
php artisan make:seeder UserSeeder
php artisan make:seeder ServiceSeeder
php artisan make:seeder CleanerSeeder
php artisan make:seeder BookingSeeder
```

### Step 8.2: Run Seeders
```bash
php artisan db:seed
```

---

## DEPLOYMENT CHECKLIST

### Pre-Deployment
- [ ] Run all tests
- [ ] Check for security vulnerabilities
- [ ] Update `.env.example`
- [ ] Clear all caches
- [ ] Optimize autoloader
- [ ] Compile assets

### Commands
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
npm run build
```

### Post-Deployment
- [ ] Verify database connection
- [ ] Test admin login
- [ ] Check all pages load
- [ ] Test CRUD operations
- [ ] Verify widgets display correctly
- [ ] Test on mobile devices

---

## TROUBLESHOOTING

### Issue: "Class not found"
```bash
composer dump-autoload
php artisan clear-compiled
```

### Issue: "500 Error"
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Issue: "UUID generation error"
Verify Supabase PostgreSQL has uuid-ossp extension:
```sql
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
```

### Issue: "Permission denied"
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Issue: "Assets not loading"
```bash
php artisan filament:assets
npm run build
```

---

## MAINTENANCE

### Daily Tasks
- Monitor dashboard stats
- Check pending bookings
- Review error logs

### Weekly Tasks
- Backup database
- Review performance metrics
- Update dependencies if needed

### Monthly Tasks
- Security audit
- Performance optimization
- Feature improvements

---

## RESOURCES & DOCUMENTATION

### Official Documentation
- Filament: https://filamentphp.com/docs
- Laravel: https://laravel.com/docs
- Supabase: https://supabase.com/docs

### Community
- Filament Discord: https://filamentphp.com/discord
- GitHub Issues: https://github.com/filamentphp/filament/issues

### Additional Packages
- Filament Shield (RBAC): https://github.com/bezhanSalleh/filament-shield
- Filament Spatie Media Library: https://github.com/filamentphp/spatie-laravel-media-library-plugin
- Filament Apex Charts: https://github.com/leandrocfe/filament-apex-charts

---

## NEXT STEPS

Setelah implementasi dasar selesai, pertimbangkan:

1. **Authentication & Authorization**
   - Implement role-based access control
   - Setup permissions per resource

2. **Additional Resources**
   - Cleaner management
   - Payment tracking
   - Rating & review system

3. **Advanced Features**
   - Real-time notifications
   - Email notifications
   - SMS notifications
   - Automated reports

4. **Integrations**
   - Payment gateways
   - Mapping services (Google Maps)
   - Push notifications
   - Third-party APIs

5. **Mobile App Integration**
   - API endpoints for mobile
   - Real-time sync
   - Offline support

---

## ESTIMASI WAKTU TOTAL

| Fase | Estimasi |
|------|----------|
| Fase 1: Instalasi | 30 menit |
| Fase 2: Models & Migrations | 45 menit |
| Fase 3: Resources | 2 jam |
| Fase 4: Dashboard & Widgets | 1.5 jam |
| Fase 5: Advanced Features | 1 jam |
| Fase 6: Customization | 45 menit |
| Fase 7: Testing | 30 menit |
| **TOTAL** | **7 jam** |

---

## KONTAK & SUPPORT

Untuk bantuan lebih lanjut:
- Review dokumentasi di folder `/docs`
- Cek Filament documentation
- Join Filament Discord community

---

**Version:** 1.0  
**Last Updated:** 2024  
**Author:** Resikan Development Team  
**Status:** Ready to Implement ✅

## 🎉 SELAMAT MENGIMPLEMENTASIKAN!

Ikuti langkah-langkah di atas secara berurutan untuk hasil terbaik.
Good luck! 🚀