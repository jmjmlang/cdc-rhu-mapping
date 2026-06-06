# traits.md - Project Conventions

Binding conventions for PHC Mapping. Also read `AGENTS.md` and relevant files in `docs/notes/`.

## 0. Golden Rule

Before writing inline `<div>`, `<table>`, `<button>`, `<input>`, or `<span>` in Blade:

1. Check `resources/views/components/`.
2. Use an existing component when it fits.
3. Extract a component when the pattern appears twice.

```blade
{{-- Correct --}}
<x-ui.button type="submit">Save</x-ui.button>
<x-ui.badge :status="$report->status" />

{{-- Wrong --}}
<button class="...">Save</button>
<span class="...">pending</span>
```

## 1. Naming

| Thing | Convention | Example |
|---|---|---|
| Controllers | PascalCase + suffix | `CaseReportController` |
| Role controllers | Namespace by role | `App\Http\Controllers\Admin\DssController` |
| Models | PascalCase singular | `CaseReport` |
| Migrations | timestamp + snake_case | `xxxx_create_case_reports_table` |
| Seeders | PascalCase + Seeder | `BarangaySeeder` |
| Middleware | PascalCase | `EnsureRole` |
| Services | PascalCase + Service | `DssService` |
| Route names | dot notation | `admin.reports.index` |
| Blade views | snake_case folders | `admin/dss/index.blade.php` |
| Components | kebab file, dot tag | `components/ui/button.blade.php` -> `<x-ui.button>` |
| PHP/JS vars | camelCase | `$healthCategories`, `activeLayers` |
| Tables/columns | snake_case plural/fields | `case_reports`, `case_report_actions`, `barangay_id` |
| Enum values | lowercase strings | `admin`, `citizen`, `pending` |

## 2. Component Namespaces

| Namespace | Folder | Purpose |
|---|---|---|
| `<x-ui.*>` | `components/ui/` | buttons, badges, alerts, cards |
| `<x-form.*>` | `components/form/` | inputs, selects, labels, errors |
| `<x-table.*>` | `components/table/` | table shell/cells |
| `<x-dss.*>` | `components/dss/` | DSS-specific UI |
| `<x-layouts.*>` | `components/layouts/` | page-level helpers |

Component rules: `@props` with defaults, slots for content, conditional classes inside components, no Tailwind in controllers.

## 3. Backend Structure

- Controllers validate, call service/model, return view/redirect/JSON.
- Business logic goes in `app/Services/` or model scopes.
- Use inline validation for simple one-off forms; FormRequest for reusable/complex validation.
- No `$this->middleware()` in constructors; use route middleware groups.
- Use `protected $fillable` / `$hidden`, not PHP attribute syntax like `#[Fillable]`.
- Prefer route model binding for approve/reject/edit/delete actions.

## 4. Routes

- Admin: prefix `/admin`, name `admin.`, middleware `auth`, `role:admin`.
- RHU: prefix `/rhu`, name `rhu.`, middleware `auth`, `role:rhu`.
- Citizen: prefix `/citizen`, name `citizen.`, middleware `auth`, `role:citizen`.
- Shared authenticated: middleware `auth`.
- RESTful names where natural: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.
- Custom state actions may use verbs like `approve`, `reject`, or `complete`; user roles are immutable after account creation.
- `/api/map-data` is auth-gated and must return only approved 30-day data.
- Case coordination routes exist for admin and RHU as `*.case-actions.*`.

## 5. Blade/Frontend

- TailwindCSS only; no custom CSS unless required by Leaflet/z-index.
- No inline `style=""` except `#phc-map` height.
- Flash messages use `<x-ui.alert>`.
- Form controls bind `old()` and show errors via form components.
- Dropdown data comes from controllers, not AJAX unless deliberately planned.
- No emoji in UI unless requested.
- UI is minimalist/mobile-first; no hover lift/scale or colored side borders.

## 6. JavaScript

- Server-rendered Blade project: no React/Vue/Inertia utilities.
- Page-specific JS goes in `@push('scripts')` and an IIFE.
- Reusable Alpine goes in `resources/js/app.js` with `Alpine.data()` only after reuse appears.
- Map code uses Fetch API, `activeLayers`/layer groups, and `clearLayers()`.
- No production `console.log`; `console.error` is acceptable for caught failures.

## 7. Database

- `Schema::defaultStringLength(191)` remains in `AppServiceProvider`.
- FK order matters: `users` -> `municipalities` -> `barangays` -> `health_categories` -> `case_reports`.
- Coordinates use `decimal(10, 7)`.
- FKs use `foreignId()->constrained()` and explicit delete behavior.
- Seeders are idempotent via `updateOrCreate`.

## 8. Security

- CSRF stays enabled.
- Validated data only for writes.
- Role violations `abort(403)`.
- No sensitive data or full names in Blade/JS/activity logs.
- Map/DSS expose aggregates only; no pending/rejected reports.

## 9. File Map

```text
app/Http/Controllers/{Admin,Citizen}/
app/Http/Middleware/EnsureRole.php
app/Models/{User,Barangay,HealthCategory,CaseReport,CaseReportAction}.php
app/Services/{DssService,CaseReportActionService}.php
database/migrations/
database/seeders/
resources/views/components/{ui,form,table,dss,layouts}/
resources/views/{admin,citizen,map,layouts}/
routes/web.php
```
