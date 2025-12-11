# Dokumentasi Resikan Admin Panel

## 📚 Overview

Dokumentasi lengkap untuk implementasi Filament Admin Panel pada aplikasi Resikan (Cleaning Service Booking System).

---

## 📁 Struktur Dokumentasi

### 1. [Database Schema](./db.md)
Berisi schema database PostgreSQL (Supabase) lengkap dengan:
- Tabel users, bookings, services
- Relationships antar tabel
- Indexes dan constraints
- SQL commands untuk setup

### 2. [Filament Admin Planning](./filament-admin-planning.md)
Dokumen perencanaan lengkap meliputi:
- 🎯 Tujuan dan scope project
- 🗄️ Database schema overview
- 📦 Technology stack
- 🚀 6 Fase implementasi
- 📊 Dashboard layout plan
- 🎨 Design guidelines (colors, badges)
- ✅ Testing checklist
- ⏱️ Estimasi timeline (6-7 jam)

### 3. [Filament Resources Structure](./filament-resources-structure.md)
Detail implementasi untuk setiap Resource:

#### User Resource
- Form schema lengkap
- Table columns & filters
- Actions & bulk actions
- Global search configuration

#### Service Resource
- Form fields (code, name, price, duration)
- Active/inactive toggle
- Price & duration filters
- CRUD operations

#### Booking Resource
- Complex form dengan repeater
- Status management
- Cleaner assignment
- Booking items relation
- Payments relation
- Custom actions (assign, change status)

#### Model Relationships
- User ↔ Bookings
- Booking ↔ Services
- Booking ↔ Cleaner
- Booking ↔ Payments
- Casts & attributes untuk setiap model

### 4. [Dashboard & Widgets](./filament-dashboard-widgets.md)
Implementasi lengkap dashboard dengan:

#### Stats Overview (4 Cards)
- Total Users dengan trend
- Booking bulan ini
- Revenue bulan ini
- Pending bookings

#### Chart Widgets
- **Bookings Chart**: Line chart 30 hari terakhir
- **Revenue Chart**: Bar chart 12 bulan terakhir
- **Service Popularity**: Pie chart top 5 services

#### Table Widgets
- **Latest Bookings**: 10 booking terbaru
- **Top Users**: User dengan booking terbanyak

#### Features
- Real-time polling
- Filters per chart
- Interactive tooltips
- Click actions
- Export functionality

### 5. [Step-by-Step Implementation Guide](./filament-step-by-step-guide.md)
Panduan implementasi dari nol hingga selesai:

#### Fase 1: Instalasi (30 menit)
- Install Filament package
- Setup panel admin
- Database connection
- Create admin user

#### Fase 2: Models & Migrations (45 menit)
- Generate semua models
- Setup relationships
- Configure fillable & casts
- Database migrations

#### Fase 3: Resources (2 jam)
- Generate User Resource
- Generate Service Resource
- Generate Booking Resource
- Setup navigation & global search
- Test CRUD operations

#### Fase 4: Dashboard & Widgets (1.5 jam)
- Stats overview widget
- Chart widgets (bookings, revenue, services)
- Table widgets (latest bookings, top users)
- Register widgets di panel

#### Fase 5: Advanced Features (1 jam)
- Export to Excel
- Bulk actions
- Notifications
- Custom actions
- Bahasa Indonesia

#### Fase 6: Customization (45 menit)
- Brand customization
- Theme colors
- Dark mode
- Navigation badges
- Performance optimization

#### Fase 7: Testing (30 menit)
- Test semua CRUD operations
- Verify dashboard widgets
- Check filters & search
- Test export functionality

---

## 🚀 Quick Start

### Prerequisites
```bash
PHP 8.2+
Laravel 12
Composer
Node.js & NPM
PostgreSQL (Supabase)
```

### Installation
```bash
# 1. Install Filament
composer require filament/filament:"^3.2"

# 2. Setup panel
php artisan filament:install --panels

# 3. Create admin user
php artisan make:filament-user

# 4. Run server
php artisan serve

# 5. Access admin panel
http://localhost:8000/admin
```

