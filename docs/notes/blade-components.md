# Blade Component Architecture

This project treats Blade components as the UI system. If markup appears twice, extract or reuse a component.

## Inventory

| Component | File | Purpose |
|---|---|---|
| `<x-ui.button>` | `resources/views/components/ui/button.blade.php` | Button variants. |
| `<x-ui.badge>` | `resources/views/components/ui/badge.blade.php` | Status/risk labels. |
| `<x-ui.alert>` | `resources/views/components/ui/alert.blade.php` | Flash banners. |
| `<x-ui.card>` | `resources/views/components/ui/card.blade.php` | Content panel. |
| `<x-form.input>` | `resources/views/components/form/input.blade.php` | Input with label/error. |
| `<x-form.select>` | `resources/views/components/form/select.blade.php` | Select with label/error. |
| `<x-form.label>` | `resources/views/components/form/label.blade.php` | Standalone label. |
| `<x-form.error>` | `resources/views/components/form/error.blade.php` | Field error. |
| `<x-table.wrapper>` | `resources/views/components/table/wrapper.blade.php` | Responsive table shell. |
| `<x-table.heading>` | `resources/views/components/table/heading.blade.php` | `th` styling. |
| `<x-table.cell>` | `resources/views/components/table/cell.blade.php` | `td` styling. |
| `<x-dss.risk-card>` | `resources/views/components/dss/risk-card.blade.php` | DSS risk card. |
| `<x-layouts.page-header>` | `resources/views/components/layouts/page-header.blade.php` | Page title/subtitle. |

## Before Writing UI

1. Search `resources/views/components/`.
2. Use an existing component if available.
3. If a pattern appears twice, create a component in the right namespace.
4. Keep one-off inline markup only when it is truly page-specific and not likely to repeat.

## Authoring Rules

- Start every component with `@props([...])` and defaults.
- Use `{{ $slot }}` for flexible body content.
- Use `<x-slot:name>` for optional sections such as header/footer/actions.
- Use `$attributes->merge()` or `$attributes->class()` so callers can extend classes safely.
- Keep conditional styling inside component files, not consuming views.
- Status/risk color logic belongs in `<x-ui.badge>` or `<x-dss.risk-card>`.
- Do not query Eloquent from Blade/components.
- Do not put Tailwind strings in controllers.
- No inline `style=""` except Leaflet map height.

## UI Standards

- Minimal, mobile-first, usable at 375px.
- TailwindCSS only.
- Semantic colors: `primary-*`, `status-*`, `risk-*`, `surface*`.
- Cards/containers use `rounded-none`; buttons/badges may use `rounded`/`rounded-sm`.
- No hover lift/scale animations, colored side borders, or emoji unless requested.
- Flash messages use `session('success')` / `session('error')` rendered through `<x-ui.alert>`.

## Layout Rules

- Pages use shared layouts; never duplicate `<html>`, `<head>`, navigation, or `@vite`.
- Layout must render `@stack('scripts')` near the end of `<body>`.
- Leaflet CDN must load before page scripts that reference `L`.
- Page-specific JS goes in `@push('scripts')` and is wrapped in an IIFE.

## Common Patterns

```blade
<x-ui.badge :status="$report->status" />
<x-form.select name="barangay_id" label="Barangay" :options="$barangays" />
<x-form.input name="number_of_cases" label="Number of Cases" type="number" required />
<x-ui.button type="submit">Submit</x-ui.button>
```

```blade
@push('scripts')
<script>
(function () {
    // Page JS only.
})();
</script>
@endpush
```
