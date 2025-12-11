# Filament Admin Panel - Planning & Implementation Guide

## 📋 Overview
Dokumen ini berisi planning lengkap untuk implementasi admin panel menggunakan Filament Laravel untuk aplikasi Resikan (cleaning service booking system).

## 🎯 Tujuan
Membuat admin panel dengan fitur:
1. **Dashboard** - Grafik dan summary statistik
2. **Manajemen User** - CRUD untuk users
3. **Manajemen Booking** - CRUD untuk bookings
4. **Manajemen Services** - CRUD untuk services
5. Fitur tambahan: Edit, Hapus, Lihat detail untuk setiap modul

## 🗄️ Database Schema (Supabase)
Berdasarkan schema yang ada:
- **users** - Data pelanggan
- **bookings** - Data booking/pesanan
- **booking_items** - Detail item per booking
- **services** - Katalog layanan
- **cleaners** - Data petugas cleaning
- **payments** - Data pembayaran
- **ratings** - Ulasan pelanggan
- **subscriptions** - Data langganan

## 📦 Teknologi Stack
- **Laravel 12** (sudah terinstall)
- **Filament v3** (akan diinstall)
- **PostgreSQL** (Supabase)
- **PHP 8.2+**

## 🚀 Fase Implementasi

### Phase 1: Setup & Installation (30 menit)
```bash
# Install Filament
composer require filament/filament:"^3.2"

# Setup Filament
php artisan filament:install --panels

# Buat user admin
php artisan make:filament-user
```

**Deliverables:**
- Filament terinstall
- Admin user terbuat
- Akses panel admin di `/admin`

### Phase 2: Model & Migration Setup (45 menit)

#### 2.1 Buat Models
```bash
php artisan make:model User # (sudah ada, tinggal adjust)
php artisan make:model Booking -m
php artisan make:model BookingItem -m
php artisan make:model Service -m
php artisan make:model Cleaner -m
php artisan make:model Payment -m
php artisan make:model Rating -m
php artisan make:model Subscription -m
```

#### 2.2 Setup Relationships
- User hasMany Bookings
- Booking belongsTo User
- Booking hasMany BookingItems
- Booking belongsTo Cleaner
- BookingItem belongsTo Service
- Booking hasMany Payments
- Booking hasOne Rating

#### 2.3 Migrations
Sesuaikan migrations dengan schema Supabase yang ada.

**Deliverables:**
- Semua models terbuat dengan relationships
- Migrations sesuai dengan schema Supabase
- Fillable & casts sudah diset

### Phase 3: Filament Resources (2 jam)

#### 3.1 User Resource
```bash
php artisan make:filament-resource User --generate
```

**Fields:**
- fullname (TextInput)
- email (TextInput, email validation)
- phone (TextInput)
- address (Textarea)
- metadata (KeyValue)
- created_at (DateTimePicker, disabled)
- updated_at (DateTimePicker, disabled)

**Table Columns:**
- fullname
- email
- phone
- created_at

**Filters:**
- Created date range
- Search by name, email, phone

**Actions:**
- View
- Edit
- Delete

#### 3.2 Service Resource
```bash
php artisan make:filament-resource Service --generate
```

**Fields:**
- code (TextInput, unique)
- name (TextInput)
- description (RichEditor)
- base_price (TextInput, numeric, prefix: Rp)
- default_duration_minutes (TextInput, numeric, suffix: menit)
- active (Toggle)
- created_at (DateTimePicker, disabled)
- updated_at (DateTimePicker, disabled)

**Table Columns:**
- code
- name
- base_price (formatted currency)
- default_duration_minutes
- active (badge)
- created_at

**Filters:**
- Active/Inactive
- Price range
- Created date

**Actions:**
- View
- Edit
- Delete (soft delete jika ada booking)

#### 3.3 Booking Resource
```bash
php artisan make:filament-resource Booking --generate
```

**Fields:**
- booking_number (TextInput, disabled/auto-generate)
- user_id (Select, searchable, relationship)
- cleaner_id (Select, searchable, relationship)
- scheduled_at (DateTimePicker)
- duration_minutes (TextInput, numeric)
- total_price (TextInput, numeric, prefix: Rp)
- status (Select: pending, confirmed, in_progress, completed, cancelled)
- address (Textarea)
- location (KeyValue: lat, lng)
- extras (KeyValue)
- booking_items (Repeater)
  - service_id (Select)
  - quantity (TextInput, numeric)
  - price (TextInput, numeric)
  - notes (Textarea)

**Table Columns:**
- booking_number
- user.fullname
- cleaner.fullname
- scheduled_at
- total_price (formatted)
- status (badge dengan warna)
- created_at

**Filters:**
- Status
- Date range
- User
- Cleaner
- Price range

**Actions:**
- View (modal dengan detail lengkap)
- Edit
- Delete
- Custom actions:
  - Assign Cleaner
  - Change Status
  - Print Invoice

**Relations:**
- booking_items (Table with service, qty, price)
- payments (Table with amount, method, status)

