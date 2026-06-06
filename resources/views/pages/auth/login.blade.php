<x-guest-layout>
    <x-slot name="title">Sign In</x-slot>

<div x-data="{
    mode: '{{ old('first_name') ? 'register' : ($defaultMode ?? 'login') }}',
    email: '{{ old('email', 'admin@gmail.com') }}',
    password: '{{ old('email') ? '' : 'admin1234' }}'
}">

    {{-- Page identity --}}
    <div class="mb-5">
        <p class="text-2xl font-bold text-gray-900" x-text="mode === 'login' ? 'Welcome back' : 'Create an account'"></p>
        <p class="text-sm text-gray-400 mt-0.5" x-text="mode === 'login' ? 'Sign in to continue' : 'Register as a citizen of Luna, Apayao'"></p>
    </div>

    {{-- Tab switcher --}}
    <div class="flex border-b border-gray-200 mb-6">
        <button type="button"
            x-on:click="mode = 'login'"
            :class="mode === 'login'
                ? 'border-b-2 border-primary-600 text-primary-700 font-semibold'
                : 'border-b-2 border-transparent text-gray-400 hover:text-gray-600'"
            class="flex-1 py-2.5 text-sm text-center transition-colors">
            Sign In
        </button>
        <button type="button"
            x-on:click="mode = 'register'"
            :class="mode === 'register'
                ? 'border-b-2 border-primary-600 text-primary-700 font-semibold'
                : 'border-b-2 border-transparent text-gray-400 hover:text-gray-600'"
            class="flex-1 py-2.5 text-sm text-center transition-colors">
            Create Account
        </button>
    </div>

    @if (session('status'))
        <x-ui.alert type="success" class="mb-4">{{ session('status') }}</x-ui.alert>
    @endif

    {{-- ── Login Form ─────────────────────────────────────────────────── --}}
    <form method="POST" action="{{ route('login') }}" x-show="mode === 'login'" class="space-y-4">
        @csrf

        <x-form.input
            name="email"
            label="Email address"
            type="email"
            :required="true"
            autofocus
            autocomplete="username"
            x-model="email" />

        <x-form.input
            name="password"
            label="Password"
            type="password"
            :required="true"
            :togglePassword="true"
            autocomplete="current-password"
            x-model="password" />

        <x-ui.button variant="primary" type="submit" class="w-full py-2.5 text-sm font-semibold">
            Sign in
        </x-ui.button>
    </form>

    {{-- Dev Autofill Options --}}
    @if(app()->environment('local', 'testing') || config('app.debug'))
        <div class="mt-6 pt-6 border-t border-dashed border-gray-200 font-mono text-xs text-gray-500" x-show="mode === 'login'">
            <div class="flex items-center justify-between mb-2">
                <span class="font-bold tracking-tight text-gray-700">DEV AUTOFILL</span>
                <span class="text-[10px] text-gray-400">Click to switch account</span>
            </div>
            <div class="grid grid-cols-3 gap-1.5">
                <button type="button"
                    x-on:click="email = 'admin@gmail.com'; password = 'admin1234'"
                    class="px-2 py-1.5 text-left bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-sm text-gray-700 transition-colors"
                    :class="email === 'admin@gmail.com' ? 'border-primary-500 ring-1 ring-primary-500 bg-primary-50/20' : ''">
                    <span class="block font-bold text-gray-900">Admin</span>
                    <span class="block text-[10px] text-gray-400 truncate">admin@gmail.com</span>
                </button>
                <button type="button"
                    x-on:click="email = 'rhu@gmail.com'; password = 'rhu1234'"
                    class="px-2 py-1.5 text-left bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-sm text-gray-700 transition-colors"
                    :class="email === 'rhu@gmail.com' ? 'border-primary-500 ring-1 ring-primary-500 bg-primary-50/20' : ''">
                    <span class="block font-bold text-gray-900">RHU</span>
                    <span class="block text-[10px] text-gray-400 truncate">rhu@gmail.com</span>
                </button>
                <button type="button"
                    x-on:click="email = 'johnmichael.talbo@gmail.com'; password = 'citizen1234'"
                    class="px-2 py-1.5 text-left bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-sm text-gray-700 transition-colors"
                    :class="email === 'johnmichael.talbo@gmail.com' ? 'border-primary-500 ring-1 ring-primary-500 bg-primary-50/20' : ''">
                    <span class="block font-bold text-gray-900">Talbo</span>
                    <span class="block text-[10px] text-gray-400 truncate">talbo@gmail.com</span>
                </button>
            </div>
        </div>
    @endif

    {{-- ── Register Form ────────────────────────────────────────────── --}}
    <form method="POST" action="{{ route('register') }}" x-show="mode === 'register'" x-cloak class="space-y-3">
        @csrf

        <div class="grid grid-cols-2 gap-3">
            <x-form.input name="first_name" label="First Name" type="text" :required="true" autocomplete="given-name" />
            <x-form.input name="last_name" label="Last Name" type="text" :required="true" autocomplete="family-name" />
        </div>

        <x-form.input name="email" label="Email address" type="email" :required="true" autocomplete="email" />

        <div class="grid grid-cols-2 gap-3">
            <x-form.select name="gender" label="Gender" :options="collect([
                (object)['id' => 'male', 'name' => 'Male'],
                (object)['id' => 'female', 'name' => 'Female'],
            ])" :required="true" />
            <x-form.input name="birthdate" label="Birthdate" type="date" :required="true" />
        </div>

        <div>
            <label for="barangay_id" class="block text-xs font-medium text-gray-600 mb-1">Barangay</label>
            <select id="barangay_id" name="barangay_id"
                class="block w-full border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Select your barangay</option>
                @foreach($barangays ?? [] as $brgy)
                    <option value="{{ $brgy->id }}" {{ old('barangay_id') == $brgy->id ? 'selected' : '' }}>{{ $brgy->name }}</option>
                @endforeach
            </select>
            @error('barangay_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <x-form.input name="password" label="Password" type="password" :required="true" :togglePassword="true" autocomplete="new-password" />
        <x-form.input name="password_confirmation" label="Confirm Password" type="password" :required="true" :togglePassword="true" autocomplete="new-password" />

        <x-ui.button variant="primary" type="submit" class="w-full py-2.5 text-sm font-semibold">
            Create Account
        </x-ui.button>

        <p class="text-xs text-gray-400 text-center leading-relaxed">
            Your account will require admin approval before you can log in.
        </p>
    </form>

</div>

</x-guest-layout>
