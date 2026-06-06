<x-app-layout>
    <x-slot name="title">Map</x-slot>
    <x-slot name="header">
        <div>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Municipality Case Report Map</h2>
                <p class="text-sm text-gray-600 mt-0.5">Verified case reports from the past 30 days in Luna, Apayao.</p>
            </div>
        </div>
    </x-slot>
    <x-map.popup-helpers />

    {{-- ── Filter Bar ──────────────────────────────────────────────────── --}}
    <div class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-20">
        <div class="px-4 py-2.5">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                <span class="text-sm font-semibold text-primary-700 shrink-0">Filter diseases</span>
                <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-x-5 gap-y-1.5">
                    <label class="flex items-center gap-1.5 cursor-pointer select-none">
                        <input type="checkbox" id="toggle-all" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" checked>
                        <span class="text-sm font-medium text-gray-700">All</span>
                    </label>
                    @foreach($categories as $cat)
                        <label class="flex items-center gap-1.5 cursor-pointer select-none min-w-0">
                            <input type="checkbox" class="category-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500 shrink-0" value="{{ $cat->id }}" checked>
                            <span class="cat-dot inline-block w-2.5 h-2.5 rounded-full bg-gray-300 shrink-0" data-cat-id="{{ $cat->id }}"></span>
                            <span class="text-sm text-gray-700 truncate">{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>
                <span id="case-count-label" class="text-sm text-gray-400 sm:ml-auto hidden"></span>
            </div>
        </div>
    </div>

    {{-- Map + Side panel layout --}}
    <div class="flex flex-col lg:flex-row">

        {{-- Map --}}
        <div class="flex-1 min-w-0">
            <x-map.leaflet-base map-id="phc-map" height="560px" mobile-height="380px" />
        </div>

        {{-- Side panel --}}
        <aside class="w-full lg:w-72 xl:w-80 bg-white border-t border-gray-100 lg:border-t-0 lg:border-l border-gray-200 flex flex-col">
            <div class="px-4 py-4 border-b border-gray-100 bg-gradient-to-br from-primary-50 to-white flex-shrink-0">
                <p class="text-base font-semibold text-gray-900">Disease Overview</p>
                <p class="text-sm text-gray-600 mt-1">A quick summary of verified reports in Luna, Apayao.</p>
            </div>
            <div id="panel-legend" class="overflow-y-auto px-4 py-1 max-h-60 lg:flex-1 lg:max-h-none">
                <p class="text-sm text-gray-300 text-center pt-6">Loading map data&hellip;</p>
            </div>
            <div class="px-4 py-3 border-t border-gray-100 space-y-2 flex-shrink-0">
                <p id="panel-summary" class="text-sm font-medium text-gray-600 leading-relaxed"></p>
                <button id="btn-recenter" type="button"
                    class="w-full flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-semibold text-primary-700 bg-primary-50 border border-primary-200 hover:bg-primary-100 transition-colors">
                    <x-ui.icon name="map-pin" class="w-4 h-4" />
                    Re-center Map
                </button>
            </div>
        </aside>
    </div>

    {{-- ── Data Visualization ──────────────────────────────────────── --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Cases per Barangay</h3>
                <div class="relative h-[220px] sm:h-[240px]">
                    <canvas id="map-chart-barangay"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Disease Distribution</h3>
                <div class="relative h-[240px] sm:h-[260px] max-w-md mx-auto lg:max-w-none">
                    <canvas id="map-chart-doughnut"></canvas>
                </div>
            </div>
        </div>
    </div>

    @include('pages.map.map-scripts')

</x-app-layout>