#### 3.4 Additional Resources (Optional untuk Phase 3)
```bash
php artisan make:filament-resource Cleaner --generate
php artisan make:filament-resource Payment --generate
php artisan make:filament-resource Rating --generate
```

**Deliverables:**
- User Resource lengkap dengan CRUD
- Service Resource lengkap dengan CRUD
- Booking Resource lengkap dengan CRUD & relations
- Form validation sudah diimplementasi
- Search & filters berfungsi

### Phase 4: Dashboard & Widgets (1.5 jam)

#### 4.1 Stats Overview Widgets
```bash
php artisan make:filament-widget StatsOverview --stats
```

**Metrics:**
1. **Total Users** - Card dengan jumlah total users
2. **Total Bookings** - Card dengan jumlah booking bulan ini
3. **Revenue Bulan Ini** - Total pendapatan bulan berjalan
4. **Pending Bookings** - Jumlah booking yang pending

**Features:**
- Trend indicator (naik/turun vs bulan lalu)
- Color coding (success, warning, danger)
- Click to filter/detail

#### 4.2 Chart Widgets

**4.2.1 Booking Chart**
```bash
php artisan make:filament-widget BookingChart --chart
```
- Line chart booking per hari (30 hari terakhir)
- Filter by status
- Interactive tooltip

**4.2.2 Revenue Chart**
```bash
php artisan make:filament-widget RevenueChart --chart
```
- Bar chart pendapatan per bulan (12 bulan terakhir)
- Comparison dengan tahun lalu
- Breakdown by payment method

**4.2.3 Service Popularity Chart**
```bash
php artisan make:filament-widget ServicePopularityChart --chart
```
- Pie chart layanan paling banyak dipesan
- Top 5 services
- Click to view details

#### 4.3 Table Widgets

**4.3.1 Recent Bookings Widget**
```bash
php artisan make:filament-widget LatestBookings --table
```
- 10 booking terbaru
- Quick actions (view, edit, change status)
- Real-time update

**4.3.2 Top Users Widget**
```bash
php artisan make:filament-widget TopUsers --table
```
- Users dengan booking terbanyak
- Total spent
- Last booking date

**Deliverables:**
- Dashboard dengan 4 stat cards
- 3 chart widgets (bookings, revenue, services)
- 2 table widgets (recent bookings, top users)
- Dashboard responsive dan informatif

### Phase 5: Advanced Features (1 jam)

#### 5.1 Global Search
Setup global search untuk:
- Users (by name, email, phone)
- Bookings (by booking number, user name)
- Services (by name, code)

#### 5.2 Bulk Actions
- Bulk delete users
- Bulk change booking status
- Bulk activate/deactivate services

#### 5.3 Export/Import
```bash
php artisan make:filament-export-action
php artisan make:filament-import-action
```
- Export bookings to Excel/CSV
- Export users to Excel/CSV
- Import services from CSV

#### 5.4 Notifications
- Admin notification untuk booking baru
- Status change notification
- Payment received notification

#### 5.5 Custom Pages
```bash
php artisan make:filament-page Reports
```
- Halaman laporan custom
- Filter by date range, status, etc.
- Export to PDF

**Deliverables:**
- Global search berfungsi
- Bulk actions implemented
- Export/Import Excel
- Notification system
- Custom report page

### Phase 6: Customization & Polish (45 menit)

#### 6.1 Theme Customization
- Set brand colors
- Custom logo
- Dark mode support
- Custom fonts

#### 6.2 Navigation
- Group navigation items
- Icons untuk setiap menu
- Badge untuk pending items
- Collapsible sidebar

#### 6.3 Authorization
```bash
php artisan make:filament-policy
```
- Setup policies untuk setiap resource
- Role-based access (Super Admin, Admin, Staff)
- Permission gates

#### 6.4 Localization
- Setup bahasa Indonesia
- Translate form labels
- Format tanggal & currency Indonesia

**Deliverables:**
- Theme custom sesuai brand
- Navigation terstruktur
- Authorization & roles implemented
- Bahasa Indonesia aktif

## 📊 Dashboard Layout Plan

```
┌─────────────────────────────────────────────────────────┐
│  DASHBOARD                                               │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐│
│  │  Total   │  │  Total   │  │ Revenue  │  │ Pending  ││
│  │  Users   │  │ Bookings │  │  Bulan   │  │ Bookings ││
│  │  1,234   │  │   567    │  │   Ini    │  │    23    ││
│  │  ↑ 12%  │  │  ↑ 8%   │  │Rp15.5jt  │  │  ↓ 2    ││
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘│
│                                                          │
│  ┌────────────────────────┐  ┌────────────────────────┐│
│  │  Bookings Trend        │  │  Revenue Chart         ││
│  │  (Line Chart)          │  │  (Bar Chart)           ││
│  │                        │  │                        ││
│  │  [Last 30 Days]        │  │  [Last 12 Months]      ││
│  └────────────────────────┘  └────────────────────────┘│
│                                                          │
│  ┌────────────────────────┐  ┌────────────────────────┐│
│  │  Service Popularity    │  │  Recent Bookings       ││
│  │  (Pie Chart)           │  │  (Table)               ││
│  │                        │  │                        ││
│  │  Top 5 Services        │  │  Latest 10 bookings    ││
│  └────────────────────────┘  └────────────────────────┘│
│                                                          │
└─────────────────────────────────────────────────────────┘
```

