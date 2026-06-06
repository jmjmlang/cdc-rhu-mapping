<x-app-layout>
    <x-slot name="title">Reports</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">All Reports</h2>
            @if(isset($pendingCount) && $pendingCount > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold bg-amber-100 text-amber-800 rounded-md self-start">
                    <x-ui.icon name="exclamation-circle" class="w-4 h-4" />
                    {{ $pendingCount }} pending
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
            @endif
            @if(session('error'))
                <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
            @endif

            {{-- ── Status summary cards ───────────────────────────────────── --}}
            <div class="grid grid-cols-3 gap-3 sm:gap-5">
                <x-ui.stat-card
                    label="Approved"
                    :value="$approvedCount"
                    accent="green" />
                <x-ui.stat-card
                    label="Pending Review"
                    :value="$pendingCount"
                    accent="amber" />
                <x-ui.stat-card
                    label="Rejected / Deleted"
                    :value="$rejectedCount"
                    accent="red" />
            </div>

            {{-- ── Pending Verification ───────────────────────────────────── --}}
            @if($pendingReports->isNotEmpty())
            <section>
                <div class="mb-3 flex items-center gap-2">
                    <h3 class="text-sm font-semibold text-gray-700">Pending Verification</h3>
                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold bg-amber-100 text-amber-800 rounded-full">{{ $pendingReports->count() }}</span>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-amber-200">
                    <x-table.wrapper class="-m-0">
                        <thead>
                            <tr class="bg-amber-50/60 border-b border-amber-100">
                                <x-table.heading class="hidden md:table-cell">No.</x-table.heading>
                                <x-table.heading>Date</x-table.heading>
                                <x-table.heading>Barangay</x-table.heading>
                                <x-table.heading class="hidden sm:table-cell">Category</x-table.heading>
                                <x-table.heading>Cases</x-table.heading>
                                <x-table.heading class="hidden lg:table-cell">Reporter</x-table.heading>
                                <x-table.heading></x-table.heading>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($pendingReports as $report)
                                <tr class="hover:bg-amber-50/30 transition-colors">
                                    <x-table.cell class="hidden md:table-cell text-gray-400 font-mono text-xs">No. {{ $report->id }}</x-table.cell>
                                    <x-table.cell class="text-gray-600 text-sm whitespace-nowrap">{{ $report->report_date->format('M d, Y') }}</x-table.cell>
                                    <x-table.cell class="font-medium text-gray-900">{{ $report->barangay->name }}</x-table.cell>
                                    <x-table.cell class="hidden sm:table-cell text-gray-600">{{ $report->healthCategory->name }}</x-table.cell>
                                    <x-table.cell class="font-semibold tabular-nums">{{ $report->number_of_cases }}</x-table.cell>
                                        <x-table.cell class="hidden lg:table-cell text-gray-600 text-sm">{{ $report->user?->name ?? 'Unknown' }}</x-table.cell>
                                    <x-table.cell>
                                        <div class="flex items-center gap-1.5">
                                            <form method="POST" action="{{ route('admin.reports.approve', $report) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-green-700 bg-green-50 hover:bg-green-100 rounded border border-green-200 transition-colors">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.reports.reject', $report) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded border border-red-200 transition-colors">Reject</button>
                                            </form>
                                            <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'report-actions-{{ $report->id }}')"
                                                class="inline-flex items-center justify-center w-7 h-7 text-gray-400 hover:text-gray-700 hover:bg-gray-100 border border-gray-200 rounded transition-colors">
                                                <x-ui.icon name="ellipsis-horizontal" class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </x-table.cell>
                                </tr>
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
                                        <div x-show="mode === 'delete'" x-cloak>
                                            <form method="POST" action="{{ route('admin.reports.destroy', $report) }}">
                                                @csrf @method('DELETE')
                                                <div class="px-6 pt-5 pb-0 space-y-4">
                                                    <div class="bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700">This report will be permanently removed from the map.</div>
                                                    <div>
                                                        <label for="del_reason_{{ $report->id }}" class="block text-sm font-medium text-gray-700 mb-1">Reason for deletion <span class="text-red-500">*</span></label>
                                                        <textarea id="del_reason_{{ $report->id }}" name="deletion_reason" rows="3" required minlength="10" maxlength="500" placeholder="Explain why this report is being removed..." class="block w-full border-gray-300 text-sm shadow-sm focus:border-red-400 focus:ring-red-400"></textarea>
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
            </section>
            @endif

            {{-- ── Approved Reports ─────────────────────────────────────────── --}}
            {{-- ── Filters ──────────────────────────────────────────────── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-2 sm:grid-cols-3 gap-3 items-end">
                    <div>
                        <label for="filter_barangay" class="block text-xs font-medium text-gray-500 mb-1">Barangay</label>
                        <select name="barangay_id" id="filter_barangay" class="block w-full border-gray-300 text-sm shadow-sm focus:border-primary-400 focus:ring-primary-400">
                            <option value="">All Barangays</option>
                            @foreach($barangays as $b)
                                <option value="{{ $b->id }}" @selected(request('barangay_id') == $b->id)>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="filter_category" class="block text-xs font-medium text-gray-500 mb-1">Category</label>
                        <select name="health_category_id" id="filter_category" class="block w-full border-gray-300 text-sm shadow-sm focus:border-primary-400 focus:ring-primary-400">
                            <option value="">All Categories</option>
                            @foreach($healthCategories as $hc)
                                <option value="{{ $hc->id }}" @selected(request('health_category_id') == $hc->id)>{{ $hc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <x-ui.button variant="primary" type="submit" size="sm" class="flex-1">Filter</x-ui.button>
                        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-white hover:bg-gray-50 rounded-lg border border-gray-200 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- ── Reports table ─────────────────────────────────────────── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                @if($reports->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-12">No reports found matching your filters.</p>
                @else
                    @php
                        $sortUrl = function(string $field) use ($sortField, $sortDir) {
                            $newDir = ($sortField === $field && $sortDir === 'asc') ? 'desc' : 'asc';
                            return request()->fullUrlWithQuery(['sort' => $field, 'dir' => $newDir]);
                        };
                        $sortState = function(string $field) use ($sortField, $sortDir) {
                            if ($sortField !== $field) return null;
                            return $sortDir === 'asc' ? 'up' : 'down';
                        };
                    @endphp
                    <x-table.wrapper class="-m-0">
                        <thead>
                            <tr class="bg-primary-50/60 border-b border-gray-100">
                                <x-table.heading class="hidden md:table-cell">No.</x-table.heading>
                                <x-table.heading>
                                    <a href="{{ $sortUrl('report_date') }}" class="inline-flex items-center gap-1 hover:text-gray-900">
                                        Date
                                        @if($sortState('report_date') === 'up')
                                            <x-ui.icon name="arrow-up" class="w-3 h-3 text-gray-500" />
                                        @elseif($sortState('report_date') === 'down')
                                            <x-ui.icon name="arrow-down" class="w-3 h-3 text-gray-500" />
                                        @else
                                            <x-ui.icon name="arrows-up-down" class="w-3 h-3 text-gray-400" />
                                        @endif
                                    </a>
                                </x-table.heading>
                                <x-table.heading>Barangay</x-table.heading>
                                <x-table.heading class="hidden sm:table-cell">Category</x-table.heading>
                                <x-table.heading>
                                    <a href="{{ $sortUrl('number_of_cases') }}" class="inline-flex items-center gap-1 hover:text-gray-900">
                                        Cases
                                        @if($sortState('number_of_cases') === 'up')
                                            <x-ui.icon name="arrow-up" class="w-3 h-3 text-gray-500" />
                                        @elseif($sortState('number_of_cases') === 'down')
                                            <x-ui.icon name="arrow-down" class="w-3 h-3 text-gray-500" />
                                        @else
                                            <x-ui.icon name="arrows-up-down" class="w-3 h-3 text-gray-400" />
                                        @endif
                                    </a>
                                </x-table.heading>
                                <x-table.heading class="hidden lg:table-cell">Reporter</x-table.heading>
                                <x-table.heading></x-table.heading>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($reports as $report)
                                <tr class="hover:bg-primary-50/30 transition-colors">
                                    <x-table.cell class="hidden md:table-cell text-gray-400 font-mono text-xs">No. {{ $report->id }}</x-table.cell>
                                    <x-table.cell class="text-gray-600 text-sm whitespace-nowrap">{{ $report->report_date->format('M d, Y') }}</x-table.cell>
                                    <x-table.cell class="font-medium text-gray-900">{{ $report->barangay->name }}</x-table.cell>
                                    <x-table.cell class="hidden sm:table-cell text-gray-600">{{ $report->healthCategory->name }}</x-table.cell>
                                    <x-table.cell class="font-semibold tabular-nums">{{ $report->number_of_cases }}</x-table.cell>
                                        <x-table.cell class="hidden lg:table-cell text-gray-600 text-sm">{{ $report->user?->name ?? 'Unknown' }}</x-table.cell>
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
                                                            <textarea id="del_reason_{{ $report->id }}" name="deletion_reason" rows="3" required minlength="10" maxlength="500" placeholder="Explain why this report is being removed..." class="block w-full border-gray-300 text-sm shadow-sm focus:border-red-400 focus:ring-red-400"></textarea>
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
                    <div class="px-4 py-3 border-t border-gray-100">{{ $reports->links() }}</div>
                @endif
            </div>

            {{-- ── Rejected / Deleted Reports ──────────────────────────────── --}}
            <section>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700">Rejected & Deleted</h3>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    @if($rejected->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-12">No rejected or deleted reports in the last 30 days.</p>
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
                                    <x-table.heading>Status</x-table.heading>
                                    <x-table.heading>Reason</x-table.heading>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($rejected as $report)
                                    @php $isDeleted = $report->trashed(); @endphp
                                    <tr class="{{ $isDeleted ? 'bg-red-50' : 'bg-amber-50' }}">
                                        <x-table.cell class="text-gray-400 font-mono text-xs">No. {{ $report->id }}</x-table.cell>
                                        <x-table.cell class="text-gray-600 text-sm whitespace-nowrap">{{ $report->report_date->format('M d, Y') }}</x-table.cell>
                                        <x-table.cell class="font-medium text-gray-900">{{ $report->barangay->name }}</x-table.cell>
                                        <x-table.cell class="text-gray-600">{{ $report->healthCategory->name }}</x-table.cell>
                                        <x-table.cell class="font-semibold tabular-nums">{{ $report->number_of_cases }}</x-table.cell>
                                            <x-table.cell class="text-gray-600 text-sm">{{ $report->user?->name ?? 'Unknown' }}</x-table.cell>
                                        <x-table.cell>
                                            @if($isDeleted)
                                                <x-ui.badge status="deleted" />
                                            @else
                                                <x-ui.badge status="rejected" />
                                            @endif
                                        </x-table.cell>
                                        <x-table.cell class="text-gray-500 text-xs max-w-xs">
                                            {{ $report->deletion_reason ?? 'No reason given' }}
                                        </x-table.cell>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-table.wrapper>
                        </div>
                    @endif
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
