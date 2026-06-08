{{-- Mobile backdrop --}}
<div x-show="sidebarOpen"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-40 bg-black/40 lg:hidden"
     @click="sidebarOpen = false"></div>

{{-- Sidebar --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-200 flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0">

    {{-- Branding --}}
    <a href="{{ route('dashboard') }}" class="block bg-primary-900 px-6 py-6 text-center shrink-0 relative">
        {{-- Close button (mobile only) --}}
        <button type="button" @click.prevent="sidebarOpen = false"
                class="absolute top-3 right-3 text-primary-400 hover:text-white lg:hidden">
            <x-ui.icon name="x-mark" class="w-4 h-4" />
        </button>
        <img src="{{ asset('images/luna-apayao_seal.png') }}" alt="Luna Seal" class="h-16 w-16 mx-auto mb-3">
        <p class="text-base font-bold text-white tracking-wide leading-tight">Healthcare Mapping</p>
        <p class="text-xs text-primary-300 mt-1">Luna, Apayao</p>
    </a>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

        @unless(Auth::user()->isRhu())
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <x-ui.icon name="squares-2x2" class="w-5 h-5 shrink-0" />
                Dashboard
            </x-nav-link>

            <x-nav-link :href="route('map.index')" :active="request()->routeIs('map.index')">
                <x-ui.icon name="map" class="w-5 h-5 shrink-0" />
                Map
            </x-nav-link>
        @endunless

        @if(Auth::user()->isCitizen())
            <x-nav-link :href="route('citizen.health-guide')" :active="request()->routeIs('citizen.health-guide')">
                <x-ui.icon name="book-open" class="w-5 h-5 shrink-0" />
                Health Guide
            </x-nav-link>
        @endif

        @if(Auth::user()->isRhu())
            <p class="px-3 pt-5 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Surveillance</p>

            <x-nav-link :href="route('rhu.dashboard')" :active="request()->routeIs('rhu.dashboard')">
                <x-ui.icon name="chart-bar" class="w-5 h-5 shrink-0" />
                RHU Dashboard
            </x-nav-link>

            <x-nav-link :href="route('rhu.case-actions.index')" :active="request()->routeIs('rhu.case-actions.*')">
                <x-ui.icon name="clipboard-document-list" class="w-5 h-5 shrink-0" />
                Case Coordination
            </x-nav-link>

            <x-nav-link :href="route('map.index')" :active="request()->routeIs('map.index')">
                <x-ui.icon name="map" class="w-5 h-5 shrink-0" />
                Disease Map
            </x-nav-link>
        @endif

        @if(Auth::user()->isAdmin())
            <p class="px-3 pt-5 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Management</p>

            <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.index')">
                <x-ui.icon name="document-text" class="w-5 h-5 shrink-0" />
                Reports
            </x-nav-link>

            <x-nav-link :href="route('admin.health-categories.index')" :active="request()->routeIs('admin.health-categories.*')">
                <x-ui.icon name="tag" class="w-5 h-5 shrink-0" />
                Categories
            </x-nav-link>

            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                <x-ui.icon name="users" class="w-5 h-5 shrink-0" />
                <span class="flex-1">Users</span>
                @if($pendingUserCount > 0)
                    <span class="ml-auto shrink-0 inline-flex items-center justify-center h-5 min-w-[20px] px-1.5 text-xs font-bold bg-amber-400 text-amber-900 rounded-full">
                        {{ $pendingUserCount }}
                    </span>
                @endif
            </x-nav-link>

            <p class="px-3 pt-5 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Analytics</p>

            <x-nav-link :href="route('admin.dss')" :active="request()->routeIs('admin.dss')">
                <x-ui.icon name="chart-bar" class="w-5 h-5 shrink-0" />
                Decision Support
            </x-nav-link>

            <x-nav-link :href="route('admin.activity-log.index')" :active="request()->routeIs('admin.activity-log.*')">
                <x-ui.icon name="clipboard-document-list" class="w-5 h-5 shrink-0" />
                Activity Log
            </x-nav-link>
        @endif

    </nav>

    {{-- User footer --}}
    <div class="border-t border-gray-200 px-4 py-4 bg-gray-50">
        <div class="flex items-center gap-3 mb-3">
            <div class="h-9 w-9 rounded-full bg-primary-100 border-2 border-primary-200 flex items-center justify-center shrink-0">
                <span class="text-sm font-bold text-primary-700">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold bg-primary-100 text-primary-700 rounded">
                    {{ ucfirst(Auth::user()->role) }}
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('profile.edit') }}"
               class="flex-1 flex items-center justify-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg transition">
                <x-ui.icon name="user" class="w-3.5 h-3.5 shrink-0" />
                Profile
            </a>
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg transition">
                    <x-ui.icon name="arrow-right-on-rectangle" class="w-3.5 h-3.5 shrink-0" />
                    Log Out
                </button>
            </form>
        </div>
    </div>

</aside>
