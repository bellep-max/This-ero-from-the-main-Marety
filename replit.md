# AfterDarkAudio - Replit Project

## Overview
AfterDarkAudio is a comprehensive audio content platform for adult creators and listeners. It supports songs, podcasts, adventures (interactive multi-part audio), radio stations, and artist management with social features.

## Tech Stack
- **Backend**: Laravel 12 (PHP 8.2)
- **Frontend**: Vue 3 + TypeScript + Inertia.js
- **Build**: Vite with `laravel-vite-plugin`
- **Database**: PostgreSQL (Replit built-in)
- **State Management**: Pinia
- **Styling**: Tailwind CSS + Bootstrap Vue Next
- **Queue**: Database driver (no Redis required)
- **Session**: Database driver
- **Cache**: Database driver

## Project Structure
- `Afterdarkv2-main/` - Main project directory
  - `app/` - Laravel backend (Controllers, Models, etc.)
  - `resources/js/` - Vue 3 frontend (Pages, Components, Layouts, Stores)
  - `database/migrations/` - Database migrations
  - `public/` - Public assets
  - `storage/` - File storage
  - `start.sh` - Startup script

## Running the App
The workflow runs `start.sh` which:
1. Clears Laravel caches
2. Starts Vite dev server (port 5173) for HMR
3. Starts Laravel on `0.0.0.0:5000`

## Environment
- App URL is set to `$REPLIT_DEV_DOMAIN` in `.env`
- Database credentials come from Replit PostgreSQL env vars
- Session/queue/cache all use database driver (no Redis needed)

## Key Fixes Made for Replit/PostgreSQL
- Removed MySQL-specific `utf8mb4_bin` collation from comments migration
- Removed MySQL charset/collation from song_tags migration  
- Simplified media library migration (removed MySQL JSON_EXTRACT functions)
- Fixed composite index migration to use PostgreSQL's `pg_indexes` instead of `information_schema.statistics`
- Fixed PayPalInit command to defer PayPal client initialization to `handle()` method
- Session/cache/queue switched from Redis to database driver

## Admin
- Admin path: `/admin`
- Admin email: `admin@admin.com` (configurable via `APP_ADMIN_EMAIL`)
