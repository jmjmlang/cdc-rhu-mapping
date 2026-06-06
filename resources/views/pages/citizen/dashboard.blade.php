<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            @if(session('success'))
                <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
            @endif
            @if(session('error'))
                <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
            @endif

            @if($citizenAnnouncements->isNotEmpty())
                <section class="space-y-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700">RHU and Admin Announcements</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Current guidance for your area and municipality-wide advisories.</p>
                    </div>
                    <div class="space-y-3">
                        @foreach($citizenAnnouncements as $announcement)
                            <x-case.announcement-card :announcement="$announcement" />
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- -- Case Map --------------------------------------------------- --}}
            <section>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700">Case Map</h3>
                    <a href="{{ route('map.index') }}" class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:underline">
                        Full map
                        <x-ui.icon name="arrow-right" class="w-3 h-3" />
                    </a>
                </div>

                <x-map.popup-helpers />
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <x-map.leaflet-base map-id="citizen-map" height="320px" mobile-height="280px" />
                </div>
            </section>

            {{-- -- My Report Stats ----------------------------------------------- --}}
            <section>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">My Reporting Activity</h3>
                <div class="grid grid-cols-3 gap-3">
                    <x-ui.stat-card label="Total Submitted" :value="$ownTotal" description="All time" accent="primary" />
                    <x-ui.stat-card label="Approved" :value="$ownApproved" description="Verified reports" accent="primary" />
                    <x-ui.stat-card label="Pending Review" :value="$ownPending" description="Awaiting admin" accent="amber" />
                </div>
            </section>

            {{-- -- My Reports ------------------------------------------------ --}}
            <section>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700">My Reports</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Submit and track your health case reports.</p>
                    </div>
                    <x-ui.button variant="primary" x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-report')">
                        <span class="inline-flex items-center gap-1.5">
                            <x-ui.icon name="plus" class="w-3.5 h-3.5" />
                            New Report
                        </span>
                    </x-ui.button>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    @if($reports->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-12">No reports yet. Use the button above to submit your first report.</p>
                    @else
                        <div class="overflow-x-auto overflow-y-auto" style="max-height: 380px;">
                        <x-table.wrapper>
                            <thead class="sticky top-0 z-10">
                                <tr class="bg-primary-50/60 border-b border-gray-100">
                                    <x-table.heading>Date</x-table.heading>
                                    <x-table.heading>Barangay</x-table.heading>
                                    <x-table.heading>Category</x-table.heading>
                                    <x-table.heading>Cases</x-table.heading>
                                    <x-table.heading>Status</x-table.heading>
                                    <x-table.heading>Info</x-table.heading>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($reports as $report)
                                    <tr class="{{ $report->trashed() ? 'bg-red-50' : 'hover:bg-primary-50/30' }} transition-colors">
                                        <x-table.cell class="text-gray-600 text-sm whitespace-nowrap">
                                            <x-ui.datetime :date="$report->report_date" :time="$report->created_at" show-time />
                                        </x-table.cell>
                                        <x-table.cell class="font-medium text-gray-900">{{ $report->barangay->name }}</x-table.cell>
                                        <x-table.cell class="text-gray-600">{{ $report->healthCategory->name }}</x-table.cell>
                                        <x-table.cell class="font-semibold tabular-nums">{{ $report->number_of_cases }}</x-table.cell>
                                        <x-table.cell>
                                            @if($report->trashed())
                                                <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">Deleted</span>
                                            @else
                                                <x-ui.badge :status="$report->status" />
                                            @endif
                                        </x-table.cell>
                                        <x-table.cell>
                                            @if($report->trashed() && $report->deletion_reason)
                                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'deletion-reason-{{ $report->id }}')" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-600 bg-white hover:bg-gray-50 rounded-lg border border-gray-200 transition-colors">View reason</button>
                                            @elseif($report->trashed())
                                                <span class="text-xs text-red-500">Removed by admin</span>
                                            @else
                                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'citizen-report-detail-{{ $report->id }}')" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-600 bg-white hover:bg-gray-50 rounded-lg border border-gray-200 transition-colors">
                                                    View Detail
                                                </button>
                                            @endif
                                        </x-table.cell>
                                    </tr>

                                    @if($report->trashed() && $report->deletion_reason)
                                        <x-ui.modal :name="'deletion-reason-' . $report->id" max-width="md">
                                            <div class="p-6">
                                                <h3 class="text-base font-semibold text-gray-900 mb-1">Report Removed</h3>
                                                <p class="text-xs text-gray-400 mb-4">{{ $report->barangay->name }} &middot; {{ $report->healthCategory->name }} &middot; {{ $report->report_date->format('M d, Y') }}</p>
                                                <div class="bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700">
                                                    <p class="text-xs font-semibold text-red-800 mb-1">Admin's Reason</p>
                                                    {{ $report->deletion_reason }}
                                                </div>
                                                <div class="mt-4 flex justify-end">
                                                    <x-ui.button variant="secondary" type="button" x-on:click="$dispatch('close-modal', 'deletion-reason-{{ $report->id }}')">Close</x-ui.button>
                                                </div>
                                            </div>
                                        </x-ui.modal>
                                    @endif

                                    @if(!$report->trashed())
                                        <x-ui.modal :name="'citizen-report-detail-' . $report->id" max-width="md">
                                            <div class="p-6 space-y-4">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <p class="text-xs font-medium text-gray-400">Report No. {{ $report->id }}</p>
                                                        <p class="text-sm text-gray-500 mt-0.5">{{ $report->report_date->format('F d, Y') }}</p>
                                                    </div>
                                                    <x-ui.badge :status="$report->status" />
                                                </div>
                                                <div class="grid grid-cols-2 gap-px bg-gray-100 border border-gray-100">
                                                    <div class="bg-white px-3 py-2.5">
                                                        <p class="text-xs text-gray-400">Barangay</p>
                                                        <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $report->barangay->name }}</p>
                                                    </div>
                                                    <div class="bg-white px-3 py-2.5">
                                                        <p class="text-xs text-gray-400">Category</p>
                                                        <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $report->healthCategory->name }}</p>
                                                    </div>
                                                    <div class="bg-white px-3 py-2.5">
                                                        <p class="text-xs text-gray-400">Cases Reported</p>
                                                        <p class="text-2xl font-bold text-gray-900">{{ $report->number_of_cases }}</p>
                                                    </div>
                                                    <div class="bg-white px-3 py-2.5">
                                                        <p class="text-xs text-gray-400">Submitted</p>
                                                        <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $report->created_at->format('M d, g:i A') }}</p>
                                                    </div>
                                                </div>
                                                @if($report->reviewed_at)
                                                    @php $isApproved = $report->status === 'approved'; @endphp
                                                    <div class="flex items-center gap-2 px-3 py-2 text-sm {{ $isApproved ? 'bg-green-50 text-green-800 border border-green-100' : 'bg-red-50 text-red-800 border border-red-100' }}">
                                                        @if($isApproved)
                                                            <x-ui.icon name="check-circle" class="w-4 h-4 shrink-0" />
                                                        @else
                                                            <x-ui.icon name="x-circle" class="w-4 h-4 shrink-0" />
                                                        @endif
                                                        Reviewed on {{ $report->reviewed_at->format('M d, Y') }}
                                                    </div>
                                                @endif
                                                @if($report->notes)
                                                    <div>
                                                        <p class="text-xs font-medium text-gray-400 mb-1">Notes</p>
                                                        <p class="text-sm text-gray-700 bg-gray-50 border border-gray-100 px-3 py-2">{{ $report->notes }}</p>
                                                    </div>
                                                @endif
                                                <div class="flex justify-end">
                                                    <x-ui.button variant="secondary" type="button" x-on:click="$dispatch('close-modal', 'citizen-report-detail-{{ $report->id }}')">Close</x-ui.button>
                                                </div>
                                            </div>
                                        </x-ui.modal>
                                    @endif
                                @endforeach
                            </tbody>
                        </x-table.wrapper>
                        </div>
                        <div class="px-4 py-3 border-t border-gray-100">{{ $reports->links() }}</div>
                    @endif
                </div>
            </section>

        </div>
    </div>

    {{-- -- Create Report Modal ----------------------------------------------- --}}
    <x-ui.modal name="create-report" focusable>
        <form method="POST" action="{{ route('citizen.reports.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-semibold text-gray-900 mb-4">Submit New Report</h2>

            <div class="space-y-4">
                <x-form.select name="barangay_id" label="Barangay" :options="$barangays" :required="true" />
                <x-form.select name="health_category_id" label="Health Category" :options="$healthCategories" :required="true" />

                <div class="grid grid-cols-2 gap-4">
                    <x-form.input name="number_of_cases" label="Number of Cases" type="number" placeholder="1" :required="true" />
                    <x-form.input name="report_date" label="Report Date" type="date" :value="now()->format('Y-m-d')" :required="true" />
                </div>

                <x-form.textarea name="notes" label="Notes (optional)" placeholder="Any additional details..." />
                <x-form.textarea name="symptoms" label="Symptoms (optional)" placeholder="e.g. fever, cough, headache..." />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-ui.button variant="secondary" type="button" x-on:click="$dispatch('close-modal', 'create-report')">Cancel</x-ui.button>
                <x-ui.button variant="primary" type="submit">Submit Report</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    @push('scripts')
    <script>
    (function () {
        var H  = window.PhcMapHelpers;
        var ml = window.PhcMapInstances['citizen-map'].markers;

        function severityColor(n) {
            if (n >= 30) return { stroke: '#991b1b', fill: '#ef4444' };
            if (n >= 15) return { stroke: '#92400e', fill: '#f59e0b' };
            if (n >= 5)  return { stroke: '#065f46', fill: '#10b981' };
            return              { stroke: '#1e40af', fill: '#60a5fa' };
        }

        fetch('{{ route("api.map-data") }}')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                data.forEach(function (item) { H.getOrAssignColor(item.category_id); });

                var byBarangay = {};
                data.forEach(function (item) {
                    if (!byBarangay[item.barangay]) {
                        byBarangay[item.barangay] = {
                            barangay:    item.barangay,
                            latitude:    item.latitude,
                            longitude:   item.longitude,
                            total_cases: 0,
                            items:       [],
                        };
                    }
                    byBarangay[item.barangay].total_cases += item.total_cases;
                    byBarangay[item.barangay].items.push(item);
                });

                Object.values(byBarangay).forEach(function (b) {
                    var c = severityColor(b.total_cases);
                    L.circle([b.latitude, b.longitude], {
                        radius:      H.severityRadius(b.total_cases),
                        color:       c.stroke,
                        fillColor:   c.fill,
                        fillOpacity: 0.30,
                        weight:      1.5,
                    })
                    .addTo(ml)
                    .bindTooltip(b.barangay, { direction: 'top', className: 'phc-tooltip' })
                    .bindPopup(
                        H.buildBarangayPopupHTML(b.barangay, b.items, null),
                        { maxWidth: 260, className: 'phc-popup' }
                    );
                });
            });
    }());
    </script>
    @endpush
</x-app-layout>
