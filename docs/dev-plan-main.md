# PHC Mapping - Master Development Blueprint

Project: primary health care case reporting, verification, mapping, and DSS for Luna, Apayao.

Stack: Laravel 13, Breeze Blade, TailwindCSS, Alpine, MySQL, LeafletJS 1.9.4 + OSM, Laravel Cloud.

Last compacted: 2026-06-04.

## 1. Architecture

```text
citizen(auth) -> submit reports(pending)
admin(auth+role) -> approve/reject, manage users/categories, view DSS
authenticated users -> map and aggregate approved 30-day data
MySQL -> municipalities, barangays, health_categories, case_reports, users
```

Constraints:

- No external map API keys.
- `Schema::defaultStringLength(191)` in `AppServiceProvider`.
- `admin` reports may bypass pending when implemented that way; citizen reports default to `pending`.
- Map/DSS use only approved reports within a rolling 30-day window.
- Repeated UI markup must be a Blade component.

## 2. Environment

Requirements: PHP 8.3+, Composer 2, Node 20+, MySQL 8, Laravel 13.

Local `.env` essentials:

```dotenv
APP_NAME="PHC Mapping"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phc-mapping-db
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

Setup commands:

```powershell
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan storage:link
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

`migrate:fresh` is destructive and requires confirmation.

## 3. Schema

Current model:

```text
municipalities (1) -> (N) barangays (1) -> (N) case_reports <- (1) health_categories
                                  ^                  ^
                                users            users(reviewed_by)
```

Tables:

| Table | Key fields |
|---|---|
| `users` | `name`, `email`, `password`, `role enum(admin,citizen,rhu)`, nullable `barangay_id` |
| `municipalities` | expansion support for multi-municipality data |
| `barangays` | `municipality_id`, `name`, `latitude decimal(10,7)`, `longitude decimal(10,7)` |
| `health_categories` | `name unique`, nullable `description` |
| `case_reports` | `user_id nullable`, `barangay_id`, `health_category_id`, `number_of_cases`, `status enum(pending,approved,rejected)`, `report_date`, nullable `notes`, `reviewed_by`, `reviewed_at` |
| `case_report_actions` | `case_report_id`, `user_id`, `action_type`, `priority`, `audience enum(admin_only,citizen_visible,affected_citizens,all_users)`, `status enum(open,completed)`, `message`, nullable `due_date`, `completed_at` |

Migration rules:

- Parent tables before child tables.
- Use `foreignId()->constrained()` with explicit `restrictOnDelete`, `cascadeOnDelete`, or `nullOnDelete`.
- No migration squashing on Laravel Cloud.
- Seeders use `updateOrCreate`.
- Barangay coordinates are approximate until MHO/NAMRIA verified.

Seeded categories: Dengue, Tuberculosis, Malnutrition, Hypertension, Diarrhea.

