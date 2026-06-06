<x-app-layout>
    <x-slot name="title">Activity Log</x-slot>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Activity Log</h2>
            <p class="text-sm text-gray-500 mt-0.5">Audit trail of system activity</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
            @endif

            <x-ui.card>
                @if($logs->isEmpty())
                    <div class="text-center py-12">
                        <p class="text-sm text-gray-400">No activity recorded yet.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($logs as $log)
                            @php
                                $props = $log->properties ?? [];
                                $displayName = $props['display_name'] ?? $props['target_name'] ?? null;

                                [$badgeText, $badgeClass] = match($log->action) {
                                    'profile_updated'  => ['PROFILE',  'bg-gray-100 text-gray-500'],
                                    'report_submitted' => ['SUBMITTED','bg-amber-50 text-amber-600'],
                                    'report_approved'  => ['APPROVED', 'bg-green-50 text-green-700'],
                                    'report_rejected'  => ['REJECTED', 'bg-red-50 text-red-600'],
                                    'report_edited'    => ['EDITED',   'bg-yellow-50 text-yellow-600'],
                                    'report_deleted'   => ['DELETED',  'bg-red-100 text-red-700'],
                                    'role_changed'     => ['ROLE',     'bg-primary-50 text-primary-700'],
                                    default            => [strtoupper(str_replace('_', ' ', $log->action)), 'bg-gray-100 text-gray-500'],
                                };
                            @endphp
                            <div class="flex items-baseline gap-3 px-1 py-2.5 font-mono text-xs">
                                {{-- Badge --}}
                                <span class="shrink-0 inline-block w-20 text-center px-1.5 py-0.5 font-semibold tracking-wide rounded {{ $badgeClass }}">
                                    {{ $badgeText }}
                                </span>

                                {{-- Main line --}}
                                <span class="flex-1 text-gray-600 truncate">
                                    User&nbsp;No.&nbsp;{{ $log->user_id }}
                                    @if($displayName)
                                        <span class="text-gray-400">({{ $displayName }})</span>
                                    @endif
                                    @if($log->user?->org_role)
                                        <span class="text-teal-600">[{{ $log->user->org_role }}]</span>
                                    @endif
                                    @if(!empty($props['report_id']))
                                        <span class="text-gray-400">&nbsp;·&nbsp;Report&nbsp;No.&nbsp;{{ $props['report_id'] }}</span>
                                    @endif
                                    @if($log->action === 'role_changed' && !empty($props['old_role']))
                                        <span class="text-gray-400">&nbsp;·&nbsp;{{ ucfirst($props['old_role']) }}&nbsp;→&nbsp;{{ ucfirst($props['new_role'] ?? '?') }}</span>
                                        @if(!empty($props['target_id']))
                                            <span class="text-gray-400">&nbsp;[target No. {{ $props['target_id'] }}]</span>
                                        @endif
                                    @endif
                                </span>

                                {{-- Timestamp --}}
                                <span class="shrink-0 text-gray-400 whitespace-nowrap">
                                    {{ $log->created_at->format('M d · g:i A') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if($logs->hasPages())
                        <div class="px-1 pt-3 border-t border-gray-100">
                            {{ $logs->links() }}
                        </div>
                    @endif
                @endif
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
