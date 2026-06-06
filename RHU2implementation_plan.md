# RHU Role Implementation Plan

## Background

The PHC Mapping system currently has two roles: `admin` (full system access) and `citizen` (report submitter). The request is to add a **Rural Health Unit (RHU)** role — the municipal health office staff — positioned between admin and citizen.

The RHU role represents the health professionals at the barangay/municipality level who need actionable health intelligence without needing full system administration access. Per Philippine health law (RA 7160, RA 11223), the RHU is responsible for disease surveillance, case monitoring, and health program delivery at the municipal level.

---

## Scope Decision: What's Realistic & Genuinely Useful

From the web research + codebase audit, the most valuable features for an RHU portal that **reuse existing infrastructure** (DssService, CaseReport, Barangay, HealthCategory) without overengineering are:

| Feature | Value | Complexity |
|---|---|---|
| RHU Dashboard with health pulse stats | High — at-a-glance overview | Low |
| Morbidity ranking table (top diseases) | High — core surveillance | Low |
| Barangay risk table (from DssService) | High — reuses DssService directly | Low |
| Disease trend chart (month-over-month) | High — outbreak signal detection | Medium |
| Risk distribution doughnut chart | Medium — visual summary | Low |
| Quick report verification (approve/reject) | High — RHU staff as secondary verifier | Medium |
| Dedicated map view (RHU-scoped) | Medium — reuses map infrastructure | Low |

**Not included (overengineered for current scope):**
- Immunization tracking (needs new tables: vaccines, coverage targets)
- Maternal/child health registry (needs new tables: pregnant women, children)
- Referral management (needs new tables: referrals)
- Notification/alert system (needs Laravel Notifications + DB tables)

These are excellent Phase 2 features but should not block the current RHU role implementation.

---

## User Review Required

> [!IMPORTANT]
> The `role` enum on `users` currently only allows `'admin'` and `'citizen'`. Adding `'rhu'` requires a new migration that ALTERs the enum. On MySQL, altering an enum is an in-place schema change. This is safe on development, but should be reviewed before running on production (Laravel Cloud).

> [!IMPORTANT]
> The RHU role will be able to **approve and reject case reports** submitted by citizens. This gives them elevated power over citizen submissions. Please confirm this is the intended behavior, or if they should only have read-only access to reports.

> [!NOTE]
> The existing `EnsureRole` middleware already supports any role string — no changes needed. The new `role:rhu` middleware guard will just work.

---

## Proposed Changes

### 1. Database Layer

#### [NEW] `database/migrations/2026_06_04_000001_add_rhu_to_users_role_enum.php`
- ALTERs the `role` enum from `['admin', 'citizen']` to `['admin', 'citizen', 'rhu']`
- Uses MySQL `MODIFY COLUMN` via raw DB statement (Laravel doesn't have a built-in enum alter method)

#### [MODIFY] `database/seeders/AdminUserSeeder.php`
- Add an RHU test user: `rhu@gmail.com` / `rhu1234` with `role = 'rhu'`

---

### 2. Model Layer

#### [MODIFY] `app/Models/User.php`
- Add `isRhu(): bool` helper method (mirrors existing `isAdmin()` / `isCitizen()`)

---

### 3. Controller Layer

#### [NEW] `app/Http/Controllers/RHU/DashboardController.php`
Handles the RHU dashboard. Uses `DssService` to produce:
- `$summary` — total cases, affected barangays, critical/high counts (last 30 days)
- `$grouped` — per-barangay DSS risk table
- `$topDiseases` — top 5 morbidity ranking with case counts
- `$trendData` — month-over-month case counts by disease for the last 2 months
- `$riskCounts` / `$riskLabels` — for the risk distribution doughnut chart

#### [NEW] `app/Http/Controllers/RHU/ReportVerificationController.php`
Handles RHU report verification actions:
- `approve(CaseReport $report)` — approves a pending report
- `reject(CaseReport $report)` — rejects a pending report
- These mirror the existing `Admin\CaseReportVerificationController` methods

---

### 4. Routes

#### [MODIFY] `routes/web.php`
Add a new RHU route group:

```php
// RHU actions
Route::middleware(['auth', 'role:rhu', 'prevent-back-history'])
    ->prefix('rhu')
    ->name('rhu.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\RHU\DashboardController::class, 'index'])
            ->name('dashboard');
        Route::patch('/reports/{report}/approve', [\App\Http\Controllers\RHU\ReportVerificationController::class, 'approve'])
            ->name('reports.approve');
        Route::patch('/reports/{report}/reject', [\App\Http\Controllers\RHU\ReportVerificationController::class, 'reject'])
            ->name('reports.reject');
    });
```

Also update `DashboardController::index()` to handle the `rhu` role and redirect to `rhu.dashboard`.

---

### 5. View Layer

#### [NEW] `resources/views/pages/rhu/dashboard.blade.php`
The RHU dashboard view — distinct look from the admin dashboard. Sections:

1. **Health Pulse stats row** — 4 stat cards: Total Cases (30d), Affected Barangays, Critical Alerts, High Risk Alerts
2. **Top Morbidity table** — table of top 5 diseases ranked by total approved cases in the last 30 days with trend arrows
3. **Barangay Risk Summary** — table showing each barangay, its worst DSS risk level (badge-colored), total cases, and dominant disease
4. **Disease Trend chart** — grouped bar chart (Chart.js, already loaded) showing this month vs. last month case counts per disease
5. **Risk Distribution doughnut** — small doughnut showing Low/Moderate/High/Critical distribution (reuses DssController palette)
6. **Pending Reports table** — table of pending citizen reports (last 30d) with Approve/Reject buttons

---

### 6. Navigation

#### [MODIFY] `resources/views/layouts/navigation.blade.php`
Add RHU-specific nav block (mirrors the admin block):

```blade
@if(Auth::user()->isRhu())
    <p class="...">Surveillance</p>
    <x-nav-link :href="route('rhu.dashboard')" ...>Dashboard</x-nav-link>
    <x-nav-link :href="route('map.index')" ...>Map</x-nav-link>
@endif
```

---

### 7. DashboardController (shared)

#### [MODIFY] `app/Http/Controllers/DashboardController.php`
Add an `isRhu()` branch that redirects to `rhu.dashboard`:
```php
if ($user->isRhu()) {
    return redirect()->route('rhu.dashboard');
}
```

---

## Verification Plan

### Manual Verification
1. Run `php artisan migrate` — verify no enum error
2. Run `php artisan db:seed --class=AdminUserSeeder` — verify RHU user created
3. Log in as `rhu@gmail.com` — verify redirect to RHU dashboard
4. Verify admin/citizen cannot access `/rhu/*` routes (403)
5. Verify RHU cannot access `/admin/*` routes (403)
6. Verify charts render with live data
7. Approve/reject a pending report as RHU — verify it works

### Route Clear After Changes
```bash
php artisan route:clear && php artisan view:clear
```

---

## Open Questions

> [!IMPORTANT]
> **Report Verification**: Should the RHU role be able to approve/reject citizen reports? Or read-only only? The plan above gives them verification power. Please confirm.

> [!IMPORTANT]
> **Data Scope**: Should the RHU see data from **all barangays** in the municipality, or only the barangays they're assigned to? Currently the system has only one municipality (Luna, Apayao) so scoping by barangay_id would require assigning specific barangays to an RHU user. The simpler approach (and the one in this plan) is **municipality-wide** — all approved reports visible to all RHU users.
