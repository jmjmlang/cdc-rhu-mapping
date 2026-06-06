<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ ($title ?? null) ? $title . ' | Healthcare Mapping System' : 'Healthcare Mapping System' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

            <!-- Chart.js (CDN, no API key needed, like Leaflet) -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

        @stack('head')
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-surface">
            @include('layouts.navigation')

            <!-- Main content -->
            <div class="lg:pl-72 flex flex-col min-h-screen">

                <!-- Mobile top bar -->
                <header class="sticky top-0 z-30 flex items-center justify-between px-4 h-14 bg-white border-b border-gray-200 shadow-sm lg:hidden">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                        <img src="{{ asset('images/luna-apayao_seal.png') }}" alt="Luna Seal" class="h-7 w-7">
                        <span class="text-sm font-bold text-gray-900 tracking-wide">Healthcare Mapping</span>
                    </a>
                    <button @click="sidebarOpen = true" class="p-1.5 text-gray-500 hover:text-gray-800">
                        <x-ui.icon name="bars-3" class="w-5 h-5" />
                    </button>
                </header>

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white border-b border-gray-200">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')

        {{-- Force reload if browser restores a bfcached page (prevents stale Alpine.js state) --}}
        <script>
            window.addEventListener('pageshow', function (e) {
                if (e.persisted) { window.location.reload(); }
            });
        </script>
    </body>
</html>
