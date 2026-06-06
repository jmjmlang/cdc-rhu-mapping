# Agent: Edit / Build Mode

Use for concrete implementation: new features, controllers, migrations, Blade views, UI changes, refactors, and bug fixes that already have a known root cause.

## Pre-Flight

1. Read `docs/dev-traits/learn.md` for past gotchas.
2. Read `docs/dev-traits/traits.md` for naming and structure.
3. Read the relevant note file:
   - Backend: `docs/notes/backend-rules.md`
   - Blade/UI: `docs/notes/blade-components.md`
   - Migration/seeder: `docs/notes/migration-standards.md`
   - Map: `docs/notes/leaflet-osm-guide.md`
4. Search existing files before creating anything.
5. Choose the smallest change that solves the request.

## Decision Tree

| Change | Steps |
|---|---|
| CRUD/resource feature | Migration if needed -> model fillable/casts/relations/scopes -> service logic -> thin controller -> route with middleware -> Blade using components -> verification. |
| Blade view/UI | Audit `resources/views/components/`; use existing component; extract a component if pattern appears twice; keep Tailwind in Blade; no repeated markup. |
| Route | Put auth/role middleware on the group; use dot names; clear route/view caches after route/layout edits. |
| Migration/schema | Stop and read migration standards fully; confirm if destructive or structural; no squashing; every change gets a new migration. |
| Seeder | Use `updateOrCreate`; keep coordinates only in `BarangaySeeder`; seed safe test data as approved only when intended. |
| Form bug | Check `@csrf`, method spoofing, old values, error display, validation rules, and FormRequest `authorize()` if used. |
| Backend/query bug | Check logs, service call, scopes, eager loading, validated payloads, and route middleware. |
| Map bug | Check Leaflet load order, `#phc-map` height, `/api/map-data` response, HTTPS tiles, approved 30-day data, and IIFE. |
| DSS bug | Check approved reports in last 30 days, category threshold keys, fallback threshold `[5, 15, 30]`, and risk badge rendering. |
| Auth/access bug | Check route group, `EnsureRole` alias in `bootstrap/app.php`, and Breeze redirect flow. |

## Route Rules

- Admin routes: `auth`, `role:admin`.
- RHU routes: `auth`, `role:rhu`.
- Citizen routes: `auth`, `role:citizen`.
- Shared authenticated routes: `auth`.
- `/api/map-data`: auth-gated, validates filters, returns only approved 30-day aggregate/no PII.

## After Editing

- Run relevant tests/builds where practical.
- Run `npm run build` after Tailwind/asset-impacting Blade changes.
- Run `php artisan route:clear` and `php artisan view:clear` after route/layout changes.
- Verify no `$request->all()` writes, no N+1 loops, no duplicated component markup.
- Produce the session report from `CLAUDE.md`.
