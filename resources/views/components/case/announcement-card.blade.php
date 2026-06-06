@props(['announcement'])

@php
    $audienceLabels = [
        'admin_only' => 'Admin only',
        'citizen_visible' => 'Affected citizens',
        'affected_citizens' => 'Affected citizens',
        'all_users' => 'All users',
    ];

    $actionLabels = [
        'field_visit' => 'Field visit',
        'health_education' => 'Health education',
        'referral' => 'Referral coordination',
        'follow_up' => 'Follow-up instruction',
        'supply_request' => 'Supply request',
        'case_validation' => 'Case validation',
        'public_advisory' => 'Public health advisory',
    ];

    $isUrgent = $announcement->priority === 'urgent';
@endphp

<article {{ $attributes->class([
    'bg-white border px-4 py-4 shadow-sm',
    $isUrgent ? 'border-red-200' : 'border-primary-100',
]) }}>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 space-y-2">
            <div class="flex flex-wrap items-center gap-2">
                <span @class([
                    'inline-flex items-center px-2 py-0.5 text-xs font-bold uppercase tracking-wide',
                    $isUrgent ? 'bg-red-100 text-red-800' : 'bg-primary-100 text-primary-800',
                ])>
                    {{ $isUrgent ? 'Urgent' : 'Advisory' }}
                </span>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    {{ $audienceLabels[$announcement->audience] ?? 'Audience' }}
                </span>
                <span class="text-xs text-gray-400">
                    {{ $announcement->caseReport->barangay->name }}, {{ $announcement->caseReport->healthCategory->name }}
                </span>
            </div>

            <div>
                <p class="text-sm font-semibold text-gray-900">
                    {{ $actionLabels[$announcement->action_type] ?? ucfirst(str_replace('_', ' ', $announcement->action_type)) }}
                </p>
                <p class="text-sm text-gray-700 mt-1 leading-relaxed">{{ $announcement->message }}</p>
            </div>

            <p class="text-xs text-gray-500">
                {{ $announcement->author?->isAdmin() ? 'Admin' : 'RHU staff' }}
                <span class="mx-1">|</span>
                {{ $announcement->created_at->format('M d, Y') }}
                @if($announcement->due_date)
                    <span class="mx-1">|</span>
                    Due {{ $announcement->due_date->format('M d, Y') }}
                @endif
            </p>
        </div>

        <x-ui.badge :status="$announcement->status" />
    </div>
</article>
