@php
    /**
     * DISPLAY-ONLY grouping.
     * Risk levels are assigned by DssService — nothing is reassigned here.
     */
    $riskOrder = ['Low' => 0, 'Moderate' => 1, 'High' => 2, 'Critical' => 3];

    $allRows = $grouped->flatMap(fn ($b) => $b['diseases'])
        ->sortByDesc(fn ($r) => [$riskOrder[$r['risk_level']], $r['total_cases']])
        ->values();

    $needsAttention = $allRows->whereIn('risk_level', ['Critical', 'High']);
    $monitoring     = $allRows->whereIn('risk_level', ['Moderate', 'Low']);
@endphp

{{-- ── Empty State ─────────────────────────────────────────────────────── --}}
@if($grouped->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-16 text-center">
        <x-ui.icon name="clipboard-document-list" class="w-10 h-10 text-gray-200 mx-auto mb-3" />
        <p class="text-sm font-medium text-gray-400">No approved case reports in the last 30 days.</p>
        <p class="text-sm text-gray-300 mt-1">Once citizens submit and you approve reports, the analysis will appear here.</p>
    </div>
@else

    {{-- ── Needs Immediate Attention (Critical + High) ─────────────────── --}}
    @if($needsAttention->isNotEmpty())
    <section>
        <div class="mb-3">
            <h3 class="text-sm font-semibold text-gray-700">Needs Immediate Attention</h3>
        </div>
        <div class="space-y-3">
            @foreach(['Critical', 'High'] as $riskLevel)
                @php
                    $rows       = $needsAttention->where('risk_level', $riskLevel);
                    $byBarangay = $rows->groupBy('barangay');
                @endphp
                @if($rows->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <x-dss.risk-group
                            :risk="$riskLevel"
                            :by-barangay="$byBarangay"
                            :collapsible="$riskLevel !== 'Critical'"
                            :open="true"
                            :scroll-at="4"
                        />
                    </div>
                @endif
            @endforeach
        </div>
    </section>
    @endif

    {{-- ── Under Monitoring (Moderate + Low) ──────────────────────────── --}}
    @if($monitoring->isNotEmpty())
    <section>
        <div class="mb-3">
            <h3 class="text-sm font-semibold text-gray-700">Under Monitoring</h3>
        </div>
        <div class="space-y-3">
            @foreach(['Moderate', 'Low'] as $riskLevel)
                @php
                    $rows       = $monitoring->where('risk_level', $riskLevel);
                    $byBarangay = $rows->groupBy('barangay');
                @endphp
                @if($rows->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <x-dss.risk-group
                            :risk="$riskLevel"
                            :by-barangay="$byBarangay"
                            collapsible
                            :open="$riskLevel === 'Moderate'"
                            :scroll-at="5"
                        />
                    </div>
                @endif
            @endforeach
        </div>
    </section>
    @endif

@endif
