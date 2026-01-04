# Seller Portal - Future Development Plan

> **Status**: 📋 PLANNED (Future Phase)  
> **Priority**: Medium  
> **Estimated Effort**: 2-3 weeks  
> **Last Updated**: January 4, 2026

---

## Executive Summary

Enable sellers to self-manage their product data through a dedicated portal, reducing admin workload and improving data freshness. Sellers can upload Excel files, view their staging data, and track processing status.

---

## Problem Statement

### Current State
- Admin must manually receive Excel files from sellers (email, WhatsApp, etc.)
- Admin uploads files via Filament dashboard
- Single point of bottleneck for all seller data updates
- No visibility for sellers on their data status

### Proposed State
- Sellers have their own portal login
- Sellers upload files directly
- Sellers can view their staging/production data
- Admin reviews and approves before production
- Scalable to 50+ sellers

---

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────┐
│                         ScentSeeker Platform                        │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌──────────────────────┐       ┌──────────────────────┐           │
│  │   Admin Panel        │       │   Seller Portal      │           │
│  │   /admin             │       │   /seller            │           │
│  ├──────────────────────┤       ├──────────────────────┤           │
│  │ • Full CRUD          │       │ • Upload Excel       │           │
│  │ • Process staging    │       │ • View own staging   │           │
│  │ • User management    │       │ • View own prices    │           │
│  │ • All sellers view   │       │ • Download template  │           │
│  │ • Approve data       │       │ • View status        │           │
│  └──────────────────────┘       └──────────────────────┘           │
│              │                            │                         │
│              └────────────┬───────────────┘                         │
│                           ▼                                         │
│                  ┌─────────────────┐                                │
│                  │   Shared DB     │                                │
│                  │   - users       │                                │
│                  │   - sellers     │                                │
│                  │   - staging_*   │                                │
│                  │   - perfumes    │                                │
│                  │   - prices      │                                │
│                  └─────────────────┘                                │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Database Changes

### 1. Modify Users Table

```php
// Migration: add_seller_relation_to_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('seller_id')->nullable()->constrained()->nullOnDelete();
    $table->enum('role', ['admin', 'seller', 'user'])->default('user');
});
```

### 2. User-Seller Relationship

```php
// User.php
public function seller(): BelongsTo
{
    return $this->belongsTo(Seller::class);
}

public function isSeller(): bool
{
    return $this->role === 'seller' && $this->seller_id !== null;
}

public function isAdmin(): bool
{
    return $this->role === 'admin' || $this->is_admin === true;
}
```

### 3. Seller Model Update

```php
// Seller.php
public function users(): HasMany
{
    return $this->hasMany(User::class);
}
```

---

## Filament Implementation

### 1. Create Seller Panel Provider

```php
// app/Providers/Filament/SellerPanelProvider.php

class SellerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('seller')
            ->path('seller')
            ->login()
            ->colors([
                'primary' => Color::Teal,
            ])
            ->discoverResources(in: app_path('Filament/Seller/Resources'), for: 'App\\Filament\\Seller\\Resources')
            ->discoverPages(in: app_path('Filament/Seller/Pages'), for: 'App\\Filament\\Seller\\Pages')
            ->discoverWidgets(in: app_path('Filament/Seller/Widgets'), for: 'App\\Filament\\Seller\\Widgets')
            ->middleware([
                EncryptCookies::class,
                // ... other middleware
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->tenant(Seller::class);  // Multi-tenancy by seller
    }
}
```

### 2. Seller Panel Resources

```
app/Filament/Seller/
├── Pages/
│   └── Dashboard.php           # Seller stats overview
├── Resources/
│   ├── StagingDataResource.php # View/upload staging data
│   └── PriceResource.php       # View production prices (read-only)
└── Widgets/
    ├── UploadWidget.php        # Quick upload widget
    └── StatusWidget.php        # Processing status
```

### 3. Panel Access Control

```php
// User.php
public function canAccessPanel(Panel $panel): bool
{
    return match ($panel->getId()) {
        'admin' => $this->isAdmin(),
        'seller' => $this->isSeller(),
        default => false,
    };
}
```

---

## Seller Features

### Dashboard
| Widget | Description |
|--------|-------------|
| **Upload Quick Action** | One-click Excel upload |
| **Processing Status** | Pending, Processed, Failed counts |
| **Recent Uploads** | Last 5 uploads with status |
| **Product Stats** | Total perfumes, prices, avg price |