## 🎨 Status Badge Colors

**Booking Status:**
- `pending` → Warning (Orange)
- `confirmed` → Info (Blue)
- `in_progress` → Primary (Purple)
- `completed` → Success (Green)
- `cancelled` → Danger (Red)

**Payment Status:**
- `pending` → Warning
- `paid` → Success
- `failed` → Danger
- `refunded` → Secondary

**Cleaner Status:**
- `active` → Success
- `inactive` → Danger
- `on_leave` → Warning

## 📝 Database Connection (Supabase)

**Configuration (.env):**
```env
DB_CONNECTION=pgsql
DB_HOST=your-supabase-project.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-password
```

**Note:** Pastikan RLS (Row Level Security) dihandle dengan service_role key atau disable untuk admin access.

## ✅ Testing Checklist

### User Management
- [ ] Bisa melihat list users
- [ ] Bisa create user baru
- [ ] Bisa edit user
- [ ] Bisa delete user
- [ ] Bisa search user
- [ ] Bisa filter user
- [ ] Form validation bekerja

### Service Management
- [ ] Bisa melihat list services
- [ ] Bisa create service baru
- [ ] Bisa edit service
- [ ] Bisa delete service
- [ ] Toggle active/inactive bekerja
- [ ] Price formatting benar
- [ ] Bisa filter by active status

### Booking Management
- [ ] Bisa melihat list bookings
- [ ] Bisa create booking baru
- [ ] Bisa edit booking
- [ ] Bisa delete booking
- [ ] Bisa assign cleaner
- [ ] Bisa change status
- [ ] Booking items bisa ditambah/edit
- [ ] Total price calculation benar
- [ ] Bisa filter by status, date, user
- [ ] Bisa view booking detail lengkap

### Dashboard
- [ ] Stat cards menampilkan data benar
- [ ] Trend indicators bekerja
- [ ] Chart menampilkan data akurat
- [ ] Widget responsive
- [ ] Recent bookings update real-time
- [ ] Click actions berfungsi

### Advanced Features
- [ ] Global search berfungsi
- [ ] Bulk actions bekerja
- [ ] Export to Excel berhasil
- [ ] Notifications muncul
- [ ] Authorization bekerja
- [ ] Bahasa Indonesia aktif

## 🔒 Security Considerations

1. **Authentication:**
   - Admin panel hanya bisa diakses user authenticated
   - Gunakan Filament Shield untuk role management

2. **Authorization:**
   - Implement policies untuk setiap resource
   - Restrict sensitive actions (delete, bulk operations)

3. **Validation:**
   - Server-side validation untuk semua form input
   - Sanitize user input
   - Validate file uploads

4. **Database:**
   - Use prepared statements (Eloquent handles this)
   - Don't expose raw SQL errors
   - Rate limit API calls

5. **API Keys:**
   - Store Supabase keys in .env
   - Never commit .env to git
   - Use service_role key untuk bypass RLS

## 📚 Resources & Documentation

- Filament Official Docs: https://filamentphp.com/docs
- Laravel Docs: https://laravel.com/docs
- Supabase Docs: https://supabase.com/docs
- Filament Shield (RBAC): https://github.com/bezhanSalleh/filament-shield
- Filament Excel Export: https://github.com/pxlrbt/filament-excel

## ⏱️ Estimasi Timeline

| Phase | Estimasi Waktu | Prioritas |
|-------|----------------|-----------|
| Phase 1: Setup & Installation | 30 menit | High |
| Phase 2: Models & Migrations | 45 menit | High |
| Phase 3: Resources (User, Service, Booking) | 2 jam | High |
| Phase 4: Dashboard & Widgets | 1.5 jam | High |
| Phase 5: Advanced Features | 1 jam | Medium |
| Phase 6: Customization & Polish | 45 menit | Low |
| **TOTAL** | **6-7 jam** | |

## 🎯 Next Steps

1. **Install Filament** dan setup admin panel
2. **Buat Models & Migrations** sesuai schema Supabase
3. **Generate Resources** untuk User, Service, Booking
4. **Build Dashboard** dengan widgets dan charts
5. **Add Advanced Features** (export, notifications, etc.)
6. **Customize & Polish** theme dan navigation
7. **Testing** menyeluruh semua fitur
8. **Deploy** ke production

## 📞 Support & Questions

Jika ada pertanyaan atau butuh bantuan implementasi, silakan:
- Review Filament documentation
- Check Laravel best practices
- Test di local environment terlebih dahulu
- Backup database sebelum testing

---

**Version:** 1.0  
**Last Updated:** 2024  
**Status:** Planning Complete ✅