### Generate Resources
```bash
# User Resource
php artisan make:filament-resource User --generate --view

# Service Resource
php artisan make:filament-resource Service --generate --view

# Booking Resource
php artisan make:filament-resource Booking --generate --view
```

### Generate Widgets
```bash
# Stats Widget
php artisan make:filament-widget StatsOverviewWidget --stats

# Chart Widgets
php artisan make:filament-widget BookingsChartWidget --chart
php artisan make:filament-widget RevenueChartWidget --chart

# Table Widgets
php artisan make:filament-widget LatestBookingsWidget
```

---

## 📊 Features Overview

### ✅ User Management
- [x] Create, Read, Update, Delete users
- [x] Search by name, email, phone
- [x] Filter by registration date
- [x] View user bookings
- [x] Export to Excel

### ✅ Service Management
- [x] Manage service catalog
- [x] Set pricing & duration
- [x] Active/inactive toggle
- [x] Filter by status & price range
- [x] Track service popularity

### ✅ Booking Management
- [x] Create & manage bookings
- [x] Assign cleaners
- [x] Change booking status
- [x] Add multiple services per booking
- [x] Calculate total price
- [x] View booking history
- [x] Filter by status, date, user, cleaner
- [x] Export bookings

### ✅ Dashboard
- [x] Real-time statistics
- [x] Trend indicators
- [x] Booking trend chart
- [x] Revenue chart
- [x] Service popularity chart
- [x] Latest bookings table
- [x] Top users table

### ✅ Advanced Features
- [x] Global search across resources
- [x] Bulk actions (delete, status change)
- [x] Export to Excel
- [x] Custom actions (assign cleaner)
- [x] Notifications
- [x] Bahasa Indonesia
- [x] Dark mode support
- [x] Responsive design

---

## 🎯 Database Schema

### Core Tables
```
users
├── bookings
│   ├── booking_items → services
│   ├── payments
│   └── ratings
├── subscriptions
└── ratings

cleaners
├── bookings
├── ratings
└── cleaner_documents

services
└── booking_items
```

### Relationships
- User **has many** Bookings
- Booking **belongs to** User
- Booking **belongs to** Cleaner
- Booking **has many** BookingItems
- BookingItem **belongs to** Service
- Booking **has many** Payments
- Booking **has one** Rating

---

## 🎨 Design System

