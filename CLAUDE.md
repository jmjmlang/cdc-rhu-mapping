# CLAUDE.md - PHC Mapping Quick Context

Auto-loaded session guide. Use `AGENTS.md` for the full agent contract.

## 1. Project

- App: PHC case reporting, verification, mapping, and DSS for Luna, Apayao.
- Stack: Laravel 13, Breeze Blade, TailwindCSS, Alpine, MySQL, LeafletJS 1.9.4 + OSM.
- Roles: `admin`, `citizen`, `rhu` via `users.role`; no separate role tables.
- Auth: login/register/profile only; email verification, password reset, and confirm password are unused.
- DB: `phc-mapping-db`; `Schema::defaultStringLength(191)` is required.
- UI: mobile-first Blade components, semantic colors, no React/Inertia/Vue.
- Deployment: Laravel Cloud.

## 2. Work Mode

| Prompt sounds like | Mode | Read first |
|---|---|---|
| "How should we..." or schema/design | Planning | `docs/dev-plan-main.md`, then relevant notes |
| Build, edit, refactor, UI, route | Edit | `docs/dev-traits/learn.md`, `docs/dev-traits/traits.md` |
| Broken, 500, regression, blank map | Debug | `docs/dev-traits/learn.md`, logs, root-cause note before edits |

## 3. Hard Rules

1. No repeated UI markup; reusable patterns are Blade components.
2. Controllers stay thin; business logic belongs in services/model scopes.
3. Validate before writing; never pass `$request->all()` to Eloquent.
4. No N+1 queries; eager-load relationships used in Blade loops.
5. Map and DSS data expose only `approved()` reports within `withinDays(30)`.
6. Seeders use `updateOrCreate`.
7. Every schema change is a new migration; never squash on Laravel Cloud.
8. Confirm before destructive DB commands, role enum changes, DSS threshold changes, or coordinate changes.
9. Leaflet needs `id="phc-map"` and `style="height: 560px;"`.
10. Leaflet scripts load before `@stack('scripts')`; page map JS uses an IIFE.

## 4. Reference Map

| Work area | File |
|---|---|
| Agent contract | `AGENTS.md` |
| Planning/build/debug protocols | `docs/agent-*.md` |
| Roadmap, schema, DSS, deployment | `docs/dev-plan-main.md` |
| Past bugs | `docs/dev-traits/learn.md` |
| Naming/patterns | `docs/dev-traits/traits.md` |
| AI limits | `docs/dev-traits/skills.md` |
| Backend | `docs/notes/backend-rules.md` |
| Blade components | `docs/notes/blade-components.md` |
| Migrations | `docs/notes/migration-standards.md` |
| Leaflet/OSM | `docs/notes/leaflet-osm-guide.md` |

## 5. Current Routes

| URL | Middleware | Who | Name |
|---|---|---|---|
| `/` | none | anyone | redirects to `login` |
| `/login` | guest | unauthenticated | `login` |
| `/register/pending` | none | post-signup | `register.pending` |
| `/map` | auth | logged-in users | `map.index` |
| `/api/map-data` | auth | logged-in users | `api.map-data` |
| `/dashboard` | auth | any role | `dashboard` |
| `/profile` | auth | logged-in users | `profile.edit` |
| `/admin/reports` | auth, role:admin | admin | `admin.reports.index` |
| `/admin/health-categories` | auth, role:admin | admin | `admin.health-categories.index` |
| `/admin/dss` | auth, role:admin | admin | `admin.dss` |
| `/admin/dss/thresholds` | auth, role:admin | admin | `admin.dss.thresholds` |
| `/admin/users` | auth, role:admin | admin | `admin.users.index` |
| `/admin/users/rhu` | auth, role:admin | admin | `admin.users.rhu.store` |
| `/admin/activity-log` | auth, role:admin | admin | `admin.activity-log.index` |
| `/admin/case-coordination` | auth, role:admin | admin | `admin.case-actions.index` |
| `/rhu/dashboard` | auth, role:rhu | RHU staff | `rhu.dashboard` |
| `/rhu/case-coordination` | auth, role:rhu | RHU staff | `rhu.case-actions.index` |
| `/rhu/reports/{report}/approve` | auth, role:rhu | RHU staff | `rhu.reports.approve` |
| `/rhu/reports/{report}/reject` | auth, role:rhu | RHU staff | `rhu.reports.reject` |
| `/citizen/health-guide` | auth, role:citizen | citizen | `citizen.health-guide` |

## 6. Activity Log

Logged actions: `profile_updated`, `report_submitted`, `report_approved`, `report_rejected`, `report_edited`, `report_deleted`, `rhu_account_created`, `case_action_created`, `case_action_completed`.

Store only display names such as `John Michael T.`, never full names. RHU account creation stores `target_id` and `target_name`; report events store `report_id` and `display_name`. User roles are immutable after account creation.

## 7. Credentials

| Role | Email | Password |
|---|---|---|
| Admin | `admin@gmail.com` | `admin1234` |
| RHU | `rhu@gmail.com` | `rhu1234` |
| Citizen | `johnmichael.talbo@gmail.com` | `citizen1234` |
| Citizen | `engiemar.balanay@gmail.com` | `citizen1234` |

## 8. Known Inconsistencies

- Some old planning snippets use stale route names/view paths. Check actual code before copying snippets.
- Older docs disagreed on card rounding and `/api/map-data` access. Current rule: cards/containers use `rounded-none`; `/api/map-data` is auth-gated and still filters `approved()->withinDays(30)`.

## 9. Session Report

```text
CHANGES MADE
  [file]: [what] - [why]

LEFT UNCHANGED
  [what] - [why]

CONFLICTS OR INACCURACIES FOUND
  [doc/code] says [X] but reality is [Y]

FUTURE DEV NOTES
  [implications for later work]
```

## 10. Future Notes

- Notification system: email/SMS admin alerts when citizen reports cross DSS thresholds.
- If public map access is requested later, move both `/map` and `/api/map-data` deliberately and keep data scoped/no PII.
