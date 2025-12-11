# 🚀 Quick Start Guide - Filament Admin Panel

## 📌 Current Status

✅ **Phase 1 Complete** - Setup & Installation Done!

- Filament v3.2 installed
- Database connected (Supabase PostgreSQL)
- Admin user created
- Server running

---

## 🔑 Login Credentials

**Admin Panel:** http://localhost:8000/admin

```
Email:    admin@resikan.com
Password: password
```

⚠️ **Change password after first login!**

---

## 🎯 Next Steps (Phase 2)

### Step 1: Create Models (15 minutes)

```bash
# Navigate to project
cd resikan_php

# Create all models
php artisan make:model Booking
php artisan make:model BookingItem
php artisan make:model Service
php artisan make:model Cleaner
php artisan make:model Payment
php artisan make:model Rating
php artisan make:model Subscription
```

### Step 2: Configure Models

Copy model code from `/docs/filament-step-by-step-guide.md` sections 2.3 to 2.9

**Key Models to Configure:**

1. **Service.php** - Add fillable, casts, relationships
2. **Booking.php** - Add fillable, casts, relationships
3. **BookingItem.php** - Add fillable, casts, relationships

### Step 3: Generate First Resource (User)

```bash
php artisan make:filament-resource User --generate --view
```

Then customize the UserResource using `/docs/filament-resources-structure.md`

### Step 4: Generate Service Resource

```bash
php artisan make:filament-resource Service --generate --view
```

### Step 5: Generate Booking Resource

```bash
php artisan make:filament-resource Booking --generate --view
```

---

## 🏃 Quick Commands

```bash
# Start server
php artisan serve

# Access admin panel
# Visit: http://localhost:8000/admin

# Clear caches (if needed)
php artisan optimize:clear

# Check routes
php artisan route:list | grep admin

# Test database connection
php artisan tinker
>>> User::count()
>>> Service::count()
>>> Booking::count()
>>> exit
```

---

## 📚 Documentation

All documentation in `/docs` folder:

- **README.md** - Overview & navigation
- **filament-step-by-step-guide.md** - Complete implementation steps
- **filament-resources-structure.md** - Detailed resource code
- **filament-dashboard-widgets.md** - Dashboard implementation
- **IMPLEMENTATION_PROGRESS.md** - Track your progress

---

## 🐛 Troubleshooting

### Issue: "Table not found"
```bash
# Check if tables exist
php artisan tinker
>>> DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'")
```

### Issue: "Class not found"
```bash
composer dump-autoload
php artisan clear-compiled
```

### Issue: "500 Error"
```bash
php artisan optimize:clear
tail -50 storage/logs/laravel.log
```

### Issue: "Permission denied"
```bash
chmod -R 775 storage bootstrap/cache
```

---

## 🎨 Customization Quick Tips

### Change Brand Name
Edit `app/Providers/Filament/AdminPanelProvider.php`:
```php
->brandName('Resikan Admin')
```

### Change Colors
```php
use Filament\Support\Colors\Color;

->colors([
    'primary' => Color::Blue,
])
```

### Add Navigation Badge
In your Resource:
```php
protected static ?string $navigationBadge = fn () => static::getModel()::where('status', 'pending')->count();
```

---

## ✅ Implementation Checklist

### Phase 1: Setup ✅
- [x] Filament installed
- [x] Database connected
- [x] Admin user created
- [x] Server running

### Phase 2: Models (NEXT)
- [ ] Create all models
- [ ] Add fillable fields
- [ ] Add casts
- [ ] Add relationships
- [ ] Test with tinker

### Phase 3: Resources
- [ ] UserResource
- [ ] ServiceResource
- [ ] BookingResource
- [ ] Test CRUD operations

### Phase 4: Dashboard
- [ ] Stats widgets
- [ ] Chart widgets
- [ ] Table widgets

### Phase 5: Advanced
- [ ] Export to Excel
- [ ] Bulk actions
- [ ] Notifications
- [ ] Bahasa Indonesia

### Phase 6: Polish
- [ ] Branding
- [ ] Navigation
- [ ] Dark mode
- [ ] Mobile responsive

---

## 🎯 Goals for Today

**Minimum:**
- [ ] Create all models
- [ ] Generate UserResource
- [ ] Test login & CRUD

**Target:**
- [ ] Create Service & Booking resources
- [ ] Test all CRUD operations
- [ ] Basic dashboard working

**Stretch:**
- [ ] Add first widget
- [ ] Configure navigation
- [ ] Add export functionality

---

## 📞 Need Help?

- **Docs:** Check `/docs` folder
- **Filament Docs:** https://filamentphp.com/docs
- **Discord:** Filament community
- **GitHub:** Filament issues

---

## 💾 Save Your Work

```bash
# Commit your changes regularly
git add .
git commit -m "Phase 2: Created models and resources"
git push
```

---

**Time Estimate:** 2-3 hours for Phases 2-3  
**Difficulty:** Medium  
**Prerequisites:** PHP 8.2+, Composer, PostgreSQL knowledge

---

**Last Updated:** 2024-12-10  
**Version:** 1.0  
**Status:** Ready to Continue! 🚀

Good luck with the implementation! 🎉