### Colors
- **Primary**: Blue (#3b82f6)
- **Success**: Green (#10b981)
- **Warning**: Orange (#f97316)
- **Danger**: Red (#ef4444)
- **Info**: Sky (#0ea5e9)

### Status Badges
#### Booking Status
- `pending` → Orange (Warning)
- `confirmed` → Blue (Info)
- `in_progress` → Purple (Primary)
- `completed` → Green (Success)
- `cancelled` → Red (Danger)

#### Payment Status
- `pending` → Orange
- `paid` → Green
- `failed` → Red
- `refunded` → Gray

---

## 📈 Dashboard Layout

```
┌─────────────────────────────────────────────────────────┐
│  DASHBOARD                                               │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐│
│  │  Total   │  │  Total   │  │ Revenue  │  │ Pending  ││
│  │  Users   │  │ Bookings │  │  Bulan   │  │ Bookings ││
│  │  1,234   │  │   567    │  │   Ini    │  │    23    ││
│  │  ↑ 12%   │  │  ↑ 8%    │  │Rp15.5jt  │  │  ↓ 2     ││
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘│
│                                                          │
│  ┌────────────────────────┐  ┌────────────────────────┐│
│  │  Bookings Trend        │  │  Revenue Chart         ││
│  │  (Line Chart)          │  │  (Bar Chart)           ││
│  │  [Last 30 Days]        │  │  [Last 12 Months]      ││
│  └────────────────────────┘  └────────────────────────┘│
│                                                          │
│  ┌────────────────────────┐  ┌────────────────────────┐│
│  │  Service Popularity    │  │  Recent Bookings       ││
│  │  (Pie Chart)           │  │  (Table)               ││
│  │  Top 5 Services        │  │  Latest 10 bookings    ││
│  └────────────────────────┘  └────────────────────────┘│
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## ⏱️ Implementation Timeline

| Phase | Task | Duration |
|-------|------|----------|
| 1 | Setup & Installation | 30 min |
| 2 | Models & Migrations | 45 min |
| 3 | Filament Resources | 2 hours |
| 4 | Dashboard & Widgets | 1.5 hours |
| 5 | Advanced Features | 1 hour |
| 6 | Customization & Polish | 45 min |
| 7 | Testing & QA | 30 min |
| **TOTAL** | | **~7 hours** |

---

## 🔧 Technology Stack

- **Backend**: Laravel 12
- **Admin Panel**: Filament v3.2
- **Database**: PostgreSQL (Supabase)
- **PHP**: 8.2+
- **Frontend**: Livewire (via Filament)
- **Charts**: Chart.js (via Filament)
- **Export**: FilamentExcel

---

## 📚 Resources

### Official Documentation
- [Filament Documentation](https://filamentphp.com/docs)
- [Laravel Documentation](https://laravel.com/docs)
- [Supabase Documentation](https://supabase.com/docs)

### Useful Packages
- [Filament Shield (RBAC)](https://github.com/bezhanSalleh/filament-shield)
- [Filament Excel Export](https://github.com/pxlrbt/filament-excel)
- [Filament Spatie Media Library](https://github.com/filamentphp/spatie-laravel-media-library-plugin)

### Community
- [Filament Discord](https://filamentphp.com/discord)
- [Filament GitHub](https://github.com/filamentphp/filament)

---

## ✅ Testing Checklist

### User Management
- [ ] Create, edit, delete users
- [ ] Search & filter functionality
- [ ] View user details & bookings
- [ ] Export to Excel

### Service Management
- [ ] CRUD operations
- [ ] Active/inactive toggle
- [ ] Price & duration filters
- [ ] Export services

### Booking Management
- [ ] Create bookings with multiple items
- [ ] Assign cleaners
- [ ] Change status
- [ ] View booking details
- [ ] Filter & search
- [ ] Export bookings

### Dashboard
- [ ] Stats cards display correctly
- [ ] Charts show accurate data
- [ ] Filters work on charts
- [ ] Widgets are responsive
- [ ] Real-time updates

---

## 🚨 Troubleshooting

### Common Issues

**"Class not found"**
```bash
composer dump-autoload
php artisan clear-compiled
```

**"500 Error"**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

**"Assets not loading"**
```bash
php artisan filament:assets
npm run build
```

**"Permission denied"**
```bash
chmod -R 775 storage bootstrap/cache
```

---

## 🎯 Next Steps

Setelah implementasi dasar:

1. **Role & Permissions**
   - Install Filament Shield
   - Setup roles (Admin, Staff, Manager)
   - Configure permissions per resource

2. **Additional Resources**
   - Cleaner management
   - Payment tracking
   - Rating & review system
   - Schedule management

3. **Notifications**
   - Email notifications
   - SMS notifications
   - Push notifications
   - In-app notifications

4. **Reports**
   - Custom report pages
   - PDF export
   - Scheduled reports
   - Analytics dashboard

5. **API Integration**
   - Mobile app endpoints
   - Third-party integrations
   - Webhook support

---

## 📝 Notes

- Semua kode menggunakan **UUID** sebagai primary key
- Database menggunakan **PostgreSQL** via Supabase
- Authentication dihandle oleh **Filament Auth**
- RLS (Row Level Security) perlu dihandle untuk production
- Backup database secara berkala
- Monitor performance untuk optimasi

---

## 📞 Support

Untuk pertanyaan atau bantuan:
1. Review dokumentasi di folder ini
2. Check Filament official docs
3. Join Filament Discord community
4. Create issue di GitHub repository

---

## 📄 License

Project ini menggunakan MIT License.

---

**Version:** 1.0  
**Last Updated:** 2024  
**Status:** Production Ready ✅

## 🎉 Happy Coding!

Ikuti dokumentasi step-by-step untuk hasil terbaik.
Semoga sukses dengan implementasi Filament Admin Panel! 🚀