### Staging Data View
- **Read-only** for processed items
- **Delete** option for pending items (before processing)
- **Filter** by batch, status, date
- **Export** option for their own data

### Upload Feature
- Same Excel template as admin
- Seller code auto-filled (from logged-in user's seller)
- File validation before staging
- Immediate feedback on parsing errors

### Production View (Read-Only)
- View their perfumes in production
- View their current prices
- See when data was last updated
- Cannot edit (admin only)

---

## Seller Onboarding Flow

```
┌──────────────────────────────────────────────────────────────────┐
│ STEP 1: Admin creates Seller                                    │
│ - Name, Code, Website, etc.                                      │
│ - Check "Create User Account" option                             │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 2: System creates User                                      │
│ - Auto-generate email: {seller_code}@seller.scentseeker.com     │
│ - Generate temporary password                                    │
│ - Set role = 'seller'                                            │
│ - Link seller_id                                                 │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 3: Send Welcome Email                                       │
│ - Login credentials                                              │
│ - Link to seller portal                                          │
│ - Link to Excel template                                         │
│ - Getting started guide                                          │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 4: Seller logs in                                           │
│ - Must change password on first login                            │
│ - Can update email to their own                                  │
│ - Downloads template                                             │
│ - Uploads first Excel file                                       │
└──────────────────────────────────────────────────────────────────┘
```

---

## Implementation Phases

### Phase 1: Foundation (Week 1)
- [ ] Add `seller_id` and `role` columns to users table
- [ ] Update User model with relationships
- [ ] Create SellerPanelProvider
- [ ] Configure authentication for seller panel
- [ ] Basic seller dashboard page

### Phase 2: Core Features (Week 2)
- [ ] Seller staging data resource (with tenant scoping)
- [ ] Upload widget with auto-seller detection
- [ ] Production prices view (read-only)
- [ ] Status widgets

### Phase 3: Onboarding (Week 2-3)
- [ ] "Create User" checkbox in SellerResource
- [ ] Welcome email notification
- [ ] Password change on first login
- [ ] Getting started documentation

### Phase 4: Polish (Week 3)
- [ ] Email notifications for processing status
- [ ] Export functionality
- [ ] Activity logging
- [ ] Testing and bug fixes

---

## Security Considerations

| Concern | Mitigation |
|---------|------------|
| **Data isolation** | Tenant scoping ensures sellers only see own data |
| **Password security** | Force password change on first login |
| **Session hijacking** | Separate session guards for each panel |
| **Brute force** | Rate limiting on login attempts |
| **Data tampering** | Sellers cannot edit production data |
| **Privilege escalation** | Role checks on all panel access |

---

## API Endpoints (Optional)

If sellers need programmatic access:

```
POST /api/seller/upload           # Upload Excel file
GET  /api/seller/staging          # List staging data
GET  /api/seller/prices           # List production prices
GET  /api/seller/status/{batch}   # Check batch status
```

Authentication via Sanctum with seller-scoped tokens.

---

## Success Metrics

| Metric | Target |
|--------|--------|
| Seller adoption rate | 80% within 3 months |
| Time to update prices | < 24 hours (vs 3-5 days) |
| Admin time saved | 50% reduction |
| Data accuracy | < 5% error rate |
| Seller satisfaction | > 4/5 rating |

---

## Dependencies

| Dependency | Version | Purpose |
|------------|---------|---------|
| filament/filament | ^3.2 | Admin/Seller panels |
| Laravel Sanctum | ^4.1 | API auth (optional) |
| Existing staging system | N/A | Reuse staging tables |

---

## Open Questions

1. **Should sellers be able to delete production data?**
   - Recommendation: No, only mark as "discontinued"

2. **How to handle seller disputes about data?**
   - Recommendation: Admin review queue for flagged items

3. **Should we charge sellers for the portal?**
   - Business decision, but can add subscription tier later

4. **What about API-only sellers?**
   - Can implement API access as Phase 5

---

## References

- [Filament Multi-tenancy](https://filamentphp.com/docs/panels/tenancy)
- [Laravel Authorization](https://laravel.com/docs/authorization)
- [Current Staging Implementation](/app/Services/DataIngestion/)

---

*Document prepared by: Senior Software Engineer*  
*Date: January 4, 2026*
