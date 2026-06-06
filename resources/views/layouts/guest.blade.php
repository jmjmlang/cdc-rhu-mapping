<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ ($title ?? null) ? $title . ' | Healthcare Mapping System' : 'Healthcare Mapping System' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-slate-50 min-h-screen flex flex-col">

        {{-- ── Top branding bar ────────────────────────────────────────────── --}}
        <header class="bg-primary-900 text-white">
            <div class="max-w-2xl mx-auto px-6 py-5 flex items-center justify-center gap-5">
                <img src="{{ asset('images/luna-apayao_seal.png') }}" alt="Municipality of Luna, Apayao"
                     class="w-12 h-12 object-contain flex-shrink-0 hidden sm:block" />
                <div class="text-center">
                    <p class="text-[10px] font-semibold tracking-[0.18em] text-primary-300 uppercase mb-1">Municipality of Luna &middot; Apayao Province</p>
                    <h1 class="text-lg sm:text-xl font-bold leading-tight tracking-tight">Public Healthcare Case Mapping System</h1>
                </div>
                <img src="{{ asset('images/spup_seal.png') }}" alt="Saint Paul University Philippines"
                     class="w-12 h-12 object-contain flex-shrink-0 hidden sm:block" />
            </div>
        </header>

        {{-- ── Main form area ───────────────────────────────────────────────── --}}
        <main class="flex-1 flex items-start justify-center px-4 py-10">
            <div class="w-full max-w-md">


                {{-- Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-7 py-8">
                    {{ $slot }}
                </div>
            </div>
        </main>

        {{-- ── Footer: institutional credit ────────────────────────────────── --}}
        <footer class="bg-white border-t border-gray-100 py-6 px-6">
            <div class="max-w-xl mx-auto text-center space-y-1">
                <p class="text-xs text-gray-500">In partnership with</p>
                <p class="text-xs font-medium text-gray-700">SPUP Community Development Center Foundation, Inc. (SPUP-CDCFI)</p>
                <p class="text-xs font-medium text-gray-700">German Doctors e.V.</p>
            </div>
        </footer>

    </body>
</html>

