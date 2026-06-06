{{--
  Shared JS utilities for PHC map circle rendering.
  Include this component BEFORE any @push('scripts') block that uses it.

  Exposes window.PhcMapHelpers with:
    PALETTE, getOrAssignColor(catId), severityRadius(n, minR?, maxR?),
    getOffsetLatLng(lat, lng, index, total), riskBadge(n),
    buildBarangayPopupHTML(barangayName, group, thisCatId|null)

  Usage:
    <x-map.popup-helpers />   (include once per page; @pushOnce keeps it safe)
--}}

@pushOnce('head')
<style>
.phc-tooltip {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,.12);
    padding: 3px 8px;
    font-size: 13px;
}
.phc-tooltip::before { display: none; }
.phc-popup .leaflet-popup-content-wrapper {
    border-radius: 6px;
    box-shadow: 0 4px 16px rgba(0,0,0,.14);
    border: 1px solid #e5e7eb;
    padding: 0;
}
.phc-popup .leaflet-popup-content { margin: 12px 14px; }
.phc-popup .leaflet-popup-tip { background: #fff; }
</style>
@endPushOnce

@pushOnce('scripts')
<script>
(function () {
    var H = window.PhcMapHelpers = {};

    // ── Color palette (8 muted, accessible colors) ───────────────────────
    H.PALETTE = [
        { fill: '#60a5fa', stroke: '#1e40af' },
        { fill: '#f59e0b', stroke: '#92400e' },
        { fill: '#a78bfa', stroke: '#5b21b6' },
        { fill: '#34d399', stroke: '#065f46' },
        { fill: '#f87171', stroke: '#991b1b' },
        { fill: '#fb923c', stroke: '#9a3412' },
        { fill: '#38bdf8', stroke: '#0c4a6e' },
        { fill: '#86efac', stroke: '#14532d' },
    ];

    H._catColorMap = {};
    H._palIdx      = 0;

    // Assign one palette color per category, consistently across the session
    H.getOrAssignColor = function (catId) {
        if (!H._catColorMap[catId]) {
            H._catColorMap[catId] = H.PALETTE[H._palIdx % H.PALETTE.length];
            H._palIdx++;
        }
        return H._catColorMap[catId];
    };

    // Radius: 200m (1 case) → 1200m (50+ cases), using sqrt scale for visual balance
    H.severityRadius = function (n, minR, maxR) {
        minR = (minR !== undefined) ? minR : 200;
        maxR = (maxR !== undefined) ? maxR : 1200;
        var clamped = Math.min(Math.max(n, 1), 100);
        // sqrt scaling so small differences at low counts are still visible
        return minR + (Math.sqrt(clamped - 1) / Math.sqrt(99)) * (maxR - minR);
    };

    // Spread N circles from the same lat/lng by 80m to avoid overlaps
    H.getOffsetLatLng = function (lat, lng, index, total) {
        if (total <= 1) return { lat: lat, lng: lng };
        var OFFSET_M = 80;
        var angle    = (index / total) * 2 * Math.PI;
        var dLat     = OFFSET_M / 111320;
        var dLng     = OFFSET_M / (111320 * Math.cos(lat * Math.PI / 180));
        return {
            lat: lat + dLat * Math.sin(angle),
            lng: lng + dLng * Math.cos(angle),
        };
    };

    // Inline risk level badge HTML
    H.riskBadge = function (n) {
        if (n >= 20) return '<span style="font-size:11px;padding:2px 7px;background:#fee2e2;color:#991b1b;font-weight:700;border-radius:3px">Critical</span>';
        if (n >= 10) return '<span style="font-size:11px;padding:2px 7px;background:#ffedd5;color:#9a3412;font-weight:700;border-radius:3px">High</span>';
        if (n >= 5)  return '<span style="font-size:11px;padding:2px 7px;background:#fef3c7;color:#92400e;font-weight:700;border-radius:3px">Moderate</span>';
        return '';
    };

    // Full barangay popup: all diseases + total
    // thisCatId = category to highlight, or null for no highlight (dashboard)
    H.buildBarangayPopupHTML = function (barangayName, group, thisCatId) {
        var total  = group.reduce(function (s, d) { return s + d.total_cases; }, 0);
        var sorted = group.slice().sort(function (a, b) { return b.total_cases - a.total_cases; });

        var rows = sorted.map(function (d) {
            var dc     = H._catColorMap[d.category_id] || H.PALETTE[0];
            var isThis = thisCatId && (d.category_id === thisCatId);
            var badge  = H.riskBadge(d.total_cases);
            var bg     = isThis
                ? 'background:#f9fafb;margin:0 -14px;padding:4px 14px;'
                : 'padding:3px 0;';
            return '<div style="display:flex;align-items:center;gap:8px;' + bg + '">' +
                '<span style="width:9px;height:9px;border-radius:50%;background:' + dc.fill +
                ';border:1.5px solid ' + dc.stroke + ';flex-shrink:0"></span>' +
                '<span style="font-size:13px;color:#374151;flex:1;white-space:nowrap">' + d.category + '</span>' +
                (badge ? badge + '&nbsp;' : '') +
                '<span style="font-size:13px;font-weight:700;color:' + dc.stroke +
                ';min-width:22px;text-align:right">' + d.total_cases + '</span>' +
                '</div>';
        }).join('');

        return '<div style="font-family:system-ui;min-width:195px;padding:2px 0">' +
            '<div style="font-size:15px;font-weight:700;color:#111;margin-bottom:2px">' + barangayName + '</div>' +
            rows +
            '<div style="border-top:1px solid #f3f4f6;margin-top:10px;padding-top:8px;' +
            'display:flex;justify-content:space-between;align-items:baseline">' +
            '<span style="font-size:12px;color:#6b7280">Total cases</span>' +
            '<span style="font-size:22px;font-weight:800;color:#111;line-height:1">' + total + '</span>' +
            '</div></div>';
    };
}());
</script>
@endPushOnce
