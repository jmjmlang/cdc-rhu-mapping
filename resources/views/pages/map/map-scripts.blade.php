@push('scripts')
<script>
(function () {
    var H          = window.PhcMapHelpers;
    var map        = window.PhcMapInstances['phc-map'].map;
    var HOME       = [18.333209, 121.354445];
    var HOME_Z     = 13;
    var catLayers  = {};          // catId -> L.layerGroup (for filter toggles)
    var brgyCircles = {};         // barangayName -> L.circle (to re-color on filter)

    document.getElementById('btn-recenter').addEventListener('click', function () {
        map.flyTo(HOME, HOME_Z, { duration: 0.8 });
    });

    fetch('{{ route("api.map-data") }}')
        .then(function (r) { return r.json(); })
        .then(function (data) {

            // Assign colors for every category up front
            data.forEach(function (item) { H.getOrAssignColor(item.category_id); });

            // Group by barangay
            var byBarangay = {};
            data.forEach(function (item) {
                if (!byBarangay[item.barangay]) byBarangay[item.barangay] = [];
                byBarangay[item.barangay].push(item);
            });

            // Per-category totals for legend panel
            var catTotals  = {};
            var totalCases = 0;
            data.forEach(function (item) {
                if (!catTotals[item.category_id])
                    catTotals[item.category_id] = { id: item.category_id, name: item.category, total: 0 };
                catTotals[item.category_id].total += item.total_cases;
                totalCases += item.total_cases;
            });
            var barangayCount = Object.keys(byBarangay).length;

            // ── One circle per barangay ──────────────────────────────────────
            // Color = dominant (most-cases) disease. Radius = total cases.
            Object.keys(byBarangay).forEach(function (brgyName) {
                var group = byBarangay[brgyName];

                // Dominant disease = the one with the most cases
                var dominant = group.reduce(function (best, item) {
                    return item.total_cases > best.total_cases ? item : best;
                }, group[0]);

                var brgyTotal = group.reduce(function (s, item) { return s + item.total_cases; }, 0);
                var c         = H._catColorMap[dominant.category_id];

                var circle = L.circle([dominant.latitude, dominant.longitude], {
                    radius:      H.severityRadius(brgyTotal),
                    color:       c.stroke,
                    fillColor:   c.fill,
                    fillOpacity: 0.35,
                    weight:      2,
                }).addTo(map);

                brgyCircles[brgyName] = { circle: circle, group: group, dominant: dominant };

                circle.bindTooltip(
                    brgyName + ' \u00b7 ' + brgyTotal + (brgyTotal !== 1 ? ' cases' : ' case'),
                    { permanent: false, direction: 'top', offset: [0, -4], className: 'phc-tooltip' }
                );

                circle.bindPopup(
                    H.buildBarangayPopupHTML(brgyName, group, null),
                    { maxWidth: 280, className: 'phc-popup' }
                );

                circle.on('mouseover', function () { this.setStyle({ fillOpacity: 0.60, weight: 3 }); });
                circle.on('mouseout',  function () { this.setStyle({ fillOpacity: 0.35, weight: 2 }); });

                // Register in a layerGroup per category so filters still work
                group.forEach(function (item) {
                    if (!catLayers[item.category_id])
                        catLayers[item.category_id] = [];
                    catLayers[item.category_id].push(brgyName);
                });
            });

            // Colour filter-bar dots
            Object.keys(H._catColorMap).forEach(function (catId) {
                var c = H._catColorMap[catId];
                document.querySelectorAll('.cat-dot[data-cat-id="' + catId + '"]')
                    .forEach(function (el) { el.style.background = c.fill; });
            });

            // Summary label in filter bar
            var lbl = document.getElementById('case-count-label');
            if (data.length) {
                lbl.textContent = barangayCount + (barangayCount !== 1 ? ' barangays' : ' barangay') +
                    ' \u00b7 ' + totalCases + (totalCases !== 1 ? ' cases' : ' case');
                lbl.classList.remove('hidden');
            }

            // Populate legend panel
            var maxCat     = Math.max.apply(null, Object.values(catTotals).map(function (c) { return c.total; })) || 1;
            var sortedCats = Object.values(catTotals).sort(function (a, b) { return b.total - a.total; });

            document.getElementById('panel-legend').innerHTML = sortedCats.length
                ? sortedCats.map(function (cat) {
                    var c   = H._catColorMap[cat.id];
                    var pct = Math.round((cat.total / maxCat) * 100);
                    return '<div style="padding:10px 0;border-bottom:1px solid #f9fafb">' +
                        '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">' +
                        '<span style="width:11px;height:11px;border-radius:50%;background:' + c.fill +
                        ';border:1.5px solid ' + c.stroke + ';flex-shrink:0"></span>' +
                        '<span style="font-size:14px;font-weight:600;color:#111;flex:1">' + cat.name + '</span>' +
                        '<span style="font-size:16px;font-weight:800;color:' + c.stroke + '">' + cat.total + '</span>' +
                        '</div>' +
                        '<div style="display:flex;align-items:center;gap:6px">' +
                        '<div style="flex:1;height:5px;background:#f3f4f6;border-radius:2px;overflow:hidden">' +
                        '<div style="height:100%;background:' + c.fill + ';width:' + pct + '%"></div></div>' +
                        H.riskBadge(cat.total) +
                        '</div></div>';
                }).join('')
                : '<p style="font-size:13px;color:#9ca3af;text-align:center;padding-top:24px">No approved reports in last 30 days.</p>';

            document.getElementById('panel-summary').textContent =
                totalCases + (totalCases !== 1 ? ' cases' : ' case') + ' reported across ' +
                barangayCount + (barangayCount !== 1 ? ' barangays' : ' barangay');

            // ── Build Charts ──────────────────────────────────────────
            var palette = [
                'rgba(13,148,136,0.75)', 'rgba(245,158,11,0.75)', 'rgba(239,68,68,0.75)',
                'rgba(59,130,246,0.75)', 'rgba(168,85,247,0.75)', 'rgba(34,197,94,0.75)',
                'rgba(249,115,22,0.75)', 'rgba(236,72,153,0.75)', 'rgba(107,114,128,0.75)',
                'rgba(14,165,233,0.75)'
            ];

            var brgyNames = Object.keys(byBarangay).sort();
            var diseaseList = sortedCats.map(function(c) { return c.name; });

            if (typeof Chart !== 'undefined' && brgyNames.length) {
                // Stacked bar: cases per barangay
                var datasets = diseaseList.map(function(name, i) {
                    var d = brgyNames.map(function(brgy) {
                        var match = data.find(function(item) { return item.barangay === brgy && item.category === name; });
                        return match ? match.total_cases : 0;
                    });
                    return { label: name, data: d, backgroundColor: palette[i % palette.length] };
                });

                new Chart(document.getElementById('map-chart-barangay'), {
                    type: 'bar',
                    data: { labels: brgyNames, datasets: datasets },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        scales: {
                            x: { stacked: true, grid: { display: false } },
                            y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } }
                        }
                    }
                });

                // Doughnut: disease distribution
                new Chart(document.getElementById('map-chart-doughnut'), {
                    type: 'doughnut',
                    data: {
                        labels: diseaseList,
                        datasets: [{ data: sortedCats.map(function(c) { return c.total; }),
                            backgroundColor: diseaseList.map(function(_, i) { return palette[i % palette.length]; }),
                            borderWidth: 1, borderColor: '#fff' }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '60%',
                        plugins: { legend: { display: true, position: 'right', labels: { boxWidth: 12, padding: 8, font: { size: 11 } } } }
                    }
                });
            }
        })
        .catch(function () {
            document.getElementById('panel-legend').innerHTML =
                '<p style="font-size:13px;color:#ef4444;text-align:center;padding:24px">Failed to load map data.</p>';
        });

    // Per-category checkbox toggle
    // Hides/shows barangay circles that have any case of the toggled category
    document.querySelectorAll('.category-checkbox').forEach(function (cb) {
        cb.addEventListener('change', function () {
            updateCircleVisibility();
            syncAll();
        });
    });

    document.getElementById('toggle-all').addEventListener('change', function () {
        var on = this.checked;
        document.querySelectorAll('.category-checkbox').forEach(function (cb) { cb.checked = on; });
        updateCircleVisibility();
    });

    function getCheckedCats() {
        return Array.from(document.querySelectorAll('.category-checkbox:checked')).map(function (cb) { return cb.value; });
    }

    function updateCircleVisibility() {
        var checked = getCheckedCats();
        Object.keys(brgyCircles).forEach(function (brgyName) {
            var info = brgyCircles[brgyName];
            var hasChecked = info.group.some(function (item) {
                return checked.indexOf(String(item.category_id)) !== -1;
            });
            if (hasChecked) {
                if (!map.hasLayer(info.circle)) map.addLayer(info.circle);
            } else {
                if (map.hasLayer(info.circle)) map.removeLayer(info.circle);
            }
        });
    }

    function syncAll() {
        document.getElementById('toggle-all').checked =
            Array.from(document.querySelectorAll('.category-checkbox')).every(function (cb) { return cb.checked; });
    }
}());
</script>
@endpush
