<x-app-layout>
    <x-slot name="title">Profile</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profile</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
            @endif

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Account Settings</h1>
                <p class="mt-1 text-sm text-gray-500">Manage your profile information, password, and account.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Profile Information --}}
                <x-ui.card title="Profile Information">
                    @include('pages.profile.update-profile-information-form')
                </x-ui.card>

                {{-- Update Password --}}
                <x-ui.card title="Update Password">
                    @include('pages.profile.update-password-form')
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
