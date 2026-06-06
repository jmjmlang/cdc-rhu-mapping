# AGENTS.md - PHC Mapping Agent Contract

Laravel 13 + Breeze Blade + TailwindCSS + Leaflet/OSM primary health care mapping system.

Last updated: 2026-06-04

## 1. Mode

Infer the mode from the task.

| Task type | First action |
|---|---|
| Planning or architecture | Read `docs/dev-plan-main.md` and the relevant note file. Surface trade-offs. Confirm before structural changes. |
| Build or edit | Read `docs/dev-traits/learn.md`, then `docs/dev-traits/traits.md`, then inspect existing files before editing. |
| Debug or regression | Do not edit first. Read `docs/dev-traits/learn.md`, inspect logs/symptoms, and write the root cause before fixing. |

Confirm before destructive DB operations, role/schema redesigns, barangay coordinate changes, DSS threshold changes, or production-impacting architecture changes.

## 2. Stack

- Backend: Laravel 13, Eloquent, MVC.
- Auth: Breeze Blade, unified `users.role` enum: `admin`, `citizen`, `rhu`.
- Frontend: Blade + TailwindCSS + Alpine. No React, Inertia, or Vue.
- UI: reusable Blade components in `resources/views/components/`; repeated markup becomes a component.
- Map: LeafletJS 1.9.4 + OpenStreetMap tiles. No external API keys.
- DB: MySQL database `phc-mapping-db`; `Schema::defaultStringLength(191)` required.
- Business logic: service layer, currently `app/Services/DssService.php`.
- Deployment: Laravel Cloud.

## 3. Hard Rules

- Read before writing; audit existing components, controllers, models, routes, migrations, and services.
- Keep controllers thin: validate, call model/service, return response.
- Never pass `$request->all()` to Eloquent writes; use validated data only.
- No N+1 queries; eager-load relationships used by Blade loops.
- Filter server-side; do not send unfiltered report collections to Blade for JS filtering.
- Prefer model scopes such as `approved()` and `withinDays(30)` over repeated raw `where()` chains.
- Middleware must `abort(403)` for role violations.
- Seeders must use `updateOrCreate`, never bare `create`.
- Every schema change is a new migration. Do not squash migrations on Laravel Cloud.
- Run `php artisan route:clear` and `php artisan view:clear` after route/layout changes.

## 4. Blade and UI Rules

Highest priority: no repeated HTML.

| Tag | File | Purpose |
|---|---|---|
| `<x-ui.button>` | `components/ui/button.blade.php` | primary/secondary/danger buttons |
| `<x-ui.badge>` | `components/ui/badge.blade.php` | status and risk badges |
| `<x-ui.alert>` | `components/ui/alert.blade.php` | flash messages |
| `<x-ui.card>` | `components/ui/card.blade.php` | content panel |
| `<x-form.input>` | `components/form/input.blade.php` | input + label + error |
| `<x-form.select>` | `components/form/select.blade.php` | select + label + error |
| `<x-form.label>` | `components/form/label.blade.php` | standalone label |
| `<x-table.wrapper>` | `components/table/wrapper.blade.php` | responsive table shell |
| `<x-table.heading>` | `components/table/heading.blade.php` | table heading cell |
| `<x-table.cell>` | `components/table/cell.blade.php` | table data cell |
| `<x-dss.risk-card>` | `components/dss/risk-card.blade.php` | DSS risk display |
| `<x-layouts.page-header>` | `components/layouts/page-header.blade.php` | page title/subtitle |

Component rules:

- Declare props with `@props([])` and defaults.
- Use slots for flexible content; named slots for multi-section components.
- Keep Tailwind classes in Blade/component files, never controllers.
- Put conditional status/risk colors inside components, especially badge/DSS components.
- Use semantic Tailwind tokens from `tailwind.config.js`: `primary-*`, `status-*`, `risk-*`, `surface*`.
- Do not use raw Tailwind status/risk colors such as `red-500` or `green-500` for statuses.
- Minimal, mobile-first, usable at 375px.
- No hover animations, scale/lift effects, colored side borders, or emoji unless requested.
- Cards/containers use `rounded-none`; buttons and badges may use `rounded`/`rounded-sm`.
- Prefer modals for report creation, detail, approval, and rejection flows when context should be preserved.

## 5. Map Rules

