<section>
    <p class="text-sm text-gray-500 mb-4">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </p>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <x-form.input id="update_password_current_password" name="current_password" label="Current Password" type="password" bag="updatePassword" :togglePassword="true" autocomplete="current-password" />
        <x-form.input id="update_password_password" name="password" label="New Password" type="password" bag="updatePassword" :togglePassword="true" autocomplete="new-password" />
        <x-form.input id="update_password_password_confirmation" name="password_confirmation" label="Confirm Password" type="password" bag="updatePassword" :togglePassword="true" autocomplete="new-password" />

        <div class="flex items-center gap-4 pt-2">
            <x-ui.button variant="primary" type="submit">{{ __('Save') }}</x-ui.button>

            @if (session('status') === 'password-updated')
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
