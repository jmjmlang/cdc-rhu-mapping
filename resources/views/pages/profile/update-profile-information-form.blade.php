<section>
    <p class="text-sm text-gray-500 mb-4">
        {{ __("Update your account's profile information.") }}
    </p>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <x-form.input name="name" label="Name" :value="$user->name" :required="true" autofocus autocomplete="name" />
        <x-form.input name="email" label="Email" type="email" :value="$user->email" :required="true" autocomplete="username" />

        {{-- Gender --}}
        <div>
            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
            <select
                id="gender"
                name="gender"
                class="block w-full border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 px-3 py-2"
            >
                <option value="">Select</option>
                <option value="male" @selected(old('gender', $user->gender) === 'male')>Male</option>
                <option value="female" @selected(old('gender', $user->gender) === 'female')>Female</option>
            </select>
            @error('gender')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <x-form.input name="birthdate" label="Birthdate" type="date" :value="$user->birthdate?->format('Y-m-d')" />
        <x-form.input name="age" label="Age" type="number" :value="$user->age" />

        @if($user->barangay)
            <p class="text-sm text-gray-500">
                <span class="font-medium text-gray-700">Barangay:</span>
                {{ $user->barangay->name }}
                <span class="text-gray-400 text-xs">(Contact admin to change)</span>
            </p>
        @endif

        @if($user->isAdmin())
            <div class="flex items-center gap-2 py-1">
                <span class="inline-flex px-2.5 py-1 text-xs font-semibold bg-primary-50 text-primary-700 rounded-md">Admin</span>
                @if($user->org_role)
                    <span class="inline-flex px-2.5 py-1 text-xs font-medium bg-teal-50 text-teal-700 rounded-sm">{{ $user->org_role }}</span>
                @endif
            </div>

            <div>
                <label for="org_role" class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                <select id="org_role" name="org_role"
                    class="block w-full border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 px-3 py-2">
                    <option value="">None</option>
                    <option value="Local Health Worker" @selected(old('org_role', $user->org_role) === 'Local Health Worker')>Local Health Worker</option>
                    <option value="SPUP-CDC" @selected(old('org_role', $user->org_role) === 'SPUP-CDC')>SPUP-CDC</option>
                    <option value="Doctor" @selected(old('org_role', $user->org_role) === 'Doctor')>Doctor</option>
                </select>
                @error('org_role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        @endif

        <div class="flex items-center gap-4 pt-2">
            <x-ui.button variant="primary" type="submit">{{ __('Save') }}</x-ui.button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
