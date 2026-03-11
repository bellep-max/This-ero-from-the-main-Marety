# AfterDarkAudio - Replit Project

## Overview
AfterDarkAudio is a comprehensive audio content platform for adult creators and listeners. It supports songs, podcasts, adventures (interactive multi-part audio), radio stations, and artist management with social features.

## Architecture (Split Codebase)
The project has been split from a Laravel+Inertia monolith into two separate codebases:

### Frontend (`frontend/`)
- **Framework**: Vite + React 18 + TypeScript SPA
- **Routing**: React Router v6 (35 lazy-loaded routes in App.tsx)
- **State**: Zustand stores (auth, app, track) with localStorage persistence
- **API Client**: Axios with cookie-based auth
- **UI**: React Bootstrap + SCSS + FontAwesome icons
- **i18n**: i18next + react-i18next
- **Carousels**: Swiper React
- **Port**: 5000 (webview)
- **Key files**:
  - `src/main.tsx` — React entry point with FontAwesome library setup
  - `src/App.tsx` — BrowserRouter with all 35 lazy-loaded routes
  - `src/api/client.ts` — Axios instance with CSRF + credentials
  - `src/api/endpoints.ts` — API URL constants
  - `src/helpers/route.ts` — route() compatibility helper (maps Ziggy route names to URLs)
  - `src/helpers/useForm.ts` — useForm() React hook (form state, validation, submit)
  - `src/stores/` — Zustand stores (auth.ts, app.ts, track.ts)
  - `src/Services/` — Service layer (AuthService, PlaylistService, etc.)
  - `src/pages/` — All 35 page components (TSX)
  - `src/Components/` — All 69+ reusable components (TSX)
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

## Key Library Mappings (Vue → React)
- Vue Router → React Router v6 (useParams, useNavigate, useSearchParams, Link)
- Pinia → Zustand (create, persist middleware)
- ref()/reactive() → useState/useMemo
- onMounted → useEffect([], [])
- bootstrap-vue-next → react-bootstrap
- vue3-toastify → react-toastify
- vue-multiselect → react-select
- @vueform/slider → rc-slider
- vue3-carousel → Swiper/SwiperSlide
- `<slot>` → children prop
- defineProps → TypeScript interface
- defineEmits → callback props
- `$t()` from i18n.ts (re-exported from i18next)

## Conversion Status
- All 35 pages converted from Vue SFC to React TSX
- All 69+ components converted from Vue SFC to React TSX
- All 3 stores converted from Pinia to Zustand
- All composables converted to React hooks
- Services and Enums unchanged (pure TS/JS, no framework deps)
- Build succeeds with 600 modules, zero compilation errors
- tsconfig strict: false for easier conversion
- Some backend API endpoints return 500 (service bindings not fully configured) — backend issues, not frontend

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
