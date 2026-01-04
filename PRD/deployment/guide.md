# ScentCents Deployment Guide

> **Status**: Documentation for future deployment  
> **Last Updated**: January 4, 2026

---

## Overview

This guide covers staging and production deployment options for ScentCents, a Laravel 12.x application with MySQL database and Redis queue support.

---

## Stack Requirements

| Component | Version | Purpose |
|-----------|---------|---------|
| PHP | 8.2+ | Laravel runtime |
| MySQL | 8.0+ | Primary database |
| Redis | 7.0+ | Queue & cache |
| Node.js | 18+ | Vite asset building |
| Composer | 2.x | PHP dependencies |

---

## Hosting Options Comparison

### Tier 1: Laravel-Optimized Platforms

| Platform | Monthly Cost | Setup Time | Best For |
|----------|--------------|------------|----------|
| **Laravel Forge** | $12 + VPS (~$6) | 30 min | Professional staging |
| **Laravel Vapor** | $39 + AWS usage | 1 hour | Serverless, auto-scaling |
| **Ploi.io** | $8 + VPS (~$6) | 30 min | Budget Forge alternative |

### Tier 2: PaaS (Zero Server Management)

| Platform | Monthly Cost | Setup Time | Best For |
|----------|--------------|------------|----------|
| **Railway** | Free tier → $5+ | 15 min | Quick staging, MySQL included |
| **Render** | Free tier → $7+ | 30 min | PostgreSQL only |
| **Fly.io** | Free tier → pay-as-you-go | 30 min | Global edge deployment |

### Tier 3: VPS (Manual Setup)

| Platform | Monthly Cost | Setup Time | Best For |
|----------|--------------|------------|----------|
| **DigitalOcean** | $6/mo | 1-2 hours | Reliable, good docs |
| **Hetzner** | €4/mo (~$5) | 1-2 hours | Best value |
| **Vultr** | $5/mo | 1-2 hours | Global locations |

---

## Recommended: Railway Deployment

Railway is recommended for staging due to ease of setup and MySQL support.

### Step 1: Create Railway Account
1. Go to [railway.app](https://railway.app)
2. Sign up with GitHub

### Step 2: Create Project
1. Dashboard → **New Project**
2. Select **Deploy from GitHub repo**
3. Choose `scentseeker` repository

### Step 3: Add MySQL Database
1. In project, click **New**
2. Select **Database → MySQL**
3. Railway auto-generates connection variables

### Step 4: Add Redis (for queues)
1. In project, click **New**
2. Select **Database → Redis**
3. Railway provides `REDIS_URL`

### Step 5: Create Build Configuration

Create `nixpacks.toml` in project root:

```toml
[phases.setup]
nixPkgs = ["php82", "php82Extensions.pdo_mysql", "php82Extensions.redis", "nodejs_18"]

[phases.install]
cmds = ["composer install --no-dev --optimize-autoloader", "npm install"]

[phases.build]
cmds = ["npm run build", "php artisan config:cache", "php artisan route:cache", "php artisan view:cache"]

[start]
cmd = "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT"
```

### Step 6: Configure Environment Variables

In Railway dashboard, set:

```env
APP_NAME=ScentCents
APP_ENV=staging
APP_KEY=base64:GENERATE_WITH_PHP_ARTISAN_KEY_GENERATE
APP_DEBUG=true
APP_URL=https://your-app.up.railway.app

DB_CONNECTION=mysql
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt

QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Railway auto-injects from plugins:
# DATABASE_URL, MYSQL_HOST, MYSQL_PORT, MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD
# REDIS_URL
```

### Step 7: Queue Worker Setup

Create separate Railway service for background jobs:

1. In project, click **New → Empty Service**
2. Point to same GitHub repo
3. Set start command: `php artisan queue:work --sleep=3 --tries=3`
4. Share environment variables with main app

### Step 8: Deploy

Push to GitHub → Railway auto-deploys

---

## Railway Pricing

| Resource | Free Tier | Paid |
|----------|-----------|------|
| Execution | $5 credit/month | $0.000231/min |
| MySQL | Included | Same pricing |
| Redis | Included | Same pricing |
| Bandwidth | 100GB free | $0.10/GB after |

**Estimated staging cost**: $5-10/month

---

## Alternative: DigitalOcean + Forge

For more control and professional setup:

### Step 1: Create DigitalOcean Droplet
- Size: Basic $6/mo (1GB RAM, 1 vCPU)
- Region: Closest to target users
- Image: Ubuntu 22.04

### Step 2: Connect Laravel Forge
1. Sign up at [forge.laravel.com](https://forge.laravel.com) ($12/mo)
2. Connect DigitalOcean account
3. Create server from existing Droplet

### Step 3: Configure Site
1. Add site with domain (or use Forge subdomain)
2. Connect GitHub repository
3. Configure environment variables
4. Enable SSL (free Let's Encrypt)

### Step 4: Database & Queue
- Forge auto-creates MySQL
- Enable queue worker in Forge UI
- Configure Redis if needed

**Total cost**: ~$18/month

---

## Pre-Deployment Checklist

Before deploying to staging:

- [ ] All tests passing locally
- [ ] Environment variables documented
- [ ] Database migrations ready
- [ ] Queue jobs tested
- [ ] Assets built (`npm run build`)
- [ ] `.env.example` updated
- [ ] Error handling for production
- [ ] Logging configured
- [ ] Admin panel secured

---

## Post-Deployment Checklist

After deploying:

- [ ] Run migrations
- [ ] Verify database connection
- [ ] Test queue workers
- [ ] Check error logs
- [ ] Test critical user flows
- [ ] Verify SSL certificate
- [ ] Set up monitoring (optional)

---

## Environment Variables Reference

```env
# Application
APP_NAME=ScentCents
APP_ENV=staging|production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://scentcents.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=scentcents
DB_USERNAME=scentcents_user
DB_PASSWORD=secure_password

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=alerts@scentcents.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Useful Commands

```bash
# Generate app key
php artisan key:generate --show

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear caches
php artisan optimize:clear

# Build assets
npm run build
```

---

## Notes

- Railway is best for quick staging with minimal setup
- DigitalOcean + Forge is best for production-like environment
- Always test migrations locally before deploying
- Keep staging environment variables separate from production