Seeded credentials:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@gmail.com` | `admin1234` |
| RHU | `rhu@gmail.com` | `rhu1234` |
| Citizen | `johnmichael.talbo@gmail.com` | `citizen1234` |
| Citizen | `engiemar.balanay@gmail.com` | `citizen1234` |

## 4. Auth and Roles

- Breeze Blade auth with `users.role`.
- RHU users are municipal health staff with surveillance dashboard and report verification access.
- `EnsureRole` middleware registered as `role` in `bootstrap/app.php`.
- Middleware failures use `abort(403)`.
- `/dashboard` dispatches by role.
- Public auth extras such as email verification/password reset/confirm password are not used.

Route groups:

```php
Route::middleware(['auth', 'role:citizen'])->prefix('citizen')->name('citizen.')->group(...);
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(...);
Route::middleware(['auth', 'role:rhu'])->prefix('rhu')->name('rhu.')->group(...);
```

## 5. Core Logic

Citizen reporting:

- `index`: show own reports eager-loaded with `barangay` and `healthCategory`.
- `create`: load barangays and health categories.
- `store`: validate input; create report with `user_id = auth()->id()` and `status = pending`.

Admin verification:

- Pending reports eager-load `user`, `barangay`, `healthCategory`.
- Approve/reject update status and review metadata where available.
- Activity log records display names only, never full names.

RHU/admin coordination:

- `case_report_actions` links operational actions to real case reports.
- Admin and RHU share `/case-coordination` pages for open/completed actions.
- Actions marked `affected_citizens` appear as citizen dashboard announcements for matching barangays. Actions marked `all_users` appear as dashboard announcements for admin, RHU, and citizens.

Map API:

- Route: `/api/map-data`, auth-gated.
- Validate `health_category_id` when supplied.
- Query: `CaseReport::approved()->withinDays(30)->with(['barangay','healthCategory'])`.
- Aggregate by barangay/category and return coordinates + totals only.

## 6. Map

Layout:

- Leaflet CSS/JS must load before page scripts using `L`.
- Layout must render `@stack('scripts')` near `</body>`.

Blade:

```blade
<div id="phc-map" class="w-full" style="height: 560px;"></div>
```

JS:

- Put page code in `@push('scripts')`.
- Wrap in `(function () { ... })();`.
- Use HTTPS OSM tiles with attribution.
- Use layers/`clearLayers()` for filters.
- Use circles for severity/area overlays; markers only when exact point display is needed.

Severity/visualization rules should not change without plan update.

## 7. DSS

Admin-only 30-day analysis:

- Source: `CaseReport::approved()->withinDays(30)->with(['barangay','healthCategory'])`.
- Group: barangay + health category.
- Output: barangay, category, total cases, risk level, recommended tasks.
- Service: `app/Services/DssService.php`.

Thresholds `[moderate_min, high_min, critical_min]`:

| Category | Thresholds |
|---|---|
| Dengue | `[5, 15, 30]` |
| Tuberculosis | `[3, 10, 20]` |
| Malnutrition | `[5, 15, 25]` |
| Hypertension | `[10, 30, 50]` |
| Diarrhea | `[5, 15, 30]` |
| Fallback | `[5, 15, 30]` |

Risk levels: Low, Moderate, High, Critical. Thresholds are provisional and need MHO sign-off before official use.

## 8. Current Routes

| URL | Middleware | Name |
|---|---|---|
| `/` | none | redirects to `login` |
| `/login` | guest | `login` |
| `/register/pending` | none | `register.pending` |
| `/map` | auth | `map.index` |
| `/api/map-data` | auth | `api.map-data` |
| `/dashboard` | auth | `dashboard` |
| `/profile` | auth | `profile.edit` |
| `/admin/reports` | auth, role:admin | `admin.reports.index` |
| `/admin/health-categories` | auth, role:admin | `admin.health-categories.index` |
| `/admin/dss` | auth, role:admin | `admin.dss` |
| `/admin/users` | auth, role:admin | `admin.users.index` |
| `/admin/users/rhu` | auth, role:admin | `admin.users.rhu.store` |
| `/admin/case-coordination` | auth, role:admin | `admin.case-actions.index` |
| `/rhu/dashboard` | auth, role:rhu | `rhu.dashboard` |
| `/rhu/case-coordination` | auth, role:rhu | `rhu.case-actions.index` |
| `/rhu/reports/{report}/approve` | auth, role:rhu | `rhu.reports.approve` |
| `/rhu/reports/{report}/reject` | auth, role:rhu | `rhu.reports.reject` |
| `/citizen/health-guide` | auth, role:citizen | `citizen.health-guide` |

Check `routes/web.php` as the source of truth before editing routes.

## 9. Deployment - Laravel Cloud

Production env:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.laravel.cloud
SESSION_DRIVER=database
CACHE_STORE=database
```

Deployment checklist:

1. Build assets.
2. Set `APP_KEY` and DB env vars.
3. Deploy.
4. Run `php artisan migrate --force` through the Cloud mechanism.
5. Seed reference data only when intended.
6. Verify login, map, `/api/map-data`, admin reports, and DSS.

Never use `migrate:fresh` or migration squashing on production.

## 10. Known Planning Drift

Older snippets may contain stale route names such as `admin.dashboard` or old view paths. Treat this file as a blueprint, but inspect actual controllers/routes before copying code.
