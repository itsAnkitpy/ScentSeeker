# Phase C: Admin & Management - Implementation Plan

> **Target Duration**: 1-2 weeks  
> **Priority**: 🔴 Critical  
> **Goal**: Enable content management through a visual admin panel instead of CLI commands

---

## Why We Need This

Currently, managing ScentSeeker data requires:
- Running artisan commands to import perfumes
- Direct database queries to update prices
- No visual way to review staging data before publishing

**With an admin panel, you can:**
- Add/edit/delete perfumes, sellers, prices visually
- Review and approve staged data from scrapers
- See dashboard stats at a glance
- Manage users without touching the database

---

## What We're Building

### Filament Admin Panel

[Filament](https://filamentphp.com) is a modern admin panel for Laravel that:
- Auto-generates CRUD interfaces from your models
- Includes authentication, permissions, widgets
- Works perfectly with existing Laravel models
- Zero custom frontend code needed

---

## Proposed Changes

### C.1: Install and Configure Filament

#### Install via Composer

```bash
composer require filament/filament:"^3.2"
php artisan filament:install --panels
```

This creates:
- `/app/Providers/Filament/AdminPanelProvider.php` - Panel configuration
- `/app/Filament/` - Directory for resources, pages, widgets

#### Configure Admin User Access

**Why**: Only certain users should access the admin panel.

**Simple approach**: Add `is_admin` boolean to users table.

##### [NEW] Migration: add_is_admin_to_users_table.php

```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_admin')->default(false);
});
```

##### [MODIFY] AdminPanelProvider.php

Configure panel to only allow admin users:

```php
->authMiddleware([
    Authenticate::class,
])
->login()
```

---

### C.2: Perfume Admin Resource

#### [NEW] app/Filament/Resources/PerfumeResource.php

```bash
php artisan make:filament-resource Perfume --generate
```

This auto-generates a CRUD interface for perfumes with:
- List view with search, filters, pagination
- Create/Edit forms with all fields
- Delete confirmation

**Customizations needed:**
- Add JSON editor for `notes` field
- Add image preview for `image_url`
- Show related prices count

---

### C.3: Seller Admin Resource

#### [NEW] app/Filament/Resources/SellerResource.php

```bash
php artisan make:filament-resource Seller --generate
```

Features:
- List sellers with their ratings
- Filter by seller type (official, reddit, online)
- Show count of prices per seller

---

### C.4: Price Admin Resource

#### [NEW] app/Filament/Resources/PriceResource.php

```bash
php artisan make:filament-resource Price --generate
```

Features:
- Show price with related perfume and seller names
- Filter by stock status
- Quick edit for price updates

---

### C.5: Staging Data Review Interface

**Why**: Before data from scrapers goes live, you need to review it.

#### [NEW] app/Filament/Resources/StagingPerfumeResource.php

```bash
php artisan make:filament-resource StagingPerfume --generate
```

Features:
- View pending staged perfumes
- Approve/reject actions
- Show validation status with color badges
- Bulk approve button

#### [NEW] app/Filament/Resources/StagingPriceResource.php

```bash
php artisan make:filament-resource StagingPrice --generate
```

---

### C.6: User Management

#### [NEW] app/Filament/Resources/UserResource.php

```bash
php artisan make:filament-resource User --generate
```

Features:
- List all users
- Toggle admin status
- View email verification status
- Cannot delete self

---

### C.7: Dashboard Widgets

#### [NEW] app/Filament/Widgets/StatsOverviewWidget.php

```bash
php artisan make:filament-widget StatsOverview --stats
```

Shows:
- Total perfumes count
- Total sellers count
- Total prices tracked
- Pending staging items count

---

## How It Works

### Architecture Diagram

```
┌─────────────────────────────────────────┐
│           Admin Panel (/admin)          │
│    ┌─────────────────────────────┐      │
│    │   Dashboard with Widgets    │      │
│    └─────────────────────────────┘      │
│                                         │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ │
│  │ Perfumes │ │ Sellers  │ │  Prices  │ │
│  └────┬─────┘ └────┬─────┘ └────┬─────┘ │
│       │            │            │       │
│  ┌────┴────────────┴────────────┴────┐  │
│  │         Staging Review            │  │
│  │  (StagingPerfume, StagingPrice)   │  │
│  └───────────────────────────────────┘  │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│        Existing Laravel Models          │
│   (Perfume, Seller, Price, User, etc)   │
└─────────────────────────────────────────┘
```

### User Flow

1. **Admin visits** `/admin` → Redirected to login if not authenticated
2. **Dashboard shows** key stats (perfumes, sellers, prices)
3. **Sidebar navigation** to each resource
4. **CRUD operations** on any resource
5. **Staging review** with approve/reject workflow

---

## Implementation Order

| Step | Task | Command / Files | Est. Time |
|------|------|-----------------|-----------|
| 1 | Install Filament | `composer require filament/filament` | 10 min |
| 2 | Run panel installer | `php artisan filament:install --panels` | 5 min |
| 3 | Add is_admin migration | New migration file | 10 min |
| 4 | Create first admin user | `php artisan tinker` | 5 min |
| 5 | C.2 - PerfumeResource | `make:filament-resource` | 30 min |
| 6 | C.3 - SellerResource | `make:filament-resource` | 20 min |
| 7 | C.4 - PriceResource | `make:filament-resource` | 20 min |
| 8 | C.5 - StagingPerfumeResource | `make:filament-resource` | 30 min |
| 9 | C.5 - StagingPriceResource | `make:filament-resource` | 20 min |
| 10 | C.6 - UserResource | `make:filament-resource` | 20 min |
| 11 | C.7 - StatsOverview widget | `make:filament-widget` | 20 min |
| 12 | Test admin panel | Manual testing | 30 min |

**Total Estimated Time**: ~4-5 hours

---

## Verification Plan

### Automated Tests

Since Filament generates standard Laravel code, existing tests should continue to pass:

```bash
php artisan test
```

### Manual Verification

1. **Access admin panel**:
   ```
   http://localhost:8000/admin
   ```
   - Should redirect to login
   - Non-admin users should see "forbidden" after login

2. **Create admin user** (via tinker):
   ```bash
   php artisan tinker
   >>> $u = \App\Models\User::first();
   >>> $u->is_admin = true;
   >>> $u->save();
   ```

3. **Test each resource**:
   - **Perfumes**: Create a new perfume, edit it, delete it
   - **Sellers**: List sellers, filter by type
   - **Prices**: View prices with perfume/seller names
   - **Staging**: View staged data, test approve action
   - **Users**: Toggle admin flag, verify can't delete self

4. **Dashboard widgets**:
   - Verify stats show correct counts
   - Compare with database: `Perfume::count()` etc.

---

## Success Criteria

- [ ] Filament installed and accessible at `/admin`
- [ ] Only users with `is_admin = true` can access panel
- [ ] Can CRUD perfumes through admin
- [ ] Can CRUD sellers through admin
- [ ] Can view/edit prices through admin
- [ ] Can review and approve staging data
- [ ] Can manage users (toggle admin, view list)
- [ ] Dashboard shows accurate stats
- [ ] All existing tests still pass

---

## Compatibility Notes

- **No breaking changes** to existing API routes
- **No changes** to public-facing pages
- Admin panel is a separate `/admin` route group
- Existing models are reused, not duplicated

---

## Files Summary

| File | Action | Priority |
|------|--------|----------|
| `composer.json` | MODIFIED (add filament) | 🔴 Critical |
| `AdminPanelProvider.php` | AUTO-CREATED by installer | 🔴 Critical |
| Migration: is_admin | CREATE | 🔴 Critical |
| `PerfumeResource.php` | CREATE | 🔴 Critical |
| `SellerResource.php` | CREATE | 🟡 High |
| `PriceResource.php` | CREATE | 🟡 High |
| `StagingPerfumeResource.php` | CREATE | 🟡 High |
| `StagingPriceResource.php` | CREATE | 🟡 High |
| `UserResource.php` | CREATE | 🟢 Medium |
| `StatsOverviewWidget.php` | CREATE | 🟢 Medium |

---

*Ready for implementation upon approval.*
