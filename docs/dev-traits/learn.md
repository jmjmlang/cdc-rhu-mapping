# learn.md - Lessons and Gotchas

Living log of bugs, root causes, fixes, and prevention rules. Read before feature work; append new repeatable lessons.

## Entry Format

```text
### [SHORT TITLE]
- Encountered: [date/phase]
- Symptom: [observable failure]
- Root Cause: [why]
- Fix: [what changed]
- Prevention Rule: [what to check next time]
```

## Caching

### Do Not Cache Eloquent Collections on WAMP/Windows
- Encountered: 2026-04-04.
- Symptom: `Attempt to read property "id" on string` after caching model collections.
- Root Cause: File cache serialization can round-trip Eloquent collection items as strings.
- Fix: Remove `Cache::remember` around `->get()` / `->first()` Eloquent results.
- Prevention Rule: Cache only scalars, counts/sums, plain arrays, or non-Eloquent aggregate collections.

## Database and Migrations

### MySQL VARCHAR Index Length Failure
- Symptom: `Specified key was too long`.
- Root Cause: `utf8mb4` indexed strings exceed key limits without length control.
- Fix: `Schema::defaultStringLength(191)` in `AppServiceProvider::boot()`.
- Prevention Rule: Verify before migrations.

### Migration Order Dependency
- Symptom: `Cannot add foreign key constraint` on child tables.
- Root Cause: Referenced parent table migration ran later.
- Fix: Order parents before children: `users` -> `municipalities` -> `barangays` -> `health_categories` -> `case_reports`.
- Prevention Rule: Check timestamps and `php artisan migrate:status`.

### Avoid PHP Attribute Fillable Syntax
- Symptom: Model expansion became brittle/inconsistent with attributes like `#[Fillable]`.
- Root Cause: Attribute syntax is less composable and inconsistent here.
- Fix: Use `protected $fillable` / `$hidden` arrays.
- Prevention Rule: Never add `#[Fillable]` / `#[Hidden]` in this project.

## Auth and Middleware

### Breeze Redirect Loop / Wrong Landing
- Symptom: Login loops or routes every role incorrectly.
- Root Cause: Breeze default intended redirect did not match role dispatch.
- Fix: Route both roles through `dashboard` or explicitly route by role.
- Prevention Rule: Test login for admin and citizen after auth changes.

### Dashboard Redirect Return Type Must Include RedirectResponse
- Encountered: 2026-06-04, RHU role branch.
- Symptom: `DashboardController::index(): Return value must be of type Illuminate\View\View, Illuminate\Http\RedirectResponse returned`.
- Root Cause: A new role branch returned `redirect()->route(...)` while the method was still typed as `View`.
- Fix: Import `Illuminate\Http\RedirectResponse` and type the method as `View|RedirectResponse`.
- Prevention Rule: When a controller action can return a redirect and a view, reflect both in the declared return type.

### EnsureRole Alias Missing
- Symptom: `role:admin` route/middleware errors.
- Root Cause: Middleware class existed but alias was not registered.
- Fix: Register `role` alias in `bootstrap/app.php`.
- Prevention Rule: Register middleware aliases before using them in routes.

### PreventBackHistory Must Not Be Global
- Encountered: 2026-04-08.
- Symptom: Navigation freezes/slows; BFCache disabled.
- Root Cause: Global `no-store` headers on all web responses.
- Fix: Register as alias and apply only to authenticated route groups.
- Prevention Rule: Never append no-store middleware globally.

## Leaflet / Map

### Gray Map Box
- Root Cause: Container has no explicit height.
- Fix: `id="phc-map"` with `style="height: 560px;"`.
- Prevention Rule: Tailwind height alone is not enough.

### `L is not defined`
- Root Cause: Leaflet script loads after page map code.
- Fix: Load Leaflet before `@stack('scripts')`.
- Prevention Rule: CDN before pushed scripts.

### Blank Tiles in Production
- Root Cause: HTTP tile URL on HTTPS page.
- Fix: Use `https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png`.
- Prevention Rule: HTTPS tiles only.

### Leaflet Overlaps Sidebar/Modals
- Root Cause: Leaflet panes use high z-index values.
- Fix: `.leaflet-container { z-index: 0 !important; position: relative; }`.
- Prevention Rule: Keep Leaflet inside a low stacking context.

### Empty Map Filter
- Root Cause: No approved reports in last 30 days, or wrong date scope.
- Fix: Approve/seed current reports and use `now()->subDays(30)->toDateString()`.
- Prevention Rule: Test with approved current data.

### Soft-Deleted Reports Hide Coordination Relations
- Encountered: 2026-06-06.
- Symptom: Case coordination page returned 500 with `Attempt to read property "barangay" on null`.
- Root Cause: `CaseReport` uses soft deletes, so `CaseReportAction::caseReport()` returned `null` for actions linked to soft-deleted reports even though `case_report_id` still pointed to an existing row.
- Fix: Load trashed reports through the action relationship and keep Blade relation output null-safe.
- Prevention Rule: For audit/coordination records that must survive report deletion, use `withTrashed()` on parent relations and null-safe display fallbacks.

## DSS

### DSS Empty
- Root Cause: DSS only analyses approved reports within 30 days.
- Fix: Approve reports or seed approved current data.
- Prevention Rule: Check report status/date before debugging DSS logic.

### `match()` Non-Exhaustive
- Root Cause: New category/risk value without fallback.
- Fix: Use `$this->thresholds[$category] ?? [5, 15, 30]` and `default` match arms.
- Prevention Rule: All status/risk/category matches need defaults.

## Environment

### Database Session/Cache Tables Missing
- Symptom: `sessions` or `cache` table missing.
- Root Cause: `.env` uses database session/cache stores before migrations run.
- Fix: Run migrations.
- Prevention Rule: Fresh setup is composer/npm/env/key/migrate/build before page load.

### Laravel Cloud APP_KEY Missing
- Symptom: `No application encryption key has been specified`.
- Root Cause: `APP_KEY` unset in production.
- Fix: Generate locally and set in Cloud env.
- Prevention Rule: Set `APP_KEY` first during deployment.

## UI / Blade

### Info Modal Section Structure
- Symptom: Awkward modal spacing and misaligned close button.
- Root Cause: Flat `p-6 space-y-*` wrapper lacks section hierarchy.
- Fix: Use `divide-y` sections with `px-5 py-4`; scroll body with `max-h-[60vh] overflow-y-auto`.
- Prevention Rule: Modal header/body/footer should be separated sections.

## Quick Checks

```powershell
php artisan route:list
php artisan migrate:status
npm run build
php artisan route:clear
php artisan view:clear
```
