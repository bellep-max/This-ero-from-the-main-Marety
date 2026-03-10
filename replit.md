# AfterDarkAudio - Replit Project

## Overview
AfterDarkAudio is a comprehensive audio content platform for adult creators and listeners. It supports songs, podcasts, adventures (interactive multi-part audio), radio stations, and artist management with social features.

## Architecture (Split Codebase)
The project has been split from a Laravel+Inertia monolith into two separate codebases:

### Frontend (`frontend/`)
- **Framework**: Vite + Vue 3 SPA (standalone, no Inertia/Ziggy)
- **Routing**: Vue Router (all 38 pages mapped)
- **State**: Pinia stores (auth, app, track)
- **API Client**: Axios with cookie-based auth
- **Styling**: Tailwind CSS + Bootstrap Vue Next + SCSS
- **Port**: 5000 (webview)
- **Key files**:
  - `src/main.ts` — app entry point
  - `src/router/index.ts` — all routes
  - `src/api/client.ts` — Axios instance with CSRF + credentials
  - `src/api/endpoints.ts` — API URL constants
  - `src/helpers/route.ts` — route() compatibility helper (maps Ziggy route names to URLs)
  - `src/helpers/useForm.ts` — useForm() composable (replaces Inertia's useForm)
  - `src/stores/` — Pinia stores (auth.ts, app.ts, track.js)
  - `src/Services/` — Service layer (AuthService, PlaylistService, etc.)
  - `src/pages/` — All 38 page components
  - `src/Components/` — All 69 reusable components
  - `src/Layouts/` — AppLayout, SettingsLayout, UserLayout + Header/Footer

### Backend API (`Afterdarkv2-main/`)
- **Framework**: Laravel 12 (PHP 8.2)
- **Auth**: Laravel Sanctum (SPA cookie-based)
- **API Prefix**: `/api/v1/`
- **Routes**: `routes/api_v1.php`
- **Port**: 8000 (console workflow)
- **Key API controllers** (`app/Http/Controllers/Api/V1/`):
  - `AuthController` — login, register, logout, user
  - `InitController` — shared app data (menus, filter presets, config)
  - `HomepageController`, `DiscoverController`, `TrendingController`, `SearchController`
  - `SongController`, `AdventureController`, `GenreController`, `ChannelController`
  - `BlogController`, `StreamController`
  - `UserProfileController`, `PlaylistController`, `PodcastController`
  - `CommentController`, `FollowerController`, `FollowingController`, `FavoriteController`
  - `NotificationController`, `ReportController`, `ActionController`
  - `UploadController`, `SettingsController`, `DownloadController`, `ShareController`
  - `UserSubscriptionController`, `UserLinktreeController`, `TermsController`
- **Admin panel**: Blade-based, untouched at `/admin`

## Database
- **Engine**: PostgreSQL (Replit built-in)
- **Driver config**: Session, cache, queue all use database driver (no Redis)

## Workflows
1. **Start application** — Runs frontend Vite dev server on port 5000
2. **Backend API** — Runs Laravel API on port 8000

## Frontend → Backend Communication
- Frontend Vite config proxies `/api/*` and `/sanctum/*` to `http://localhost:8000`
- Auth uses Sanctum SPA cookies (withCredentials: true on Axios)
- CSRF: Frontend calls `/sanctum/csrf-cookie` before auth requests
- CORS configured in `config/cors.php` to allow frontend origin

## Key Fixes Made for Replit/PostgreSQL
- Removed MySQL-specific `utf8mb4_bin` collation from comments migration
- Removed MySQL charset/collation from song_tags migration
- Simplified media library migration (removed MySQL JSON_EXTRACT functions)
- Fixed composite index migration to use PostgreSQL's `pg_indexes`
- Fixed PayPalInit command to defer PayPal client initialization
- Session/cache/queue switched from Redis to database driver
- Cache headers disabled in local development environment

## Admin
- Admin path: `/admin`
- Admin email: `admin@admin.com` (configurable via `APP_ADMIN_EMAIL`)