- Map container is always `id="phc-map"` with `style="height: 560px;"`.
- Leaflet CSS/JS must load before page map code; view scripts go through `@push('scripts')` and layout `@stack('scripts')`.
- Wrap map JS in an IIFE.
- Use HTTPS OSM tiles and required OSM attribution.
- `/api/map-data` returns only `CaseReport::approved()->withinDays(30)` data; no pending/rejected data and no PII.

## 6. Required References

| Area | Read |
|---|---|
| Full plan, phases, deployment | `docs/dev-plan-main.md` |
| Planning protocol | `docs/agent-planning-mode.md` |
| Build/edit protocol | `docs/agent-edit-mode.md` |
| Debug protocol | `docs/agent-debug-mode.md` |
| Migrations/seeders | `docs/notes/migration-standards.md` |
| Controllers/services/queries | `docs/notes/backend-rules.md` |
| Blade/components/layout | `docs/notes/blade-components.md` |
| Map work | `docs/notes/leaflet-osm-guide.md` |
| Conventions | `docs/dev-traits/traits.md` |
| Past gotchas | `docs/dev-traits/learn.md` |
| Capabilities/limits | `docs/dev-traits/skills.md` |

## 7. Current Domain Snapshot

Schema:

```text
municipalities -> barangays -> case_reports <- health_categories
                      ^               ^
                    users -----------/  (user_id + reviewed_by)
```

Key tables: `municipalities`, `barangays(municipality_id)`, `health_categories(description nullable)`, `case_reports(notes, reviewed_by, reviewed_at)`, `case_report_actions(case_report_id, user_id, action_type, audience, status)`, `users(role, barangay_id nullable)`.

Routes to treat as current source-of-truth unless code says otherwise:

| URL | Middleware | Who | Name |
|---|---|---|---|
| `/` | none | anyone | redirects to `login` |
| `/login` | guest | unauthenticated | `login` |
| `/register/pending` | none | post-signup | `register.pending` |
| `/map` | auth | logged-in users | `map.index` |
| `/api/map-data` | auth | logged-in users | `api.map-data` |
| `/dashboard` | auth | role dispatch | `dashboard` |
| `/profile` | auth | logged-in users | `profile.edit` |
| `/admin/reports` | auth, role:admin | admin | `admin.reports.index` |
| `/admin/health-categories` | auth, role:admin | admin | `admin.health-categories.index` |
| `/admin/dss` | auth, role:admin | admin | `admin.dss` |
| `/admin/users` | auth, role:admin | admin | `admin.users.index` |
| `/admin/users/rhu` | auth, role:admin | admin | `admin.users.rhu.store` |
| `/admin/case-coordination` | auth, role:admin | admin | `admin.case-actions.index` |
| `/rhu/dashboard` | auth, role:rhu | RHU staff | `rhu.dashboard` |
| `/rhu/case-coordination` | auth, role:rhu | RHU staff | `rhu.case-actions.index` |
| `/rhu/reports/{report}/approve` | auth, role:rhu | RHU staff | `rhu.reports.approve` |
| `/rhu/reports/{report}/reject` | auth, role:rhu | RHU staff | `rhu.reports.reject` |
| `/citizen/health-guide` | auth, role:citizen | citizen | `citizen.health-guide` |

Seeded credentials:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@gmail.com` | `admin1234` |
| RHU | `rhu@gmail.com` | `rhu1234` |
| Citizen | `johnmichael.talbo@gmail.com` | `citizen1234` |
| Citizen | `engiemar.balanay@gmail.com` | `citizen1234` |

## 8. Session Report

After every task, report:

```text
CHANGES MADE
  [file path]: [what changed] - [why it was needed]

LEFT UNCHANGED
  [what] - [why not changed]

CONFLICTS OR INACCURACIES FOUND
  [doc/code] states [X] but [reality is Y]

FUTURE DEV NOTES
  [implications for upcoming work]
```

## 9. Common Mistakes

| Avoid | Do instead |
|---|---|
| Repeated inline UI markup | Use/extract Blade components |
| Business logic in controllers/Blade | Move to service/model scope |
| RHU/admin communication as free-text chat | Link coordination actions to `case_reports` |
| `$request->all()` writes | Use validated payloads |
| Inline `status = approved` filters | Use `CaseReport::approved()` |
| Inline 30-day date filters | Use `CaseReport::withinDays(30)` |
| Hardcoded barangay coordinates outside seeder | Keep coordinates in `BarangaySeeder` |
| Global map JS | IIFE inside `@push('scripts')` |
| Missing Leaflet height | Add `style="height: 560px;"` |
| `migrate:fresh` without confirmation | Ask first |
| Role enum change without migration/plan | Plan and confirm first |
