<x-app-layout>
    <x-slot name="title">RHU Dashboard</x-slot>
    <x-slot name="header">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold tracking-widest font-mono bg-teal-900 text-teal-200 rounded-sm uppercase">RHU</span>
                    <span class="text-xs font-mono text-gray-500 tracking-widest uppercase">Rural Health Unit Portal</span>
                </div>
                <h2 class="font-mono font-black text-2xl md:text-3xl text-gray-900 tracking-tight">Health Surveillance Dashboard</h2>
                <p class="font-mono text-sm text-gray-500 mt-1">Luna, Apayao &mdash; Data window: last 30 days &mdash; Approved reports only</p>
            </div>
            <div class="hidden sm:flex items-center gap-2 mt-1">
                <span class="font-mono text-xs text-gray-500 border border-gray-200 px-2 py-1 rounded-sm">
                    {{ now()->format('Y-m-d H:i') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
            @endif
            @if(session('error'))
                <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
            @endif

            @if($globalAnnouncements->isNotEmpty())
                <section class="space-y-3">
                    <div>
                        <h3 class="font-mono text-sm font-bold text-gray-600 uppercase tracking-widest">All User Announcements</h3>
                        <p class="font-mono text-xs text-gray-500 mt-1">Open municipality-wide advisories from Case Coordination.</p>
                    </div>
                    <div class="space-y-3">
                        @foreach($globalAnnouncements as $announcement)
                            <x-case.announcement-card :announcement="$announcement" />
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- SECTION A — Health Pulse (4 KPI cards, monospace style) --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            <section>
                <p class="font-mono text-sm font-bold text-gray-600 uppercase tracking-widest mb-3">
                    A. Municipality Health Pulse — Last 30 Days
                </p>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

                    <div class="bg-white border-2 border-gray-900 p-4">
                        <p class="font-mono text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Total Cases</p>
                        <p class="font-mono text-3xl font-black text-gray-900 tabular-nums">{{ number_format($summary['total_cases']) }}</p>
                        <p class="font-mono text-xs text-gray-400 mt-1">approved, last 30d</p>
                    </div>

                    <div class="bg-white border-2 border-gray-900 p-4">
                        <p class="font-mono text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Affected Barangays</p>
                        <p class="font-mono text-3xl font-black text-gray-900 tabular-nums">{{ $summary['affected_barangays'] }}</p>
                        <p class="font-mono text-xs text-gray-400 mt-1">with active case reports</p>
                    </div>

                    <div class="bg-white border-2 {{ $summary['critical_count'] > 0 ? 'border-red-600' : 'border-gray-900' }} p-4">
                        <p class="font-mono text-xs font-bold {{ $summary['critical_count'] > 0 ? 'text-red-600' : 'text-gray-500' }} uppercase tracking-widest mb-2">Critical Alerts</p>
                        <p class="font-mono text-3xl font-black {{ $summary['critical_count'] > 0 ? 'text-red-700' : 'text-gray-900' }} tabular-nums">{{ $summary['critical_count'] }}</p>
                        <p class="font-mono text-xs text-gray-400 mt-1">disease&times;barangay pairs</p>
                    </div>

                    <div class="bg-white border-2 {{ $summary['high_count'] > 0 ? 'border-orange-500' : 'border-gray-900' }} p-4">
                        <p class="font-mono text-xs font-bold {{ $summary['high_count'] > 0 ? 'text-orange-600' : 'text-gray-500' }} uppercase tracking-widest mb-2">High Risk</p>
                        <p class="font-mono text-3xl font-black {{ $summary['high_count'] > 0 ? 'text-orange-700' : 'text-gray-900' }} tabular-nums">{{ $summary['high_count'] }}</p>
                        <p class="font-mono text-xs text-gray-400 mt-1">disease&times;barangay pairs</p>
                    </div>

                </div>
            </section>

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- SECTION B — Disease Trend charts --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            <section>
                <p class="font-mono text-sm font-bold text-gray-600 uppercase tracking-widest mb-3">
                    B. Disease Trend Analysis
                </p>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                    {{-- Month-over-month bar chart --}}
                    <div class="lg:col-span-2 bg-white border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="font-mono text-sm font-bold text-gray-800 uppercase tracking-wide">Month-over-Month</p>
                                <p class="font-mono text-xs text-gray-500">This month vs. last month &middot; case count per disease</p>
                            </div>
                            <div class="flex gap-3 text-xs font-mono text-gray-500">
                                <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 bg-teal-600"></span> This month</span>
                                <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 bg-gray-300"></span> Last month</span>
                            </div>
                        </div>
                        <div style="height: 260px;">
                            <canvas id="rhu-trend-chart"></canvas>
                        </div>
                    </div>

                    {{-- Risk distribution doughnut --}}
                    <div class="bg-white border border-gray-200 p-5 flex flex-col">
                        <p class="font-mono text-sm font-bold text-gray-800 uppercase tracking-wide mb-1">Risk Distribution</p>
                        <p class="font-mono text-xs text-gray-500 mb-4">Disease &times; Barangay pairs by DSS risk level</p>
                        <div class="flex-1 flex items-center justify-center">
                            <div style="height: 200px; width: 200px;">
                                <canvas id="rhu-risk-doughnut"></canvas>
                            </div>
                        </div>
                        <div class="mt-4 space-y-1">
                            @php
                                $legendBorder = ['border-gray-400', 'border-amber-500', 'border-orange-500', 'border-red-600'];
                                $legendText   = ['text-gray-500',  'text-amber-700',  'text-orange-700',  'text-red-700'];
                            @endphp
                            @foreach($riskLabels as $i => $label)
                                <div class="flex items-center justify-between font-mono text-xs">
                                    <span class="flex items-center gap-1.5">
                                        <span class="inline-block w-2.5 h-2.5 border-2 {{ $legendBorder[$i] }}"></span>
                                        <span class="{{ $legendText[$i] }} font-semibold">{{ strtoupper($label) }}</span>
                                    </span>
                                    <span class="text-gray-500 tabular-nums">{{ $riskCounts[$i] ?? 0 }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </section>

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- SECTION C — Top Morbidity + Barangay Risk --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            <section>
                <p class="font-mono text-sm font-bold text-gray-600 uppercase tracking-widest mb-3">
                    C. Morbidity &amp; Barangay Risk Intelligence
                </p>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                    {{-- Top 5 morbidity table --}}
                    <div class="bg-white border border-gray-200">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="font-mono text-sm font-bold text-gray-800 uppercase tracking-wide">Top Morbidity</p>
                            <p class="font-mono text-xs text-gray-500">Approved cases — last 30 days</p>
                        </div>
                        @if($topDiseases->isEmpty())
                            <p class="font-mono text-xs text-gray-400 text-center py-8">No approved case data in the last 30 days.</p>
                        @else
                            <x-table.wrapper class="rounded-none border-0">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <x-table.heading class="font-mono text-xs tracking-widest bg-gray-50 text-gray-400 font-bold uppercase">Rank</x-table.heading>
                                        <x-table.heading class="font-mono text-xs tracking-widest bg-gray-50 text-gray-400 font-bold uppercase">Disease</x-table.heading>
                                        <x-table.heading class="font-mono text-xs tracking-widest bg-gray-50 text-gray-400 font-bold uppercase text-right">Cases</x-table.heading>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($topDiseases as $i => $disease)
                                        <tr class="{{ $i === 0 ? 'bg-teal-50' : '' }}">
                                            <x-table.cell class="font-mono text-xs text-gray-400 tabular-nums font-bold">
                                                #{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                                            </x-table.cell>
                                            <x-table.cell class="font-mono text-sm font-semibold {{ $i === 0 ? 'text-teal-800' : 'text-gray-800' }}">
                                                {{ $disease['name'] }}
                                            </x-table.cell>
                                            <x-table.cell class="font-mono font-black tabular-nums text-right {{ $i === 0 ? 'text-teal-700' : 'text-gray-700' }}">
                                                {{ number_format($disease['total_cases']) }}
                                            </x-table.cell>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </x-table.wrapper>
                        @endif
                    </div>

                    {{-- Barangay risk table (from DssService groupByBarangay) --}}
                    <div class="bg-white border border-gray-200">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="font-mono text-sm font-bold text-gray-800 uppercase tracking-wide">Barangay Risk Status</p>
                            <p class="font-mono text-xs text-gray-500">DSS risk assessment — last 30 days</p>
                        </div>
                        @if($grouped->isEmpty())
                            <p class="font-mono text-xs text-gray-400 text-center py-8">No risk data available.</p>
                        @else
                            <x-table.wrapper class="rounded-none border-0">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <x-table.heading class="font-mono text-xs tracking-widest bg-gray-50 text-gray-400 font-bold uppercase">Barangay</x-table.heading>
                                        <x-table.heading class="font-mono text-xs tracking-widest bg-gray-50 text-gray-400 font-bold uppercase">Risk</x-table.heading>
                                        <x-table.heading class="font-mono text-xs tracking-widest bg-gray-50 text-gray-400 font-bold uppercase">Top Disease</x-table.heading>
                                        <x-table.heading class="font-mono text-xs tracking-widest bg-gray-50 text-gray-400 font-bold uppercase text-right">Cases</x-table.heading>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($grouped as $row)
                                        @php $topDisease = $row['diseases']->first(); @endphp
                                        <tr>
                                            <x-table.cell class="font-mono font-semibold text-gray-800">{{ $row['barangay'] }}</x-table.cell>
                                            <x-table.cell>
                                                <x-ui.badge :status="$row['worst_risk']" class="font-mono text-xs tracking-widest uppercase rounded-none" />
                                            </x-table.cell>
                                            <x-table.cell class="font-mono text-xs text-gray-500">{{ $topDisease['health_category'] ?? '—' }}</x-table.cell>
                                            <x-table.cell class="font-mono font-bold tabular-nums text-right text-gray-700">{{ number_format($row['total_cases']) }}</x-table.cell>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </x-table.wrapper>
                        @endif
                    </div>

                </div>
            </section>

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- SECTION D — Pending Reports for Verification --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            <section>
                <p class="font-mono text-sm font-bold text-gray-600 uppercase tracking-widest mb-3">
                    D. Pending Verification Queue — Last 30 Days
                </p>

                <div class="bg-white border border-gray-200">
                    @if($pending->isEmpty())
                        <div class="py-12 text-center">
                            <p class="font-mono text-xs text-gray-400">[ NO PENDING REPORTS ]</p>
                            <p class="font-mono text-xs text-gray-300 mt-1">All citizen submissions have been processed.</p>
                        </div>
                    @else
                        <x-table.wrapper class="rounded-none border-0">
                            <thead>
                                <tr class="border-b-2 border-gray-900">
                                    <x-table.heading class="font-mono text-xs tracking-widest bg-gray-50 text-gray-500 font-bold uppercase">Report ID</x-table.heading>
                                    <x-table.heading class="font-mono text-xs tracking-widest bg-gray-50 text-gray-500 font-bold uppercase">Date</x-table.heading>
                                    <x-table.heading class="font-mono text-xs tracking-widest bg-gray-50 text-gray-500 font-bold uppercase">Reporter</x-table.heading>
                                    <x-table.heading class="font-mono text-xs tracking-widest bg-gray-50 text-gray-500 font-bold uppercase">Barangay</x-table.heading>
                                    <x-table.heading class="font-mono text-xs tracking-widest bg-gray-50 text-gray-500 font-bold uppercase">Category</x-table.heading>
                                    <x-table.heading class="font-mono text-xs tracking-widest bg-gray-50 text-gray-500 font-bold uppercase text-right">Cases</x-table.heading>
                                    <x-table.heading class="font-mono text-xs tracking-widest bg-gray-50 text-gray-500 font-bold uppercase">Actions</x-table.heading>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($pending as $report)
                                    <tr>
                                        <x-table.cell class="font-mono text-xs text-gray-400 tabular-nums">
                                            #{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}
                                        </x-table.cell>
                                        <x-table.cell class="font-mono text-xs text-gray-600">
                                            {{ $report->report_date->format('Y-m-d') }}
                                        </x-table.cell>
                                        <x-table.cell class="font-mono text-xs text-gray-600">{{ $report->user?->name ?? '—' }}</x-table.cell>
                                        <x-table.cell class="font-mono text-sm font-semibold text-gray-800">{{ $report->barangay->name }}</x-table.cell>
                                        <x-table.cell class="font-mono text-xs text-gray-600">{{ $report->healthCategory->name }}</x-table.cell>
                                        <x-table.cell class="font-mono font-black tabular-nums text-right text-gray-800">{{ $report->number_of_cases }}</x-table.cell>
                                        <x-table.cell>
                                            <div class="flex items-center gap-2">
                                                <form method="POST" action="{{ route('rhu.reports.approve', $report) }}">
                                                    @csrf @method('PATCH')
                                                    <x-ui.button type="submit" variant="primary" size="sm"
                                                        class="font-mono text-xs tracking-widest uppercase rounded-none">
                                                        Approve
                                                    </x-ui.button>
                                                </form>
                                                <form method="POST" action="{{ route('rhu.reports.reject', $report) }}">
                                                    @csrf @method('PATCH')
                                                    <x-ui.button type="submit" variant="danger" size="sm"
                                                        class="font-mono text-xs tracking-widest uppercase rounded-none">
                                                        Reject
                                                    </x-ui.button>
                                                </form>
                                            </div>
                                        </x-table.cell>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-table.wrapper>
                        @if($pending->hasPages())
                            <div class="px-4 py-3 border-t border-gray-100 font-mono text-xs">
                                {{ $pending->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </section>

        </div>
    </div>

    @push('scripts')
    <script>
    (function () {
        'use strict';

        var trendLabels   = @json($trendLabels);
        var thisMonthData = @json($thisMonthData);
        var lastMonthData = @json($lastMonthData);
        var riskLabels    = @json($riskLabels);
        var riskCounts    = @json($riskCounts);
        var riskColors    = @json($riskColors);

        // ── Month-over-Month grouped bar chart ──────────────────────────────
        var trendCtx = document.getElementById('rhu-trend-chart');
        if (trendCtx) {
            new Chart(trendCtx, {
                type: 'bar',
                data: {
                    labels: trendLabels,
                    datasets: [
                        {
                            label: 'This Month',
                            data: thisMonthData,
                            backgroundColor: 'rgba(13,148,136,0.8)',
                            borderColor: 'rgba(13,148,136,1)',
                            borderWidth: 1,
                        },
                        {
                            label: 'Last Month',
                            data: lastMonthData,
                            backgroundColor: 'rgba(209,213,219,0.8)',
                            borderColor: 'rgba(156,163,175,1)',
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: "'Courier New', monospace", size: 10 },
                                maxRotation: 30,
                            },
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: {
                                font: { family: "'Courier New', monospace", size: 10 },
                                precision: 0,
                            },
                        },
                    },
                },
            });
        }

        // ── Risk Distribution doughnut ───────────────────────────────────────
        var doughnutCtx = document.getElementById('rhu-risk-doughnut');
        if (doughnutCtx) {
            new Chart(doughnutCtx, {
                type: 'doughnut',
                data: {
                    labels: riskLabels,
                    datasets: [{
                        data: riskCounts,
                        backgroundColor: riskColors,
                        borderWidth: 2,
                        borderColor: '#fff',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { display: false },
                    },
                },
            });
        }
    }());
    </script>
    @endpush
</x-app-layout>
