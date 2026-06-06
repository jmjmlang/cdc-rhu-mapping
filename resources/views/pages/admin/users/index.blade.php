<x-app-layout>
    <x-slot name="title">Users</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Account Registrations</h2>
                <p class="text-sm text-gray-500 mt-0.5">Review and approve citizen account requests</p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button type="button" variant="primary" size="sm" x-data="" x-on:click="$dispatch('open-modal', 'create-rhu-user')">
                    Add RHU
                </x-ui.button>
                @if($pending->count() > 0)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold bg-amber-100 text-amber-800 rounded-md">
                        {{ $pending->count() }} pending {{ Str::plural('request', $pending->count()) }}
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
            @endif

            {{-- ── Pending accounts ─────────────────────────────────────── --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Pending Approval</h3>

                @if($pending->isEmpty())
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-12 text-center">
                        <x-ui.icon name="check-circle" class="w-9 h-9 text-gray-200 mx-auto mb-3" />
                        <p class="text-base text-gray-400 font-medium">No pending registrations.</p>
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <x-table.wrapper>
                            <thead>
                                <tr class="bg-primary-50/60 border-b border-gray-100">
                                    <x-table.heading>Name</x-table.heading>
                                    <x-table.heading class="hidden sm:table-cell">Email / ID</x-table.heading>
                                    <x-table.heading class="hidden md:table-cell">Gender</x-table.heading>
                                    <x-table.heading class="hidden md:table-cell">Age</x-table.heading>
                                    <x-table.heading>Registered</x-table.heading>
                                    <x-table.heading></x-table.heading>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($pending as $user)
                                    <tr class="bg-amber-50/40">
                                        <x-table.cell>
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                                    <span class="text-xs font-bold text-amber-700">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <span class="font-medium text-gray-800 text-sm">{{ $user->name }}</span>
                                            </div>
                                        </x-table.cell>
                                        <x-table.cell class="hidden sm:table-cell text-gray-500 text-sm font-mono">
                                            {{ $user->email }}
                                        </x-table.cell>
                                        <x-table.cell class="hidden md:table-cell text-gray-600 text-sm capitalize">
                                                {{ $user->gender ?? 'Not set' }}
                                        </x-table.cell>
                                        <x-table.cell class="hidden md:table-cell text-gray-600 text-sm">
                                                {{ $user->age ? $user->age . ' yrs' : 'Not set' }}
                                        </x-table.cell>
                                        <x-table.cell class="text-gray-500 text-sm whitespace-nowrap">
                                            <x-ui.datetime :date="$user->created_at" />
                                        </x-table.cell>
                                        <x-table.cell>
                                            <div class="flex items-center gap-2 justify-end">
                                                <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                                                    @csrf @method('PATCH')
                                                    <x-ui.button type="submit" variant="primary" size="sm">Approve</x-ui.button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.users.reject', $user) }}">
                                                    @csrf @method('PATCH')
                                                    <x-ui.button type="submit" variant="danger" size="sm">Reject</x-ui.button>
                                                </form>
                                            </div>
                                        </x-table.cell>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-table.wrapper>
                    </div>
                @endif
            </div>

            {{-- ── Recently reviewed ────────────────────────────────────── --}}
            @if($recent->isNotEmpty())
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Recently Reviewed</h3>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <x-table.wrapper>
                            <thead>
                                <tr class="bg-primary-50/60 border-b border-gray-100">
                                    <x-table.heading>Name</x-table.heading>
                                    <x-table.heading class="hidden sm:table-cell">Email / ID</x-table.heading>
                                    <x-table.heading class="hidden md:table-cell">Gender</x-table.heading>
                                    <x-table.heading class="hidden md:table-cell">Age</x-table.heading>
                                    <x-table.heading>Registered</x-table.heading>
                                    <x-table.heading>Status</x-table.heading>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recent as $user)
                                    <tr>
                                        <x-table.cell>
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                                    <span class="text-xs font-bold text-gray-500">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <span class="font-medium text-gray-700 text-sm">{{ $user->name }}</span>
                                            </div>
                                        </x-table.cell>
                                        <x-table.cell class="hidden sm:table-cell text-gray-500 text-sm font-mono">
                                            {{ $user->email }}
                                        </x-table.cell>
                                        <x-table.cell class="hidden md:table-cell text-gray-600 text-sm capitalize">
                                                {{ $user->gender ?? 'Not set' }}
                                        </x-table.cell>
                                        <x-table.cell class="hidden md:table-cell text-gray-600 text-sm">
                                                {{ $user->age ? $user->age . ' yrs' : 'Not set' }}
                                        </x-table.cell>
                                        <x-table.cell class="text-gray-500 text-sm whitespace-nowrap">
                                            <x-ui.datetime :date="$user->created_at" />
                                        </x-table.cell>
                                        <x-table.cell>
                                            <x-ui.badge :status="$user->account_status" />
                                        </x-table.cell>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-table.wrapper>
                    </div>
                </div>
            @endif

            {{-- ── All Users ────────────────────────────────────────────── --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">All Users ({{ $allUsers->count() }})</h3>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="overflow-y-auto max-h-[480px] phc-scrollbar">
                        <x-table.wrapper>
                            <thead class="sticky top-0 z-10">
                                <tr class="bg-primary-50/60 border-b border-gray-100">
                                    <x-table.heading>Name</x-table.heading>
                                    <x-table.heading class="hidden sm:table-cell">Email</x-table.heading>
                                    <x-table.heading>Role</x-table.heading>
                                    <x-table.heading class="hidden md:table-cell">Barangay</x-table.heading>
                                    <x-table.heading>Status</x-table.heading>
                                    <x-table.heading class="hidden lg:table-cell">Registered</x-table.heading>
                                    <x-table.heading></x-table.heading>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($allUsers as $user)
                                    <tr>
                                        <x-table.cell>
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-full {{ $user->role === 'admin' ? 'bg-primary-100' : 'bg-gray-100' }} flex items-center justify-center shrink-0">
                                                    <span class="text-xs font-bold {{ $user->role === 'admin' ? 'text-primary-700' : 'text-gray-500' }}">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <span class="font-medium text-gray-800 text-sm">{{ $user->name }}</span>
                                            </div>
                                        </x-table.cell>
                                        <x-table.cell class="hidden sm:table-cell text-gray-500 text-sm font-mono">
                                            {{ $user->email }}
                                        </x-table.cell>
                                        <x-table.cell>
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-1.5">
                                                    <x-ui.badge :status="$user->role === 'rhu' ? 'RHU' : ucfirst($user->role)" />
                                                    @if($user->id === auth()->id())
                                                        <span class="text-xs font-medium text-gray-500">(You)</span>
                                                    @endif
                                                </div>
                                                @if(in_array($user->role, ['admin', 'rhu'], true) && $user->org_role)
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-teal-50 text-teal-700 rounded-sm">
                                                        {{ $user->org_role }}
                                                    </span>
                                                @endif
                                            </div>
                                        </x-table.cell>
                                        <x-table.cell class="hidden md:table-cell text-gray-600 text-sm">
                                            {{ $user->barangay?->name ?? 'Not assigned' }}
                                        </x-table.cell>
                                        <x-table.cell>
                                            <x-ui.badge :status="$user->account_status" />
                                        </x-table.cell>
                                        <x-table.cell class="hidden lg:table-cell text-gray-500 text-sm whitespace-nowrap">
                                            <x-ui.datetime :date="$user->created_at" />
                                        </x-table.cell>
                                        <x-table.cell>
                                            <button
                                                type="button"
                                                x-data=""
                                                x-on:click="$dispatch('open-modal', 'edit-user-{{ $user->id }}')"
                                                class="px-2.5 py-1 text-xs font-medium text-gray-600 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors"
                                            >Edit</button>
                                        </x-table.cell>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-table.wrapper>
                    </div>
                </div>
            </div>

            @include('pages.admin.users.edit-modal')

            <x-ui.modal name="create-rhu-user" maxWidth="md">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Create RHU Account</h3>
                            <p class="text-sm text-gray-500 mt-1">RHU accounts are fixed to the RHU role.</p>
                        </div>
                        <button type="button" x-on:click="$dispatch('close-modal', 'create-rhu-user')" class="text-gray-400 hover:text-gray-600">
                            <x-ui.icon name="x-mark" class="w-4 h-4" />
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.users.rhu.store') }}" class="space-y-4">
                        @csrf
                        <x-form.input name="name" label="Full Name" required />
                        <x-form.input name="email" label="Email" type="email" required />
                        <x-form.input name="org_role" label="RHU Designation" placeholder="e.g. Rural Health Midwife" required />
                        <x-form.select name="barangay_id" label="Assigned Barangay (optional)" :options="$barangays" />
                        <x-form.input name="new_password" label="Temporary Password" type="password" required />

                        <div class="bg-primary-50 border border-primary-100 px-4 py-3 text-sm text-primary-800">
                            This creates a single-role RHU account. It cannot be converted into admin or citizen from user management.
                        </div>

                        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                            <x-ui.button variant="secondary" type="button" x-on:click="$dispatch('close-modal', 'create-rhu-user')">Cancel</x-ui.button>
                            <x-ui.button variant="primary" type="submit">Create RHU Account</x-ui.button>
                        </div>
                    </form>
                </div>
            </x-ui.modal>

        </div>
    </div>
</x-app-layout>
