<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Dashboard</h2>
            <div class="flex items-center gap-2 mt-1">
                <span class="inline-flex px-2 py-0.5 text-xs font-semibold bg-primary-50 text-primary-700 rounded-md">Admin</span>
                @if(auth()->user()->org_role)
                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-teal-50 text-teal-700 rounded-sm">{{ auth()->user()->org_role }}</span>
                @endif

            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            @if(session('success'))
                <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
            @endif
            @if(session('error'))
                <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
            @endif

            @if($globalAnnouncements->isNotEmpty())
                <section class="space-y-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700">All User Announcements</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Open municipality-wide advisories from Case Coordination.</p>
                    </div>
                    <div class="space-y-3">
                        @foreach($globalAnnouncements as $announcement)
                            <x-case.announcement-card :announcement="$announcement" />
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ── Stats row ──────────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-5">
                <x-ui.stat-card
                    label="Pending Review"
                    :value="$pendingCount"
                    accent="amber" />
                <x-ui.stat-card
                    label="Approved Reports"
                    :value="$approvedCount"
                    accent="green" />
                <x-ui.stat-card
                    label="Total Cases (Approved)"
                    :value="number_format($totalCases)"
                    accent="primary" />
                <x-ui.stat-card
                    label="Malnutrition Cases"
                    :value="number_format($malnutritionCases)"
                    accent="red" />
            </div>

            {{-- ── Map overview ───────────────────────────────────────────── --}}
            <section>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700">Case Map</h3>
                    <a href="{{ route('map.index') }}" class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:underline">
                        Full map
                        <x-ui.icon name="arrow-right" class="w-3.5 h-3.5" />
                    </a>
                </div>
                    {{-- Shared map helpers (CSS + JS) must appear before @push('scripts') --}}
                <x-map.popup-helpers />
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <x-map.leaflet-base map-id="dashboard-map" height="320px" mobile-height="280px" />
                </div>
            </section>

            {{-- ── Pending Reports ─────────────────────────────────────────── --}}
            <section>
                <div class="mb-3">
                    <h3 class="text-sm font-semibold text-gray-700">Pending Verification</h3>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    @if($pending->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-12">No pending reports in the last 30 days.</p>
                    @else
                        <div class="overflow-x-auto overflow-y-auto" style="max-height: 380px;">
                        <x-table.wrapper class="-m-0">
                            <thead class="sticky top-0 z-10">
                                <tr class="bg-primary-50/60 border-b border-gray-100">
                                    <x-table.heading>No.</x-table.heading>
                                    <x-table.heading>Date</x-table.heading>
                                    <x-table.heading>Reporter</x-table.heading>
                                    <x-table.heading>Barangay</x-table.heading>
                                    <x-table.heading>Category</x-table.heading>
                                    <x-table.heading>Cases</x-table.heading>
                                    <x-table.heading>Actions</x-table.heading>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($pending as $report)
                                    <tr class="hover:bg-primary-50/30 transition-colors">
                                        <x-table.cell class="text-gray-400 font-mono text-xs">No. {{ $report->id }}</x-table.cell>
                                        <x-table.cell class="text-gray-600 text-sm">{{ $report->report_date->format('M d, Y') }}</x-table.cell>
                                        <x-table.cell class="text-gray-600 text-sm">{{ $report->user?->name ?? 'Unknown' }}</x-table.cell>
                                        <x-table.cell class="font-medium text-gray-900">{{ $report->barangay->name }}</x-table.cell>
                                        <x-table.cell class="text-gray-600">{{ $report->healthCategory->name }}</x-table.cell>
                                        <x-table.cell class="font-semibold tabular-nums">{{ $report->number_of_cases }}</x-table.cell>
                                        <x-table.cell>
                                            <div class="flex flex-col sm:flex-row gap-1.5 sm:gap-2">
                                                <form method="POST" action="{{ route('admin.reports.approve', $report) }}">
                                                    @csrf @method('PATCH')
                                                    <x-ui.button variant="primary" type="submit" size="sm">Approve</x-ui.button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.reports.reject', $report) }}">
                                                    @csrf @method('PATCH')
                                                    <x-ui.button variant="danger" type="submit" size="sm">Reject</x-ui.button>
                                                </form>
                                            </div>
                                        </x-table.cell>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-table.wrapper>
                        </div>
                        <div class="px-4 py-3 border-t border-gray-100">{{ $pending->links() }}</div>
                    @endif
                </div>
            </section>

            {{-- ── Recently Approved ───────────────────────────────────────── --}}
            <section>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700">Recently Approved</h3>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-primary-600 bg-primary-50 hover:bg-primary-100 border border-primary-200 transition-colors">
                            View All
                            <x-ui.icon name="arrow-right" class="w-3.5 h-3.5" />
                        </a>
                        <x-ui.button variant="primary" size="sm" x-data="" x-on:click.prevent="$dispatch('open-modal', 'admin-create-report')">
                            <span class="inline-flex items-center gap-1.5">
                                <x-ui.icon name="plus" class="w-3.5 h-3.5" />
                                New Report
                            </span>
                        </x-ui.button>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    @if($approved->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-12">No approved reports in the last 30 days.</p>
                    @else
                        <div class="overflow-x-auto overflow-y-auto" style="max-height: 380px;">
                        <x-table.wrapper class="-m-0">
                            <thead class="sticky top-0 z-10">
                                <tr class="bg-primary-50/60 border-b border-gray-100">
                                    <x-table.heading>No.</x-table.heading>
                                    <x-table.heading>Date</x-table.heading>
                                    <x-table.heading>Barangay</x-table.heading>
                                    <x-table.heading>Category</x-table.heading>
                                    <x-table.heading>Cases</x-table.heading>
                                    <x-table.heading>Reporter</x-table.heading>
                                    <x-table.heading></x-table.heading>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($approved as $report)
                                    <tr class="hover:bg-primary-50/30 transition-colors">
                                        <x-table.cell class="text-gray-400 font-mono text-xs">No. {{ $report->id }}</x-table.cell>
                                        <x-table.cell class="text-gray-600 text-sm whitespace-nowrap">
                                            <x-ui.datetime :date="$report->report_date" :time="$report->created_at" show-time />
                                        </x-table.cell>
                                        <x-table.cell class="font-medium text-gray-900">{{ $report->barangay->name }}</x-table.cell>
                                        <x-table.cell class="text-gray-600">{{ $report->healthCategory->name }}</x-table.cell>
                                        <x-table.cell class="font-semibold tabular-nums">{{ $report->number_of_cases }}</x-table.cell>
                                        <x-table.cell class="text-gray-600 text-sm">{{ $report->user?->name ?? 'Unknown' }}</x-table.cell>
                                        <x-table.cell>
                                            <button
                                                type="button"
                                                x-data=""
                                                x-on:click="$dispatch('open-modal', 'report-actions-{{ $report->id }}')"
                                                class="inline-flex items-center justify-center w-7 h-7 text-gray-400 hover:text-gray-700 hover:bg-gray-100 border border-gray-200 rounded transition-colors"
                                            >
                                                <x-ui.icon name="ellipsis-horizontal" class="w-3.5 h-3.5" />
                                            </button>
                                        </x-table.cell>
                                    </tr>

                                    {{-- Report actions modal (view/edit/delete) --}}
                                    @php $modalName = "report-actions-{$report->id}"; @endphp
                                    <x-ui.modal :name="$modalName" max-width="lg">
                                        <div x-data="{ mode: 'view' }" class="divide-y divide-gray-100">
                                            <div class="px-6 pt-6 pb-4 flex items-start justify-between">
                                                <div>
                                                    <h2 class="text-xl font-bold text-primary-700">Report No. {{ $report->id }}</h2>
                                                    <p class="text-sm text-gray-600 mt-1">{{ $report->user?->name ?? 'Unknown' }}</p>
                                                    <p class="text-xs text-gray-400 mt-0.5">{{ $report->created_at->format('M d, Y, g:i A') }}</p>
                                                </div>
                                                <button type="button" x-on:click="$dispatch('close-modal', '{{ $modalName }}')" class="text-gray-300 hover:text-gray-500 transition-colors mt-1">
                                                    <x-ui.icon name="x-mark" class="w-4 h-4" />
                                                </button>
                                            </div>

                                            <div class="px-6 py-2.5 bg-primary-50/60 flex gap-1">
                                                <button type="button" x-on:click="mode = 'view'" :class="mode === 'view' ? 'bg-white border-gray-300 text-gray-900 shadow-sm rounded-lg' : 'border-transparent text-gray-400 hover:text-gray-700 rounded-lg'" class="px-3 py-1.5 text-xs font-medium border transition-all">View Details</button>
                                                <button type="button" x-on:click="mode = 'edit'" :class="mode === 'edit' ? 'bg-white border-gray-300 text-gray-900 shadow-sm rounded-lg' : 'border-transparent text-gray-400 hover:text-gray-700 rounded-lg'" class="px-3 py-1.5 text-xs font-medium border transition-all">Edit Report</button>
                                                <button type="button" x-on:click="mode = 'delete'" :class="mode === 'delete' ? 'bg-white border-red-200 text-red-700 shadow-sm rounded-lg' : 'border-transparent text-gray-400 hover:text-red-600 rounded-lg'" class="px-3 py-1.5 text-xs font-medium border transition-all">Delete Report</button>
                                            </div>

                                            {{-- View panel --}}
                                            <div x-show="mode === 'view'" x-cloak class="px-6 py-5 space-y-3">
                                                <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                                                    <div><span class="text-xs text-gray-400 font-medium">Barangay</span><p class="font-medium text-gray-900">{{ $report->barangay->name }}</p></div>
                                                    <div><span class="text-xs text-gray-400 font-medium">Category</span><p class="font-medium text-gray-900">{{ $report->healthCategory->name }}</p></div>
                                                    <div><span class="text-xs text-gray-400 font-medium">Date</span><p class="text-gray-700">{{ $report->report_date->format('M d, Y') }}</p></div>
                                                    <div><span class="text-xs text-gray-400 font-medium">Cases</span><p class="font-semibold text-gray-900">{{ $report->number_of_cases }}</p></div>
                                                    <div><span class="text-xs text-gray-400 font-medium">Reporter</span><p class="text-gray-700">{{ $report->user?->name ?? 'Unknown' }}</p></div>
                                                    <div><span class="text-xs text-gray-400 font-medium">Submitted</span><p class="text-gray-700">{{ $report->created_at->format('M d, Y g:i A') }}</p></div>
                                                </div>
                                                @if($report->notes)
                                                    <div><span class="text-xs text-gray-400 font-medium">Notes</span><p class="text-sm text-gray-700 mt-0.5">{{ $report->notes }}</p></div>
                                                @endif
                                            </div>

                                            {{-- Edit panel --}}
                                            <div x-show="mode === 'edit'" x-cloak>
                                                <form method="POST" action="{{ route('admin.reports.update', $report) }}">
                                                    @csrf @method('PATCH')
                                                    <div class="px-6 pt-5 pb-0 space-y-4">
                                                        <x-form.select name="barangay_id" label="Barangay" :options="$barangays" :selected="$report->barangay_id" :required="true" />
                                                        <x-form.select name="health_category_id" label="Health Category" :options="$healthCategories" :selected="$report->health_category_id" :required="true" />
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <x-form.input name="number_of_cases" label="Number of Cases" type="number" :value="$report->number_of_cases" :required="true" />
                                                            <x-form.input name="report_date" label="Report Date" type="date" :value="$report->report_date->format('Y-m-d')" :required="true" />
                                                        </div>
                                                        <x-form.textarea name="notes" label="Notes (optional)" :value="$report->notes" placeholder="Any corrections or details..." />
                                                    </div>
                                                    <div class="px-6 pb-5 pt-4 flex justify-end gap-3">
                                                        <x-ui.button type="button" variant="secondary" x-on:click="$dispatch('close-modal', '{{ $modalName }}')">Cancel</x-ui.button>
                                                        <x-ui.button type="submit" variant="primary">Save Changes</x-ui.button>
                                                    </div>
                                                </form>
                                            </div>

                                            {{-- Delete panel --}}
                                            <div x-show="mode === 'delete'" x-cloak>
                                                <form method="POST" action="{{ route('admin.reports.destroy', $report) }}">
                                                    @csrf @method('DELETE')
                                                    <div class="px-6 pt-5 pb-0 space-y-4">
                                                        <div class="bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700">
                                                            This report will be permanently removed from the map. The submitting citizen will see your deletion reason on their dashboard.
                                                        </div>
                                                        <div>
                                                            <label for="del_reason_{{ $report->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                                Reason for deletion <span class="text-red-500">*</span>
                                                            </label>
                                                            <textarea id="del_reason_{{ $report->id }}" name="deletion_reason" rows="3" required minlength="10" maxlength="500" placeholder="Explain why this report is being removed (min. 10 characters)..." class="block w-full border-gray-300 text-sm shadow-sm focus:border-red-400 focus:ring-red-400"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="px-6 pb-5 pt-4 flex justify-end gap-3">
                                                        <x-ui.button type="button" variant="secondary" x-on:click="$dispatch('close-modal', '{{ $modalName }}')">Cancel</x-ui.button>
                                                        <x-ui.button type="submit" variant="danger">Confirm Delete</x-ui.button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </x-ui.modal>
                                @endforeach
                            </tbody>
                        </x-table.wrapper>
                        </div>
                    @endif
                </div>
            </section>

            {{-- ── Admin Create Report Modal ───────────────────────────────── --}}
            <x-ui.modal name="admin-create-report" focusable>
                <form method="POST" action="{{ route('admin.reports.store') }}" class="p-6">
                    @csrf

                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Create New Report</h2>

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
                        <x-ui.button variant="secondary" type="button" x-on:click="$dispatch('close-modal', 'admin-create-report')">Cancel</x-ui.button>
                        <x-ui.button variant="primary" type="submit">Create Report</x-ui.button>
                    </div>
                </form>
            </x-ui.modal>

        </div>
    </div>

    @push('scripts')
    <script>
    (function () {
        var H  = window.PhcMapHelpers;
        var ml = window.PhcMapInstances['dashboard-map'].markers;

        fetch('{{ route("api.map-data") }}')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                data.forEach(function (item) { H.getOrAssignColor(item.category_id); });

                var byBarangay = {};
                data.forEach(function (item) {
                    if (!byBarangay[item.barangay]) {
                        byBarangay[item.barangay] = {
                            barangay:  item.barangay,
                            latitude:  item.latitude,
                            longitude: item.longitude,
                            items:     [],
                        };
                    }
                    byBarangay[item.barangay].items.push(item);
                });

                Object.values(byBarangay).forEach(function (b) {
                    var brgyTotal = b.items.reduce(function (s, item) { return s + (item.total_cases || 1); }, 0);
                    var dominant  = b.items.reduce(function (best, item) {
                        return (item.total_cases || 1) > (best.total_cases || 1) ? item : best;
                    }, b.items[0]);
                    var c     = H._catColorMap[dominant.category_id];
                    var color = c ? c.stroke : '#0f766e';
                    var fill  = c ? c.fill   : '#14b8a6';

                    L.circle([b.latitude, b.longitude], {
                        radius:      H.severityRadius(brgyTotal),
                        color:       color,
                        fillColor:   fill,
                        fillOpacity: 0.4,
                        weight:      2,
                    })
                    .addTo(ml)
                    .bindTooltip(b.barangay + ' · ' + brgyTotal + ' case' + (brgyTotal !== 1 ? 's' : ''), { direction: 'top', className: 'phc-tooltip' })
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
