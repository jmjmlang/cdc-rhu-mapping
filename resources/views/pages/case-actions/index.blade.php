<x-app-layout>
    <x-slot name="title">Case Coordination</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">Case Coordination</h2>
                <p class="text-sm text-gray-500 mt-1">RHU actions and citizen-visible guidance by barangay and disease.</p>
            </div>
            <x-ui.badge status="RHU" />
        </div>
    </x-slot>

    @php
        $storeRoute = route('rhu.case-actions.store');
        $completeRouteName = 'rhu.case-actions.complete';
        $indexRoute = route('rhu.case-actions.index');
        $actionLabels = [
            'field_visit' => 'Field visit',
            'health_education' => 'Health education',
            'referral' => 'Referral coordination',
            'follow_up' => 'Follow-up instruction',
            'supply_request' => 'Supply request',
            'case_validation' => 'Case validation',
            'public_advisory' => 'Public health advisory',
        ];
        $audienceLabels = [
            'admin_only' => 'Admin only',
            'citizen_visible' => 'Affected citizens',
            'affected_citizens' => 'Affected citizens',
            'all_users' => 'All users',
        ];
        $pairOptions = $reportPairs->map(fn ($pair) => [
            'barangay_id' => (string) $pair->barangay_id,
            'barangay_name' => $pair->barangay?->name ?? 'Unknown barangay',
            'health_category_id' => (string) $pair->health_category_id,
            'health_category_name' => $pair->healthCategory?->name ?? 'Unknown disease',
            'total_cases' => (int) $pair->total_cases,
            'latest_report_date' => $pair->latest_report_date,
        ])->values();
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
            @endif
            @if(session('error'))
                <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-ui.stat-card label="Open Actions" :value="$openCount" accent="amber" />
                <x-ui.stat-card label="Public Updates" :value="$citizenVisibleCount" accent="primary" />
                <x-ui.stat-card label="Completed" :value="$completedCount" accent="green" />
            </div>

            <section
                class="bg-white border border-gray-200 rounded-none"
                x-data="{
                    barangayId: @js(old('barangay_id', '')),
                    diseaseId: @js(old('health_category_id', '')),
                    pairs: @js($pairOptions),
                    diseases() {
                        const seen = {};
                        return this.pairs
                            .filter((pair) => String(pair.barangay_id) === String(this.barangayId))
                            .filter((pair) => {
                                if (seen[pair.health_category_id]) return false;
                                seen[pair.health_category_id] = true;
                                return true;
                            });
                    },
                    selectedPair() {
                        return this.pairs.find((pair) =>
                            String(pair.barangay_id) === String(this.barangayId)
                            && String(pair.health_category_id) === String(this.diseaseId)
                        );
                    },
                    resetDisease() {
                        this.diseaseId = '';
                    },
                }"
            >
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Add coordination action</h3>
                    <p class="text-sm text-gray-500 mt-1">Select one barangay, then one disease. No citizen names are shown here.</p>
                </div>

                <form method="POST" action="{{ $storeRoute }}" class="p-5 space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div>
                            <label for="barangay_id" class="block text-sm font-medium text-gray-700 mb-1">Barangay *</label>
                            <select
                                id="barangay_id"
                                name="barangay_id"
                                x-model="barangayId"
                                x-on:change="resetDisease()"
                                required
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 px-3 py-2"
                            >
                                <option value="">Select barangay...</option>
                                @foreach($barangayOptions as $barangay)
                                    <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                                @endforeach
                            </select>
                            @error('barangay_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="health_category_id" class="block text-sm font-medium text-gray-700 mb-1">Disease *</label>
                            <select
                                id="health_category_id"
                                name="health_category_id"
                                x-model="diseaseId"
                                x-bind:disabled="!barangayId"
                                required
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 px-3 py-2 disabled:bg-gray-100 disabled:text-gray-400"
                            >
                                <option value="">Select disease...</option>
                                <template x-for="pair in diseases()" :key="pair.health_category_id">
                                    <option :value="pair.health_category_id" x-text="pair.health_category_name"></option>
                                </template>
                            </select>
                            @error('health_category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div x-show="selectedPair()" x-cloak class="bg-primary-50 border border-primary-100 px-4 py-3 text-sm text-primary-800">
                        <span class="font-semibold">Selected pair:</span>
                        <span x-text="selectedPair()?.barangay_name"></span>
                        <span>/</span>
                        <span x-text="selectedPair()?.health_category_name"></span>
                        <span class="text-primary-600">
                            (<span x-text="selectedPair()?.total_cases"></span> active reported case(s))
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="action_type" class="block text-sm font-medium text-gray-700 mb-1">Action *</label>
                            <select id="action_type" name="action_type" required class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 px-3 py-2">
                                @foreach($actionLabels as $value => $label)
                                    <option value="{{ $value }}" @selected(old('action_type', 'follow_up') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                            <select id="priority" name="priority" required class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 px-3 py-2">
                                <option value="routine" @selected(old('priority') === 'routine')>Routine</option>
                                <option value="urgent" @selected(old('priority') === 'urgent')>Urgent</option>
                            </select>
                        </div>

                        <div>
                            <label for="audience" class="block text-sm font-medium text-gray-700 mb-1">Visibility *</label>
                            <select id="audience" name="audience" required class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 px-3 py-2">
                                <option value="admin_only" @selected(old('audience') === 'admin_only')>Admin only</option>
                                <option value="affected_citizens" @selected(old('audience') === 'affected_citizens' || old('audience') === 'citizen_visible')>Affected citizens</option>
                                <option value="all_users" @selected(old('audience') === 'all_users')>All users</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <x-form.input name="due_date" label="Due date" type="date" />
                        </div>

                        <div class="flex items-end">
                            <x-ui.button type="submit" variant="primary" class="w-full">Add Action</x-ui.button>
                        </div>
                    </div>

                    <x-form.textarea name="message" label="Instruction / coordination note" rows="4" required placeholder="Example: RHU will conduct barangay validation tomorrow and advise affected residents to visit the health center if symptoms worsen." />
                </form>
            </section>

            <section class="space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Coordination log</h3>
                        <p class="text-sm text-gray-500 mt-1">Admin and RHU share this operational view.</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ $indexRoute }}?status=open" class="px-4 py-2 text-sm font-semibold border {{ $status === 'open' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-200' }}">Open</a>
                        <a href="{{ $indexRoute }}?status=completed" class="px-4 py-2 text-sm font-semibold border {{ $status === 'completed' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-200' }}">Completed</a>
                        <a href="{{ $indexRoute }}?status=all" class="px-4 py-2 text-sm font-semibold border {{ $status === 'all' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-200' }}">All</a>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-none">
                    @if($actions->isEmpty())
                        <p class="text-sm text-gray-500 text-center py-12">No coordination actions yet.</p>
                    @else
                        <x-table.wrapper class="-m-0 rounded-none border-0">
                            <thead>
                                <tr class="bg-primary-50/60 border-b border-gray-100">
                                    <x-table.heading>Area / Disease</x-table.heading>
                                    <x-table.heading>Action</x-table.heading>
                                    <x-table.heading>Message</x-table.heading>
                                    <x-table.heading>Visibility</x-table.heading>
                                    <x-table.heading>Status</x-table.heading>
                                    <x-table.heading></x-table.heading>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($actions as $action)
                                    @php
                                        $caseReport = $action->caseReport;
                                        $barangayName = $caseReport?->barangay?->name ?? 'Unavailable barangay';
                                        $healthCategoryName = $caseReport?->healthCategory?->name ?? 'Unavailable disease';
                                    @endphp
                                    <tr>
                                        <x-table.cell>
                                            <p class="text-sm font-semibold text-gray-900">{{ $barangayName }}</p>
                                            <p class="text-xs text-gray-600">{{ $healthCategoryName }}</p>
                                            @if($caseReport?->trashed())
                                                <p class="text-xs text-gray-500 mt-1">Original report deleted</p>
                                            @endif
                                        </x-table.cell>
                                        <x-table.cell>
                                            <p class="text-sm font-semibold text-gray-800">{{ $actionLabels[$action->action_type] ?? ucfirst(str_replace('_', ' ', $action->action_type)) }}</p>
                                            <p class="text-xs {{ $action->priority === 'urgent' ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                                {{ ucfirst($action->priority) }}@if($action->due_date) / due {{ $action->due_date->format('M d') }}@endif
                                            </p>
                                        </x-table.cell>
                                        <x-table.cell class="max-w-md">
                                            <p class="text-sm text-gray-800 leading-relaxed">{{ $action->message }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ ucfirst($action->author?->role ?? 'staff') }} update / {{ $action->created_at->format('M d, g:i A') }}
                                            </p>
                                        </x-table.cell>
                                        <x-table.cell>
                                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 text-xs font-semibold rounded-md whitespace-nowrap bg-gray-100 text-gray-700">
                                                {{ $audienceLabels[$action->audience] ?? 'Audience' }}
                                            </span>
                                        </x-table.cell>
                                        <x-table.cell>
                                            <x-ui.badge :status="$action->status" />
                                        </x-table.cell>
                                        <x-table.cell>
                                            @if($action->status === 'open')
                                                <form method="POST" action="{{ route($completeRouteName, $action) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-ui.button type="submit" variant="secondary" size="sm">Complete</x-ui.button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-500">{{ $action->completed_at?->format('M d, Y') }}</span>
                                            @endif
                                        </x-table.cell>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-table.wrapper>
                        <div class="px-4 py-3 border-t border-gray-100">{{ $actions->links() }}</div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
