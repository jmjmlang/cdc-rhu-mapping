# skills.md - AI Capability and Constraint Catalogue

Use when deciding whether a change is in scope or needs user confirmation.

## Can Do

- Inspect Laravel/Blade/Tailwind code and follow existing structure.
- Create/update migrations, models, scopes, casts, relationships, and seeders.
- Write idempotent seeders using `updateOrCreate`.
- Build thin controllers and route groups with auth/role middleware.
- Add service-layer methods for domain logic.
- Build Blade views with existing components or extract new components.
- Wire Tailwind/Alpine/Blade interactions.
- Implement Leaflet maps using OSM, Fetch API, layers, circles, markers, and popups.
- Update DSS aggregation, thresholds, risk output, and admin views when confirmed.
- Diagnose Laravel logs, route issues, migration errors, N+1 risks, and map/API failures.
- Run safe verification commands such as build, route/view clear, route list, and migrate status.

## Must Not Do Without Confirmation

- Run destructive DB commands (`migrate:fresh`, drops, production rollback).
- Add/change/remove roles or enum values.
- Change schema architecture or add major tables.
- Change DSS thresholds or the 30-day analytical window.
- Change barangay coordinates or boundary assumptions.
- Make map/API data public.
- Squash migrations for Laravel Cloud.
- Introduce React, Vue, Inertia, Google Maps, Mapbox, or paid API keys.
- Push/deploy production changes.

## Must Never Do

- Pass `$request->all()` to Eloquent writes.
- Use bare `create()` in seeders.
- Expose pending/rejected reports through map/DSS endpoints.
- Store full names in activity-log properties.
- Put business logic in Blade.
- Put Tailwind class strings in controllers.
- Use PHP attribute syntax like `#[Fillable]` for models.
- Leave `dd()`, `dump()`, `var_dump()`, or debug `console.log` in production code.
- Cache Eloquent model collections on WAMP/Windows file cache.

## Known Gaps

| Gap | Mitigation |
|---|---|
| Exact barangay GPS/boundaries | Treat seeded coordinates as approximate; verify with MHO/NAMRIA before production reliance. |
| Official DSS thresholds | Current thresholds are provisional; get MHO sign-off before changes. |
| Laravel Cloud CLI drift | Verify current CLI syntax before deployment work. |
| Real barangay GeoJSON | Current map uses coordinate/radius proxies. |
