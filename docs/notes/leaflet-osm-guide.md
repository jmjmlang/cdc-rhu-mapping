# Leaflet + OpenStreetMap Guide

Map work uses LeafletJS 1.9.4 with OSM tiles. No external API keys.

## Access and Data

- `/map` and `/api/map-data` are auth-gated.
- `/api/map-data` must always apply `CaseReport::approved()->withinDays(30)`.
- Response exposes only aggregate health map fields: barangay/category labels, coordinates, totals, optional land area/risk metadata. No PII.

## OSM Requirements

```js
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    detectRetina: true,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);
```

- HTTPS only; HTTP causes mixed-content failures in production.
- Attribution is required.
- OSM shared tiles are fine for small scale; consider tile hosting only if traffic grows.

## Layout and Container

```html
<div id="phc-map" class="w-full" style="height: 560px;"></div>
```

- `id="phc-map"` is the project standard.
- Explicit pixel height is mandatory.
- Leaflet CSS/JS must load before any script that references `L`.
- Page map scripts go in `@push('scripts')`; layout renders `@stack('scripts')`.

## Initialization Pattern

```blade
@push('scripts')
<script>
(function () {
    const map = L.map('phc-map').setView([18.2830, 121.1480], 13);
    const markersLayer = L.layerGroup().addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        detectRetina: true,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
})();
</script>
@endpush
```

Use an IIFE to avoid global pollution.

## Rendering Rules

- Use `fetch()` for `/api/map-data?health_category_id=...`.
- Validate category IDs server-side.
- Clear prior layers before rendering a new filter.
- Use `L.circle` for severity/area overlays and `L.marker` only when exact point markers are needed.
- Keep popups short for mobile.
- Call `map.invalidateSize()` if the map is shown after being hidden in a tab/modal.

## Known Gotchas

| Symptom | Cause | Fix |
|---|---|---|
| Gray map box | Missing height | Add `style="height: 560px;"`. |
| `L is not defined` | Leaflet JS loads after page code | Move CDN before `@stack('scripts')`. |
| Blank tiles | HTTP tile URL or network issue | Use HTTPS OSM URL. |
| Empty markers | No approved report in 30-day window | Approve/seed test data; verify scopes. |
| Map overlaps modal/sidebar | Leaflet z-index pane | Keep `.leaflet-container { z-index: 0; position: relative; }` in CSS. |
