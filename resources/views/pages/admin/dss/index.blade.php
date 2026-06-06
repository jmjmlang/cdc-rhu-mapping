<x-app-layout>
    <x-slot name="title">Decision Support</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Decision Support</h2>
            @if($summary['critical_count'] > 0 || $summary['high_count'] > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold bg-rose-100 text-rose-800 rounded-md">
                    <x-ui.icon name="exclamation-triangle" class="w-4 h-4" />
                    {{ $summary['critical_count'] + $summary['high_count'] }} area(s) need attention
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- ── Summary Stats ─────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-5">
                <x-ui.stat-card
                    label="Total Cases"
                    :value="number_format($summary['total_cases'])"
                    accent="primary" />
                <x-ui.stat-card
                    label="Affected Barangays"
                    :value="$summary['affected_barangays']"
                    accent="amber" />
                <x-ui.stat-card
                    label="Critical Alerts"
                    :value="$summary['critical_count']"
                    accent="red" />
                <x-ui.stat-card
                    label="High Risk"
                    :value="$summary['high_count']"
                    accent="orange" />
            </div>

            {{-- ── Charts ────────────────────────────────────── --}}
            @if(!empty($barangayNames))
            <section>
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4">Cases by Barangay</h3>
                        <div class="relative h-[260px] sm:h-[300px]">
                            <canvas id="dss-chart-barangay"></canvas>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4">Risk Distribution</h3>
                        <div class="relative h-[260px] sm:h-[300px] max-w-md mx-auto xl:max-w-none">
                            <canvas id="dss-chart-risk"></canvas>
                        </div>
                    </div>
                </div>
            </section>
            @endif

            {{-- ── Risk Level Reference ─────────────────────────────────── --}}
            <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                <span class="font-medium text-gray-400">Risk basis (cases / barangay-disease, last 30 days):</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-gray-100 text-gray-500 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                    Low &lt; {{ $thresholds['moderate'] }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 font-medium border border-amber-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    Moderate {{ $thresholds['moderate'] }}–{{ $thresholds['high'] - 1 }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-orange-50 text-orange-700 font-medium border border-orange-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                    High {{ $thresholds['high'] }}–{{ $thresholds['critical'] - 1 }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-red-50 text-red-700 font-medium border border-red-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    Critical ≥ {{ $thresholds['critical'] }}
                </span>
            </div>

            {{-- ── Priority Action Table ───────────────────────────────── --}}
            @include('pages.admin.dss.barangay-cards')



        </div>
    </div>

    @if(!empty($barangayNames))
        @push('scripts')
        <script>
        (function () {
            if (typeof Chart === 'undefined') return;

            var barangayCanvas = document.getElementById('dss-chart-barangay');
            var riskCanvas = document.getElementById('dss-chart-risk');

            if (barangayCanvas) {
                new Chart(barangayCanvas, {
                    type: 'bar',
                    data: {
                        labels: @json($barangayNames),
                        datasets: @json($chartByBarangay)
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { stacked: true, grid: { display: false } },
                            y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } }
                        },
                        plugins: {
                            legend: { position: 'top', labels: { boxWidth: 12, padding: 10, font: { size: 11 } } }
                        }
                    }
                });
            }

            if (riskCanvas) {
                new Chart(riskCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: @json($riskLabels),
                        datasets: [{
                            data: @json($riskCounts),
                            backgroundColor: @json($riskColors),
                            borderColor: '#fff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 14, font: { size: 11 } } }
                        }
                    }
                });
            }
        }());
        </script>
        @endpush
    @endif
</x-app-layout>
