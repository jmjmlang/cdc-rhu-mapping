@props([
    'risk',                  // 'Critical' | 'High' | 'Moderate' | 'Low'
    'byBarangay',            // Collection grouped by barangay name → diseases
    'collapsible' => true,   // false = always expanded (Critical)
    'open'        => false,  // pre-expand the <details> toggle
    'scrollAt'    => 5,      // show scroll when barangay count exceeds this
])

@php
    // All display-only — never mutates risk levels from DssService
    $styles = [
        'Critical' => ['dot' => 'bg-red-500',   'header' => 'bg-red-50',   'tag' => 'bg-red-100 text-red-700',    'label' => 'text-red-600',   'actionBg' => 'bg-red-50/60'],
        'High'     => ['dot' => 'bg-orange-400', 'header' => 'bg-orange-50','tag' => 'bg-orange-100 text-orange-700','label' => 'text-orange-600','actionBg' => 'bg-orange-50/60'],
        'Moderate' => ['dot' => 'bg-amber-400',  'header' => 'bg-amber-50', 'tag' => 'bg-amber-100 text-amber-700', 'label' => 'text-amber-600', 'actionBg' => 'bg-amber-50/50'],
        'Low'      => ['dot' => 'bg-gray-300',   'header' => 'bg-gray-50',  'tag' => 'bg-gray-100 text-gray-500',  'label' => 'text-gray-400',  'actionBg' => 'bg-gray-50/60'],
    ];

    $allActions = [
        'Critical' => [
            'Deploy a health response team to the affected barangay immediately.',
            'Alert the Municipal Health Office.',
            'Set up a temporary health station if needed.',
            'Conduct a community-wide health information drive.',
        ],
        'High' => [
            'Increase health worker visits to the affected areas.',
            'Prepare and pre-position medical supplies; coordinate with MHO.',
            'Conduct health awareness sessions in affected barangays.',
        ],
        'Moderate' => [
            'Schedule community health education sessions.',
            'Ensure adequate medical supplies are available.',
            'Follow up on all reported cases.',
        ],
        'Low' => [
            'Maintain regular health surveillance.',
            'Continue ongoing health education programs.',
        ],
    ];

    $s           = $styles[$risk];
    $actions     = $allActions[$risk];
    $scrollClass = $byBarangay->count() > $scrollAt ? 'max-h-64 overflow-y-auto' : '';
@endphp

@if($byBarangay->isNotEmpty())

@if(!$collapsible)

    {{-- ── Non-collapsible (Critical) ──────────────────────────────────── --}}
    <div class="flex items-center gap-2.5 px-5 py-3 border-b border-gray-100 {{ $s['header'] }}">
        <span class="w-2 h-2 rounded-full {{ $s['dot'] }} shrink-0"></span>
        <x-ui.badge :status="$risk" />
    </div>

    <div class="{{ $scrollClass }} divide-y divide-gray-100">
        @foreach($byBarangay as $barangayName => $diseases)
            @php $total = $diseases->sum('total_cases'); @endphp
            <div class="flex items-start justify-between gap-4 px-5 py-3.5 hover:bg-primary-50/30 transition-colors">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-gray-900">{{ $barangayName }}</p>
                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                        @foreach($diseases->sortByDesc('total_cases') as $d)
                            <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-md font-medium {{ $s['tag'] }}">
                                {{ $d['health_category'] }}&nbsp;<span class="opacity-60">{{ $d['total_cases'] }}</span>
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="shrink-0 text-right pt-0.5">
                    <p class="text-sm font-bold tabular-nums text-gray-800">{{ number_format($total) }}</p>
                    <p class="text-xs text-gray-400">total cases</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="px-5 py-3.5 border-t border-gray-100 {{ $s['actionBg'] }}">
        <p class="text-xs font-semibold {{ $s['label'] }} mb-2">Recommended actions</p>
        <ul class="grid sm:grid-cols-2 gap-x-6 gap-y-1">
            @foreach($actions as $action)
                <li class="flex items-start gap-2 text-xs text-gray-600">
                    <span class="w-1 h-1 rounded-full {{ $s['dot'] }} mt-1.5 shrink-0"></span>
                    {{ $action }}
                </li>
            @endforeach
        </ul>
    </div>

@else

    {{-- ── Collapsible (High / Moderate / Low) ─────────────────────────── --}}
    <details class="group" @if($open) open @endif>

        <summary class="flex items-center gap-2.5 px-5 py-3 border-b border-gray-100 {{ $s['header'] }} cursor-pointer list-none select-none hover:brightness-[0.97] transition-all">
            <span class="w-2 h-2 rounded-full {{ $s['dot'] }} shrink-0"></span>
            <x-ui.badge :status="$risk" />
            <span class="ml-auto text-gray-300 group-open:rotate-180 transition-transform duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </span>
        </summary>

        <div class="{{ $scrollClass }} divide-y divide-gray-100">
            @foreach($byBarangay as $barangayName => $diseases)
                @php $total = $diseases->sum('total_cases'); @endphp
                <div class="flex items-start justify-between gap-4 px-5 py-3.5 hover:bg-primary-50/30 transition-colors">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800">{{ $barangayName }}</p>
                        <div class="flex flex-wrap gap-1.5 mt-1.5">
                            @foreach($diseases->sortByDesc('total_cases') as $d)
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-md font-medium {{ $s['tag'] }}">
                                    {{ $d['health_category'] }}&nbsp;<span class="opacity-60">{{ $d['total_cases'] }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="shrink-0 text-right pt-0.5">
                        <p class="text-sm font-semibold tabular-nums text-gray-700">{{ number_format($total) }}</p>
                        <p class="text-xs text-gray-400">total cases</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="px-5 py-3.5 border-t border-gray-100 {{ $s['actionBg'] }}">
            <p class="text-xs font-semibold {{ $s['label'] }} mb-1.5">Recommended actions</p>
            <ul class="flex flex-wrap gap-x-6 gap-y-1">
                @foreach($actions as $action)
                    <li class="flex items-start gap-2 text-xs text-gray-500">
                        <span class="w-1 h-1 rounded-full {{ $s['dot'] }} mt-1.5 shrink-0"></span>
                        {{ $action }}
                    </li>
                @endforeach
            </ul>
        </div>

    </details>

@endif

@endif
