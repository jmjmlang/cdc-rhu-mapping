@props(['mapId', 'height' => '560px', 'mobileHeight' => '400px', 'minZoom' => 11, 'maxZoom' => 17])

{{--
  Reusable Leaflet map base.
  Pushes CDN + shared CSS once (even if used multiple times on the same page).
  Pushes per-instance init script and exposes window.PhcMapInstances[mapId]
  so the consuming page can attach its own data-loading logic.

  Usage:
    <x-map.leaflet-base map-id="phc-map" height="560px" mobile-height="400px" />

  In the page @push('scripts'):
    var instance = window.PhcMapInstances['phc-map'];
    var map      = instance.map;
    var markers  = instance.markers;
--}}

@pushOnce('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <style>
        .leaflet-control-zoom a {
            width: 32px !important; height: 32px !important; line-height: 32px !important;
            font-size: 16px !important; color: #0d655a !important;
            border-color: #d1d5db !important; background: #fff !important;
        }
        .leaflet-control-zoom a:hover { background: #f0fdfa !important; }
        .leaflet-control-attribution { font-size: 10px; }
        .leaflet-control-layers { border: 1px solid #d1d5db !important; border-radius: 4px !important; font-size: 12px; }
        .leaflet-control-layers-toggle { width: 32px !important; height: 32px !important; }
    </style>
@endPushOnce

@push('head')
    <style>
        #{{ $mapId }} { height: {{ $mobileHeight }}; }
        @media (min-width: 640px) { #{{ $mapId }} { height: {{ $height }}; } }
    </style>
@endpush

<div id="{{ $mapId }}" class="w-full"></div>

@push('scripts')
<script>
(function() {
    window.PhcMapInstances = window.PhcMapInstances || {};

    var mapId = {!! json_encode($mapId) !!};

    var regionBounds = L.latLngBounds(
        L.latLng(17.9, 121.05),
        L.latLng(18.75, 121.65)
    );

    var voyager = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 20,
        subdomains: 'abcd',
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>'
    });

    var imagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 18,
        attribution: 'Imagery &copy; Esri'
    });

    var labels = L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 18,
        pane: 'shadowPane'
    });

    var map = L.map(mapId, {
        maxBounds: regionBounds,
        maxBoundsViscosity: 1.0,
        minZoom: {{ $minZoom }},
        maxZoom: {{ $maxZoom }},
        layers: [voyager]
    }).setView([18.333209, 121.354445], 13);

    L.control.layers({ 'Map': voyager, 'Satellite': imagery }, null, { position: 'topright' }).addTo(map);

    map.on('baselayerchange', function(e) {
        if (e.name === 'Satellite') {
            labels.addTo(map);
        } else {
            map.removeLayer(labels);
        }
    });

    var markersLayer = L.layerGroup().addTo(map);

    window.PhcMapInstances[mapId] = { map: map, markers: markersLayer };
})();
</script>
@endpush
